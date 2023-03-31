<?php
include_once("include/display_table.class.php");
include_once("defaults.class.php");
include_once("dbconst.class.php");
include_once("VoucherRegister.class.php");
error_reporting(1);
class pp_deposits
{
	public $actionPage = "../pp_deposits.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_billperiod;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_voucher_register = new VoucherRegister($this->m_dbConn);
	}

	public function startProcess()
	{
		$errorExists=0;
		try {
			$this->m_dbConn->begin_transaction();
			if($_REQUEST['insert']=='Submit' && $errorExists==0)
			{
				// if (!isset($_POST['deposits_type']) && !isset($_POST['deposit_amt']) && !isset($_POST['deposit_date'])
				// && !isset($_POST['deposit_chrgs']) && !isset($_POST['member_cat']) && !isset($_POST['introducer_1']) && !isset($_POST['introducer_2']) && !isset($_POST['ac_op']) && !isset($_POST['status']) && !isset($_POST['installment_amt']) &&
				// !isset($_POST['period']) && !isset($_POST['int_rate']) && !isset($_POST['int_amt']) && !isset($_POST['joint_name']) && !isset($_POST['note_footer']) && !isset($_POST['deposit_maturity_date']) && !isset($_POST['maturity_type']) && !isset($_POST['maturity_cal']) && !isset($_POST['maturity_cal_method']) && !isset($_POST['maturity_amt']) && !isset($_POST['agent_name']) && !isset($_POST['auto_renew'])) {
				// 	throw new Exception("Please fill/Select all fileds", 1);
				// } 
				echo "<pre>";
				print_r($_POST);
				echo "</pre>";
				$deposit_type = $_POST['deposit_type'];
				$deposit_amt = $_POST['deposit_amt'];
				$deposit_date = getDBFormatDate($_POST['deposit_date']);
				$deposit_chrgs = $_POST['deposit_chrgs'];
				$member_category = $_POST['member_cat'];
				$introducer_1_id = $_POST['introducer_1'];
				$introducer_2_id = $_POST['introducer_2'];
				$ac_op = $_POST['ac_op'];
				$status = $_POST['status'];
				$installment_amt = $_POST['installment_amt'];
				$period = $_POST['period'];
				$int_rate = $_POST['int_rate'];
				$int_amt = $_POST['int_amt'];
				$joint_name = $_POST['joint_name'];
				$note = $this->m_dbConn->escapeString($_POST['note_footer']);

				$deposit_maturity_date = getDBFormatDate($_POST['deposit_maturity_date']);
				$deposit_maturity_type = $_POST['maturity_type'];
				$deposit_maturity_cal = $_POST['maturity_cal'];
				$deposit_maturity_cal_method = $_POST['maturity_cal_method'];
				$maturity_amt = $_POST['maturity_amt'];

				$agent = $_POST['agent_name'];
				$auto_renew = $_POST['auto_renew'];
				$account_type = $_POST['account_type'];

				//Receipt Fields
				
				$Receipt_Type 	 = $_POST['receipt_type'];

				if($Receipt_Type == RECEIPT_CASH){
					
					$Receipt_Bank_id = $_POST['cash_name'];
					$Receipt_payer_bank = '--';
					$Receipt_payer_branch = '--';
					$Receipt_depposit_slip = DEPOSIT_CASH;
					$Receipt_Cheque_No = CASH_CHEQUE_NO; 
				}
				else{
					$Receipt_Bank_id = $_POST['cheque_bank_name'];
					$Receipt_payer_bank = $_POST['payer_bank'];
					$Receipt_payer_branch = $_POST['payer_branch'];
					$Receipt_depposit_slip = $_POST['bank_deposit_slip'];
					$Receipt_Cheque_No = $_POST['cheque_number']; 
				}
				
				if (is_null($auto_renew)) {
					$auto_renew = 0;
				}

				$member_id = $_POST['id'];

				$sql = "INSERT INTO pp_deposits (member_id, subcategory_id, amount, deposit_date, maturity_date, deposit_chrgs, member_cat_id, introducer_1, introducer_2, ac_operator, status, installment_amt, period, int_rate, int_amt, joint_name, note, maturity_type, maturity_cal, maturity_cal_method, maturity_amt, agent_id, auto_renew) VALUES('$member_id', '$deposit_type', '$deposit_amt', '$deposit_date', '$deposit_maturity_date', '$deposit_chrgs', '$member_category', '$introducer_1_id', '$introducer_2_id', '$ac_op', '$status', '$installment_amt', '$period', '$int_rate', '$int_amt', '$joint_name', '$note', '$deposit_maturity_type', '$deposit_maturity_cal', '$deposit_maturity_cal_method', '$maturity_amt', '$agent', '$auto_renew')";			
				
				$deposit_id = $this->m_dbConn->insert($sql);
			
				$ledger_name = $this->getSubCategory($deposit_type).'_'.$deposit_id;

				// Inserting in ledger table
				
				$sql1 = "INSERT INTO ledger (society_id, categoryid, ledger_name, show_in_bill, taxable, sale, purchase, income, expense, payment, receipt, opening_type, opening_balance, note, opening_date, parent_id) VALUES (".$_SESSION['society_id'].", '".$deposit_type."', '".$ledger_name."', 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, '".$note."', '".$deposit_date."', ".$member_id.")";

				$ledger_id = $this->m_dbConn->insert($sql1);

				// Inserting ledger_id in loan table
				$sql2 = "UPDATE pp_deposits SET ledger_id = ".$ledger_id." WHERE id = ".$deposit_id;

				$res2 = $this->m_dbConn->update($sql2);
				
				if($res2){
					$doCommit = true;
					if($account_type == FIXED_DEPOSIT_ACCOUNT){
					
						$insert_query="insert into chequeentrydetails (`VoucherDate`,`ChequeDate`,`ChequeNumber`,`Amount`,`PaidBy`,`BankID`,`PayerBank`,`PayerChequeBranch`,`DepositID`,`EnteredBy`,`Comments`) values ('".$deposit_date."','".$deposit_date."','".$Receipt_Cheque_No."','".$deposit_amt."','".$ledger_id."','".$Receipt_Bank_id."','".$this->m_dbConn->escapeString($Receipt_payer_bank)."','".$this->m_dbConn->escapeString($Receipt_payer_branch)."','".$Receipt_depposit_slip."','".$_SESSION['login_id']."','".$note."')";
						$RefNo = $this->m_dbConn->insert($insert_query);

						$VoucherData = array();

						$receiptByArray = array("Date"=>$deposit_date,"RefNo"=>$RefNo,"RefTable"=>TABLE_CHEQUE_DETAILS , "VoucherType"=>VOUCHER_RECEIPT, "By"=>"", "To"=>$Receipt_Bank_id,"Debit"=>"","Credit"=>$deposit_amt,"Note"=>$note);
						$receiptToArray = array("Date"=>$deposit_date,"RefNo"=>$RefNo,"RefTable"=>TABLE_CHEQUE_DETAILS , "VoucherType"=>VOUCHER_RECEIPT, "By"=>$ledger_id, "To"=>"", "Debit"=>$deposit_amt, "Credit"=>"","Note"=>$note);

						array_push($VoucherData,$receiptByArray);
						array_push($VoucherData,$receiptToArray);
						
						$VoucherRegisterResult = $this->obj_voucher_register->processdata($VoucherData);
						$doCommit = $VoucherRegisterResult['status'] == true;
					}

					if($doCommit)
					{
						$this->m_dbConn->commit();
						return 'Insert';
					}
					else
					{
						$this->m_dbConn->rollback();
						return 'Failed to insert record';
					}
				}
			}				
		} catch (Exception $e) {
			echo $e->getMessage();
			$this->m_dbConn->rollback();
		}
		 

	}
	
	public function combobox11($query,$id,$showDefaultText = false)
	{
		if($showDefaultText == true)
		{
			$str ="<option value='0'>Please Select</option>";
		}
		else
		{
			$str;	
		}
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				$i=0;
				foreach($value as $k => $v)
				{
					if($i==0)
					{
						if($v==$id)
						{
							$sel = "selected";
						}
						else
						{
							$sel = "";	
						}
						$str.="<OPTION VALUE=".$v." ".$sel.">";
					}
					else
					{
						$str.=$v."</OPTION>";
					}
					$i++;
				}
			}
		}
			return $str;
	}
	public function display1($rsas)
	{
			$thheader=array('Wing','Society Name','Address','City','Region','Postal Code','Country','Phone No.1','Phone No.2','Fax No.','Email id','Member Since');
			$this->display_pg->edit="getsociety";
			$this->display_pg->th=$thheader;
			$this->display_pg->mainpg="society.php";
			$res=$this->society_list_show($rsas);
			return $res;
	}
	public function pgnation()
	{
		if(isset($_SESSION['sadmin']))
		{
			//$sql1 = "select * from society where status='Y' order by society_id desc";
			
			//$cntr = "select count(*) as cnt from society where status='Y' ";
			$sql1 = "select * from society where status='Y' and society_id='".$_SESSION['society_id']."' order by society_id desc";
			//echo $sql1;
			$cntr = "select count(*) as cnt from society where status='Y' and society_id='".$_SESSION['society_id']."'";
		}
		else
		{
			$sql1 = "select * from society where status='Y' and society_id='".$_SESSION['society_id']."' order by society_id desc";
			//echo $sql1;
			$cntr = "select count(*) as cnt from society where status='Y' and society_id='".$_SESSION['society_id']."'";
			
		}
		
		$this->display_pg->sql1=$sql1;
		$this->display_pg->cntr1=$cntr;
		$this->display_pg->mainpg="society.php";
		$limit="20";
		$page = $_REQUEST['page'];
		$extra="";
		$res=$this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	
	public function society_list_show($res)
	{
		if($res<>"")
		{
		?>
		<table align="center" border="0">
		<tr height="30" bgcolor="#CCCCCC">
        	<th width="220">Society Name</th>
            <th width="200">Address</th>
            <th width="100">Landmark</th>
            <th width="90">Phone No.</th>
            <th width="170">Email</th>
            <?php if(isset($_SESSION['sadmin'])){?>
            <th width="100">View</th>
            <th width="100">Add</th>
            <?php }?>
            
            <?php if(isset($_SESSION['admin'])){?>
            <th width="100">Action</th>
            <?php }?>
            <th width="90">View Report</th>
            <!--<th width="90">Import Wing</th>-->
        	<th width="50">Edit</th>
            <?php if(isset($_SESSION['sadmin'])){?>
            <th width="70">Delete</th>
            <?php }?>
        </tr>
        <?php foreach($res as $k => $v){?>
        <tr height="25" bgcolor="#BDD8F4" align="center">
        	<td align="center"><?php echo $res[$k]['society_name'];?></td>
            <td align="center">
			<div style="overflow-y:scroll;overflow-x:hidden;width:190px; height:80px; border:solid #CCCCCC 1px;" align="center">
			<?php echo $res[$k]['society_add'];?>
            </div>
            </td>
            <td align="center"><?php echo $res[$k]['landmark'];?></td>
            <td align="center"><?php echo $res[$k]['phone'];?></td>
            <td align="center"><a href="mailto:<?php echo $res[$k]['email'];?>" style="color:#0000FF" target="_blank"><?php echo $res[$k]['email'];?></a></td>
            
            <?php if(isset($_SESSION['sadmin'])){?>
            <td align="center"><a href="wing.php?imp&idd=<?php echo time();?>&sa&sid=<?php echo $res[$k]['society_id'];?>&id=<?php echo rand('0000000','9999999');?>" style="color:#0000FF;"><b>View Wing</b></a></td>
            <td align="center"><a href="wing.php?imp&ssid=<?php echo $res[$k]['society_id'];?>&s&idd=<?php echo time();?>" style="color:#0000FF;"><b>Add Wing</b></a></td>
            <?php }else{?>
            <td align="center"><a href="wing.php?imp&ssid=<?php echo $res[$k]['society_id'];?>&s&idd=<?php echo time();?>" style="color:#0000FF;"><b>Add Wing</b></a></td>
            <?php }?>
            
            
            <td align="center">
            <a href="reports.php?&sid=<?php echo $res[$k]['society_id'];?>" style="color:#0000FF;"><b>Member's Due</b></a>
            </td>
            
           <!-- <td align="center">
            <a href="wing_import.php?&sid=<?php //echo $res[$k]['society_id'];?>" style="color:#0000FF;"><b>Import Wing</b></a>
            </td>-->
            
            <td align="center">
            <a href="javascript:void(0);" onclick="society_edit(<?php echo $res[$k]['society_id']?>)"><img src="images/edit.gif" /></a>
            </td>
            
            <?php if(isset($_SESSION['sadmin'])){?>
            <td align="center">
            <?php if($this->chk_delete_perm_sadmin()==1){?>
            <a href="javascript:void(0);" onclick="del_society(<?php echo $res[$k]['society_id']?>);"><img src="images/del.gif" /></a>
            <?php }else{?>
            <a href="del_control_sadmin.php?prm" target="_blank" style="text-decoration:none;"><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a>
            <?php }?>
            </td>
            <?php }?>
        </tr>
        <?php }?>
        </table>
		<?php
		}
		else
		{
			?>
            <table align="center" border="0">
            <tr>
            	<td><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
            </tr>
            </table>	
            <?php	
		}
	}
	public function chk_delete_perm_sadmin()
	{
		$sql = "select * from del_control_sadmin where status='Y'";
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_sadmin'];
	}
	public function selecting()
	{
		$sql1 = "select s.society_id,s.society_code,s.society_name,s.circle,DATE_FORMAT(registration_date, '%d-%m-%Y'),s.registration_no,s.society_add,s.city,s.landmark,s.state,s.region,s.postal_code,s.country,s.phone,s.phone2,s.fax_number, s.pan_no, s.tan_no, s.service_tax_no, s.email,s.url, s.member_since, s.bill_cycle, s.int_rate, s.int_tri_amt, s.int_method, s.rebate_method, s.rebate, s.chq_bounce_charge, s.bill_method, s.show_wing, s.show_parking, s.show_area,s.calc_int,s.property_tax_no,s.water_tax_no,s.show_share,s.bill_footer, s.sms_start_text, s.sms_end_text, s.send_reminder_sms, s.bill_due_date,s.show_floor,s.unit_presentation, s.cc_email ,s.bill_as_link,s.email_contactno,s.neft_notify_by_email,s.show_address_in_email,s.apply_service_tax,s.service_tax_threshold,s.igst_tax_rate,s.cgst_tax_rate,s.sgst_tax_rate,s.cess_tax_rate ,s.gstin_no,s.apply_GST_On_Interest,s.apply_GST_above_Threshold,DATE_FORMAT(s.gst_start_date, '%d-%m-%Y') from society as s where s.society_id='".$_REQUEST['societyId']."'";
		$var=$this->m_dbConn->select($sql1);
		return $var;
	}
	public function deleting()
	{
		$sql0 = "select count(*)as cnt from unit where society_id='".$_REQUEST['societyId']."' and status='Y'";
		$res0 = $this->m_dbConn->select($sql0);
		
		if($res0[0]['cnt']==0)
		{
			$sql1="update society set status='N' where society_id='".$_REQUEST['societyId']."'";
			$this->m_dbConn->update($sql1);
			
			echo "msg1";
		}
		else
		{
			echo "msg";	
		}
	}
	
	public function del_society()
	{
		$sql1 = "update society set status='N' where society_id='".$_REQUEST['sss_id']."'";
		$this->m_dbConn->update($sql1);
		
		$sql2 = "update login set status='N' where society_id='".$_REQUEST['sss_id']."'";
		$this->m_dbConn->update($sql2);
		
	}
	
	public function check_socty_exist()
	{
		$sql = "select count(*)as cnt from society where society_name = '".$this->m_dbConn->escapeString($_REQUEST['soc_name'])."' and landmark='".$this->m_dbConn->escapeString($_REQUEST['landmark'])."' and status='Y'";
		$res = $this->m_dbConn->select($sql);
		
		if($res[0]['cnt']==0)
		{
			echo 0;
			echo '####';
		}
		else
		{
			echo 1;
			echo '####'.$_REQUEST['landmark'];
		}
	}
///////////////////////////////////////////////////////
/*	
	public function getInterestMethod($id)
	{
		
	$str.="<option value=''>Please Select</option>";
	$data =array('delay after due days','Full month');
	
		foreach($data as $value)
			{
				
				
						$str.="<OPTION VALUE=".$value.">";
					
					
				}
			
		
			return $str;
	}
	
*/

public function AddPeriods($cycleID , $yearID)
{
		$PeriodName = '';		
		$IsSuccess = false;	
				
		$FetchPeriod = $this->m_dbConn->select("select count(YearID) as count from `period` where `Billing_cycle`='".$cycleID."' and `YearID`= '".$yearID."'");
									
		if($FetchPeriod[0]['count'] == 0)
		{ 
		
			$months = getMonths($cycleID);
			
			$PrevYear =  $yearID - 1;
			$sqlFetchData = $this->m_dbConn->select("SELECT * FROM `year`  where  `YearID`= '".$PrevYear."'");
			
			$begin_date = $this->obj_billperiod->getBeginDate(end($months),$sqlFetchData[0]['YearDescription']);
			$end_date = $this->obj_billperiod->getEndDate(end($months),$sqlFetchData[0]['YearDescription']); 
									
			$insert_query="insert into period(`Billing_cycle`,`Type`,`YearID`,`PrevPeriodID`,`IsYearEnd`,`BeginingDate`,`EndingDate` )
									 values(".$cycleID.",'".end($months)."',".$PrevYear.",'0', '1','".$begin_date ."','".$end_date."')";
			$prevPeriod = $this->m_dbConn->insert($insert_query);	
			
			$this->obj_billperiod->setPeriod($months ,$cycleID,$yearID);
			$IsSuccess = true;	
		}
		else
		{
				$IsSuccess = false;	
		}
		
		return $IsSuccess;
				
}


	/* -------------------------------- DEPOSITS RELATED -------------------------- */
	public function getMembers() {
		$sql = "SELECT DISTINCT owner_name FROM member_main";
		return $this->m_dbConn->select($sql);
	}

	public function combobox($query)
	{
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{
				foreach($value as $k => $v)
				{
						
					$str.="<OPTION VALUE=".$v.">";
					$str.=$v."</OPTION>";
				}
			}
		}
		return $str;
	}

	public function comboboxForAgent($query)
	{
		$data = $this->m_dbConn->select($query);

		if(!is_null($data))
		{
			foreach($data as $key => $value)
			{		
				$str.="<OPTION VALUE=".$value['agent_id'].">";
				$str.=$value['agent_id']." - ".$value['agent_name']."</OPTION>";
			}
		}
		return $str;
	}


	public function getLoanSubCategory() {
		$sql = "SELECT category_name FROM account_category WHERE parentcategory_id IN (SELECT category_id FROM account_category WHERE category_name = 'LOAN')";

		
		return $this->m_dbConn->select($sql);
	}

	public function getSubCategory($id) {
		$sql = "SELECT category_name FROM account_category WHERE category_id = '".$id."'";
		$res = $this->m_dbConn->select($sql);

		return $res[0]['category_name'];
	}

	public function comboboxDepositType($query, $category_id = 0)
	{
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{	
			$str = "";		
			for ($i = 0; $i < sizeof($data); $i++) {
				$selected = ($data[$i]['category_id'] == $category_id)? "selected":"";
				$str.="<OPTION VALUE=".$data[$i]['category_id']." ".$selected.">".$data[$i]['category_name']."</OPTION>";
			}
		}
		return $str;
	}

	public function comboboxMaturityType($query)
	{
		$data = $this->m_dbConn->select($query);
		// var_dump($data);

		if(!is_null($data))
		{			
			for ($i = 0; $i < sizeof($data); $i++) {
				$str.="<OPTION VALUE=".$data[$i]['id'].">";
				$str.=$data[$i]['type']."</OPTION>";	
			}
		}
		return $str;
	}

	public function comboboxAcOp($query)
	{
		$data = $this->m_dbConn->select($query);
		// var_dump($data);

		if(!is_null($data))
		{			
			for ($i = 0; $i < sizeof($data); $i++) {
				$str.="<OPTION VALUE=".$data[$i]['operator_id'].">";
				$str.=$data[$i]['operator_type']."</OPTION>";	
			}
		}
		return $str;
	}

	public function comboboxName($query)
	{
		$data = $this->m_dbConn->select($query);
		if(!is_null($data))
		{			
			for ($i = 0; $i < sizeof($data); $i++) {
				$str.="<OPTION VALUE=".$data[$i]['member_id'].">";
				$str.=$data[$i]['owner_name']."</OPTION>";	
			}
		}
		return $str;
	}

	public function getLoanForAMember() 
	{

		$sql = "";

		if (isset($_REQUEST['id'])) 
		{
			$sql .= "SELECT * FROM pp_loan WHERE member_id = '".$_REQUEST['id']."'";
			return $this->m_dbConn->select($sql);
		}
		else {
			echo '<script>alert("Enter all the fields");</script>';
			return;
		}
	}

	public function getLedger ($id) {
		$sql = "";

		if (isset($_REQUEST['id'])) 
		{
			$sql .= "SELECT * FROM ledger WHERE parent_id = '".$_REQUEST['id']."'";
			return $this->m_dbConn->select($sql);
		}
		else {
			echo '<script>alert("Invalid ledger");</script>';
			return;
		}
	}

	public function getDepositDetail($category_id, $member_id){

		$query = "SELECT SUM(ltable.Debit) - SUM(ltable.Credit) as total_balance, d.deposit_date as start_date, d.ledger_id, d.maturity_cal, d.maturity_date, d.amount as deposit_amt, l.ledger_name FROM  `pp_deposits` as d JOIN ledger as l ON d.ledger_id = l.id LEFT JOIN liabilityregister as ltable ON ltable.LedgerID = d.ledger_id  where member_id = '$member_id'";

		if($category_id == SAVING_ACCOUNT){
			$query .= " AND subcategory_id = '$category_id'";
		}
		else{
			$query .= " AND subcategory_id IN (SELECT category_id from account_category WHERE parentcategory_id = '$category_id')";
		}
		$query .= "  group by d.id";
		return $this->m_dbConn->select($query);
	}
}
?>
<?php
include_once("include/display_table.class.php");
include_once("defaults.class.php");
include_once("dbconst.class.php");
include_once("bill_period.class.php");
include_once("VoucherRegister.class.php");
include_once("utility.class.php");
error_reporting(1);
class pp_loan
{
	public $actionPage = "../pp_loan.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	public $obj_billperiod;
	public $obj_voucher_register;
	public $debug_trace;
	public $obj_utility; 

	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->m_dbConnRoot = $dbConnRoot;
		$this->obj_voucher_register = new VoucherRegister($this->m_dbConn); 
		$this->debug_trace = 0;
		$this->obj_utility = new utility($this->m_dbConn);
	}
	public function startProcess()
	{
		try{
			// echo "<pre>";
			// print_r($_POST);
			// echo "</pre>";
			$this->m_dbConn->begin_transaction();
			$errorExists=0;
			if($_REQUEST['insert']=='Submit' && $errorExists==0)
			{
				if (!isset($_POST['loan_type']) && !isset($_POST['loan_amt']) && !isset($_POST['loan_date'])
				&& !isset($_POST['maturity_date']) && !isset($_POST['int_rate']) && !isset($_POST['loan_chrgs']) && !isset($_POST['referred_1']) && !isset($_REQUEST['referred_2']) && !isset($_REQUEST['joint_name']) && !isset($_POST['note_footer']) &&
				!isset($_POST['loan_period']) && !isset($_POST['installment_amt']) && !isset($_POST['mortgage'])) {
					return "Please fill all required field";
				} 
				else {
						
					
					$loan_type = $_POST['loan_type'];
					$loan_amt = $_POST[	'loan_amt'];
	
					$loan_date = getDBFormatDate($_POST['loan_date']);
					$maturity_date = getDBFormatDate($_POST['maturity_date']);
	
					$int_rate = $_POST['int_rate'];
					$loan_chrgs = $_POST['loan_chrgs'];
	
					// if (sizeof($_POST['mortgage']) == 1) {
					// 	$mortgage = $_POST['mortgage'][0];
					// }
					// else if (sizeof($_POST['mortgage']) == 2) {
					// 	$mortgage = $_POST['mortgage'][0].','.$_POST['mortgage'][1];
					// }

					$mortgage = $_POST['mortgage'];

					$mortgage_account = $_POST['mortgage_account'];
	
					$referred_1_id = $_POST['referred_1'];
					$referred_2_id = $_POST['referred_2'];
	
					$joint_name = $_POST['joint_name'];
					$note = trim($_POST['note_footer']);
					// var_dump($note);
					$loan_period = $_POST['loan_period'];
					$installment_amt = $_POST['installment_amt'];
					
					$interest_amt = $_POST['interest_amt'];
					$maturity_amt = $_POST['maturity_amt'];
					$bank_id = $_POST['bank_id'];
					$bank_leaf = $_POST['bank_leaf'];
					$cheque_number = $_POST['cheque_number'];
	
					$member_id = $_POST['id'];

					if($this->debug_trace)
					{
						echo "<pre>";
						print_r($_POST);
						echo "</pre>";
					}
	
					$sql = "INSERT INTO pp_loan (member_id, subcategory_id, amount, loan_date, maturity_date, interest_rate, interest_amt, maturity_amt, bank_id, installment_amt, loan_charges, mortgage, mortgage_account, ref_by_1, ref_by_2, joint_name, note) VALUES(".$member_id.", ".$loan_type.", ".$loan_amt.", '".$loan_date."', '".$maturity_date."', ".$int_rate.", ".$interest_amt.", ".$maturity_amt.", ".$bank_id.", ".$installment_amt.", ".$loan_chrgs.", '".$mortgage."', '".$mortgage_account."', ".$referred_1_id.", ".$referred_2_id.", '".$joint_name."', '".$note."')";			
					
					if($this->debug_trace)
					{
						echo "<br>sql : ".$sql;	
					}
					
					$RefNo = $res = $this->m_dbConn->insert($sql);
	
					$ledger_name = $this->getSubCategory($loan_type).'_'.$res;
	
					// Inserting in ledger table
					$sql = "";
	
					$sql1 = "INSERT INTO ledger (society_id, categoryid, ledger_name, show_in_bill, taxable, sale, purchase, income, expense, payment, receipt, opening_type, opening_balance, note, opening_date, parent_id) VALUES (".$_SESSION['society_id'].", '".$loan_type."', '".$ledger_name."', 0, 0, 1, 1, 1, 1, 1, 1, 2, 0, '".$note."', '".$loan_date."', ".$member_id.")";
					
					if($this->debug_trace)
					{
						echo "<br>sql1 : ".$sql1;
					}
			
					$ledger_id = $this->m_dbConn->insert($sql1);
	
					$payment_insert_quert ="insert into paymentdetails (`ChequeDate`,`ChequeNumber`,`Amount`,`PaidTo`,`EnteredBy`,`PayerBank`,`Comments`,`VoucherDate`,
					`ChqLeafID`,`ModeOfPayment`) values ('".$loan_date."','".$cheque_number."','".$loan_amt."','".$ledger_id
					."','".$_SESSION['login_id']."','".$this->m_dbConn->escapeString($bank_id)."','".$this->m_dbConn->escapeString($note)."','".$loan_date."','"
					.$bank_leaf."',0)";
							
					
					
					$Payment_id = $this->m_dbConn->insert($payment_insert_quert);	

					if($this->debug_trace)
					{
						echo "<br>".$res2;
					}

					// Inserting ledger_id in loan table
					$sql2 = "UPDATE pp_loan SET ledger_id = ".$ledger_id.", `payment_id` = '".$Payment_id."' WHERE id = ".$res;
					$res2 = $this->m_dbConn->update($sql2);
					
					if($this->debug_trace)
					{
						echo "<br>".$payment_insert_quert;
						echo "<br>".$sql2;
					}


					$VoucherData = array();
					
					if($res2 > 0) // greater than means table is updated
					{
						//echo "<br>Inside the res";
						// Payment trasaction 
						$PaymentByArray = array("Date"=>$loan_date,"RefNo"=>$RefNo,"RefTable"=>TABLE_LOAN , "VoucherType"=>VOUCHER_PAYMENT, "By"=>$ledger_id, "To"=>"","Debit"=>$loan_amt,"Credit"=>"","Note"=>$note);
						$PaymentToArray = array("Date"=>$loan_date,"RefNo"=>$RefNo,"RefTable"=>TABLE_LOAN , "VoucherType"=>VOUCHER_PAYMENT, "By"=>"", "To"=>$bank_id, "Debit"=>"", "Credit"=>$loan_amt,"Note"=>$note);
						
						array_push($VoucherData,$PaymentByArray);
						array_push($VoucherData,$PaymentToArray);

						$VoucherRegisterResult1 = $this->obj_voucher_register->processdata($VoucherData);

						$VoucherData = array();

						// // Interest transaction
						
						// $InterestByArray = array("Date"=>$loan_date,"RefNo"=>$RefNo,"RefTable"=>TABLE_LOAN , "VoucherType"=>VOUCHER_LOAN,"By"=>INTEREST_ON_PRINCIPLE_DUE, "To"=>"","Debit"=>$interest_amt,"Credit"=>"");
						// $InterestToArray = array("Date"=>$loan_date,"RefNo"=>$RefNo,"RefTable"=>TABLE_LOAN , "VoucherType"=>VOUCHER_LOAN,"By"=>"", "To"=>$ledger_id, "Debit"=>"", "Credit"=>$interest_amt);
						
						// array_push($VoucherData,$InterestByArray);
						// array_push($VoucherData,$InterestToArray);

						// Bank Charges transaction

						$BankChargesByArray = array("Date"=>$loan_date,"RefNo"=>$RefNo,"RefTable"=>TABLE_LOAN , "VoucherType"=>VOUCHER_JOURNAL, "By"=>BANK_CHARGES, "To"=>"","Debit"=>$loan_chrgs,"Credit"=>"","Note"=>$note);
						$BankChargeToArray = array("Date"=>$loan_date,"RefNo"=>$RefNo,"RefTable"=>TABLE_LOAN , "VoucherType"=>VOUCHER_JOURNAL, "By"=>"", "To"=>$member_id, "Debit"=>"", "Credit"=>$loan_chrgs,"Note"=>$note);
						
						array_push($VoucherData,$BankChargesByArray);
						array_push($VoucherData,$BankChargeToArray);
						
						if($this>debug_trace)
						{
							echo "<pre>";
							print_r($VoucherData);
							echo "</pre>";
						}


						$VoucherRegisterResult2 = $this->obj_voucher_register->processdata($VoucherData);

						if($VoucherRegisterResult1['status'] == true && $VoucherRegisterResult2['status'] == true)
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
			}
		}
		catch(Exception $e){
			echo $e->getMessage();
			$this->m_dbConn->rollback();
		}
		
	}
	
	public function getBankLeafs($BankID)
	{
		$Query = "SELECT id,LeafName FROM `chequeleafbook` WHERE BankID = '".$BankID."' and `Status` = 'Y' AND LeafName != ''";
		return	$this->combobox11($Query,0,true);
	}

	public function getChequeNo($LeafID)
	{
		$String = "";
		$Query = "SELECT StartCheque, EndCheque, CustomLeaf FROM `chequeleafbook` WHERE id = '".$LeafID."'";
		$LeafDetails = $this->m_dbConn->select($Query);

		$IsCustomLeaf = $LeafDetails[0]['CustomLeaf']; 
		$BeginChequeNo = $LeafDetails[0]['StartCheque']; 
		$EndChequeNo = $LeafDetails[0]['EndCheque'];

		if($LeafDetails[0]['CustomLeaf'] == 0)
		{
			$ChequeNoList = $this->getUnusedChequeNo($LeafID,$BeginChequeNo,$EndChequeNo);
			$String .= '<select name="cheque_number" id="cheque_number" tabindex="14">';
			$String .= "<option value=''>Select Cheque Number</option>";
			foreach($ChequeNoList as $v){
				$String .= "<option value='".$v."'>$v</option>";
			}
			$String .= '</select>'; 
		}
		else
		{
			$String .= '<input type="text" name="cheque_number" id="cheque_number" tabindex="14" class="field_input"/>';
		}

		return $String;

	}

	public function getUnusedChequeNo($LeafID,$BeginChequeNo,$EndChequeNo){

		$Query = "SELECT ChequeNumber FROM `paymentdetails` where ChqLeafID = '".$LeafID."'";
		$ChequeNoList = $this->m_dbConn->select($Query);
		
		$UsedChequeNo = array_column($ChequeNoList,'ChequeNumber');

		$UnUsedChequeNo = array();

		for($i = $BeginChequeNo; $i <= $EndChequeNo; $i++){

			if(!in_array($i,$UsedChequeNo))
			{
				array_push($UnUsedChequeNo,$i);
			}
		}
		
		return $UnUsedChequeNo;
	}

  

	public function combobox11($query,$id,$showDefaultText = false)
	{
		if($showDefaultText == true)
		{
			$str ="<option value='0'>Please Select </option>";
		}
		else
		{
			$str = null;	
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

	/* --------------------------- LOAN RELATED ---------------------------------- */
	public function getMembers() {
		$sql = "SELECT DISTINCT owner_name FROM member_main";
		return $this->m_dbConn->select($sql);
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

	public function combobox($query)
	{
		$data = $this->m_dbConn->select($query);
		// var_dump($data);

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

	public function comboboxLoanType($query)
	{
		$data = $this->m_dbConn->select($query);
		// var_dump($data);

		if(!is_null($data))
		{			
			for ($i = 0; $i < sizeof($data); $i++) {
				$str.="<OPTION VALUE=".$data[$i]['category_id'].">";
				$str.=$data[$i]['category_name']."</OPTION>";	
			}
		}
		return $str;
	}


	public function comboboxName($query)
	{
		$data = $this->m_dbConn->select($query);
		// var_dump($data);

		if(!is_null($data))
		{			
			for ($i = 0; $i < sizeof($data); $i++) {
				$str.="<OPTION VALUE=".$data[$i]['member_id'].">";
				$str.=$data[$i]['owner_name']."</OPTION>";	
			}
		}
		return $str;
	}

	public function getLoanForAMember($id) 
	{

		$sql = "SELECT *, owner_name FROM pp_loan as loan JOIN member_main as m ON loan.member_id = m.member_id";
		if (isset($id) && !empty($id)) 
		{
			$sql .= " WHERE loan.member_id = '".$id."'";
		}
		// echo "<br>sql : ".$sql;
		return $this->m_dbConn->select($sql);
	}

	public function getLedger ($id, $category_id = 0, $reIndexKey = "") {

		$sql = "";
		if (isset($id) && !empty($id)) 
		{
			$sql .= "SELECT * FROM ledger WHERE parent_id = '".$id."'";
			if(!empty($category_id)){
				$sql .= " AND categoryid IN (SELECT category_id from account_category WHERE parentcategory_id = '$category_id')";
			}
			
			$result = $this->m_dbConn->select($sql);

			if(!empty($reIndexKey)){
				return $this->obj_utility->reindex($result, $reIndexKey);
			}
			return $this->m_dbConn->select($sql);
		}		
	}


	public function list_loan_show()
	{
		$loans = $this->getLoanForAMember($_REQUEST['id']);
		// var_dump($loans);
		?>
        <table id="example" class="display" cellspacing="0" style="width:100%">
		<thead>
        <tr>
        	<th>Sr No.</th>
        	<th>Loan Type</th>
			<th>Member Name</th>
        	<th>Amount</th>
        	<th>Status</th>
            <th>Loan Date</th>
            <th>Maturity Date</th>
        	<th>Interest Rate</th>
            <th>Installment</th>
            <th>Loan Charges</th>
            <th>Mortgage</th>
            <th>Joint Name</th>
            
			<?php if($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN)
            {?>
            	<!--<th width="50">View</th>-->
			<?php }?>
            <?php if($_SESSION['role'] <> ROLE_ADMIN_MEMBER)
			{?>
             <!--<th width="50">Transfer Ownership</th>-->
            <?php }?>
			<?php if(IsReadonlyPage() == false && ($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN)){?>
            <th width="50">Edit</th>
           <!-- <th width="70">Delete</th>-->
            <?php } ?>
        </tr>
		</thead>
		<tbody>
	      	<?php 
	      		for ($i = 0; $i < sizeof($loans); $i++) 
	      		{
	      			
	      			$subcategory = $this->getSubCategory($loans[$i]['subcategory_id']);
					   
	      			
	      	?>

			    <tr height="25" bgcolor="#BDD8F4" align="center">
			        <td  align="center" style="width: 10%;"><?php echo $i + 1; ?></td>
			        <td  align="center"><?php echo $subcategory; ?>
					<td  align="center"><?php echo $loans[$i]['owner_name']; ?>
		        	<td  align="center" style="width: 10%;"><?php echo $loans[$i]['amount']; ?></td>
		        	<td  align="center" style="width: 10%;"><?php echo $loans[$i]['status'] == '0' ? "Open" : "Closed";?></td>
		            <td  align="center" style="width: 10%;"><?php echo getDisplayFormatDate($loans[$i]['loan_date']); ?></td>
		            <td  align="center" style="width: 10%;"><?php echo getDisplayFormatDate($loans[$i]['maturity_date']); ?></td>
		        	<td  align="center" style="width: 10%;"><?php echo $loans[$i]['interest_rate']; ?>%</td>
		            <td  align="center" style="width: 10%;"><?php echo $loans[$i]['installment_amt']; ?></td>
		            <td  align="center" style="width: 10%;"><?php echo $loans[$i]['loan_charges']; ?></td>
		            <td  align="center" style="width: 10%;"><?php echo $this->getMortgage($loans[$i]['mortgage']); ?></td>

		            <td align="center" style="width: 10%;"><b><?php echo $loans[$i]['joint_name'] == '' ? '---':$loans[$i]['joint_name']; ?></b></td>
		            <?php 
		            	if($_SESSION['role'] <> ROLE_ADMIN_MEMBER)
						{
					?>
		           		
		            <?php 
		        		} 
		        	?>
		            <?php 
		            	if(IsReadonlyPage() == false && ($_SESSION['role'] == ROLE_SUPER_ADMIN || $_SESSION['role'] == ROLE_ADMIN))
		            	{
		            ?>
			            <td align="center">
			            <a href="view_member_profile.php?edt&scm&id=<?php echo $res[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view">
			            <img src="images/edit.gif" />
			            </a>
			            </td>
		            
		            <?php 
						} 
					?>
		        </tr>
        	<?php
	      		} // For loop end
	      	?>

		</tbody>
        </table>
       <?php
	}

	public function getMortgage($id){
		
		$string = '';
		switch($id){
			case SAVING_ACCOUNT:
				$string = 'Saving';
				break;
			case DAILY_DEPOSIT_ACCOUNT:
				$string = 'Daily Deposit';
				break;
			case MONTHLY_DEPOSIT_ACCOUNT:
				$string = 'Monthly Deposit';
				break;
			case FIXED_DEPOSIT_ACCOUNT:
				$string = 'Fixed';
				break;
			default:
				$string = '--';
				break;		
		}
		return $string;
	}
	// public function insertLoan() 
	// {
	// 	if (isset($_POST['submit'])) 
	// 	{
	// 		if (!isset($_POST['loan_type']) && !isset($_POST['loan_amt']) && !isset($_POST['loan_date'])
	// 		&& !isset($_POST['maturity_date']) && !isset($_POST['int_rate']) && !isset($_POST['loan_chrgs']) && !isset($_POST['referred_1']) && !isset($_REQUEST['referred_2']) && !isset($_REQUEST['joint_name']) && !isset($_POST['note_footer']) &&
	// 		!isset($_POST['loan_period']) && !isset($_POST['installment_amt']) && !isset($_POST['mortgage'])) {
	// 			return;
	// 		} 
	// 		else {
				
	// 			$loan_type = $_POST['loan_type'];
	// 			$loan_amt = $_POST['loan_amt'];

	// 			$loan_date = getDBFormatDate($_POST['loan_date']);
	// 			$maturity_date = getDBFormatDate($_POST['maturity_date']);

	// 			$int_rate = $_POST['int_rate'];
	// 			$loan_chrgs = $_POST['loan_chrgs'];

	// 			if (sizeof($_POST['mortgage']) == 1) {
	// 				$mortgage = $_POST['mortgage'][0];
	// 			}
	// 			else if (sizeof($_POST['mortgage']) == 2) {
	// 				$mortgage = $_POST['mortgage'][0].','.$_POST['mortgage'][1];
	// 			}

	// 			$referred_1_id = $_POST['referred_1'];
	// 			$referred_2_id = $_POST['referred_2'];

	// 			$joint_name = $_POST['joint_name'];
	// 			$note = $_POST['note_footer'];

	// 			$loan_period = $_POST['loan_period'];
	// 			$installment_amt = $_POST['installment_amt'];


	// 			$member_id = $_POST['id'];

	// 			$sql = "INSERT INTO pp_loan (member_id, subcategory_id, amount, loan_date, maturity_date, interest_rate, installment_amt, loan_charges, mortgage, ref_by_1, ref_by_2, joint_name, note) VALUES(".$member_id.", ".$loan_type.", ".$loan_amt.", '".$loan_date."', '".$maturity_date."', ".$int_rate.", ".$installment_amt.", ".$loan_chrgs.", '".$mortgage."', ".$referred_1_id.", ".$referred_2_id.", '".$joint_name."', '".$note."')";			

	// 			$res = $this->m_dbConn->insert($sql);

	// 			$ledger_name = $this->getSubCategory($loan_type).'_'.$res;

	// 			// Inserting in ledger table
	// 			$sql = "";

	// 			$sql1 = "INSERT INTO ledger (society_id, categoryid, ledger_name, show_in_bill, taxable, sale, purchase, income, expense, payment, receipt, opening_type, opening_balance, note, opening_date, parent_id) VALUES (".$_SESSION['society_id'].", '".$loan_type."', '".$ledger_name."', 0, 0, 1, 1, 1, 1, 1, 1, 2, 0, '".$note."', '".$loan_date."', ".$member_id.")";

	// 			$ledger_id = $this->m_dbConn->insert($sql1);

	// 			// Inserting ledger_id in loan table
	// 			$sql2 = "UPDATE pp_loan SET ledger_id = ".$ledger_id." WHERE id = ".$res;

	// 			$res2 = $this->m_dbConn->update($sql2);


	// 			header("Location: /beta_aws_14_pp/pp_loan.php?prf&mkm&tik_id=1559720253&id=".$member_id."&show&imp");
	// 		}
	// 	}
	// 	else {
	// 		return;
	// 	} 

		
	// }

	public function getAccountNumber($member_id, $mortgage, $account_number = 0){

		if($mortgage == SAVING_ACCOUNT){
			$query = "SELECT d.id, l.ledger_name FROM `pp_deposits` as d JOIN ledger as l ON d.ledger_id = l.id where subcategory_id = '$mortgage' AND member_id = '$member_id'";
		}
		else{
			$query = "SELECT d.id, l.ledger_name FROM `pp_deposits` as d JOIN ledger as l ON d.ledger_id = l.id JOIN account_category as ac ON d.subcategory_id = ac.category_id WHERE ac.parentcategory_id = '$mortgage' AND d.member_id = '$member_id'";
		}

		if(!empty($account_number) && $account_number <> 0){
			
			$query .= " AND d.id = '$account_number'";
		}
		return $this->m_dbConn->select($query);
	}
}
?>
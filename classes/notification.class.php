<?php if(!isset($_SESSION)){ session_start(); }
include_once("include/display_table.class.php");
include_once("dbconst.class.php");
include_once("utility.class.php");
include_once("activate_user_email.class.php");

class notification //extends dbop
{
	public $actionPage="../notification.php";
	public $m_dbConn;
	public $obj_utility;
	public $m_dbConnRoot;
	public $obj_activation ;
	//public $display_pg;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->obj_utility = new utility($this->m_dbConn);
		$this->m_dbConnRoot = new dbop(true);
		$this->obj_activation = new activation_email($this->m_dbConn, $this->m_dbConnRoot);
		//echo "test2";
	}
	
	public function startProcess()
	{
		$errorExists=0;
		
		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			
		}
		else
		{
			echo "<script>alert('error');</script>";
			return $errString;
		}
	}
	
	public function pgnation_bill($society_id, $wing_id, $unit_id, $period_id, $Supplemenary_bills)
	{
		//echo 'Inside pgnation_bill';
		$sqlPeriod = "SELECT max(id),PeriodID,BillDate,DueDate FROM `billregister` where `PeriodID` = '".$period_id."' ";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		$sql1 = 'select bill.UnitID, bill.PeriodID, unittbl.unit_id, unittbl.unit_no, wingtbl.wing, societytbl.society_name, societytbl.society_code, membertbl.owner_name,membertbl.member_id, membertbl.mob, membertbl.email,DATE_FORMAT(bill.bill_read_timestamp,"%d-%m-%Y %H:%i:%s") as bill_read_timestamp from billdetails as bill JOIN unit as unittbl on bill.UnitID = unittbl.unit_id JOIN wing as wingtbl on unittbl.wing_id = wingtbl.wing_id JOIN society as societytbl on unittbl.society_id = societytbl.society_id JOIN member_main as membertbl ON membertbl.unit = unittbl.unit_id where bill.PeriodID = "' . $period_id . '" and societytbl.society_id = "' . $society_id . '" and bill.BillType="'.$Supplemenary_bills.'" and membertbl.member_id IN (SELECT `member_id` FROM (select  DISTINCT(unit) ,`member_id` from `member_main` where ownership_date <="'.$resultPeriod[0]['BillDate'].'"  ORDER BY ownership_date desc) as member_id Group BY unit)'; 
		
		if($wing_id <> 0)
		{
			$sql1 .= ' and unittbl.wing_id = "' . $wing_id . '"';
		}		
		if($unit_id <> 0)
		{
			$sql1 .= ' and unittbl.unit_id = "' . $unit_id . '"';
		}
		
		$sql1 .= ' Group BY unittbl.unit_id ORDER BY unittbl.sort_order ASC';
		$cntr1 = "select count(bill.UnitID) as cnt,DATE_FORMAT(bill.bill_read_timestamp,'%d-%m-%Y %H:%i:%s') as bill_read_timestamp from billdetails as bill JOIN unit as unittbl on bill.UnitID = unittbl.unit_id JOIN wing as wingtbl on unittbl.wing_id = wingtbl.wing_id JOIN society as societytbl on unittbl.society_id = societytbl.society_id JOIN member_main as membertbl ON membertbl.unit = unittbl.unit_id where bill.PeriodID = '" . $period_id . "' and societytbl.society_id = '" . $society_id . "' and bill.BillType='".$Supplemenary_bills."' and membertbl.member_id IN (SELECT  `member_id` FROM (select  DISTINCT(unit) ,`member_id` from `member_main` where ownership_date <='".$resultPeriod[0]['BillDate']."'  ORDER BY ownership_date desc) as member_id Group BY unit) "; 
		
		if($wing_id <> 0)
		{
			$cntr1 .= ' and unittbl.wing_id = "' . $wing_id . '"';
		}		
		if($unit_id <> 0)
		{
			$cntr1 .= ' and unittbl.unit_id = "' . $unit_id . '"';
		}
		$cntr1 .= ' Group BY unittbl.unit_id ORDER BY unittbl.sort_order ASC';
		
		$result = $this->m_dbConn->select($sql1);
		
		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr1;
		$this->display_pg->mainpg	= "notification.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "&wing_id=" . $wing_id . "&period_id=" . $period_id ;
		
		//$res	= $this->display_pg->pagination($cntr1,$mainpg,$sql1,$limit,$page,$extra);
		//return $res;
		$this->display1($result, $period_id, $Supplemenary_bills);
	}
	
	public function display1($rsas, $periodID,  $Supplemenary_bills)
	{
		//echo "inside display1";
		$thheader=array('Member Name','Wing','Unit No.', 'E-Mail', 'Mobile No', 'Notification');
		$this->display_pg->edit="notify";
		$this->display_pg->th=$thheader;
		$this->display_pg->mainpg="notification.php";
			//	echo "calling showunit";
		//$res=$this->display_pg->display_new($rsas);
		$res=$this->show_unit($rsas, $periodID,  $Supplemenary_bills);
		//echo "exiting display1";
		return $res;
	}
	
	public function show_unit($res, $periodID,  $Supplemenary_bills)
	{
		$billText = "Maintenance"	;
		if($Supplemenary_bills == '1')
		{
			$billText = "Supplementary"	;
		}
		$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $periodID . "'";
		
		$sqlResult = $this->m_dbConn->select($sqlPeriod);
		//print_r($sqlResult);
		echo "<b><font color='#0000FF'> ".$billText."  Bill's For : " . $sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'] . "</font></b><br><br>";
		?>
		<input type='button' value='Send EMail To All Selected Units' id='btn_all_email' onclick='EMailSentAll();' />&nbsp;&nbsp;&nbsp;&nbsp;
        <?php
		if($_SESSION['feature'][CLIENT_FEATURE_SMS_MODULE] == 1)
			{
				?>
				<input type="button" value="Send SMS To All Selected Units" id="btn_all_sms" onclick="SMSSentAll(1);" />	&nbsp;&nbsp;&nbsp;&nbsp;
					
			<?php 
				}
				else
				{?>
                	<input type="button" value="Send SMS To All Selected Units" id="btn_all_sms" onclick="SMSSentAll();"   title="Your Not Subscribe For SMS" disabled/>		
            <?php }
            ?>
            <input type="button" value="Send Mobile Notification To All Selected Units" />
            <?php  
			echo "<br><br><br>";   
		if($res<>"")
		{
			$str_unit_ary = '';
			
			if(!isset($_REQUEST['page']))
			{
				$_REQUEST['page'] = 1;
			}
			$iCounter = 1 + (($_REQUEST['page'] - 1) * 50);
		?>
            <table align="center" class="display" cellspacing="0" width="100%" id="example">
			<thead>
				<tr height="30">
					<th><input type="checkbox" id="chk_all" onclick="SelectAll(this);"/></th>
					<th width="50">Sr No</th>
					<th width="300">Member Name</th>
					<th width="100">Wing</th>
					<th width="100">Unit No.</th>
					<th width="100">Email ID</th>
					<th width="200">Last Email Notification</th>					
					<th width="100">Mobile No.</th>
					<th width="200">Last SMS Notification</th>
                    <th width="200">SMS Delivery</th>
                    <th width="200">Bill Read TimeStamp</th>
                    <th width="100">Due Amount </th>
					<th width="50">View</th>
					<?php if($_SESSION['login_id'] == 4 || $_SESSION['login_id'] == 169)
					{
						?>
							<th width="100" colspan="3">Notification</th>
						<?php
					}
					else
					{
						?>
							<th width="100" colspan="2">Notification</th>
						<?php
					}
					?>
					
				</tr>
			</thead>
			<tbody>
        <?php foreach($res as $k => $v)
		{
			$aryNotification = array();
			$aryNotification = $this->getNotificationSentDetails($res[$k]['unit_id'], $periodID);
			$sEmailDetails = "";
			$sSMSDetails = "";
			$SMSDeliverTime = "";
			if(sizeof($aryNotification['EMAIL']) > 0)
			{
				$sEmailDetails = $aryNotification['EMAIL'][0];
			}
			if(sizeof($aryNotification['SMS']) > 0)
			{
				$sSMSDetails = $aryNotification['SMS'][0];
			}
			if(sizeof($aryNotification['SMSDeliveryDate']) > 0)
			{
				$SMSDeliverTime = $aryNotification['SMSDeliveryDate'][0];
			}
			$str_unit_ary .= $res[$k]['unit_id'] . '#';
			$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
        		$UnitNo = str_replace($specialChars, '', $res[$k]['unit_no']);
		?>
        	<tr height="25" align="center">
			<td><input type="checkbox" value="1" id="chk_<?php echo $res[$k]['unit_id']; ?>" /></td>	
        	<td align="center"><?php echo $iCounter++;?></td>
            <td align="center"><a href="view_member_profile.php?scm&id=<?php echo $res[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank"><?php echo $res[$k]['owner_name'];?></a></td>
            <td align="center"><?php echo $res[$k]['wing'];?></td>
            <td align="center"><?php echo $res[$k]['unit_no'];?></td>
            <td align="center"><?php echo $res[$k]['email'];?></td>
            <td align="left"><?php echo $sEmailDetails;?></td>						
            <td align="center"><?php echo $res[$k]['mob'];?></td>
            <td align="left"><?php echo $sSMSDetails;?></td>			
            <td align="left"><?php echo $SMSDeliverTime;?></td>	
            <td align="center"><?php echo $res[$k]['bill_read_timestamp'];?></td>		
            <?php $Url = "member_ledger_report.php?&uid=".$res[$k]['UnitID']; ?>
            <td align="center"><a href="#" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');" style="color:#0000FF;"><?php echo $this->obj_utility->getDueAmount($res[$k]['UnitID']); ?></a></td>            
             
			<td align="center"><iframe src="<?php echo 'pdfbill.php?pdffile=maintenance_bills/' . $res[$k]['society_code'] . '/' . $sqlResult[0]['type']. ' ' . $sqlResult[0]['YearDescription'] . '/bill-' . $res[$k]['society_code'] . '-' . $UnitNo  . '-' . $sqlResult[0]['type']. ' ' . $sqlResult[0]['YearDescription'] . '-'. $Supplemenary_bills.'.pdf'; ?>" name="pdfexport_<?php echo $res[$k]['unit_id']; ?>" id="pdfexport_<?php //echo $res[$k]['unit_id']; ?>" style="border:0px solid #0F0;width:40px;height:40px;"></iframe></td>
			
            <td align="center">
            <input type="button" id="send_email" value="Send Email" onclick="sendEmail(<?php echo $res[$k]['unit_id']; ?>);" />
            </td>
                        
           	<td align="center">
            <?php $sql = "SELECT count(*) AS tCount FROM `notification` WHERE `UnitID` = '".$res[$k]['unit_id']."' AND DATE_FORMAT(`SentBillSMSDate`, '%Y-%m-%d') = '".date("Y-m-d")."' and `BillType` = '".$Supplemenary_bills."' "; 
				  $SMSCount = $this->m_dbConn->select($sql);
			?>
            
            <?php if($_SESSION['feature'][CLIENT_FEATURE_SMS_MODULE] == 1){?>
				<input type="button" id="send_sms_<?php echo $res[$k]['unit_id']; ?>" value="Send SMS" onclick="sendSMS(<?php echo $res[$k]['unit_id']; ?>);" <?php if($SMSCount[0]['tCount'] > 0) { ?> disabled="disabled" style="background-color:#999999;" <?php } ?> />
			<iframe src="" name="sendsms_<?php echo $res[$k]['unit_id']; ?>" id="sendsms_<?php echo $res[$k]['unit_id']; ?>" style="border:0px solid #0F0;width:0px;height:0px;"></iframe>
			<?php }
				else
				{?>
                	 <input type="button" id="send_sms" value="Send SMS"   disabled="disabled"  title="Your Not Subscribe For SMS" />
            <?php } ?>     
            
			
            </td>
            <?php 
            //if($_SESSION['login_id'] == 4 || $_SESSION['login_id'] == 169)
            {
            	?>
            		<td>
            			<input type="button" id=id="send_android_notification_<?php echo $res[$k]['unit_id']; ?>" onclick="sendMobileNotification(<?php echo $res[$k]['unit_id']; ?>, <?php echo $_SESSION['society_id']; ?>, '<?php echo $res[$k]['email'] ?>');" value="Send Android Notification"/>
            		</td>
            	<?php
            }
            ?>
            <td align="center">
            	<div id="status_<?php echo $res[$k]['unit_id']; ?>" style="color:#0033FF; font-weight:bold;"></div>
            </td>
            </tr>
        <?php 
			}
		?>
			</tbody>
        </table>
		<input type="hidden" id="unit_ary" value="<?php echo $str_unit_ary; ?>" />
		<br />
		<input type="button" value="Send EMail To All Selected Units" id="btn_all_email" onclick="EMailSentAll();" />&nbsp;&nbsp;&nbsp;&nbsp;
        
          <?php if($_SESSION['feature'][CLIENT_FEATURE_SMS_MODULE] == 1){?>
				<input type="button" value="Send SMS To All Selected Units" id="btn_all_sms" onclick="SMSSentAll(1);" />	&nbsp;&nbsp;&nbsp;&nbsp;
				<?php
			}
				else
				{?>
                	<input type="button" value="Send SMS To All Selected Units" id="btn_all_sms" onclick="SMSSentAll();"   title="Your Not Subscribe For SMS" disabled/>		
            <?php } ?>     
		
						<input type="button" value="Send Mobile Notification To All Selected Units"  onclick="sendMobileNotificationAllSelected();" />	
		<br /><br />
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
	
	public function combobox($query,$id, $defaultText = 'Please Select', $defaultValue = '')
	{
		$str = '';
		
		if($defaultText != '')
		{
			$str .= "<option value='" . $defaultValue . "'>" . $defaultText . "</option>";
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
						if($id==$v)
						{
							$sel = 'selected';
						}
						else
						{
							$sel = '';
						}
						
						$str.="<OPTION VALUE=".$v.' '.$sel.">";
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
	
	function getNotificationSentDetails($unitID, $periodID)
	{
		$aryNotification = array();
		$aryNotification['EMAIL'] = array();
		$aryNotification['SMS'] = array();
		$aryNotification['SMSDeliveryDate'] = array();
		
		$sqlSelectDetails = "Select * from `notification` where `UnitID` = '" . $unitID . "' and `PeriodID` = '" . $periodID . "' ORDER BY ID DESC";	
		$detailsResult = $this->m_dbConn->select($sqlSelectDetails);
		$sqlEmailQ = "select * from `emailqueue` where `dbName`='".$_SESSION["dbname"]."' and `SocietyID`='".$_SESSION["society_id"]."' and `UnitID`='".$unitID."' and `PeriodID`='".$periodID."' and `PeriodID`<>0 and `Status`=0";
		//echo $sqlEmailQ;
		$arEMailQueue = $this->m_dbConnRoot->select($sqlEmailQ);
		$EmailQueueCount = sizeof($arEMailQueue);
		$defaultDate = '0000-00-00 00:00:00';
		
		if($EmailQueueCount > 0)
		{
			array_push($aryNotification['EMAIL'], "In Queue");
		}
		if($detailsResult <> "")
		{
			for($iCnt = 0; $iCnt < sizeof($detailsResult); $iCnt++)
			{
				if($detailsResult[$iCnt]['SentBillSMSDate'] <> $defaultDate)
				{
					$arySplit = explode(" ", $detailsResult[$iCnt]['SentBillSMSDate']);
					array_push($aryNotification['SMS'], getDisplayFormatDate($arySplit[0]) . " " . $arySplit[1]);
					
					$aryDateSplit = explode(",", $detailsResult[$iCnt]['DeliveryReport']);
					$strDate = str_replace('"', "", $aryDateSplit[5]);
					$DateTimeStamp = explode(' ',$strDate);
					//print_r($DateTimeStamp);
					array_push($aryNotification['SMSDeliveryDate'], getDisplayFormatDate($DateTimeStamp[0]) . " ".$DateTimeStamp[1]);
				}
				else if($detailsResult[$iCnt]['SentBillEmailDate'] <> $defaultDate && $EmailQueueCount == 0)
				{
					$arySplit = explode(" ", $detailsResult[$iCnt]['SentBillEmailDate']);
					array_push($aryNotification['EMAIL'], getDisplayFormatDate($arySplit[0]) . " " . $arySplit[1]);
				}
			}
		}
		//echo '<br/>Unit : ' . $unitID . '<br/>';
		//print_r($aryNotification);
		return $aryNotification;
	}
	
	/*-------------------------------------------------------View Bill Report Function------------------------------------------------------------------*/
	
	public function bill_view_report($society_id, $wing_id, $unit_id, $period_id, $Supplemenary_bills,$loginstatus,$read)
	{
	 	$loginstatus=$_POST['ac_status'];
		 $read=$_POST['read_type'];
		
		//echo 'Inside pgnation_bill';
		$sqlPeriod = "SELECT max(id),PeriodID,BillDate,DueDate FROM `billregister` where `PeriodID` = '".$period_id."' ";
		$resultPeriod = $this->m_dbConn->select($sqlPeriod);
		$sql1 = 'select bill.UnitID, bill.PeriodID, unittbl.unit_id, unittbl.unit_no, wingtbl.wing, societytbl.society_name, societytbl.society_code, membertbl.owner_name,membertbl.member_id, membertbl.mob, membertbl.email,DATE_FORMAT(bill.bill_read_timestamp,"%d-%m-%Y %H:%i:%s") as bill_read_timestamp from billdetails as bill JOIN unit as unittbl on bill.UnitID = unittbl.unit_id JOIN wing as wingtbl on unittbl.wing_id = wingtbl.wing_id JOIN society as societytbl on unittbl.society_id = societytbl.society_id JOIN member_main as membertbl ON membertbl.unit = unittbl.unit_id where bill.PeriodID = "' . $period_id . '" and societytbl.society_id = "' . $society_id . '" and bill.BillType="'.$Supplemenary_bills.'" and membertbl.member_id IN (SELECT `member_id` FROM (select  DISTINCT(unit) ,`member_id` from `member_main` where ownership_date <="'.$resultPeriod[0]['BillDate'].'"  ORDER BY ownership_date desc) as member_id Group BY unit)'; 
		
		if($wing_id <> 0)
		{
			$sql1 .= ' and unittbl.wing_id = "' . $wing_id . '"';
		}		
		if($unit_id <> 0)
		{
			$sql1 .= ' and unittbl.unit_id = "' . $unit_id . '"';
		}
		
		
		
		$sql1 .= ' Group BY unittbl.unit_id ORDER BY unittbl.sort_order ASC';
		$cntr1 = "select count(bill.UnitID) as cnt,DATE_FORMAT(bill.bill_read_timestamp,'%d-%m-%Y %H:%i:%s') as bill_read_timestamp from billdetails as bill JOIN unit as unittbl on bill.UnitID = unittbl.unit_id JOIN wing as wingtbl on unittbl.wing_id = wingtbl.wing_id JOIN society as societytbl on unittbl.society_id = societytbl.society_id JOIN member_main as membertbl ON membertbl.unit = unittbl.unit_id where bill.PeriodID = '" . $period_id . "' and societytbl.society_id = '" . $society_id . "' and bill.BillType='".$Supplemenary_bills."' and membertbl.member_id IN (SELECT  `member_id` FROM (select  DISTINCT(unit) ,`member_id` from `member_main` where ownership_date <='".$resultPeriod[0]['BillDate']."'  ORDER BY ownership_date desc) as member_id Group BY unit) "; 
		
		if($wing_id <> 0)
		{
			$cntr1 .= ' and unittbl.wing_id = "' . $wing_id . '"';
		}		
		if($unit_id <> 0)
		{
			$cntr1 .= ' and unittbl.unit_id = "' . $unit_id . '"';
		}
		$cntr1 .= ' Group BY unittbl.unit_id ORDER BY unittbl.sort_order ASC';
		
		$result = $this->m_dbConn->select($sql1);
		//print_r($result);
		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr1;
		$this->display_pg->mainpg	= "notification.php";

		$limit	= "25";
		$page	= $_REQUEST['page'];
		$extra	= "&wing_id=" . $wing_id . "&period_id=" . $period_id ;
		$data=$this->display2($result, $period_id, $Supplemenary_bills);
		return $data;
	}
	
	public function display2($rsas, $periodID,  $Supplemenary_bills)
	{
		//echo "inside display2";
		$thheader=array('Sr No','Member Name','Wing','Unit No.', 'E-Mail');
		$this->display_pg->edit="notify";
		$this->display_pg->th=$thheader;
		$this->display_pg->mainpg="notification.php";
		$res=$this->show_unit_report($rsas, $periodID,  $Supplemenary_bills);
		//echo "exiting display1";
		//$res1=$this->display_pg->display_new($res);
		
		return $res;
	}
	
	public function show_unit_report($res, $periodID,  $Supplemenary_bills)
	{
		$loginstatus=$_POST['ac_status'];
		 $read=$_POST['read_type'];
		$billText = "Maintenance"	;
		if($Supplemenary_bills == '1')
		{
			$billText = "Supplementary"	;
		}
		$sqlPeriod = "Select periodtbl.type, yeartbl.YearDescription from period as periodtbl JOIN year as yeartbl ON periodtbl.YearID = yeartbl.YearID where periodtbl.ID = '" . $periodID . "'";
		
		$sqlResult = $this->m_dbConn->select($sqlPeriod);
		//print_r($sqlResult);
		echo "<b><font color='#0000FF'> ".$billText."  Bill's For : " . $sqlResult[0]['type'] . " "  . $sqlResult[0]['YearDescription'] . "</font></b><br><br>";
		if($res<>"")
		{
			//print_r($res);
			$str_unit_ary = '';
			
			if(!isset($_REQUEST['page']))
			{
				$_REQUEST['page'] = 1;
			}
			$iCounter = 1 + (($_REQUEST['page'] - 1) * 50);
		?>
            <table align="center" class="display" cellspacing="0" width="100%" id="example" >
			<thead>
				<tr height="30">
					<!--<th><input type="checkbox" id="chk_all" onclick="SelectAll(this);"/></th>-->
					<th width="50">Sr No</th>
					<th width="300">Member Name</th>
					<th width="100">Wing</th>
					<th width="100">Unit No.</th>
					<th width="100">Email ID</th>
                    <th width="100">Mobile No.</th>
					<!--<th width="200">Last Email Notification</th>					
					<th width="200">Last SMS Notification</th>
                    <th width="200">SMS Delivery</th>-->
                    <th width="200">Bill Read TimeStamp</th>
                    <th width="100">Due Amount </th>
                   <!-- <th width="100">View </th>-->
					<th width="100">Login Status</th>
					<th width="100" >Activation Link</th>
											  
				</tr>
			</thead>
			<tbody>
           
        <?php foreach($res as $k => $v)
		{ // echo "test  record";
			$str_unit_ary .= $res[$k]['unit_id'] . '#';
			$specialChars = array('/','.', '*', '%', '&', ',', '(', ')', '"');
        	$UnitNo = str_replace($specialChars, '', $res[$k]['unit_no']);
		 	$RetStatus =$this->obj_activation->CheckIfMappingAlreadyExist($res[$k]['email'],$_SESSION['society_id'],  $res[$k]['unit_id']);
			//print_r($RetStatus);
			$bShow = false;
			$bRead=false;
			if($loginstatus == 0 || ($loginstatus == 1 && $RetStatus == ACCOUNT_EXIST_ACTIVE) || ($loginstatus == 2 && ($RetStatus == NO_ACCOUNT || $RetStatus==ACCOUNT_EXIST_MAPPING_NOT_FOUND) ))   
			{
				$bShow = true;
			}
			 if($read==0 || ($read==1 && $res[$k]['bill_read_timestamp'] <> 0) ||( $read==2 && $res[$k]['bill_read_timestamp']==0) )
			{
				$bRead=true;
			}
			
			if($bShow==true && $bRead==true)
			{
				//echo"test" .$res[$k]['member_id'];
			?>
        	<tr height="25" align="center">
			<!--<td><input type="checkbox" value="1" id="chk_<?php //echo $res[$k]['unit_id']; ?>" /></td>-->	
        	<td align="center"><?php echo $iCounter++;?></td>
            <td align="center"><a href="view_member_profile.php?scm&id=<?php echo $res[$k]['member_id'];?>&tik_id=<?php echo time();?>&m&view" target="_blank"><?php echo $res[$k]['owner_name'];?></a></td>
            <td align="center"><?php echo $res[$k]['wing'];?></td>
            <td align="center"><?php echo $res[$k]['unit_no'];?></td>
            <td align="center"><?php echo $res[$k]['email'];?></td>
            <td align="center"><?php echo $res[$k]['mob'];?></td>
            <td align="center"><?php echo $res[$k]['bill_read_timestamp'];?></td>		
            <?php $Url = "member_ledger_report.php?&uid=".$res[$k]['UnitID']; ?>
            <td align="center"><a href="#" onClick="window.open('<?php echo $Url; ?>','popup','type=fullWindow,fullscreen,scrollbars=yes');" style="color:#0000FF;"><?php echo $this->obj_utility->getDueAmount($res[$k]['UnitID']); ?></a></td>            
           <td align="center">
        <?php 
			if($RetStatus == ACCOUNT_EXIST_ACTIVE)
						{
							echo "Active";
						} 
						else
						{
							echo "Inactive";
						}
						?>
            </td>
            
            <td align="center">
            	<?php if($RetStatus == ACCOUNT_EXIST_ACTIVE)
                {?>
                
                <a onclick="emailPromtWindow(<?php echo "'". $res[$k]['email']."'" ?>,<?php echo "'".$name."'" ?>,<?php echo "'".$res[$k]['code']."'" ?>);" ><i class='fa  fa-envelope-o'  style='font-size:10px;font-size:1.25vw;color:#F00; display:none;' ></i></a>
                <?php }
				else
				{
               echo "<input type='button' value='Send Email' name='Send_activation_email' id='Send_activation_email' onClick=\"SendActEmail('Member','". $res[$k]['unit_no'] ."','". $_SESSION['society_id'] ."','". getRandomUniqueCode()."','".  $res[$k]['email'] ."','".$res[$k]['owner_name']."')\"/>";
                 }?>
                </td>
          
                
            <!--<td align="center">
            	<div id="status_<?php// echo $res[$k]['unit_id']; ?>" style="color:#0033FF; font-weight:bold;"></div>
            </td>-->
            <?php }?> 
            </tr>
        <?php 
			}
		?>
			</tbody>
        </table>
		<input type="hidden" id="unit_ary" value="<?php echo $str_unit_ary; ?>" />
		<br />
		<br /><br />
		<?php
		}
		else
		{
			?>
            <table align="center" border="0">
            <tr>
            	<td><font color="#FF0000" size="2"><b>No Records Found notification record.</b></font></td>
            </tr>
            </table>
            <?php		
		}
	}
}
?>
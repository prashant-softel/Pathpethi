<?php
include_once("include/dbop.class.php");
include_once("include/display_table.class.php");
include_once("GeneratePaymentReport.class.php");

class chequeleafbook extends dbop
{
	public $actionPage = "../chequeleafbook.php";
	public $m_dbConn;
	public $obj_view_bank_statement;
	
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->obj_view_bank_statement = new Payment_Report($this->m_dbConn);

		/*//$this->curdate		= $this->display_pg->curdate();
		//$this->curdate_show	= $this->display_pg->curdate_show();
		//$this->curdate_time	= $this->display_pg->curdate_time();
		//$this->ip_location	= $this->display_pg->ip_location($_SERVER['REMOTE_ADDR']);*/
		$this->actionPage = "../chequeleafbook.php?bankid=".$_REQUEST["BankID"];
		dbop::__construct();
	}

	public function startProcess()
	{
		$errorExists = 0;

		/*//$curdate 		=  $this->curdate;
		//$curdate_show	=  $this->curdate_show;
		//$curdate_time	=  $this->curdate_time;
		//$ip_location	=  $this->ip_location;*/
		//echo $_REQUEST['insert'];
		$StartChq= "0";
		$EndChq = "0";
		if(isset($_POST['CustomLeaf']) && $_POST['CustomLeaf'] == 1)
		{
		}
		else
		{
			
			$StartChq = $_POST['StartCheque'];
			$EndChq = $_POST['EndCheque'];
		}

		if($_REQUEST['insert']=='Insert' && $errorExists==0)
		{
			$insert_query="insert into chequeleafbook (`LeafName`,`StartCheque`,`EndCheque`,`BankID`,`Comment`,`CustomLeaf`,`LeafCreatedYearID`) values ('".$_POST['LeafName']."','".$StartChq."','".$EndChq."','".$_POST['BankID']."','".$_POST['Comment']."','".$_POST['CustomLeaf']."','".$_SESSION['default_year']."')";
			$data = $this->insert($insert_query);
			return "Insert";
		}
		else if($_REQUEST['insert']=='Update' && $errorExists==0)
		{
			$up_query="update chequeleafbook set `LeafName`='".$_POST['LeafName']."',`StartCheque`='".$StartChq."',`EndCheque`='". $EndChq."',`BankID`='".$_POST['BankID']."',`Comment`='".$_POST['Comment']."',`CustomLeaf`='".$_POST['CustomLeaf']."' , `LeafCreatedYearID`='".$_SESSION['default_year']."'    where id='".$_POST['id']."'";
			$data = $this->update($up_query);
			return "Update";
		}
		else
		{
			return $errString;
		}
	}
	public function combobox($query)
	{
		
		$id=0;
		//echo "<script>alert($query)<//script>";
		$str.="<option value='-1'>Please Select Leaf</option>";
		$str.="<option value='0'>Add New Leaf Book</option>";
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
		$thheader = array('LeafName','CustomLeaf','StartCheque','EndCheque','BankID','Comment');
		$this->display_pg->edit		= "getchequeleafbook";
		$this->display_pg->th		= $thheader;
		$this->display_pg->mainpg	= "chequeleafbook.php";

		$res = $this->display_pg->display_new($rsas);
		return $res;
	}
	public function pgnation($BankID)
	{
		$sql1 = "select id,`LeafName`,`CustomLeaf`,`StartCheque`,`EndCheque`,`BankID`,`Comment` from chequeleafbook where status='Y' and BankID='".$BankID."' and LeafCreatedYearID	='".$_SESSION['default_year']."'";
		$cntr = "select count(status) as cnt from chequeleafbook where status='Y' and LeafCreatedYearID = '".$_SESSION['default_year']."'";

		$this->display_pg->sql1		= $sql1;
		$this->display_pg->cntr1	= $cntr;
		$this->display_pg->mainpg	= "chequeleafbook.php";

		$limit	= "50";
		$page	= $_REQUEST['page'];
		$extra	= "";

		$res	= $this->display_pg->pagination($cntr,$mainpg,$sql1,$limit,$page,$extra);
		return $res;
	}
	public function NewUI($BankID)
	{
		$sql1 = "select id,`LeafName`,`CustomLeaf`,`StartCheque`,`EndCheque`,`BankID`,`Comment` from chequeleafbook where `status`='Y' and `BankID`='".$BankID."' and `LeafCreatedYearID` = '".$_SESSION['default_year']."'";
		//echo $sql1;
		$AllLeaf = $this->m_dbConn->select($sql1);
		//var_dump($AllLeaf);
		if(isset($AllLeaf))
		{
		$StartCheque = $AllLeaf[0]["StartCheque"];	
		$EndCheque = $AllLeaf[0]["EndCheque"];
		$LeafID = $AllLeaf[0]["id"];	
		//echo "id:".$LeafID;
		//$AllLeaf = $this->m_dbConn->select('SELECT * FROM `chequeleafbook` where `id`='.$LeafID);
		
		//print_r($AllLeaf);
		if(isset($AllLeaf))
		{
		?>
		<table id="example" class="display" cellspacing="0" width="100%">
        <thead>
		<tr height="30">
        	<th width="50">Leaf Name</th>
        	<th width="30">Custom Leaf</th>
            <th width="100">Start Cheque</th>
            <th width="100">End Cheque</th>
            <th width="100">Comment</th>
            <th width="100">Total of Cheques</th>
            <th width="100">Cheques Issued</th>
            <th width="100">Cheques not issued</th>
            <th width="100">Action</th>
            <th width="100">Report</th>
            <th width="100">Edit</th>
         </tr>
        </thead>
        <tbody> 
         <?php 
		 
		 //echo "Count:".count($arIssued);
		 for($i = 0; $i < sizeof($AllLeaf); $i++)
		{
			$StartChequeNumber= $AllLeaf[$i]['StartCheque'];
			$EndChequeNumber= $AllLeaf[$i]['EndCheque'];
			$LeafName = $AllLeaf[$i]['LeafName'];
			$CustomLeaf = $AllLeaf[$i]['CustomLeaf'];			
			$iTotalChqs =  $EndChequeNumber - $StartChequeNumber;
			$iTotalChqs = $iTotalChqs + 1;
			$arIssued = array();
			
			$sqlCnt = "SELECT Count(*) as cnt FROM `paymentdetails` where `ChqLeafID`='".$AllLeaf[$i]['id']."' ";
			$res= $this->m_dbConn->select($sqlCnt);
			$iIssued = $res[0]['cnt'];
				/*for($iChqCount = $StartChequeNumber; $iChqCount < $EndChequeNumber; $iChqCount++)
				{
					$SlipDetails = $this->obj_view_bank_statement->GetChqLeafDetails2($iChqCount, $AllLeaf[$i]["id"]);
					echo "<br>size : ".sizeof($SlipDetails);
					if(sizeof($SlipDetails) > 0 )
					{
						//var_dump($SlipDetails);
						$PaidBy = $SlipDetails[0]['PaidTo'];
						$UnitNo = "";
						if($PaidBy <> "")
						{
							$UnitNo = $this->obj_view_bank_statement->getLedgerName($PaidBy);
						}
						$ChequeDate = $SlipDetails[0]['ChequeDate'];
						$ChequeNo = $SlipDetails[0]['ChequeNumber'];
						$Amount = $SlipDetails[0]['Amount'];
						$comments = $SlipDetails[0]['Comments'];			
						
						$arIssued[$iChqCount] = $iChqCount; 
					}
				}*/
			
			//$iIssued = count($arIssued);
			//echo "Issued:".$iIssued;
			$NonIssused = abs($iTotalChqs - $iIssued);

			if($CustomLeaf == 1)
			{
				$iTotalChqs = '-';
				$NonIssused = '-';
			}

			echo "<tr>
					 <td>
					 ".$LeafName."</td><td>".$CustomLeaf."</td><td>".$StartChequeNumber."</td><td>".$EndChequeNumber."</td><td>".$AllLeaf[$i]['Comment']."<td>".$iTotalChqs."</td><td>".$iIssued."</td><td>".$NonIssused."</td><td><a href=PaymentDetails.php?bankid=".$BankID."&LeafID=".$AllLeaf[$i]["id"]."&CustomLeaf=". $CustomLeaf .">Select For Payment</a></td><td><a href=GeneratePaymentReport.php?leafid=".$AllLeaf[$i]["id"].">View</a></td><td><a href='javascript:void(0);' id=edit-" .$AllLeaf[$i]["id"]." onclick='getchequeleafbook(this.id);'><img src='images/edit.gif'/></a></td>
					 </tr>";
		}
		 ?>
         </thead>
         </table>
        <?php
		}
		}
	}
	public function selecting()
	{
		$sql = "select `id`,`LeafName`,`CustomLeaf`,`StartCheque`,`EndCheque`,`BankID`,`Comment` from chequeleafbook where id='".$_REQUEST['chequeleafbookId']."' and `LeafCreatedYearID` = '".$_SESSION['default_year']."'";
		//echo $sql;
		$res = $this->select($sql);
		return $res;
	}
	public function deleting()
	{
		$sql = "update chequeleafbook set status='N' where id='".$_REQUEST['chequeleafbookId']."' and LeafCreatedYearID = '".$_SESSION['default_year']."'";
		$res = $this->update($sql);
	}
}
?>
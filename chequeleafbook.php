<?php include_once "ses_set_as.php"; ?>
<?php include_once "check_default.php" ?>
<?php
if(isset($_SESSION['admin']))
{
	include_once("includes/header.php");
}
else
{
	include_once("includes/head_s.php");
}

include_once("classes/chequeleafbook.class.php");
$obj_chequeleafbook = new chequeleafbook($m_dbConn);
$BankID = $_REQUEST["bankid"];
?>
 

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/pagination.css" >
    <script type="text/javascript" src="lib/js/jquery.min.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript" src="js/chequeleafbook.js"></script>
    <script language="javascript" type="application/javascript">
	function go_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeIn("slow");
		});
        setTimeout('hide_error()',8000);	
    }
    function hide_error()
    {
		$(document).ready(function()
		{
			$("#error").fadeOut("slow");
		});
    }
	function Redirect()
	{
		var LeafID = document.getElementById("LeafBook").value;
		var BankID = document.getElementById("BankID").value;
		//var CustomLeaf = document.getElementById("CustomLeaf").value;
		//alert(LeafID);
		if(LeafID >= 1)
		{
			window.location.href='PaymentDetails.php?bankid='+BankID+'&LeafID='+LeafID ;
		}
	}
	function OpenPage()
	{
		var LeafID = document.getElementById("LeafBook").value;
		if(LeafID >= 1)
		{
			window.location.href='GeneratePaymentReport.php?leafid='+LeafID;
		}
		
	}
	function validateForm()
	{
		var sSelectedLeafName = document.getElementById("LeafName").value;
		var sStartChq = document.getElementById("StartCheque").value;
		var sEndChq = document.getElementById("EndCheque").value;
		var sInsertType = document.getElementById("insert").value;
		var isCustomLeaf = document.getElementById("CustomLeaf").checked;		
		if(parseInt(sEndChq) < parseInt(sStartChq))
		{
			alert("End Cheque Number must be greater than Start Cheque Number.");
			return false;
		}
		//alert(sSelectedLeafName);
		<?php 			
			$select_query = "select * from chequeleafbook";
			$select_data = $obj_chequeleafbook->m_dbConn->select($select_query);
			for($iHeaderCount = 0; $iHeaderCount <= sizeof($select_data); $iHeaderCount++)
			{
				//echo "<script>alert('test')<//script>";
				$LeafName = $select_data[$iHeaderCount]["LeafName"]; 
				if($LeafName != "")
				{
					?>
					//echo "test";
					
					var sLeafName = '<?php echo $LeafName; ?>';
	
					//alert(sLeafName);
					if((sLeafName.toUpperCase() == sSelectedLeafName.toUpperCase() ) && sInsertType == "Insert")
					{
						alert("Leaf Name <"+sSelectedLeafName +"> already exist, Please enter different one.");
						return false;
					}
					else
					{
						//return true;
					}
				<?php
				}
			}
		?>
		if(isCustomLeaf == 0 && (sEndChq == "" || sStartChq == ""))
		{
			alert('Start and End cheque number should not be empty.');
			return false;
		}
	
		return true;

	}
	function LeafChanged()
	{
		var LeafID = document.getElementById("LeafName").value;
		//alert(LeafID);
		if(LeafID.length == 0)
		{
			var lTable = document.getElementById("AddNewSlip");
			lTable.style.display = (lTable.style.display == "table") ? "none" : "table";
			
/*			var lTable2 = document.getElementById("SelectLeaf");
			lTable2.style.display =  "none" ;*/
			
			var lTable3 = document.getElementById("NewLeaf");
			lTable3.style.display =  "none" ;
		}
		else
		{
			document.getElementById("LeafName").value = "";
			document.getElementById("StartCheque").value = "";
			document.getElementById("EndCheque").value = "";
			document.getElementById("Comment").value = "";
			document.getElementById("CustomLeaf").checked = false;
			var lTable = document.getElementById("AddNewSlip");
			lTable.style.display = (lTable.style.display == "table") ? "none" : "table";
			//document.getElementById("LeafName").value = "";
		}
	}
	function OnCancel()
	{
			//var lTable = document.getElementById("SelectLeaf");
			//lTable.style.display = (lTable.style.display == "none") ? "table" : "none" ;
			var lTable = document.getElementById("AddNewSlip");
			lTable.style.display =  "none" ;
			
			//var lTable2 = document.getElementById("NewLeaf");
//			lTable2.style.display =  "button" ;
//			
			location.reload(true);
	}
	</script>
</head>

<body>
<center>
<br>
<div class="panel panel-info" id="panel" style="display:none">
    <div class="panel-heading" id="pageheader">Cheque Leaf Book</div>

<?php
$star = "<font color='#FF0000'>*</font>";
if(isset($_REQUEST['msg']))
{
	$msg = "Sorry !!! You can't delete it. ( Dependency )";
}
else if(isset($_REQUEST['msg1']))
{
	$msg = "Deleted Successfully.";
}
else{}
?>

<form name="chequeleafbook" id="chequeleafbook" method="post" action="process/chequeleafbook.process.php" onSubmit="return validateForm()">
<table align='center'>
<tr>
<td>
<?php
		$sqlBankName = "select BankName from bank_master where BankID = '" . $_REQUEST['bankid'] . "'";
		$sqlResult = $m_dbConn->select($sqlBankName);
		echo '<center><h3>'.$sqlResult[0]['BankName'].'</h3></center>';
	?>

<?php
echo $msg;
//echo $_POST['ShowData'];
if(isset($msg) || isset($_POST['ShowData']))
{
	if(isset($_POST['ShowData']))
	{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
	}
	else
	{
	?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $msg; ?></b></font></td></tr>
	<?php
	}
}
else
{
?>
		<tr height='30'><td colspan='4' align='center'><font color='red' size='-1'><b id='error' style='display:none;'><?php echo $_POST['ShowData']; ?></b></font></td></tr>
<?php
}
?>
<br>
<?php
if(IsReadonlyPage() == false)
{?>
<button type="button" class="btn btn-primary" onClick="LeafChanged(this)" id="Newleaf">Add New Cheque Leaf Book</button>
<?php }?>
<table id="AddNewSlip" style="display:none">

		<tr>
			<td>Leaf Name</td>
			<td><input type="text" name="LeafName" id="LeafName" /></td>
		</tr>
		<tr>
			<td>Custom Leaf</td>
			<td><input type="checkbox" name="CustomLeaf" id="CustomLeaf" onChange="CustomLeafChanged()" value="1" /></td>
		</tr>
        <tr>
			<td>Comment</td>
			<td><input type="text" name="Comment" id="Comment" /></td>
		</tr>
        <tr id="StartChequeRow">
			<td>Start Cheque</td>
			<td><input type="text" name="StartCheque" id="StartCheque" /></td>
		</tr>
		<tr  id="EndChequeRow">
			<td>End Cheque</td>
			<td><input type="text" name="EndCheque" id="EndCheque" /></td>
		</tr>
        
		
		<tr>
			
			<td><input type="hidden" name="BankID" id="BankID" value="<?php echo $_REQUEST["bankid"] ?>"   /></td>
		</tr>
		<tr><td></td>
			<td align="center" colspan="2"><input type="hidden" name="id" id="id"><input type="submit" name="insert" id="insert" value="Insert"  >
            <input type"button" onClick="OnCancel()" value="Cancel" style="width:50px"/>
            </td>
		</tr>
        </table>
        <table id="SelectLeaf" style="display:none">
        <tr>
        <td>
        <select id="LeafBook" onChange="LeafChanged()" name="LeafBook"> <?php //echo $obj_chequeleafbook->combobox("select id,LeafName from chequeleafbook where BankID=".$_REQUEST["bankid"]);  ?>
        </select>
        <br><br>
        <input type="button" onClick="Redirect()" value="Select for Payment"/>
        <input type="button" onClick="OpenPage()" value="View Report"/>
        </td>
        </tr>
</table>
</form>
<script>
function CustomLeafChanged()
{
	if(document.getElementById("CustomLeaf").checked)
	{
		document.getElementById("StartChequeRow").style.visibility = "hidden";
		document.getElementById("EndChequeRow").style.visibility = "hidden";
		document.getElementById("CustomLeaf").value = "1";
	}
	else
	{
		document.getElementById("StartChequeRow").style.visibility = "visible";
		document.getElementById("EndChequeRow").style.visibility = "visible";
		document.getElementById("CustomLeaf").value = "0";
	}
}
</script>
<table align="center">
<tr>
<td>
<?php
//echo "<br>";
//$str1 = $obj_chequeleafbook->pgnation($BankID);
//echo "<br>";
//echo $str = $obj_chequeleafbook->display1($str1);
//echo "<br>";
$str1 = $obj_chequeleafbook->NewUI($BankID);
echo "<br>";
?>
</td>
</tr>
</table>
</center>
</div>
<?php include_once "includes/foot.php"; ?>
<?php if(!isset($_SESSION)){ session_start(); } 
include_once("classes/include/dbop.class.php");
$dbconn = new dbop();?>
<?php
$SocietyEmailID = ""; 
	if($_SESSION['society_id'])
	{
		$sql = "SELECT `email` FROM `society` where `society_id`= '".$_SESSION['society_id'] ."' ";
		$res = $dbconn->select($sql);
		$SocietyEmailID = $res[0]['email'];
		if($SocietyEmailID == ""){?>
			<script>
				alert('Please Set Society EmailID.');
				window.location.href = 'society.php?id=<?php echo $_SESSION['society_id']?>&show&imp';
			</script>   
		<?php }
		
	}
  //if($_SESSION['default_interest_on_principle'] == 0 || $_SESSION['default_current_asset'] == 0 || $_SESSION['default_bank_account'] == 0 ||$_SESSION['default_cash_account'] == 0 || $_SESSION['default_due_from_member'] == 0 || $_SESSION['society_id'] == 0 || $_SESSION['default_penalty_to_member'] == 0 || $_SESSION['default_tds_payable'] == 0 || $_SESSION['default_adjustment_credit'] == 0 || $_SESSION['defaultEmailID'] == "")
	if($_SESSION['default_year'] == 0 ||$_SESSION['default_interest_on_principle'] == 0 || $_SESSION['default_current_asset'] == 0 || $_SESSION['default_bank_account'] == 0 ||$_SESSION['default_cash_account'] == 0 || $_SESSION['default_due_from_member'] == 0 || $_SESSION['society_id'] == 0 || $_SESSION['default_penalty_to_member'] == 0 || $_SESSION['default_tds_payable'] == 0 || $_SESSION['default_adjustment_credit'] == 0)
	{
		?>
			<script>
				alert('Please set Default values for Ledger , Account Category and Society EmailID.');
				window.location.href = 'defaults.php?alog';
			</script>   
		<?php
	}
			
?>

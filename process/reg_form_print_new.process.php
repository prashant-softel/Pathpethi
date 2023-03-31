<?php	
		include_once("../classes/include/dbop.class.php");
		include_once("../classes/add_comment.class.php");
		$dbConn = new dbop();
		$dbConnRoot = new dbop(true);
		$obj_add_comment = new add_comment($dbConn, $dbConnRoot);
		$add_comment = $obj_add_comment->startProcess();
?>

<script language="javascript">
window.location.href = '../reg_form_print_new.php?id=<?php echo $_POST['id']?>&srm&msg#view';
</script>
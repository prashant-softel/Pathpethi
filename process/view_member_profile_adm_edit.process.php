<?php	include_once "../classes/view_member_profile_adm_edit.class.php" ;
		include_once("../classes/include/dbop.class.php");
	  	$dbConn = new dbop();
		$obj_view_member_profile_adm_edit = new view_member_profile_adm_edit($dbConn);
		$update_member_profile = $obj_view_member_profile_adm_edit->update_member_profile();
?>

<script language="javascript" type="application/javascript">
	window.location.href = '../view_member_profile_adm.php?scm&id=<?php echo $_POST['id'];?>&tik_id=<?php echo time();?>&m&view';
</script>

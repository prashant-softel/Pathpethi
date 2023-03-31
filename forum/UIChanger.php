<?php if(!isset($_SESSION)){ session_start(); }

if($_REQUEST['type'] == 'ap')
{
?>

<script> window.location.href = 'adm/index.php?sid=' + '<?php echo $_SESSION['phpbb_session_id'];?>';</script>
<?php }
else
{ ?> 
<script> window.location.href = 'index.php';</script>
<?php } ?>

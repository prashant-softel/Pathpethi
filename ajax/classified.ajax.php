
<br />
<font size='1'><table class='xdebug-error xe-notice' dir='ltr' border='1' cellspacing='0' cellpadding='1'>
<tr><th align='left' bgcolor='#f57900' colspan="5"><span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span> Notice: Undefined property: ajaxclass::$class_tool in C:\wamp\www\Form_maker\classes\ajaxmaker.class.php on line <i>6</i></th></tr>
<tr><th align='left' bgcolor='#e9b96e' colspan='5'>Call Stack</th></tr>
<tr><th align='center' bgcolor='#eeeeec'>#</th><th align='left' bgcolor='#eeeeec'>Time</th><th align='left' bgcolor='#eeeeec'>Memory</th><th align='left' bgcolor='#eeeeec'>Function</th><th align='left' bgcolor='#eeeeec'>Location</th></tr>
<tr><td bgcolor='#eeeeec' align='center'>1</td><td bgcolor='#eeeeec' align='center'>0.0010</td><td bgcolor='#eeeeec' align='right'>260664</td><td bgcolor='#eeeeec'>{main}(  )</td><td title='C:\wamp\www\Form_maker\main\sampleajax.php' bgcolor='#eeeeec'>..\sampleajax.php<b>:</b>0</td></tr>
<tr><td bgcolor='#eeeeec' align='center'>2</td><td bgcolor='#eeeeec' align='center'>0.0640</td><td bgcolor='#eeeeec' align='right'>275848</td><td bgcolor='#eeeeec'>ajaxclass->ajaxcreate(  )</td><td title='C:\wamp\www\Form_maker\main\sampleajax.php' bgcolor='#eeeeec'>..\sampleajax.php<b>:</b>11</td></tr>
</table></font>
<?php include_once("../class/classified.class.php");
$obj_classified = new classified;

echo $_REQUEST["method"]."@@@";

if($_REQUEST["method"]=="edit")
{
	$select_type = $obj_classified->selecting();

	foreach($select_type as $k => $v)
	{
		foreach($v as $kk => $vv)
		{
			echo $vv."#";
		}
	}
}

if($_REQUEST["method"]=="delete")
{
	$obj_classified->deleting();
	return "Data Deleted Successfully";
}

?>
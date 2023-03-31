<html>
<head>
	<title>Calender</title> 
<?php
/**
 * Writes localised date
 *
 * @param   string   the current timestamp
 *
 * @return  string   the formatted date
 *
 * @access  public
 */

/*function PMA_localisedDate($timestamp = -1, $format = '')
{
    global $datefmt, $month, $day_of_week;

    if ($format == '') {
        $format = $datefmt;
    }

    if ($timestamp == -1) {
        $timestamp = time();
    }

    $date = preg_replace('@%[aA]@', $day_of_week[(int)strftime('%w', $timestamp)], $format);
    $date = preg_replace('@%[bB]@', $month[(int)strftime('%m', $timestamp)-1], $date);
	$strTime = 'Time'; //to translate
	
    return strftime($date, $timestamp);
} // end of the 'PMA_localisedDate()' function

MA_localisedDate();*/

$page_title = $strCalendar;

$day_of_week = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
$month = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$strTime = 'Time';
$strGo = 'Go'
?>

<style>
/* Calendar */
	table.calendar      { width: 100%; }
	table.calendar td   { text-align: center; }
	
	table.calendar td a { display: block; }
	
	table.calendar td a:hover {
		background-color: #CC9999;
	}
	
	table.calendar th {
		background-color: #00CCFF;
	}
	
	table.calendar td.selected {
		background-color: #FF0000;
	}
	
	img.calendar { border: none; }
	form.clock   { text-align: center; }
/* end Calendar */
</style>


<script type="text/javascript">
//<![CDATA[
var month_names = new Array("<?php echo implode('","', $month); ?>");
var day_names = new Array("<?php echo implode('","', $day_of_week); ?>");
var submit_text = "<?php echo $strGo . ' (' . $strTime . ')'; ?>";
//]]>
</script>
<script type="text/javascript" src="../javascript/tbl_change.js"></script>
</head>
<body onLoad="initCalendar();">
<div id="calendar_data"></div>
<div id="clock_data"></div>
</body>
</html>

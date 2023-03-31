<?php if(!isset($_SESSION)){ session_start(); }
include_once("classes/include/check_session.php");
//include_once("classes/include/dbop.class.php");
include_once("classes/head.class.php");
include_once("header.php");
//$m_dbConnRoot = new dbop(true);
$m_objHead_S = new head($m_dbConnRoot);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Way2Society</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="icon" type="image/png" href="favicon.ico">
<meta name="description" content="Contact us page - free business website template available at TemplateMonster.com for free download."/>
<link href="csss/style.css" rel="stylesheet" type="text/css" />
<link href="cssss/layout.css" rel="stylesheet" type="text/css" />
</head>
<body>

<div style="width:100%;background:#000;height:50px;color:#FFFFFF;">
	<table style="width:100%;vertical-align:middle;">
    	<tr>
        	<td style="width:50%; text-align:left;">
            	<font style="padding:10x;margin-left:170px;vertical-align:central;float:left"><br>Welcome to Housing Society Accounting Software </font>
           	</td>
            <td style="width:50%; text-align:right;">
            	<?php if($_SESSION['name'] <> '')
				{
					?>
                    <font color="#FFF" style="font-size:12px;padding-right:180px;float:right;margin-top:10px" >Welcome <b><?php echo $_SESSION['name']; ?></b>&nbsp;<a href="logout.php">[Logout]</a><br>
                        <?php $societyName = $m_objHead_S->GetSocietyName($_SESSION['society_id']);
                        if($societyName <> '')
                        {
                        ?>
                            To <b><?php echo $m_objHead_S->GetSocietyName($_SESSION['society_id']);?></b>
                            <a href="initialize.php?imp" style="color:#00CCFF">[Change]</a><br>
                        <?php
                        }
                        else
                        {
                        ?>
                            <a href="initialize.php">[Initialize]</a><br>
                        <?php
                        }
                        ?>
                    </font>
                    <?php
				}
				else
				{
					?>
                    	<font color="#FFF" style="font-size:12px;padding-right:180px;float:right;margin-top:10px" >Welcome <b>Guest</b>&nbsp;<a href="logout.php">[Logout]</a><br>
					<?php
				}
				?>
			</td>
        </tr>
    </table>
</div>
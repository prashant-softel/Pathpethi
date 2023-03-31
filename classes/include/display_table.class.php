<?php 
include_once("dbop.class.php");

class display_table //extends dbop
{
	public $m_dbConn;
	function __construct($dbConn = '')
	{
		if(isset($dbConn) && $dbConn <> '')
		{
			/*echo '<script>alert("Is Set");</script>';*/
			$this->m_dbConn = $dbConn;
		}
		else
		{
			/*echo '<script>alert("Not Set");</script>';*/
			$this->m_dbConn = new dbop();
		}
	}
		
	function display_new($rsas, $ShowEditOption = true, $ShowDeleteOption = true)
	{  
		$str = "<table border = '0'  align = 'center' cellpadding=2 cellspacing=2>";

		if(!is_null($rsas))
		{
			
			$cnt2=$cnt1-1;
			
			$str.="<tr class='head' height='30' bgcolor='#CCCCCC'>"; // style='color:#FFFFFF; background-color:#999999'
		
			if($ShowEditOption)
			{
				$str .= "<th align='center' width=80>&nbsp;&nbsp;";
				$str .= "Edit";
				$str .= "&nbsp;</th>";
			}
			
			if($ShowDeleteOption)
			{
				$str .= "<th align='center' width=80>&nbsp;&nbsp;";
				$str .= "Delete";
				$str .= "&nbsp;&nbsp;</th>";
			}
			
			$countth=count($this->th);
			
			for($k=0;$k<$countth;$k++)
			{	
				$str .= "<th>&nbsp;&nbsp;";
				$str .= $this->th[$k];
				$str .= "&nbsp;&nbsp;</th>";
			}
			$str.="</tr>";
			
			foreach($rsas as $key => $value)
			{				
				$str .= "<tr height='25' bgcolor='#BDD8F4'>"; //  bgcolor='#EEB9EA'
				$i = 0;
				
				foreach($value as $k => $v)
				{
					if($i == 0)
					{
						if($ShowEditOption)
						{
							$str .= 
						"<td align='center' valign='top'><a id='edit-".$v."' onClick='".$this->edit."(this.id);'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;'/></a></td>";
						}
						##########################################################
						/*
						if($_SESSION['username']=='admin')
						{
							if($this->m_dbConn->delete_perm_admin()==1)
							{
								$str .= "<td align='center' valign='top'><a id='delete-".$v."' onClick='".$this->edit."(this.id);'><img src='../images/del.gif' border='0' alt='Delete'  style='cursor:pointer;'/></a></td>";
							}
							else
							{
								$str .= "<td align='center' valign='top'><font color=#FF0000 style='font-size:11px;'><b>Not Allowed</b></font></td>";
							}
						}
						else
						{
							if($this->chk_delete_perm_emp()==1)
							{
								$str .= "<td align='center' valign='top'><a id='delete-".$v."' onClick='".$this->edit."(this.id);'><img src='../images/del.gif' border='0' alt='Delete'  style='cursor:pointer;'/></a></td>";							
							}
							else
							{
								$str .= "<td align='center' valign='top'><font color=#FF0000 style='font-size:11px;'><b>Not Allowed</b></font></td>";
							}
						}
						*/
						if($ShowDeleteOption)
						{
							if($this->chk_delete_perm_sadmin()==1)
							{
								$str .= "<td align='center' valign='top'><a id='delete-".$v."' onClick='".$this->edit."(this.id);'><img src='../images/del.gif' border='0' alt='Delete'  style='cursor:pointer;'/></a></td>";
							}
							else
							{
								$str .= "<td align='center' valign='top'><a href='del_control_admin.php?prm' target='_blank' style='text-decoration:none'><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a></td>";
							}
						}
                        ##########################################################
						
						$i++;
					}
					else
					{
						if(substr($v,0,9)=="../upload")
						{
							$str.="<td valign='top'><img name=".$i." src=".stripslashes($v)." width='90' height='70'></td>";
						}
						else
						{
							if(strlen($v)>100)
							{
								if($v<>"")
								{
									$str .= "<td valign='top' width='500px'>&nbsp;&nbsp;".$v."&nbsp;&nbsp;</td>";
								}
								else
								{
									$str .= "<td valign='top'>&nbsp;</td>";
								}
							}
							else
							{
								if($v<>"")
								{	
									$str .= "<td valign='top' align='center'>&nbsp;&nbsp;".stripslashes($v)."&nbsp;&nbsp;</td>";	
								}
								else
								{
								$str .= "<td valign='top'>&nbsp;</td>";
								}
							}
						}
						$i++;
					}
				}
				$str .= "</tr>";
			}
		}		
		
		$str.="</table>";
		return $str;
	}
	
	function display_datatable($rsas, $ShowEditOption = true, $ShowDeleteOption = true,$ShowViewOption = false,$ShowPrintOption = false,$ShowViewModifyOptions = true)
	{  
		?>
		
		
		
		<?php
		if(!is_null($rsas))
		{
			?>
       <table id="example" class="display" cellspacing="0" width="100%">
		<thead>
        <?php
			$cnt2=$cnt1-1;
			?>
			<tr>
			<?php
			
			if($ShowPrintOption)
			{
				?>
				<th align='center' width=8>Print</th>
				<?php
			}
        	
			if($ShowViewOption)
			{
				?>
				<th align='center' width=8>View</th>
				<?php
			}
			
			if($ShowEditOption && $ShowViewModifyOptions)
			{
				?>
				<th align='center' width=8>Edit</th>
				<?php
			}
			
			if($ShowDeleteOption && $ShowViewModifyOptions)
			{
				?>
				<th align='center' width=8>Delete</th>
				<?php
			}
			
			$countth=count($this->th);
			
			for($k=0;$k<$countth;$k++)
			{	
				?>
				<th align="center"><?php echo $this->th[$k]; ?></th>
				<?php
			}
			?>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach($rsas as $key => $value)
			{		
				?>		
				<tr height='25' bgcolor='#BDD8F4'>
				<?php
				$i = 0;
				
				foreach($value as $k => $v)
				{
					if($i == 0)
					{
						if($ShowPrintOption)
						{
							?>
							<td align='center' valign='top' width=8><a id='print-<?php echo $v; ?>' onClick='<?php echo $this->print; ?>(this.id);'><img src='images/print.png' border='0' alt='view' style='cursor:pointer;' width="25" height="20"/></a></td>
							<?php
						}
						if($ShowViewOption)
						{
							?>
							<td align='center' valign='top' width=8><a id='view-<?php echo $v; ?>' onClick='<?php echo $this->view; ?>(this.id);'><img src='images/view.jpg' border='0' alt='view' style='cursor:pointer;' width="18" height="15"/></a></td>
							<?php
						}
						if($ShowEditOption && $ShowViewModifyOptions)
						{
							?>
							<td align='center' valign='top' width=8><a id='edit-<?php echo $v; ?>' onClick='<?php echo $this->edit; ?>(this.id);'><img src='images/edit.gif' border='0' alt='Edit' style='cursor:pointer;' width="18" height="15"/></a></td>
							<?php
						}
						
						if($ShowDeleteOption && $ShowViewModifyOptions)
						{
							
								?>
								<td align='center' valign='top' width=8><a id='delete-<?php echo $v; ?>' onClick='<?php echo $this->edit; ?>(this.id);'><img src='images/del.gif' border='0' alt='Delete' style='cursor:pointer;'/></a></td>
								<?php
							//if($this->chk_delete_perm_sadmin()==1)
							//{
								?>
								<!--<td align='center' valign='top'><a id='delete-<?php //echo $v; ?>' onClick='<?php //echo $this->edit; ?>"(this.id);'><img src='images/edit.gif' border='0' alt='Delete' style='cursor:pointer;'/></a></td>-->
								<?php
							//}
							//else
							//{
								?>
									<!--<td align='center' valign='top'><a href='del_control_admin.php?prm' target='_blank' style='text-decoration:none'><font color=#FF0000 style='font-size:10px;'><b>Not Allowed</b></font></a></td>-->
								<?php
							//}
						}
                        
						$i++;
					}
					else
					{
						if(substr($v,0,9)=="../upload")
						{
							?>
								<td valign='top'><img name="<?php echo $i; ?>" src="<?php echo stripslashes($v); ?>" width='90' height='70'></td>
							<?php
						}
						else
						{
							if(strlen($v)>100)
							{
								if($v<>"")
								{
									?>
										<td valign='top' width='500px'><?php echo $v; ?></td>
									<?php
								}
								else
								{
									?>
										<td valign='top'>&nbsp;</td>
									<?php
								}
							}
							else
							{
								if($v<>"")
								{	
									?>
										<td valign='top' align='center'><?php echo stripslashes($v); ?></td>	
									<?php
								}
								else
								{
									?>
										<td valign='top'>&nbsp;</td>
									<?php
								}
							}
						}
						$i++;
					}
				}
				?>
					</tr>
				<?php
			}
			?>
			</tbody>
			<?php
		}		
		?>
			</table>
		<?php
		//return $str;
	}
	 
	function pagination($countq,$link,$sql,$l,$nm,$extra)	 
	{
		
	///////////////////////////////////////////  Original  /////////////////////////////////////////////////	
	$sel_query=$countq;
	//$res=$this->m_dbConn->select($sel_query);
	$res=$this->m_dbConn->select($sel_query);
	
	if($res<>"")
	{
		$k = 0;
		foreach($res as $kk => $vv)
		{
		$k++;
		}
	}
	
		//// My recent add code
		
		/*
		if($k==1)
		{
			$total_pages = 1;
		}
		else
		{
		*/
		
		//// My recent add code
		
		$pos = strrpos($sql,"group by");
		
		if($k>=2)
		{
			$total_pages = $k;
		}
		else
		{
			if($k==1 && $pos==true)
			{
				//echo $pos;
				$total_pages = 1;
			}
			else
			{
				if(isset($_GET['grp']))
				{
					$total_pages = $res[0]['cnt'];
				}
				else
				{
					$total_pages = $res[0]['cnt'];
				}
			}
		}
		//echo $total_pages;
		//	} // My recent add code

	$adjacents = 3;
	$targetpage = $link; 
	
	$limit = $l; 
	$page=0;							
	$page = $nm;
	if($page) 
		$start = ($page - 1) * $limit; 		
	else
		$start = 0;								
	

	if ($page == 0) $page = 1;					
	$prev = $page - 1;							
	$Next = $page + 1;							
	$lastpage = ceil($total_pages/$limit);		
	$lpm1 = $lastpage - 1;
	
	
	if(is_null($nm))
			{
				$pgnum=1;
			}
			else
			{
				$pgnum=$nm;
			}
		if(is_null($l) || $l==0)
			{
				$pgrows=5;
			}
			else
			{
				$pgrows=$l;
			}
	
	
	$max=' limit '.($pgnum-1)*$pgrows.','.$pgrows;
		$sel_query=$sql." $max";
		//echo $sel_query;
		//$res=$this->m_dbConn->select($sel_query);
		$res=$this->m_dbConn->select($sel_query);
	
	
	
	$pagination = "";
	if($lastpage > 1)
	{	
		$pagination .= "<div class=\"pagination\">";
		//Previous button
		if ($page > 1) 
			$pagination.= "<a href=\"$targetpage?page=$prev".$extra."\"> Previous</a>";
		else
			$pagination.= "<span class=\"disabled\"> Previous</span>";	
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<span class=\"current\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage?page=$counter".$extra."\">$counter</a>";					
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter".$extra."\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage?page=$lpm1".$extra."\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage?page=$lastpage".$extra."\">$lastpage</a>";		
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage?page=1".$extra."\">1</a>";
				$pagination.= "<a href=\"$targetpage?page=2".$extra."\">2</a>";
				$pagination.= "...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter".$extra."\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage?page=$lpm1".$extra."\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage?page=$lastpage".$extra."\">$lastpage</a>";		
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage?page=1".$extra."\">1</a>";
				$pagination.= "<a href=\"$targetpage?page=2".$extra."\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?page=$counter".$extra."\">$counter</a>";					
				}
			}
		}
		
		//Next button
		if ($page < $counter - 1) 
			$pagination.= "<a href=\"$targetpage?page=$Next".$extra."\">Next </a>";
		else
			$pagination.= "<span class=\"disabled\">Next </span>";
		$pagination.= "</div>\n";		
	}
	echo $pagination;
	return $res;
	
	}
	
	public function chk_delete_perm_sadmin()
	{
		$sql = "select * from del_control_sadmin where status='Y'";
		//$res = $this->m_dbConn->select($sql);
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_sadmin'];
	}
	public function chk_delete_perm_emp()
	{
		$sql = "select * from del_control_emp where status='Y'";
		//$res = $this->m_dbConn->select($sql);
		$res = $this->m_dbConn->select($sql);
		return $res[0]['del_control_emp'];
	}
	
	public function curdate()
	{
		$sql = "select curdate()as curdate";
		//$res = $this->m_dbConn->select($sql);	
		$res = $this->m_dbConn->select($sql);	

		return $res[0]['curdate'];
	}
	public function curdate_show()
	{
		$sql = "select curdate()as curdate";
		//$res = $this->m_dbConn->select($sql);	
		$res = $this->m_dbConn->select($sql);	

		$date 	  = explode('-',$res[0]['curdate']);
		$date_new = $date[2].'-'.$date[1].'-'.$date[0];
		
		return $date_new;
	}
	public function ip_location($ip)
	{
		if($_SERVER['HTTP_HOST']<>"localhost")
		{
			$location = file_get_contents("http://attuit.in/ip_location/ip_location.php");
			return $location;
		}
		else
		{
			return 'Local-Local-Local';
		}
	}
	
	public function curdate_time()
	{
		if($_SERVER['HTTP_HOST']=='localhost')
		{
			$time_india 	= mktime(date('H')+5,date('i')+30,date('s'));
			$curdate_time	= date('Y-m-d h:i A',$time_india);
			return $curdate_time;
		}
		else
		{
			$s  = "SELECT DATE_ADD((SELECT DATE_ADD((SELECT FROM_UNIXTIME( UNIX_TIMESTAMP() , '%Y-%m-%d %h:%i:%s %p' )),INTERVAL 5 HOUR)),INTERVAL 30 MINUTE) AS date_time_without_ampm";
			//$r  = $this->m_dbConn->select($s);
			$r  = $this->m_dbConn->select($s);
			
			$date_time_without_ampm = $r[0]['date_time_without_ampm'];
			
			$s1  = "SELECT DATE_FORMAT(STR_TO_DATE('".$date_time_without_ampm."', '%Y-%m-%d %H:%i:%s'), '%Y-%m-%d %h:%i %p')as date_time";
			//$r1  = $this->m_dbConn->select($s1);
			$r1  = $this->m_dbConn->select($s1);
		
			$curdate_time = $r1[0]['date_time'];
			
			return $curdate_time;
		}	
	}
}
?>
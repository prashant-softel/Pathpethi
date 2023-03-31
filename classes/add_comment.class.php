<?php
include_once("include/display_table.class.php");
include_once("dbconst.class.php");

class add_comment 
{
	public $actionPage = "../add_comment.php";
	public $m_dbConn;
	public $m_dbConnRoot;
	
	function __construct($dbConn, $dbConnRoot)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg=new display_table($this->m_dbConn);
		$this->m_dbConnRoot = $dbConnRoot;
	}

	public function startProcess()
	{
		$errorExists = 0;

		if($_REQUEST['submit']=='Submit' && $errorExists==0)
		{
			if(isset($_POST['comment']) && isset($_POST['id']) && $_POST['comment'] <> ''  && $_POST['id'] <> '')
			{
				if($_POST['hide_name']=='')
				{
					$hide_name = 0;
				}
				else
				{
					$hide_name = 1;
				}
				$sql = "insert into add_comment(`service_prd_reg_id`,`commenter_id`,`hide_name`,`comment`, `name`)values('".$_POST['id']."','".$_SESSION['login_id']."','".$hide_name."','".addslashes(trim($_POST['comment']))."', '".$_SESSION['name']."')";
				$data = $this->m_dbConnRoot->insert($sql);
				return "Insert";		
			}
			else
			{
				return 'Record Not Submitted..';
			}
				
		}
		if($_REQUEST['submit']=='Update' && $errorExists==0)
		{
			$sql="update add_comment set `comment`='".$this->m_dbConn->escapeString(trim(ucwords($_POST['comment'])))."'  where `service_prd_reg_id`=".$_POST['id']." and `commenter_id`=".$_SESSION['login_id']." and  `add_comment_id`='".$_POST['comment_id']."' ";
			//echo $sql;
			$this->m_dbConnRoot->update($sql);			
		}
	}
	

	
	
	public function pgnation()
	{
		//$sql1 = "select * from add_comment as ac,login as l where ac.commenter_id=l.login_id and ac.status='Y' and l.status='Y' and ac.service_prd_reg_id='".$_REQUEST['id']."' order by add_comment_id desc";
		$sql1 = "SELECT * FROM `add_comment` WHERE `status`='Y' and `service_prd_reg_id`='".$_REQUEST['id']."' order by add_comment_id desc";			
		$this->display_pg->edit="getcomment";
		$this->display_pg->mainpg="reg_form_print_new.php";
		$result=$this->m_dbConnRoot->select($sql1);		
		$res=$this->show($result);				
	}	
	
	public function show($res)
	{
		//print_r($res);
		if($res<>"")
		{
			?>
            <table align="center" border="0" style="width:100%;">
            <?php
			foreach($res as $k => $v)
			{
			?>
            <tr><td>
			<center>
            <div id="middle11" align="center">
            <table align="center" border="0" style="width:80%;"><tr><td>            
                <table align="center" border="0" style="width:80%;">
                <tr align="center">
                    <td align="center">
                        <?php 						
                        if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_SUPER_ADMIN)
                        {
                            echo $res[$k]['name'].'&nbsp;&nbsp;'.$res[$k]['timestamp'];
                        }
                        else if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_ADMIN)
                        {
                            if($res[$k]['name']=='Super Admin')
                            {
                                if($res[$k]['hide_name']==0)
                                {
                                    echo $res[$k]['name'].'&nbsp;&nbsp;'.$res[$k]['timestamp'];	
                                }
                                else
                                {
                                    echo "<font color=#FF0000>Name is hidden by commenter</font>";		
                                }
                            }
                            else
                            {
                                echo $res[$k]['name'].'&nbsp;&nbsp;'.$res[$k]['timestamp'];
                            }
                        }
                        else
                        { 
                            if($res[$k]['hide_name']==0)
                            {
                                echo $res[$k]['name'].'&nbsp;&nbsp;'.$res[$k]['timestamp'];
                            }
                            else
                            { 
                                echo "<font color=#FF0000>Name is hidden by commenter</font>";		 
                            }
                        }
                        ?>
                    </td>
                    
                    <td width="30" align="center">
                        <?php
                        if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_SUPER_ADMIN)
                        {
                        ?>
                        <a href="javascript:void(0);" onclick="del_comment(<?php echo $res[$k]['add_comment_id'];?>);"><img src="images/del.gif" /></a>
                        <?php	
                        }
                        else if(isset($_SESSION['role']) && $_SESSION['role']==ROLE_ADMIN)
                        {
                            if($res[$k]['name']<>"Super Admin")
                            {
                        ?>
                        <a href="javascript:void(0);" onclick="del_comment(<?php echo $res[$k]['add_comment_id'];?>);"><img src="images/del.gif" /></a>
                        <?php
                            }
                        }
                        else
                        {
                            if($_SESSION['login_id']==$res[$k]['commenter_id'])
                            {
                            ?>
                            <a href="javascript:void(0);" onclick="del_comment(<?php echo $res[$k]['add_comment_id'];?>);"><img src="images/del.gif" /></a>
                            <?php
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr height="10"><td colspan="2"></td></tr>
                <tr align="center">
                    <td colspan="3">
                        &nbsp; - <?php echo $res[$k]['comment'];?>
                    </td>
                </tr>
                </table>                       
            </td></tr></table>
            </div>
            </center>
            </td></tr>
            <?php
			}
			?>
            </table>
            <?php
		}
		else
		{
			?>
            <center>
            <table align="center" border="0">
            <tr>
            	<td><font color="#FF0000" size="2"><b>No Records Found.</b></font></td>
            </tr>
            </table>
            </center>
            <?php		
		}
	}
	
	
	
	public function selecting()
	{
		$sql = "select `add_comment_id`,`comment`,commenter_id,logintbl.member_id from `add_comment` as commenttbl JOIN `login` as logintbl on commenttbl.commenter_id=".$_SESSION['login_id']." where add_comment_id=".$_REQUEST['commentId']." and commenttbl.status = 'Y' ";
		$res = $this->m_dbConnRoot->select($sql);
		return $res;
	}
	public function deleting()
	{
		/*$sql = "update period set status='N' where id='".$_REQUEST['bill_periodId']."'";
		$res = $this->m_dbConn->update($sql);*/
	}
	
	
	public function del_comment()
	{
		if(isset($_REQUEST['del_comment']))
		{
			//$sql = "update add_comment set status='N' where add_comment_id='".$_REQUEST['str']."'";
			//$res = $this->update($sql);
			
			$sql = "delete from add_comment where add_comment_id='".$_REQUEST['str']."'";			
			$res = $this->m_dbConnRoot->delete($sql);
		}
	}
}
?>
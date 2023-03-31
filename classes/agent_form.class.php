<?php
error_reporting(1);
include_once("dbconst.class.php");
include_once("include/display_table.class.php");
include_once("utility.class.php");

class agent_form
{
	public $actionPage = "../agentform.php";
    public $obj_utility;
	function __construct($dbConn)
	{
		$this->m_dbConn = $dbConn;
		$this->display_pg = new display_table($this->m_dbConn);
        $this->obj_utility = new utility($this->m_dbConn);
	}
    public function startProcess()
	{
	//echo "h";
		$errorExists = 0;
        if($_REQUEST['insert']=='submit' && $errorExists==0)
		{														
			if($_POST['accounttype']<>'' && $_POST['agentname']<>'')
			{             
                $insert_query="insert into pp_agent(`agent_name`,`account_type`,`subgl_code`,`age`,`qualification`,`address`,`city`,`state`,`pin_no`,`area`,`tel_no`,`mobile_no`,`commission`,`tds`,`joining_date`,`ref_by`,`sar_charge`,`edu_ses`,`pan_card`,`saving_account_no`) values('".$_POST['agentname']."','".$_POST['accounttype']."','".$_POST['subglcode']."','".$_POST['age']."','".$_POST['qualification']."','".$_POST['resi_add']."','".$_POST['city']."','".$_POST['state']."','".$_POST['pinno']."','".$_POST['area']."','".$_POST['telno']."','".$_POST['mob']."','".$_POST['comm']."','".$_POST['tds']."','".getDBFormatDate($_POST['datepicker'])."','".$_POST['refferedby']."','".$_POST['sarcharges']."','".$_POST['edu']."','".$_POST['pancard']."','".$_POST['savingacno']."')";
                $this->m_dbConn->insert($insert_query);    
                return "Insert";            								
            }								
        }
        else if($_REQUEST['insert']=='Update' && $errorExists==0)
            {
                $up_query="update pp_agent set `agent_name`='".$_POST['agentname']."',`account_type`='".$_POST['accounttype']."',`subgl_code`='".$_POST['subglcode']."',`age`='".$_POST['age']."',`qualification`='".$_POST['qualification']."',`address`='".$_POST['resi_add']."',`city`='".$_POST['city']."',`state`='".$_POST['state']."',`pin_no`='".$_POST['pinno']."',`area`='".$_POST['area']."',`tel_no`='".$_POST['telno']."',`mobile_no`='".$_POST['mob']."',`commission`='".$_POST['comm']."',`tds`='".$_POST['tds']."',`joining_date`='".getDBFormatDate($_POST['datepicker'])."',`ref_by`='".$_POST['refferedby']."',`sar_charge`='".$_POST['sarcharges']."',`edu_ses`='".$_POST['edu']."',`pan_card`='".$_POST['pancard']."',`saving_account_no`='".$_POST['savingacno']."' where agent_id='".$_POST['agent_id']."'";                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                $data = $this->m_dbConn->update($up_query);			
                return "Update";
            }             
    }
        public function combobox($query, $id)
        {		
            echo "hi";
            $str.="<option value=''>Please Select</option>";
            $data = $this->m_dbConn->select($query);
            print_r($data);
            if(!is_null($data))
            {
                foreach($data as $key => $value)
                {
                    $i=0;
                    foreach($value as $k => $v)
                    {
                        if($i==0)
                        {
                            if($v==$id)
                            {
                                $sel = "selected";
                            }
                            else
                            {
                                $sel = "";	
                            }
                            $str.="<OPTION VALUE=".$v." ".$sel.">";
                        }
                        else
                        {
                            $str.=$v."</OPTION>";
                        }
                        $i++;
                    }
                }
            }
                return $str;
                
    }
  
    public function getAgentData()
    { 
       
        $sql ="select agent.agent_id,agent.agent_name, cycletable.Description, agent.age, agent.qualification, agent.commission, agent.ref_by from pp_agent as agent,billing_cycle_master as cycletable where cycletable.ID = agent.account_type and agent.status='Y' order by agent_id";
        $res = $this->m_dbConn->select($sql); 
        //print_r($res);
        return $res;	
    }
    public function deleting($agent_id)
	{
		$sql = "update pp_agent set status='N' where agent_id='".$agent_id."'";
        $res = $this->m_dbConn->update($sql);    
        return  "Delete";    
    
	}
    public function selecting($agentId)
	{
	    $sql = "SELECT `agent_id`,`agent_name`,`account_type`,`subgl_code`,`age`,`qualification`,`address`,`city`,`state`,`pin_no`,`area`,`tel_no`,`mobile_no`, `commission`, `tds` , `joining_date` , `ref_by` , `sar_charge` , `edu_ses` , `pan_card` , `saving_account_no`  FROM `pp_agent` WHERE `agent_id` = '".$agentId."'";		
	    $res = $this->m_dbConn->select($sql);	
        //print_r($res);
	    return $res;
	}

    public function GetDetailsReport($agentId)
	{
        $sql ="select agent.agent_id,agent.agent_name, cycletable.Description, agent.age, agent.qualification, agent.commission, agent.address,agent.mobile_no , agent.tds from pp_agent as agent,billing_cycle_master as cycletable where cycletable.ID = agent.account_type and agent.status='Y' and `agent_id` = '".$agentId."'";
	    $res = $this->m_dbConn->select($sql);	
	    return $res;
	} 
    public function GetDetailsDailyReport($agentId)
	{
       $sql ="select agent_id, date , sum(amount) as amount from daily_collection dc where agent_id = '".$agentId."' GROUP BY date desc";
       $res = $this->m_dbConn->select($sql);	
	   return $res;
	}
    
    public function GetDetailsDailyExistingReport($agentId)
	{
       $sql ="select ChequeDate as Date, sum(Amount) as Amount, comments from chequeentrydetails c where PaidBy = '".$agentId."' GROUP BY ChequeDate desc";
       $res = $this->m_dbConn->select($sql);
       $res = $this->obj_utility->reindex($res, 'Date');	
	   return $res;
	}
    
}

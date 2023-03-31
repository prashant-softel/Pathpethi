<?php 

class DailyCollection{

    public $DBConn;

    public function __construct($conn)
    {
        $this->DBConn = $conn;    
    }

    public function getCollectionList($agent_id, $date){

        $query = "SELECT * FROM pp_agent WHERE agent_id = '$agent_id'";
        $result = $this->DBConn->select($query);
        echo json_encode(array('status'=>'success', 'message'=>'Record found', 'Data'=>$result));
    }
}






?>
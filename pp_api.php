<?php 

// POST
// LIST
// PUT

include_once('classes/include/dbop.class.php');
include_once('classes/daily_collection.class.php');

/*

Request URL http://patpedhi.com/API/DailyCollection/CollectionList
Request URL http://patpedhi.com/pp_api.php?module=DailyCollection&method=CollectionList
data {"agent_id":1,"date":"2022-01-30","db_name":1}
*/
ini_set('display_error', E_ALL);
// echo "12";

if(!isset($_REQUEST['module']) || empty($_REQUEST['module']) || !isset($_REQUEST['method']) || empty($_REQUEST['method'])){

    // BAD Request
    // echo "3";
    http_response_code(404);
    print_message('failed', 'module or method is not set in URL. Please check');
    exit();
}

// Daily Collection API URI
if($_REQUEST['module']  == "DailyCollection"){

    if($_REQUEST['method'] == 'CollectionList'){

        if(!isset($_REQUEST['data']) || empty($_REQUEST['data'])){
            http_response_code(301);
            print_message('failed', 'data array is not set');
            exit();
        }
        
        $data = json_decode($_REQUEST['data'], true);

        if(empty($data)){
            http_response_code(301);
            print_message('failed', 'Unable to parse data array');
            exit();
        }

        extract($data);

        if(empty($agent_id) || !preg_match("/[0-9]/", $agent_id)){
            http_response_code(301);
            print_message('failed', 'Agent ID must be set and It should must be numeric.');
            exit();
        }

        if(empty($date) || strtotime($date) == 0){
            http_response_code(301);
            print_message('failed', 'Please check the date value. Date should be valid');
            exit();
        }

        if(empty($db_name) || $db_name == 0){
            http_response_code(301);
            print_message('failed', 'Database is not valid');
            exit();
        }

        $db_name = 'hostmjbt_pp'.$db_name;

        $conn = new dbop(false, $db_name);
        
        $obj_daily_collection = new DailyCollection($conn);   
        $obj_daily_collection->getCollectionList($agent_id, $date);
    }
}


function print_message($message_type,  $message){

    echo  json_encode(array('status'=>$message_type, 'message'=>$message));
}


?>

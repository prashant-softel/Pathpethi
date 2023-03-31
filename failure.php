<?php   
echo "
<html>
<head>
<title>
</title>
<body>
</body>
</html>";


    
    //$dbConn = new dbop();
    //$obj_neft = new neft($dbConn);
    //$obj_ChequeDetails = new ChequeDetails($dbConn);

    try
    {
//      echo "<pre>";
      //print_r($_REQUEST);
  //    echo "</pre>";
      include_once("classes/include/dbop.class.php");
    include_once("classes/dbconst.class.php");
    include_once("classes/ChequeDetails.class.php");
    include_once("classes/include/fetch_data.php");
    include_once("classes/PaymentGateway.class.php");
    //echo "incl";
    $dbConn = new dbop();
    //$obj_neft = new neft($m_dbConn);
    //$obj_ChequeDetails = new ChequeDetails($dbConn);

    $obj_Payment_Gateway = new PaymentGateway($dbConn);
    //echo "obj";
    $status=$_POST["status"];
    $unmapstatus=$_POST["unmappedstatus"];
    $firstname=$_POST["firstname"];
    $amount=$_POST["amount"];
    $txnid=$_POST["txnid"];
    $posted_hash=$_POST["hash"];
    $key=$_POST["key"];
    $productinfo=$_POST["productinfo"];
    $Comments = $productinfo;
    $email=$_POST["email"];
    $payuMoneyId=$_POST["payuMoneyId"];
    $LoginID = $_REQUEST["LID"];
    $PaidBy = $_REQUEST["UID"];//its UnitID
    $PaidTo = $_REQUEST["PT"];
    $BillType = $_REQUEST["BT"];
    $Mode = $_REQUEST["mode"];
    $PaymentID = $_REQUEST["paymentId"];
    $bank_ref_num = $_REQUEST["bank_ref_num"];
    $amount_split = $_REQUEST["amount_split"];

    if(!isset($_REQUEST["mode"]) || $_REQUEST["mode"] == "")
    {
        $Mode = "-";
    }
    $BankCode = $_REQUEST["bankcode"];
    //$salt="GQs7yium";
    //$salt="e5iIg1jwi8";

$salt = $_SESSION["PGSalt"];
    $Date = Date('Y-m-d');
    //echo "LID:".$LoginID." date:".$Date ." txnid: ". $txnid ." amt:". $amount ." paidby:". $PaidBy ." paidto:". $PaidTo." bankcode:". $BankCode ." none:". "-" ."type:". "-2" ."Comments:". $Comments ."BT:". $_REQUEST['BT'];

    If (isset($_POST["additionalCharges"])) 
    {
           $additionalCharges=$_POST["additionalCharges"];
            $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
            
    }
    else 
    {	  

            $retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;

    }
    		 $hash = hash("sha512", $retHashSeq);
      
     if ($hash != $posted_hash) 
     {
         echo "Invalid Transaction. Please try again";
	   }
     else 
     {

       //echo "<h3>Your order status is ". $status .".</h3>";
       //echo "<h4>Your transaction id for this transaction is ".$txnid.". You may try making the payment by clicking the link below.</h4>";
        
         //echo "<b> This is demo transaction.</b>";
         //$obj_ChequeDetails->AddNewValues($Date, $Date, $txnid, $amount, $PaidBy, $_REQUEST['PaidTo'], $BankCode, "-", "-2", $Comments, $_REQUEST['BT'],0,0,0,0,1);
        //echo $LoginID ."|".$PaidBy."|".$PaidTo."|".$Date."|".$amount."|".$BillType."|".$txnid."|".$status."|".$unmapstatus."|".$payuMoneyId."|".$Mode."|".$Comments;
       $sql = "INSERT INTO `paymentgatewaytransactions`(`GatewayID`, `LoginID`, `UnitID`,`PaidTo`, `Date`, `Amount`, `BillType`, `TranxID`, `Status`,`unmappedstatus`, `payuMoneyId`, ,`ref_num`,`split_amt`,`PaymentID`,`Mode`, `Comments`) VALUES (1,'".$LoginID."','".$PaidBy."','".$PaidTo."','".$Date."','".$amount."','".$BillType."','".$txnid."','".$status."','".$unmapstatus."','".$payuMoneyId."','".$bank_ref_num."','".$amount_split."','".$PaymentID."','".$Mode."','".$Comments ."')";
        echo "sql:".$sql;
         $obj_Payment_Gateway->CompletePayment(1,$LoginID,$PaidBy,$PaidTo,$Date,$amount,$BillType,$txnid,$status,$unmapstatus,$payuMoneyId, $bank_ref_num, $amount_split, $PaymentID,$Mode,$Comments);
 
      }  
       
       
$URL = "https://way2society.com/Response.php?TrnxID=".$txnid;
       echo "url:".$URL;
//header("Location: ".$URL);
  }
  catch(Exception $ex)
  {
    echo "exception:".$ex;
  }         
    ?>
    <!--Please enter your website homepagge URL -->
    <p><a href=http://localhost/testing/success_failure/PayUMoney_form.php> Try Again</a></p>

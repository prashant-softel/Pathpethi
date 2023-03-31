<?php
include_once('config.php');
class DBConnection
{
    public function __construct($DB_NAME, $connectRootDB = false)
    {
        try {
            if ($connectRootDB) {
                $conn = new mysqli(DB_HOST_ROOT, DB_USER_ROOT, DB_PASSWORD_ROOT, DB_DATABASE_ROOT);
            } else {
                $DB_NAME = 'hostmjbt_pp'.$DB_NAME;
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, $DB_NAME);
            }
            if ($conn->connect_errno) {

                echo json_encode(array('status' => 'failed', 'message' => 'Unable to connect DB'));
                exit();
            }
        } catch (Exception $e) {

            echo $e->getMessage();
        }
    }
}

<?php if(!isset($_SESSION)){ session_start(); }
include_once("../include/dbop.class.php");
include_once("../include/display_table.class.php");

class genbill extends dbop
{
	public $actionPage="Maintenance_bill_edit.php";
	function __construct()
	{
		$this->display_pg=new display_table();
		dbop::__construct();
	}
	
	public function startProcess()
	{
	}
}
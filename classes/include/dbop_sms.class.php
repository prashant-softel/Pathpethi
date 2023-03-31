<?php
 	//Turn off all error reporting
   	error_reporting(0);
	include('DbConnection.class.php');
	date_default_timezone_set('Asia/Kolkata');	
	class dbop_sms extends DbConnection
	{	
		//public $obj_con;
		private $m_bIsTransaction;
		
		function __construct($bAccessRoot = false , $dbName = "")
		{
			DbConnection::__construct($bAccessRoot , $dbName);
			$this->m_bIsTransaction = false;
			mysqli_autocommit($this->mMysqli, true);
		}	
		
		function begin_transaction()
		{
			$this->m_bIsTransaction = true;
			mysqli_autocommit($this->mMysqli, false);
			mysqli_query($this->mMysqli, 'START TRANSACTION');
		}
		
		function commit()
		{
			$this->m_bIsTransaction = false;
			mysqli_commit($this->mMysqli);
			mysqli_autocommit($this->mMysqli, true);
		}
		
		function rollback()
		{
			$this->m_bIsTransaction = false;
			mysqli_rollback($this->mMysqli);
			mysqli_autocommit($this->mMysqli, true);
		}
		
		function select($sql, $iDim=0)
		{
			if (is_null($sql) || empty($sql))
			{
				echo $this->error("Problem In SQL Query!","Your sql query seems wrong. Please check your SQL query.");
				die();
			}
			/*if(!preg_match("/^select/", $sql))
			{
				echo $this->error("Wron SQL Query!", "Check your SQL Query. Pass only SQL select queries/statements.");
				die();
			}*/
			
			$count = 0;
			$result = $this->mMysqli->query($sql) or die($this->mMysqli->error);//"Problem In Query Execution!", ""));
			if ($iDim == 1) 
			{
				while($row = $result->fetch_array(MYSQL_NUM))
				{
					$data[$row[0]] = $row[1];
				}
			} 
			elseif($iDim == 2) 
			{
				while($row = $result->fetch_array(MYSQL_BOTH))
				{
					$data[$row[0]][$row[1]] = $row;
				}
			} 
			else 
			{
				while($row = $result->fetch_array(MYSQL_ASSOC))
				{
					$data[$count] = $row;
					$count++;
				}
			}
			$result->close();
			
			/*if(!isset($data))
			{
				$data = "";
			}*/
			return $data;
		}
		
		function insert($sql)
		{
			if(is_null($sql) || empty($sql))
			{
				echo $this->error("Problem In SQL Query!","Your sql query seems wrong. Please check your SQL query.");
				die();
			}
			/*if(!preg_match("/^insert/", $sql))
			{
				echo $this->error("Wrong SQL Query!", "Check you SQL Query. Pass only SQL insert queries/statements.");
				die();
			}*/
			
			$result = $this->mMysqli->query($sql) or die($this->mMysqli->error);
			return $this->mMysqli->insert_id;
		}

		function update($sql)
		{
			if(is_null($sql) || empty($sql))
			{
				echo $this->error("Problem In SQL Query!","Your sql query seems wrong. Please check your SQL query.");
				die();
			}
			/*if(!preg_match("/^update/", $sql))
			{
				echo $this->error("Wrong SQL Query!", "Check you SQL Query. Pass only SQL update queries/statements.");
				die();
			}*/
			
			$result = $this->mMysqli->query($sql) or die($this->mMysqli->error);
			return $this->mMysqli->affected_rows;
		}
		
		function delete($sql)
		{
			if(is_null($sql) || empty($sql))
			{
				echo $this->error("Problem In SQL Query!","Your sql query seems wrong. Please check your SQL query.");
				die();
			}
			/*if(!preg_match("/^delete/", $sql))
			{
				echo $this->error("Wrong SQL Query!", "Check you SQL Query. Pass only SQL delete queries/statements.");
				die();
			}*/
						
			$result = $this->mMysqli->query($sql) or die($this->mMysqli->error);
			return $this->mMysqli->affected_rows;
		}
		
		function error($message, $details="")
		{
			$no = mysql_errno();
			$msg = mysql_error();
			$err_msg = "<pre>";
			$err_msg .="<h1>$message</h1>";
			$err_msg .="$details<br><br>";
			$err_msg .="<b>Error Number \t:</b> $no<br>";
			$err_msg .="<b>Error Message     :</b> $msg<br>";
			$err_msg .="<b>File Name :</b> ".__FILE__."<br><br>";
			$err_msg .="<hr></pre>";
			return $err_msg;
		}
		
		function escapeString($message)
		{
			return mysqli_escape_string($this->mMysqli, (trim($message)));
		}
		
		public function resultCount($res)
		{	
			$cnt = mysqli_num_rows($res);	
			return $cnt;
		}
		
		public function ExecStoredProcWithoutWithParameters($proc_name)
		{
			$sqlQuery = 'CALL '.$proc_name;
			 if ($this->mMysqli->multi_query($sqlQuery))
			{
				$result = array();
				do 
				{
						// Lets work with the first result set
						if ($res = $this->mMysqli->use_result())
						{
							// Loop the first result set, reading it into an array
							while ($row = $res->fetch_array(MYSQLI_ASSOC))
							{
								$result[] = $row;
							}
						
							// Close the result set
							$res->close();
						}
				} while ($this->mMysqli->more_results() && $this->mMysqli->next_result());
			 }
			else
			{
				echo '<p>There were problems with your query [' . $sqlQuery. ']:<br /><strong>Error Code ' . $this->mMysqli->errno . ' :: Error Message ' . $this->mMysqli->error . '</strong></p>';
			}
 				
				return $result;
    	}
		
		
	}
?>
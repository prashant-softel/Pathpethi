<?php

class validation
{
	function IsEmptyCombo($fieldvalue)
	{	
		if($fieldvalue == "")
		{
			return "is empty. ";
		}	
		else
		{
			return "";
		}
	}

function IsEmptyRadio($fieldvalue)
{	
	if($fieldvalue == "")
	{
		return "Please Select gender";
	}	
	else
	{
		return "";
	}
}

	
	/* Child Functions*/
	public function CheckIsAlphabetic($fieldvalue)
{ 
		if (preg_match("/^[a-zA-Z  ]+$/", $fieldvalue))
		{
			return true;
		}
		else
		{
			return false;
		}		 
	}	
	public function CheckIsAlphaNumeric($fieldvalue)
	{ 
       	if (preg_match("/^[a-zA-Z0-9]+$/", $fieldvalue))
	  	{
	     	return true;
	   	}
	   	else
		{
			return false;
		}		 
	}
	
	public function CheckIsNumeric($fieldvalue)
	{ 
		if (preg_match("/^[0-9]+$/", $fieldvalue))
		{
			return true;
		}
		else
		{
			return false;
		}		 
	}
	
	function emailValidator($elem)
	{
	   $err = "";
	   if(preg_match("/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/",$elem))
	   {
			return true;
	   }
	   else
	   {
			return false;
	   }
	}
	
	function CheckIsDouble($fieldvalue)
	{ 
		if (!preg_match("/^[0-9]*(\.|[0-9]+)[0-9]*$/", $fieldvalue))
		{
			return false;
		}
		else
		{
			if(preg_match("/^\.$/", $fieldvalue))
			{
				return false;
			}
			else
			{
				 return true;
			}
		}		 
	}
	
	function CheckIsValidEmail($fieldvalue)
	{ 
		if (preg_match("/^[a-zA-Z0-9_.]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+\.{0,1}[a-zA-Z0-9]*$/", $fieldvalue))
		{
			return true;
		}
		else
		{
			return false;
		}		 
	}
	
	
	/*Main Functions*/
	/* function checks for empty and alphabetic(no spaces) */
	function Double($fieldvalue)
	{
		if(!empty($fieldvalue))
		{
			if($this->CheckIsDouble($fieldvalue))
			{
				return "";
			}
			else
			{
				return "is not valid Double Value. ";
			}
		}
		else
		{
			return "is empty. ";
		}	
	}
	
	function Email($fieldvalue)
	{
		if(!empty($fieldvalue))
		{
			if($this->CheckIsValidEmail($fieldvalue))
			{
				return "";
			}
			else
			{
				return "is not valid email id. ";
			}
		}
		else
		{
			return "is empty. ";
		}	
	}
	
	
	public function Alphabetic($fieldvalue)
	{
		if(!empty($fieldvalue))
		{
			if($this->CheckIsAlphabetic($fieldvalue))
			{
				return "";
			}
			else
			{
				return "is not alphabetic. ";
			}
		}
		else
		{
			return "is empty. ";
		}	
	}
	
	public function Numeric($fieldvalue)
	{	
		if(!empty($fieldvalue))
		{
			if($this->CheckIsNumeric($fieldvalue))
			{
				return "";
			}
			else
			{
				return "is not Numeric. ";
			}
		}
		else
		{
			return "is empty. ";
		}	
	}
	
	public function AlphaNumeric($fieldvalue)
	{
		if(!empty($fieldvalue))
		{
			if($this->CheckIsAlphaNumeric($fieldvalue))
			{
				return "";
			}
			else
			{
				return "is not AlphaNumeric. ";
			}
		}
		else
		{
			return "is empty. ";
		}	
	}

}

?>
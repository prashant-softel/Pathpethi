<?php 
	include_once("dbop.class.php");
	include_once("../dbconst.class.php");
	$m_dbConnRoot = new dbop(true);
	$m_dbConn = new dbop();
	
	$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
	$host     = $_SERVER['HTTP_HOST'];
	$script   = $_SERVER['SCRIPT_NAME'];
	$params   = $_SERVER['QUERY_STRING'];
	$referer  =  $_SERVER['HTTP_REFERER'];
	
	$currentUrl = $protocol . '://' . $host . $script . '?' . $params;
	//echo $currentUrl;
	$currentUrl = str_replace('&', '_**_', $currentUrl);
	
	$pos = strrpos($script, '/');
	$scriptName = substr($script, ($pos + 1));
	
	setTemplateViewAccordingUrl($scriptName);
	if(checkSession() == false)
	{
		header('Location: logout.php?url='.$currentUrl);
	}
	else if(hasPageAcess($_SESSION['role'], $scriptName) == 0)
	{
		redirectUser($scriptName);
	}
	else if(hasModuleAccess($scriptName) == 0)
	{
		redirectUser($scriptName);
	}
	else
	{
		if(isSupported($scriptName) == 0)
		{
			redirectUser($scriptName);
		}
	}
			
	function checkSession()
	{	
		//if(isset($_SESSION['login_id']) && isset($_SESSION['society_id']) 
			//			&& isset($_SESSION['role']) && isset($_SESSION['dbname']))
		if(isset($_SESSION['login_id']))
		{
			//Valid Session
			return true;
		}
		else
		{
			//Invalid session. Redirect to login page.
			return false;
		}
	}
	
	function redirectUser($scriptName)
	{
		if($scriptName <> 'initialize.php')
		{
			echo '<script>alert("Request Denied !!! \nYou are not authorized to access this page......");</script>';
			header( "refresh:0;url=initialize.php?imp" );
			die();
		}
	}
	
	function isSupported($profile)
	{
		$bSupported = 1;
		$aryProfiles = $_SESSION['profile'];

		if($aryProfiles <> '')
		{
			if(array_key_exists($profile, $aryProfiles))
			{
				$bValue = $aryProfiles[$profile];
				if($bValue == 0)
				{
					$bSupported = 0;
				}
			}
		}
		
		//echo 'Profile Supported : ' . $bSupported;
		return $bSupported;
		//return true;
	}
	
	function hasPageAcess($role, $page)
	{
		$bHasAccess = 1;
		
		//Cominations of various roles.
		$ary_All = array('Super Admin', 'Admin', 'Admin Member', 'Member');
		$ary_SuperAdmin = array('Super Admin');
		$ary_Admin = array('Admin');
		$ary_Member = array('Member');
		$ary_Admin_Member = array('Admin Member');
		$ary_AdminMember_Member = array('Admin Member', 'Member');
		$ary_MasterAdmin = array('Master Admin');
		$ary_Master_SuperAdmin = array('Master Admin','Super Admin');
		
		$ary_SuperAdmin_Admin = array('Super Admin', 'Admin');
		$ary_SuperAdmin_Admin_AdminMember = array('Super Admin', 'Admin', 'Admin Member');
		
		$arrayPages = array('initialize.php' => $ary_All,
							'home_s.php' => $ary_SuperAdmin_Admin_AdminMember,
							'Dashboard.php' => $ary_All,
							'genbill.php' => $ary_SuperAdmin_Admin_AdminMember,
							'BankAccountDetails.php' => $ary_SuperAdmin_Admin_AdminMember,
							'ChequeDetails.php' => $ary_SuperAdmin_Admin_AdminMember, 
							'PaymentDetails.php'  => $ary_SuperAdmin_Admin_AdminMember,
							'updateInterest.php' => $ary_SuperAdmin_Admin_AdminMember,
							'reverse_charges.php' => $ary_SuperAdmin_Admin_AdminMember,
							'settings.php' => $ary_SuperAdmin_Admin,
							'society.php' => $ary_SuperAdmin_Admin_AdminMember,
							'wing.php' => $ary_SuperAdmin_Admin,
							'unit_search.php' => $ary_SuperAdmin_Admin_AdminMember,
							'unit.php' => $ary_SuperAdmin_Admin,
							'list_member.php' => $ary_SuperAdmin_Admin_AdminMember,
							'reportmain.php' => $ary_SuperAdmin_Admin_AdminMember,
							'module.php' => $ary_SuperAdmin_Admin,
							'notification.php' => $ary_SuperAdmin_Admin_AdminMember,
							'neft.php' => $ary_AdminMember_Member,
							'client.php' => $ary_MasterAdmin,
							'client_details.php' => $ary_Master_SuperAdmin);
		
		$aryRole = $arrayPages[$page];

		if(sizeof($aryRole) > 0 && !in_array($role, $aryRole))
		{
			$bHasAccess = 0;
		}
		
		//echo 'Has Access : ' . $bHasAccess;
		return $bHasAccess;
		//return 1;
	}
	
	function hasModuleAccess($module)
	{
		$bSupported = 1;
		$aryModules = $_SESSION['module'];
		$aryModulePage = array('servicerequest.php' => 'service_request',
								'notices.php' => 'notice',
								'events_view.php' => 'event',
								'Document_view.php' => 'document',
								'service_prd_reg_view.php' => 'service',
								'service_prd_reg_search.php' => 'service',
								'Ads.php' => 'classified',
								'Forum.php' => 'forum',
								'Directory.php' => 'directory');
		
		for($iModuleCnt = 0; $iModuleCnt < sizeof($aryModules); $iModuleCnt++)
		{
			if(array_key_exists($module, $aryModulePage) && $aryModules[$aryModulePage[$module]] == '0')
			{
				$bSupported = 0;
				break;
			}
		}
		//echo 'Module Access for ' . $module . ' : ' . $bSupported;
		return $bSupported;
	}
	
	function  bIsReportOrValidationPage($scriptName)
	{
		$aryReportOrValidationPage = array('BankEntriesValidation.php','RegistersValidation.php');
		if (in_array($scriptName, $aryReportOrValidationPage)) 
		{
			return true;
		}
		else
		{
				return false;
		}
		
		
	}
	
	function  IsReadonlyPage()
	{
		if($_SESSION['is_year_freeze'] == 1 && ($_SESSION['role'] == ROLE_ADMIN_MEMBER || $_SESSION['role'] == ROLE_ADMIN))
		{
			return true;	
		}
		else
		{
			return false;	
		}	
		
	}
	
	
	function setTemplateViewAccordingUrl($scriptName)
	{
		$scriptName2 = pathinfo($scriptName);
		$urlFileName = $scriptName2['filename'];
		
		$tabList = array( 
            "soctabList" => array (	"addnotice","addservicerequest","document","Documents","Document_view"
												,"events","Events_m","events_view","events_view_as","events_view_as_self"
												,"events_view_details","Forum","Gallery","gallery_group","gallery_upload"
												,"servicerequest","service_prd_reg","service_prd_reg_edit","service_prd_reg_search"
												,"service_prd_reg_view","service_prd_reg_view_other","notices","notices_m"
												,"Dashboard","view_member_profile","ViewNotice","viewrequest","neft"
											),
            "acctabList" => array ( "accounting_report","account_category","add_sharecertificate","AssetSummary","asset_report","BalanceSheet","BankAccountDetails","BankDetails","BankEntriesValidation"
												,"BankReco","BankRecoReport","bank_reconciliation","bank_statement","bg","billmaster","bill_period","bill_year","cash_flow_details","cash_flow_report","cat"
												,"ChequeDetails","chequeleafbook","ContributionLedger","ContributionLedgerDetailed","createvoucher","defaults","depositgroup","dues_advance_frm_member_report"
												,"ExpenseDetails","expense_register","expense_register_report","expense_report","FixedDeposit","FixedDepositReport","genbill","generalSMSHistory","GenerateDepositSlip"
												,"GeneratePaymentReport","import_payments_receipts","IncomeDetails","IncomeStmt","ledger","LedgerValidate","ledger_details","ledger_print","LiabilitySummary"
												,"income_details","income_register","unit","unit_search","unit_sorting","unit_tariff_details","updateInterest","TrialBalance","common_period","list_member"
												,"MaintenanceBill_m","society","Maintenance_bill_edit","PaymentDetails","payments_m","import","view_member_profile_adm","view_member_profile_adm_edit"
												,"view_member_profile_mem_edit","VoucherEdit","wing","OpeningBalance","reportmain","multiple_ledger_print","NeftDetails","notification","reverse_charges","sendGeneralMsgs","view_ledger_details"
											)
         );
		 
		while (strtolower(current($tabList))) 
		 {
			if(in_array(strtolower($urlFileName),strtolower(current($tabList))))
			{
				$key =  key($tabList);
				if($key ==  "soctabList")
				{
					$_SESSION["View"] = "MEMBER";	
				}
				else
				{
					$_SESSION["View"] = "ADMIN";	
				}
				break;
			}
			else
			{
			   next($tabList);
			}
		}
		
	}
	
	function hasFeatureAccess($sfeatureName ="")
	{
		if(isset($_SESSION['society_client_id']) &&  $_SESSION['society_client_id'] <> 0 && $_SESSION['society_client_id'] <> 1)
		{
			return true;
		}	
		else
		{
			return false;	
		}
	}
	

?>
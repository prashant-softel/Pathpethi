<?php if(!isset($_SESSION)){ session_start(); } ?>
<?php 
	
	if($_SESSION['default_interest_on_principle'] == 0 || $_SESSION['default_current_asset'] == 0 || $_SESSION['default_bank_account'] == 0 ||
			$_SESSION['default_cash_account'] == 0 || $_SESSION['default_due_from_member'] == 0 || $_SESSION['society_id'] == 0 || $_SESSION['default_penalty_to_member'] == 0 || $_SESSION['default_tds_payable'] == 0 || $_SESSION['default_year'] == 0)
			{
				?>
                	<script>
						alert('Please set Default values for Ledger and Account Category.');
						window.location.href = 'defaults.php?alog';
                    </script>   
                <?php
			}
?>

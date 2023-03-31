<?php include_once("includes/head_s.php");
?>
<?php
include_once("classes/list_member.class.php");
include_once('classes/utility.class.php');

$obj_list_member = new list_member($m_dbConn);
$obj_utility = new utility($m_dbConn);

?>
<link rel="stylesheet" type="text/css" href="css/pagination.css">
<link href="css/messagebox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/jsViewLedgerDetails.js"></script>
<script type="text/javascript" src="js/ajax_new.js"></script>
<script language="javascript" type="application/javascript">
	function go_error() {
		setTimeout('hide_error()', 10000);
	}

	function hide_error() {
		document.getElementById('error_del').style.display = 'none';
	}

	function del_member(member_id) {
		var conf = confirm("Are you sure you want to delete it ?");
		if (conf == true) {
			document.getElementById('error_del').style.display = '';
			document.getElementById('error_del').innerHTML = 'Wait...';
			remoteCall("ajax/del_member.php", "member_id=" + member_id, "res_del_member");
		}
	}

	function res_del_member() {
		var res = sResponse; //alert(res);
		window.location.href = 'list_member.php?scm&tm=<?php echo time(); ?>&del';
	}

	
	$(document).ready(function() {
		$('#example').dataTable({
			"bDestroy": true
		}).fnDestroy();
		if (localStorage.getItem("client_id") != "" && localStorage.getItem("client_id") != 1) {
			//alert("hey");
			$('#example').dataTable(

				{
					//alert("Hello");
					"stateSave": true,
					"stateDuration": 0,
					dom: 'T<"clear">Blfrtip',
					"aLengthMenu": [
						[10, 25, 50, 100, -1],
						[10, 25, 50, 100, "All data"]
					],
					buttons: [{
							extend: 'colvis',
							width: 'inherit'
							/*,
															collectionLayout: 'fixed three-column'*/
						}

					],
					"oTableTools": {
						"aButtons": [{
								"sExtends": "copy",
								"mColumns": "visible"
							},
							{
								"sExtends": "csv",
								"mColumns": "visible"
							},
							{
								"sExtends": "xls",
								"mColumns": "visible"
							},
							{
								"sExtends": "pdf",
								"mColumns": "visible"
							},
							{
								"sExtends": "print",
								"mColumns": "visible",
								"sMessage": printMessage + " "
							}
						],
						"sRowSelect": "multi"
					},
					aaSorting: [],

					fnInitComplete: function(oSettings, json) {
						//var otb = $(".DTTT_container")
						//alert("fnInitComplete");
						$(".DTTT_container").append($(".dt-button"));

						//get sum of amount in column at footer by class name sum
						this.api().columns('.sum').every(function() {
							var column = this;
							var total = 0;
							var sum = column
								.data()
								.reduce(function(a, b) {
									if (a.length == 0) {
										a = '0.00';
									}
									if (b.length == 0) {
										b = '0.00';
									}
									var val1 = parseFloat(String(a).replace(/,/g, '')).toFixed(2);
									var val2 = parseFloat(String(b).replace(/,/g, '')).toFixed(2);
									total = parseFloat(parseFloat(val1) + parseFloat(val2));
									return total;
								});
							$(column.footer()).html(format(sum, 2));
						});

					}

				});
			//alert("End If");
		} else {
			$('#example').dataTable({
				/*dom: 'T<"clear">lfrtip',*/
				"stateSave": true,
				"stateDuration": 0,
				dom: 'T<"clear">Blfrtip',
				"aLengthMenu": [
					[10, 25, 50, 100, -1],
					[10, 25, 50, 100, "All data"]
				],
				buttons: [{
					extend: 'colvis',
					width: 'inherit'
					/*,
													collectionLayout: 'fixed three-column'*/
				}],
				
				"oTableTools": {
					"aButtons": [{
							"sExtends": "copy",
							"mColumns": "visible"
						},
						{
							"sExtends": "csv",
							"mColumns": "visible"
						},
						{
							"sExtends": "xls",
							"mColumns": "visible"
						},
						{
							"sExtends": "pdf",
							"mColumns": "visible"
						},
						{
							"sExtends": "print",
							"mColumns": "visible",
							"sMessage": printMessage + " "
						}
					],
					"sRowSelect": "multi"
				},
				aaSorting: [],

				fnInitComplete: function(oSettings, json) {
					//var otb = $(".DTTT_container")
					//alert("fnInitComplete");
					$(".DTTT_container").append($(".dt-button"));

					//get sum of amount in column at footer by class name sum
					this.api().columns('.sum').every(function() {
						var column = this;
						var total = 0;
						var sum = column
							.data()
							.reduce(function(a, b) {
								if (a.length == 0) {
									a = '0.00';
								}
								if (b.length == 0) {
									b = '0.00';
								}
								var val1 = parseFloat(String(a).replace(/,/g, '')).toFixed(2);
								var val2 = parseFloat(String(b).replace(/,/g, '')).toFixed(2);
								total = parseFloat(parseFloat(val1) + parseFloat(val2));
								return total;
							});
						$(column.footer()).html(format(sum, 2));
					});

				}

			});
			//alert("End");
		}
		//alert("End of function");
	});
</script>

<?php if (isset($_REQUEST['del'])) { ?>

	<body onLoad="go_error();">
	<?php } else { ?>

		<body>
		<?php } ?>
		<br>
		<center>
			<div class="panel panel-info" id="panel" style="display:block">
				<div class="panel-heading" id="pageheader">List of Members</div>
				<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'Super Admin') { ?>

				<?php } ?>

				<center>
					<br>
					<!--<a href="unit.php?imp&ssid=<?php echo $_SESSION['society_id']; ?>&idd=<?php echo time(); ?>"><input type="button" value="Add Unit"></a>-->
					<button type="button" class="btn btn-primary" onClick="window.location.href='unit.php?imp&ssid=<?php echo $_SESSION['society_id']; ?>&idd=<?php echo time(); ?>'" style="margin-right:5%">Add New Member</button>

					<table align="center" border="0" style="width:100%">
						<tr>
							<td valign="top" align="center">
								<font color="red"><?php if (isset($_GET['del'])) {
														echo "<b id=error_del>Record deleted Successfully</b>";
													} else {
														echo '<b id=error_del></b>';
													} ?></font>
							</td>
						</tr>
						<tr>
							<td>
								<?php
								$member_list = $obj_list_member->getAllMemberDetails();
								$member_category_Arr = $obj_utility->getMemberCategory();
								
								?>
								<table id="example" class="display" cellspacing="0" style="width:100%">
									<thead>
										<tr>
											<th>Sr. No.</th>
											<th>Member Category</th>
											<th>Name</th>
											<th>Occupation</th>
											<th>Aadhar No.</th>
											<th>Pan No.</th>
											<th>Mobile</th>
											<th>Email Id</th>
											<th>Created At</th>											
										</tr>
									</thead>
									<tbody>
										<?php 
										$cnt = 1;
										// echo "<pre>";
										// print_r($member_list);
										// echo "</pre>";
										foreach ($member_list as $memberDetails) {
											extract($memberDetails);
											?>
											<tr>
												<td><?=$cnt++?></td>
												<td><?=$member_category_Arr[$member_category]['member_category_name']?></td>
												<td><a href="member_profile.php?member_id=<?=$member_id?>"><?=$owner_name?></a></td>
												<td><?=$member_occupation?></td>
												<td><?=$member_aadhar_number?></td>
												<td><?=$member_pan_number?></td>
												<td><?=$mob?></td>
												<td><?=$email?></td>
												<td><?=$member_created_at?></td>
											</tr>
											<?php } ?>
									</tbody>
								</table>
							</td>
						</tr>
					</table>




				</center>
			</div>
		</center>
		<?php include_once "includes/foot.php"; ?>
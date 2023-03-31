	<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="media/js/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="datatools/js/dataTables.tableTools.js"></script>
	<script type="text/javascript" language="javascript" src="resources/syntax/shCore.js"></script>
	<script type="text/javascript" language="javascript" src="resources/demo.js"></script>
    <script type="text/javascript" src="datatools/js/dataTables.buttons.min.js"></script> 
    <script type="text/javascript" src="datatools/js/buttons.colVis.min.js"></script> 
     <script type="text/javascript" src="js/shortcut.js"></script> 
      <script type="text/javascript" src="js/shortcutkeys.js"></script> 
	
	<link rel="stylesheet" type="text/css" href="media/css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="datatools/css/dataTables.tableTools.css">
	<link rel="stylesheet" type="text/css" href="resources/syntax/shCore.css">
    
	<!--DatePicker-->
	<script language="javascript" src="jquery/jquery-ui.min.js"></script>
	<link rel="stylesheet" type="text/css" href="jquery/jquery-ui.css" >
    <link href="datatools/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="datatools/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
	<!--DatePicker-->

	<style type="text/css" class="init">
		.loader 
		{
			position: fixed;
			left: 0px;
			top: 0px;
			width: 100%;
			height: 100%;
			z-index: 9999;
			opacity:0.8;
			background: url('images/loader/page-loader.gif') 50% 50% no-repeat rgb(114,118,122);
		}
	</style>
	
	<script type="text/javascript" language="javascript" class="init">
	
	var minGlobalCurrentYearStartDate = localStorage.getItem("minGlobalCurrentYearStartDate");
	var maxGlobalCurrentYearEndDate = localStorage.getItem("maxGlobalCurrentYearEndDate");
		
		$(document).ready(function() {
			if(localStorage.getItem("client_id") != "" && localStorage.getItem("client_id") != 1)
			{
					$('#example').DataTable( {
					dom: 'T<"clear">Blfrtip',
					"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
					buttons: 
					[
						{
							extend: 'colvis',
							width:'inherit'/*,
							collectionLayout: 'fixed three-column'*/
						}
					],
					"oTableTools": 
					{
						"aButtons": 
						[
							{ "sExtends": "copy", "mColumns": "visible" },
							{ "sExtends": "csv", "mColumns": "visible" },
							{ "sExtends": "xls", "mColumns": "visible" },
							{ "sExtends": "pdf", "mColumns": "visible" },
							{ "sExtends": "print", "mColumns": "visible" }
						],
					 "sRowSelect": "multi"
				},
				aaSorting : [],
					
				fnInitComplete: function ( oSettings ) {
					//var otb = $(".DTTT_container")
					$(".DTTT_container").append($(".dt-button"));
				}
				
			} );	
		}
		else
		{	
			
				$('#example').DataTable( {
				/*dom: 'T<"clear">lfrtip',*/
				dom: 'T<"clear">Blfrtip',
				"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
				buttons: 
				[
					{
						extend: 'colvis',
						width:'inherit'/*,
						collectionLayout: 'fixed three-column'*/
					}
				],
				"oTableTools": 
				{
					"aButtons": 
					[
						{ "sExtends": "copy", "mColumns": "visible" },
						{ "sExtends": "csv", "mColumns": "visible" },
						{ "sExtends": "xls", "mColumns": "visible" },
						{ "sExtends": "pdf", "mColumns": "visible" },
						{ "sExtends": "print", "mColumns": "visible" }
					],
				 "sRowSelect": "multi"
			},
			aaSorting : [],
				
			fnInitComplete: function ( oSettings ) {
				//var otb = $(".DTTT_container")
				$(".DTTT_container").append($(".dt-button"));
			}
			
		} );
		}
	} );
	</script>
	
	<script>
		function showLoader()
		{
			//$(".loader").fadeIn("slow");
		}
		
		function hideLoader()
		{
			//$(".loader").fadeOut("slow");
		}
		
		function hideLoaderFast()
		{
			//$(".loader").fadeOut("fast");
		}
		
		//document.write('<div class="loader"></div>');
		hideLoaderFast();
	</script>

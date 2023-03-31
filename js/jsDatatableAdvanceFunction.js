

function datatableRowGrouping(iColumnNo, iColumnCount)
{
	var table = $('#example').dataTable({
	dom: 'T<"clear">lfrtip',
        "columnDefs": [
            { "visible": false, "targets": iColumnNo }
        ],
        "order": [[iColumnNo, 'asc' ]],
		"aLengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
        "displayLength": 25,
        "drawCallback": function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;
 
            api.column(iColumnNo, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td colspan="' + iColumnCount + '">'+group+'</td></tr>'
                    );
 
                    last = group;
                }
            } );
        }
    } );
 
    // Order by the grouping
    $('#example tbody').on( 'click', 'tr.group', function () {
        var currentOrder = table.order()[0];
        if ( currentOrder[0] === 2 && currentOrder[1] === 'asc' ) {
            table.order( [iColumnNo, 'desc' ] ).draw();
        }
        else {
            table.order( [iColumnNo, 'asc' ] ).draw();
        }
    } );
}
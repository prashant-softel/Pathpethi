function updateRecord(count,id)
{
    debugger;
    var agentname = document.getElementById('agent_id').value;
    var date = document.getElementById('datepicker').value;
    var memberid = document.getElementById('memberid_'+count).value;
    var ledgerid = document.getElementById('leadgerid_'+count).value;
    var amount = document.getElementById('amount_'+count).innerText;
    var remark = document.getElementById('remark_'+count).value;
    var status = document.getElementById('status_'+count).checked ? 1 : 0 ;
    
    $.ajax({
      url : "ajax/ajaxDailyCollection.php",
      type : "POST",
      data: { "method":"update","agentid":agentname, "date":date, "memberid":memberid, "ledgerid": ledgerid,"amount":amount,"status":status,"remark":remark} ,
      success : function(data){	
        try {
          let result = res.split('@@@');
          let data = JSON.parse("["+result[1]+"]");
          if(data[0].status == 'success'){
            $('#status_'+count).attr('disabled',true);
            $('#remark_'+count).attr('disabled',true);
            $('#insert_'+count).attr('disabled',true);
          }
        } catch (error) {
          console.log(error);  
        }
      },
   });
}

$(document).on('click', '.update', function(e) {
  console.log('bingp');
  debugger;
  let row = $(this).attr('data-row');
  let agent_id = $('#agent_id').val();
  let date = $('#date_'+row).text();
  let amount = $('#amount_'+row).text();
  let checkbox = $("#status_"+row).is(':checked');
  let comment = $("#comment_"+row).val();

  if(checkbox){
    $.ajax({
      url:'ajax/ajaxDailyCollection.php',
      type:'POST',
      data:{'method': 'agent_daily_collection' ,'agent_id':agent_id, 'date':date, 'amount':amount, 'comment':comment},
      success:function(res){
        try {
          let result = res.split('@@@');
          let data = JSON.parse("["+result[1]+"]");
          if(data[0].status == 'success'){
            $('#status_'+row).attr('disabled',true);
            $('#comment_'+row).attr('disabled',true);
            $('#update_'+row).attr('disabled',true);
          }
        } catch (error) {
          console.log(error);  
        }
      }
    });
  }
  console.log(agent_id, date, amount, checkbox, comment);
  return false;

})

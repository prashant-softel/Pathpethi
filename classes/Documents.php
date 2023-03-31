<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
include_once("classes/doc.class.php");
$objdoc = new document($m_dbConn);
?>
<div class="panel panel-info" style="margin-top:6%;margin-left:3.5%; border:none;width:70%">
 
    <div class="panel-heading" style="font-size:20px">
         Documents
    </div>
    <form enctype="multipart/form-data" action="process/document.process.php" method="POST">   
    <div class="panel-body">                        
        <div class="table-responsive">
         <table cellspacing="5">           	            
            <tr>
            	<td valign="middle"></td>
        		<th>Document Name</th>
                <td>&nbsp;:&nbsp;</td>
                <td><input type="text" name="doc_name" id="doc_name" /></td>
            </tr>
            <tr>
            	<td valign="middle"></td>
        		<th>Category</th>
                <td>&nbsp;:&nbsp;</td>
                <td>
                	<select id="category" name="category">
                    	<option value="0">Visible To Super Admin/Admin</option>
                        <option value="1">Visible To All</option>
                    </select>
                </td>
            </tr>
             <tr>
            	<td valign="middle"></td>
        		<th>Note</th>
                <td>&nbsp;:&nbsp;</td>
                <td> <textarea type="text" name="note" id="note" style="width: 200px; height: 40px;"> </textarea></td>
            </tr>
            <tr><td colspan="4"><br /> </td></tr>
           <tr>
           		<td colspan="4" align="left" style="color:#003399; font-size:16px;"><b>Attach your documents</b></td>
           </tr>
           <!-- MAX_FILE_SIZE must precede the file input field -->
        <!--   <input type="hidden" name="MAX_FILE_SIZE" value="30000" />  -->
           <tr> 
           		<td valign="middle"></td>
           		<td>Upload Document</td>   
                <td>&nbsp;:&nbsp;</td>       
                <!-- Name of input element determines name in $_FILES array -->
                <td><input name="userfile" type="file" /></td>
           </tr>      
           <tr>
               <td colspan="4"> <input type="submit" value="Upload" align="left" /></td>
           </tr>    
        </table>                                                   
         </form>
           
			
		
        </div>
        <div class="panel-footer">
        Print
        </div>        
</div>

</div>

<?php include_once "includes/foot.php"; ?>
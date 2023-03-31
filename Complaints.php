<?php include_once "ses_set_s.php"; ?>
<?php include_once("includes/head_s.php");
// include_once("RightPanel.php");    
include_once("classes/home_s.class.php");
include_once("classes/dbconst.class.php");
?>
 <div class="panel panel-info" id="panel" style="margin-top:6%;margin-left:3.5%; border:none;width:70%;display:none">
 
    <div class="panel-heading" id="pageheader" style="font-size:20px">
     Compaints
    </div>
    <div class="panel-body">                        
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Raised Date</th>
                        <th>Status</th>
                        <th>Last Update</th>
                        <th>Days</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                	<?php if($_REQUEST['cm'] == 1 || $_REQUEST['cm'] == 0)
						  {?>
                    <tr>
                        <td>1</td>
                        <td>Newspaper</td>
                        <td>News paper not delivered for 6 days starting 30st April</td>
                        <td>6th May 2015</td>
                        <td>Active</td>
                        <td>6th May 2015</td>
                        <td>5</td>
                        <td>View</td>
                    </tr>
                    <?php
						  }
						  if($_REQUEST['cm'] == 2 || $_REQUEST['cm'] == 0)
						  {
					?>
                    <tr>
                        <td>2</td>
                        <td>Swimming Pool</td>
                        <td>Swimming pool water looks too dirty</td>
                        <td>6th May 2015</td>
                        <td>Active</td>
                        <td>6th May 2015</td>
                        <td>2</td>
                        <td>View</td>
                    </tr>                    
                    <?php
						  }
					?>
                </tbody>
            </table>
        </div>
        
        <div class="panel-footer">
        Print
        </div>
        <br>
        <br />
        <br>
        <br />
               <!-- /.row (nested) -->
        <div class="panel panel-info"  >
            <div class="panel-heading" style="font-size:20px;">
                Raise New Complaint
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <form role="form">
                        <table>
                        <tr>
                        <td>
                        <label>Category</label>
                        </td>
                        <td>
                        <select class="form-control">
                            <option>Festivals</option>
                            <option>Drainage</option>
                            <option>Swimming Pool</option>
                            <option>Cleaning</option>
                        </select>
                        </td>
                        </tr>    
                        
                        <tr>
                        <td>
                            <label>Subject</label>
                            </td>
                            <td>
                            <input class="form-control" placeholder="Enter Subject">
                            </td>
                            </tr>
                       
                        
                        <tr>
                        <td>
                            <label>Description</label>
                            </td>
                            <td>
                            <textarea class="form-control" rows="3" id="Desc" name="Desc"></textarea>
                            </td>
                            </tr>
                        
                        
                        <tr>
                        <td colspan="2" style="text-align:right">
                        <button type="submit" class="btn btn-default">Submit</button>
                        <button type="reset" class="btn btn-default">Reset</button>
                        </td>
                        </tr>
                        </table>
                    </form>
                    </div>
                    <!-- /.col-lg-6 (nested) -->
                     </div>
                </div>
            </div>            
      </div>
</div>



<?php include_once "includes/foot.php"; ?>
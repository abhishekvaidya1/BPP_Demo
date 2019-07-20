<script src="<?php echo base_url();?>js/highcharts.js"></script>
<script src="<?php echo base_url();?>js/pareto_js_highchart.js"></script>
<script src="<?php echo base_url();?>js/exporting.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/sweetalert-dev.js"></script>
<link rel="stylesheet" type="text/css" id="theme" href="<?php echo base_url();?>css/sweetalert.css"/>
<body>  
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Home</a></li>
    <li><a href="#">Logsheet Report</a></li>
</ul>
<div class="page-content-wrap">
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal"  method="post" name="myForm1" id="myForm1" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><strong>LogSheet Report</strong> </h3>
                       
                    </div>
                    <div class="panel-body">                                                                        
                        <div class="row">
                            
                            <table class=" table">
                                <tr>
                                    <th style=" text-align: right;">Select Date</th>
                                    <th style=" text-align: left;"><input type="text" class=" form-control datepicker s_date" name="part_no" required="" style=" width: 100px;"></th>
                                    <th style=" text-align: right;">Select Plant</th>
                                    <th >
                                        <select class=" select2 machine" style=" width: 300px;">
                                            <option value="">Select</option>
                                        <option value="90mm">90 mm</option>
                                        <option value="120mm">120 mm</option>
                                        <option value="90mmold">90 mm Old</option>
                                        <option value="100mm">100 mm</option>
                                        </select>
                                    </th>
                                    <th style=" text-align: right;">Select Shift</th>
                                    <th ><select class="select2 form-control  shift" style=" width: 300px;">
                                        <option value="">Select</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </select></th>
                                    <th ><label class="btn btn-primary pull-left" id="submitbutton" name="print_option" value="">Submit</label></th>
                                </tr> 
                            </table>
                            
                            
                        </div>
                        <div id="show_item_data"></div>    
                    </div>
                </div>
            </form>
        </div>
    </div>                    
</div>
<!--<button class="btn btn-default" onClick="noty({text: 'Successful action', layout: 'topRight', type: 'success'});">Left top</button> -->
<div id="loader" style="  margin-left: 500px; margin-top: 300px; position: fixed; width: 100px; display:  none;"><img src="loading.gif" style=" width: 150px;"></div>
<div></div
<script type="text/javascript" src="<?php echo base_url();?>js/plugins/jquery/jquery.min.js"></script>
<script>
$(document).on('click', '#submitbutton', function (e) {


$("#loader").show();
$("#loader").fadeOut(2000);
$.ajax({
type: "post",
url: "<?php echo base_url('login_controller/get_logsheet_report'); ?>",
data: {s_date:$('.s_date').val(),machine:$('.machine').val(),shift:$('.shift').val()},
cache: false,
async: false,
success: function (data) {

$('#show_item_data').html(data);

}
});
});





</script>

</body>
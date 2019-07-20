<script src="<?php echo base_url();?>js/highcharts.js"></script>
<script src="<?php echo base_url();?>js/pareto_js_highchart.js"></script>
<script src="<?php echo base_url();?>js/exporting.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js/sweetalert-dev.js"></script>
<link rel="stylesheet" type="text/css" id="theme" href="<?php echo base_url();?>css/sweetalert.css"/>
<body>  
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Home</a></li>
    <li><a href="#">Daily Plant CaCo3 Consumption Report</a></li>
</ul>
<div class="page-content-wrap">
    <div class="row">
        <div class="col-md-12">
            <form class="form-horizontal"  method="post" name="myForm1" id="myForm1" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><strong>Daily Plant CaCo3 Consumption Report</strong> </h3>
                       
                    </div>
                    <div class="panel-body">                                                                        
                        <div class="row">
                            <div class="col-md-5">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Select Date</label>
                                        <div class="col-md-5">                                        
                                            <input type="text" class=" form-control datepicker s_date" name="part_no" required="" >
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="panel-body">
                                    <div class="form-group" id="show">
                                        
                                        <label class="btn btn-primary pull-left" id="submitbutton" name="print_option" value="">Submit</label>
                                    </div> 
                                </div>
                            </div>
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
url: "<?php echo base_url('login_controller/get_caco3_report'); ?>",
data: {s_date:$('.s_date').val()},
cache: false,
async: false,
success: function (data) {
 
$('#show_item_data').html(data);

}
});
});
function show_all_part_no () {
//$("#loader").show();
//$("#loader").fadeOut(4000);
var s_date = $('.s_date').val();
var e_date = $('.e_date').val();
var shift = $('.shift').val();
$.ajax({
type: "post",
url: "<?php echo base_url('login_controller/get_part_no'); ?>",
data: {s_date: s_date,e_date:e_date,shift:shift},
cache: false,
async: false,
success: function (data) {


$('#show').show();
$('#show_part_no_data').html(data);
$('.select').select2();
}
});
}

$(document).on('change', '.s_date', function (e) {
//$(".s_date").change(function(e) {
var s_date = $(this).val();
//if (e.handled !== true) {
//        e.handled = true;
//        return;
//    }
$.ajax({
type: "post",
url: "<?php echo base_url('login_controller/get_part_no_scrap'); ?>",
data: {s_date: s_date},
cache: false,
async: false,
success: function (data) {
//alert(data);
$('#append_item').html(data);
$('.select2').select2();
}
});

});




</script>
            <!-- END PLUGINS -->

            <!-- THIS PAGE PLUGINS -->
        
            <script type='text/javascript' src='js/plugins/noty/jquery.noty.js'></script>
            <script type='text/javascript' src='js/plugins/noty/layouts/topCenter.js'></script>
            <script type='text/javascript' src='js/plugins/noty/layouts/topLeft.js'></script>
            <script type='text/javascript' src='js/plugins/noty/layouts/topRight.js'></script>            
            
            <script type='text/javascript' src='js/plugins/noty/themes/default.js'></script>
            <script type="text/javascript">                                            
                function notyConfirm(){
                    noty({
                        text: 'Do you want to continue?',
                        layout: 'topRight',
                        buttons: [
                                {addClass: 'btn btn-success btn-clean', text: 'Ok', onClick: function($noty) {
                                    $noty.close();
                                    noty({text: 'You clicked "Ok" button', layout: 'topRight', type: 'success'});
                                }
                                },
                                {addClass: 'btn btn-danger btn-clean', text: 'Cancel', onClick: function($noty) {
                                    $noty.close();
                                    noty({text: 'You clicked "Cancel" button', layout: 'topRight', type: 'error'});
                                    }
                                }
                            ]
                    })                                                    
                }    
                
                // abc
            </script>
</body>


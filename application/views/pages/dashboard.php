<script src="<?php echo base_url();?>js/highcharts.js"></script>
<script src="<?php echo base_url();?>js/exporting.js"></script>
<!--<script src="https://code.highcharts.com/modules/export-data.js"></script>-->
<body >
<!--<div class="col-md-12">
    <div class="panel-body" style=" background-color:  #F0F8FF;">   
     START VISITORS BLOCK 
    <div class="panel panel-default" style="box-shadow: 5px 5px 5px 5px gray;">
        <div class="panel-heading ui-draggable-handle" style="background-color:  lightgrey;">
            <div class="panel-title-box" >
                <h3><input type="text" class="datepicker s_date" style=" width: 80px;"></h3>
            </div>
        </div>
    </div>
</div>
<div class="page-content-wrap" >
<div class="row">
    <div id="append_item">
        
    </div>
    </div>
    </div>-->



<div class="page-content-wrap">
    <div class="row">
        <div class="col-md-12" style=" background-color: #F0F8FF;">
            <form class="form-horizontal"  method="post" name="myForm1" id="myForm1" enctype="multipart/form-data">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>
                            <table>
                                <tr>
                                    <th><input type="text" class="datepicker s_date form-control" style=" width: 100px;"></th>
                                    <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th><button class="btn btn-danger show_dash" type="button" >Show</button></th>
                                    <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    <th >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                    
                                    
                                    
                                    <th  style=" text-align: center;">BPP DASHBOARD</th>
                                </tr>
                            </table>
                        </h3>
                    </div>
                        <div class="panel-body" style=" background-color: #ddd;">
                        <div class="row">
                            <div id="append_item"></div>
                        </div>
                        </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="loader" style="  margin-left: 500px; margin-top: 300px; position: fixed; width: 100px; display:  none;"><img src="loading.gif" style=" width: 150px;"></div>

<script>


$(document).on('click','.show_dash',function(e)
{
    var select_date=$('.s_date').val();

$('#append_item').html('');
$("#loader").show();
$("#loader").fadeOut(2000);
$.ajax({
type: "post",
url: "<?php echo base_url('login_controller/dashboard_page'); ?>",
data: {select_date:select_date},
cache: false,
async: false,
success: function (data) {
$('#append_item').html(data);
load_excess();
load_excess1();
load_excess2();
}

});

});


</script>







 <script type="text/javascript" src="<?php echo base_url();?>js/sweetalert-dev.js"></script>
<link rel="stylesheet" type="text/css" id="theme" href="<?php echo base_url();?>css/sweetalert.css"/>
<link href="fSelect.css" rel="stylesheet">
<style>
#table-wrapper {
  position:relative;
}
#table-scroll {
  height:250px;
  overflow:auto;  
  margin-top:20px;
}
#table-wrapper table {
  width:100%;
    
}
#table-wrapper table * {
/*  background:yellow;
  color:black;*/
}
#table-wrapper table thead th .text {
  position:absolute;   
  top:-20px;
  z-index:2;
  height:20px;
  width:35%;
  border:1px solid red;
}
td{
    border-top-color:  white;
}
th{
    border-top-color:  white;
}
/*table {
    border-collapse: collapse;
}*/
</style>
<script src="fSelect.js"></script>
<body>  
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Home</a></li>
    <li><a href="#">Extrusion Logsheet</a></li>
</ul>

<div class="page-content-wrap" >
<div class="row">
    <div class="col-md-12">
        <form class="form-horizontal" id="myForm1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Power Reading Details</strong> </h3>
                    <ul class="panel-controls">
        <!--                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span></a></li>-->
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
                </div>
                <?php
                $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
                $date1 = $date->format('Y-m-d');
                ?>
                <div class="panel-body" style=" background-color:  #F0F8FF;">                                                                        
                    <div class="row">
                        <table class="table" style=" border: none;" >
                            <tr style=" border-top-color:  #F0F8FF;">
                                <th style=" border-top-color:  #F0F8FF;width: 100px;">Select Date</th>
                                <td style=" width: 100px;border-top-color:  #F0F8FF;"><input type="text" class=" form-control datepicker c_date"  value="<?php echo $date1; ?>" name="c_date" style="width: 100px;"></td>
                                <th style="border-top-color:  #F0F8FF;width: 100px;">Select Plant</th>
                                <td style=" width: 100px;border-top-color:  #F0F8FF;">
                                    <select class=" form-control select2 machine" name="type_of_machine" required="" style=" width: 100px;">
                                        <option value="">Select</option>
                                        <option value="90mm">90 mm</option>
                                        <option value="120mm">120 mm</option>
                                        <option value="90mmold">90 mm Old</option>
                                        <option value="100mm">100 mm</option>
                                    </select>
                                </td>
                                <th style="border-top-color:  #F0F8FF;width: 100px;">Select Shift</th>
                                <td style="border-top-color:  #F0F8FF; width: 100px;" ><select class=" form-control select2 shift check_data_exist" name="shift" required="" style=" width: 100px;">
                                        <option value="">Select</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>

                                    </select></td></tr>
                            <tr>
                                <th style="border-top-color:  #F0F8FF;width: 100px;">Opening</th>
                                <td style="border-top-color:  #F0F8FF;width: 100px;"><input type="text" class=" form-control  opening"  required="" name="opening" value="0" style="width: 100px;"></td>
                                <th style="border-top-color:  #F0F8FF;width: 100px; ">Closing</th>
                                <td style="border-top-color:  #F0F8FF;"><input type="text" class=" form-control  closing"  required="" style="width: 100px;" name="closing"></td>
                                
                                <td style="border-top-color:  #F0F8FF;"><label class="btn btn-primary " id="btn1" name="print_option"  >Save</label></td>

                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-md-12">
        <form class="form-horizontal" id="myForm2">
            <input type="text" class="logsheet_id" style="display:none;" name="logsheet_id">
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Production Entry Form</strong> </h3>
                    <ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">                                                                        
                    <div class="row">
                        
                        <table class="table" style=" border: none;" >
                            
                            
                            
                            <tr>
                                <th style="border-top-color:  #F0F8FF;width: 200px;">Operator</th>
                                <th style="border-top-color:  #F0F8FF;width: 100px;"><select class=" form-control  select2 operator" name="operator_name" style="width: 100px;">
                                        <option value="">Select</option>
                                        <option value="Operator 1">Operator 1</option>
                                        <option value="Operator 2">Operator 2</option>
                                        <option value="Operator 3">Operator 3</option>

                                    </select></th>
                                <th style="border-top-color:  #F0F8FF; text-align: center;width: 300px;">Running HRS</th>
                                <td style="border-top-color:  #F0F8FF;text-align: center; width: 300px; "><input type="text" class=" form-control running_hrs time"  required="" style="width: 100px;" placeholder="hh:mm" name="running_hrs" onkeypress='return isNumber(event)'></td>
                                
                                <td style="border-top-color:  #F0F8FF; "><label class="btn btn-primary " id="btn2" name="print_option"  >Save</label></td>

                            </tr>
                            
                            <tr style=" border-top-color:  #F0F8FF; display: none;">
                                <th style=" border-top-color:  #F0F8FF;width: 100px;">Select Date</th>
                                <td style=" width: 100px;border-top-color:  #F0F8FF;"><input type="text" class=" form-control  c_date_prod" name="c_date" readonly="" style=" color: black; width: 100px;"></td>
                                <th style="border-top-color:  #F0F8FF;width: 100px;">Select Plant</th>
                                <td style=" width: 100px;border-top-color:  #F0F8FF;">
                                    <input type="text" class=" form-control  machine_prod" name="type_of_machine" readonly="" style=" color: black; width: 100px;" >
                                </td>
                                <th style="border-top-color:  #F0F8FF;width: 100px;">Select Shift</th>
                                <td style="border-top-color:  #F0F8FF; width: 100px;" >
                                    <input type="text"  class=" form-control  shift_prod" name="shift" readonly="" style=" color: black; width: 100px;">
                                </td></tr>
<!--                            <tr>
                                <th style="border-top-color:  #F0F8FF;width: 100px;">Select Operator</th>
                                <td style="border-top-color:  #F0F8FF;width: 100px;"><select class=" form-control  select2 operator" name="operator_name" style="width: 100px;">
                                        <option value="">Select</option>
                                        <option value="Operator 1">Operator 1</option>
                                        <option value="Operator 2">Operator 2</option>
                                        <option value="Operator 3">Operator 3</option>

                                    </select></td>
                                <th style="border-top-color:  #F0F8FF;">Running HRS</th>
                                <td style="border-top-color:  #F0F8FF;"><input type="text" class=" form-control running_hrs time"  required="" style="width: 100px;" placeholder="hh:mm" name="running_hrs"></td>
                                
                                <td style="border-top-color:  #F0F8FF;"><label class="btn btn-primary " id="btn2" name="print_option"  >Save</label></td>

                            </tr>-->
                        </table>
                        
                        
                        

                    </div>
                </div>
            </div>
        </form>
    </div>    
    
    <div class="col-md-12" style=" background-color: #F0F8FF;">
        <form class="form-horizontal" id="myForm">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><strong>Development Section</strong> </h3>
                    <ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">                                                                        
                    <div class="row">
                        <table class="table" style=" background-color: #F0F8FF;">
                            <tr>
                                <th style="text-align: center;"><button type="button" class="btn  sect" value="1" style=" background-color:  lightslategray;">WINDERS</button></th>
                                <th style="text-align: center;"><button type="button" class="btn  sect" value="2" style=" background-color:  lightcoral;">RAW MATERIAL</button></th>
                                <th style="text-align: center;"><button type="button" class="btn  sect" value="3" style=" background-color:  lightgoldenrodyellow;">DENIER PRODUCTION</button></th>
                                <th style="text-align: center;"><button type="button" class="btn  sect" value="4" style=" background-color:   lightgray;">WASTAGE</button></th>
                                <th style="text-align: center;"><button type="button" class="btn  sect" value="5" style=" background-color:     lightgreen;">BREAK DOWN</button></th>

                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div> 
    
    <div class="col-md-12" style=" background-color: #F0F8FF; display: none;" id="sect_1">
        <form class="form-horizontal" id="myForm3">
            <input type="text" class="logsheet_id" style="display:none;" name="logsheet_id">
            <input type="text" class="d_type_of_machine" style="display:none;" name="type_of_machine">
            <input type="text" class="d_shift" style="display:none;" name="shift">
            <input type="text" class="d_c_date" style="display:none;" name="c_date">
            <div class="panel panel-default">
                <div class="panel-heading" style=" background-color:  lightslategray;">
                    <h3 class="panel-title" ><strong>WINDER</strong> </h3>
                    <ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">                                                                        
                    <div class="row">
                        <table class="table" style=" background-color: #F0F8FF;">
                            <tr>
                                <th style="text-align: center;">Select Winder</th>
                                <th style="text-align: center;">
                                    <select class=" form-control select2 winder" multiple="" style=" width: 500px;" name="operator_name[]">
<!--                                        <option value="">Select</option>-->
                                        <option value="Winder 1">Winder 1</option>
                                        <option value="Winder 2">Winder 2</option>
                                        <option value="Winder 3">Winder 3</option>
                                        <option value="Winder 4">Winder 4</option>
                                        <option value="Winder 5">Winder 5</option>
                                        <option value="Winder 6">Winder 6</option>
                                        <option value="Winder 7">Winder 7</option>
                                        <option value="Winder 8">Winder 8</option>
                                        <option value="Winder 9">Winder 9</option>
                                        <option value="Winder 10">Winder 10</option>

                                    </select>
                                </th>
                                <td style="text-align: center;"><label class="btn btn-primary " id="btn3" name="print_option"  >Save</label></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>   
    
    <div class="col-md-12" style=" background-color: #F0F8FF; display: none;" id="sect_2">
        <form class="form-horizontal" id="myForm4">
            <input type="text" class="logsheet_id" style="display:none;" name="logsheet_id">
            <input type="text" class="d_type_of_machine" style="display:none;" name="type_of_machine">
            <input type="text" class="d_shift" style="display:none;" name="shift">
            <input type="text" class="d_c_date" style="display:none;" name="c_date">
            <div class="panel panel-default">
                <div class="panel-heading" style=" background-color:  lightcoral;">
                    <h3 class="panel-title"><strong>RAW MATERIAL</strong> </h3>
                    <ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">                                                                        
                    <div class="row">
                        <table class="table" style=" background-color: #F0F8FF;">
                            <tr>
                                <th><button class="btn btn-default" data-toggle="modal" data-target="#modal_large" type="button">Show List</button>&nbsp;&nbsp;&nbsp;Total Production - <label id="total_prod"></label></th>    
                            <th></th>    
                            </tr>
                            <tr>
                            <th><div id="selected_raw_list_data"></div></th>
                            </tr>
                        </table>
                        <label class="btn btn-primary pull-right" id="btn4" name="print_option"  >Save</label>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-md-12" style=" background-color: #F0F8FF; display: none;" id="sect_3">
        <form class="form-horizontal" id="myForm5">
            <input type="text" class="logsheet_id" style="display:none;" name="logsheet_id">
            <input type="text" class="d_type_of_machine" style="display:none;" name="type_of_machine">
            <input type="text" class="d_shift" style="display:none;" name="shift">
            <input type="text" class="d_c_date" style="display:none;" name="c_date">
            <div class="panel panel-default">
                <div class="panel-heading" style=" background-color:  lightgoldenrodyellow;">
                    <h3 class="panel-title"><strong>Denier Production Details - &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Production - <label id="total_prod1"></label></strong> </h3>
                    <ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">                                                                        
                    <div class="row">
                        <div id="table-wrapper">
                            <div id="table-scroll">
                                <table class="table table-bordered" style=" background-color: #F0F8FF;">
                                    <tr>
                                        <th style="text-align: center;">Denier</th>
                                        <th style="text-align: center;">Tape Type</th>
                                        <th style="text-align: center;">Caco3 %</th>
                                        <th style="text-align: center;">Marking</th>
                                        <th style="text-align: center;">Output Kg</th>
                                        <th style="text-align: center;">#</th>

                                    </tr>
                                    <tbody id="append_denier_prod">
                                        <tr>
                                            <th><input type="text" class=" form-control" name="denier[]" required=""></th>  
                                            <th><select class=" form-control select2" name="tape_type[]" style=" width: 300px;">
                                                    <option value="">Select</option>
                                                    <option value="BLACK">BLACK</option>
                                                    <option value="SU">SU</option>
                                                    <option value="WHITE">WHITE</option>
                                                    <option value="ORANGE">ORANGE</option>
                                                    <option value="RAT BLUE">RAT BLUE</option>
                                                </select></th>
                                            <th><input type="text" class=" form-control" name="caco3[]" required=""></th>
                                            <th><select class=" form-control select2" name="marking[]" style=" width: 300px;">
                                                    <option value="">Select</option>
                                                    <option value="RED">RED</option>
                                                    <option value="RED GREEN">RED GREEN</option>
                                                    <option value="RED BLUE">RED BLUE</option>
                                                    <option value="BLACK">BLACK</option>
                                                    <option value="WHITE">WHITE</option>
                                                </select></th>
                                            <th><input type="text" class=" form-control output_kg_total" name="output_kg[]" required=""></th>
                                            <th><label class=" btn btn-primary btn-rounded add_denier">ADD</label></th>
                                        </tr>

                                    </tbody>

                                </table>
                            </div> </div>
                        <label class="btn btn-primary pull-right" id="btn5" name="print_option"  >Save</label>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-md-12" style=" background-color: #F0F8FF; display: none;" id="sect_4">
        <form class="form-horizontal" id="myForm6">
            <input type="text" class="logsheet_id" style="display:none;" name="logsheet_id">
            <input type="text" class="d_type_of_machine" style="display:none;" name="type_of_machine">
            <input type="text" class="d_shift" style="display:none;" name="shift">
            <input type="text" class="d_c_date" style="display:none;" name="c_date">
            <div class="panel panel-default">
                <div class="panel-heading" style=" background-color:  lightgray;">
                    <h3 class="panel-title"><strong>Wastage</strong> </h3>
                    <ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">                                                                        
                    <div class="row">
                        <table class="table table-bordered" style=" background-color: #F0F8FF;">
                            <tr>
                                <th style="text-align: center;" colspan="2">Starting</th>
                                <th style="text-align: center;" colspan="2">Running</th>
                                <th style="text-align: center;" colspan="2">Dressing</th>
                            </tr>
                            <tr>
                                <th style=" text-align:center; ">Wastage</th>
                                <th style=" text-align:center; ">Nos.</th>
                                <th style=" text-align:center; ">Wastage</th>
                                <th style=" text-align:center; ">Nos.</th>
                                <th style=" text-align:center; ">Wastage</th>
                                <th style=" text-align:center; ">Nos.</th>
                            </tr>
                            <tbody>
                                <tr>
                                    <th style=" text-align:center; "><input type="text" class=" form-control" name="start_wastage"></th>  
                                    <th style=" text-align:center; "><input type="text" class=" form-control" name="start_wastage_no"></th>  
                                    <th style=" text-align:center; "><input type="text" class=" form-control" name="run_wastage"></th>  
                                    <th style=" text-align:center; "><input type="text" class=" form-control" name="run_wastage_no"></th>  
                                    <th style=" text-align:center; "><input type="text" class=" form-control" name="dress_wastage"></th>  
                                    <th style=" text-align:center; "><input type="text" class=" form-control" name="dress_wastage_no"></th>  
                                </tr>
                            </tbody>

                        </table>
                        <label class="btn btn-primary pull-right" id="btn6" name="print_option"  >Save</label>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-md-12" style=" background-color: #F0F8FF; display: none;" id="sect_5">
        <form class="form-horizontal" id="myForm7">
            <input type="text" class="logsheet_id" style="display:none;" name="logsheet_id">
            <input type="text" class="d_type_of_machine" style="display:none;" name="type_of_machine">
            <input type="text" class="d_shift" style="display:none;" name="shift">
            <input type="text" class="d_c_date" style="display:none;" name="c_date">
            <div class="panel panel-default">
                <div class="panel-heading" style=" background-color:  lightgreen;">
                    <h3 class="panel-title"><strong>Break Down</strong> </h3>
                    <ul class="panel-controls">
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
                </div>
                <div class="panel-body">                                                                        
                    <div class="row">
                        <div id="table-wrapper">
                            <div id="table-scroll">
                                <table class="table table-bordered " style=" background-color: #F0F8FF; ">
                                    <tr>
                                        <th style="text-align: center;">Hrs</th>
                                        <th style="text-align: center;">Reason</th>

                                        <th style="text-align: center;">#</th>

                                    </tr>

                                    <tbody id="append_break_down" style="">
                                        <tr>
                                            <th style=" width: 100px;"><input type="text" class=" form-control time" name="hrs[]" style=" width: 100px;" placeholder='hh:mm' ></th>  
                                            <th><textarea type="text" class=" form-control" name="reasons[]" style=" height: 40px;"></textarea></th>
                                            <th><label class=" btn btn-primary btn-rounded add_break_down">ADD</label></th>
                                        </tr>

                                    </tbody>

                                </table>
                            </div>
                        </div>
                        <label class="btn btn-primary pull-right" id="btn7" name="print_option"  >Save</label>
                    </div>
                </div>
            </div>
        </form>
        
    </div>
    <div class="col-md-12" style=" background-color: #F0F8FF; ">
        <form class="form-horizontal" >
            <div class="panel panel-default">
                <div class="panel-body">                                                                        
                    <button class=" btn btn-danger submit_all pull-right" type="button">Submit</button>   
                </div>
            </div>
        </form>
    </div>

    
     
</div> 

</div>
<div class="modal" id="modal_large" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="largeModalHead">Raw Material List</h4>
                    </div>
                    <div class="modal-body" style=" background-color:#F5F5F5;">
                        <div class="panel-body">
                               
                        <table class="table datatable table-bordered" id="datatable1" style=" border-top-color: #F5F5F5;">
                            <thead  style=" border-top-color: #F5F5F5;">
                            <tr style=" background-color:#F5F5F5; border-top-color: #F5F5F5;">
                                <th style=" text-align: center;">#</th>
                                <th style=" text-align: center;">RmID</th>
                                <th style=" text-align: center;">Item</th>
                                <th style=" text-align: center;">Material Description</th>
                                <th style=" text-align: center;">Grade</th>
                                <th style=" text-align: center;">Per Bag Qty(KG)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <th><input type="checkbox" class="raw_checkbox icheckbox" value="1"></th>
                                <th>1</th>
                                <th><input type="text" class="form-control item_nm" value="PP" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control mat_desc" value="REPOL" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control grade" value="HO30SG" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control bag_qty" value="25.00" style="color: black;" readonly=""></th>
                            </tr>
                            <tr>
                                <th><input type="checkbox" class="raw_checkbox icheckbox" value="2"></th>
                                <th>2</th>
                                <th><input type="text" class="form-control item_nm" value="CAL" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control mat_desc" value="SUPERPACK" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control grade" value="AF" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control bag_qty" value="25.00" style="color: black;" readonly=""></th>
                            </tr>
                            <tr>
                                <th><input type="checkbox" class="raw_checkbox icheckbox" value="3"></th>
                                <th>3</th>
                                <th><input type="text" class="form-control item_nm" value="CAL" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control mat_desc" value="WELSET" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control grade" value="" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control bag_qty" value="25.00" style="color: black;" readonly=""></th>
                            </tr>
                            <tr>
                                <th><input type="checkbox" class="raw_checkbox icheckbox" value="4"></th>
                                <th>4</th>
                                <th><input type="text" class="form-control item_nm" value="CAL" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control mat_desc" value="PLASTSTIFF" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control grade" value="" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control bag_qty" value="25.00" style="color: black;" readonly=""></th>
                            </tr>
                            <tr>
                                <th><input type="checkbox" class="raw_checkbox icheckbox" value="5"></th>
                                <th>5</th>
                                <th><input type="text" class="form-control item_nm" value="MB" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control mat_desc" value="ORANGE" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control grade" value="AF" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control bag_qty" value="25.00" style="color: black;" readonly=""></th>
                            </tr>
                            <tr>
                                <th><input type="checkbox" class="raw_checkbox icheckbox" value="6"></th>
                                <th>6</th>
                                <th><input type="text" class="form-control item_nm" value="MB" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control mat_desc" value="YELLOW" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control grade" value="HO30SG" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control bag_qty" value="25.00" style="color: black;" readonly=""></th>
                            </tr>
                            <tr>
                                <th><input type="checkbox" class="raw_checkbox icheckbox" value="7"></th>
                                <th>7</th>
                                <th><input type="text" class="form-control item_nm" value="PP" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control mat_desc" value="ADL" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control grade" value="AS30N" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control bag_qty" value="25.00" style="color: black;" readonly=""></th>
                            </tr>
                            <tr>
                                <th><input type="checkbox" class="raw_checkbox icheckbox" value="8"></th>
                                <th>8</th>
                                <th><input type="text" class="form-control item_nm" value="MB" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control mat_desc" value="OMEGA" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control grade" value="POLYCOM" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control bag_qty" value="25.00" style="color: black;" readonly=""></th>
                            </tr>
                            <tr>
                                <th><input type="checkbox" class="raw_checkbox icheckbox" value="9"></th>
                                <th>9</th>
                                <th><input type="text" class="form-control item_nm" value="MB" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control mat_desc" value="BLUE TONE" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control grade" value="SOLTEX" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control bag_qty" value="25.00" style="color: black;" readonly=""></th>
                            </tr>
                            <tr>
                                <th><input type="checkbox" class="raw_checkbox icheckbox" value="10"></th>
                                <th>10</th>
                                <th><input type="text" class="form-control item_nm" value="LLDPE" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control mat_desc" value="RELIANCE LLDPE" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control grade" value="AF" style="color: black;" readonly=""></th>
                                <th><input type="text" class="form-control bag_qty" value="25.00" style="color: black;" readonly=""></th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger get_raw_list">Submit</button>                        
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>                        
                    </div>
<!--                </div>-->
            </div>
        </div> 
        </div> 

<div id="loader" style="  margin-left: 500px; margin-top: 300px; position: fixed; width: 100px; display:  none;"><img src="loading.gif" style=" width: 150px;"></div>
<div></div
<script type="text/javascript" src="<?php echo base_url();?>js/plugins/jquery/jquery.min.js"></script>

<script>








var winder = null;
$('.winder').change(function(event) {
if ($(this).val().length > 5) {
alert("Maximum 5 Winder Allowed");
$(this).val(winder);
} else {winder = $(this).val();}
});

(function($) {
    $(function() {
        $('.test').fSelect();
        $('.datatable').dataTable();
$('#datatable1').dataTable();
    });
})(jQuery);


 $(function(){
$('.get_raw_list').click(function(){
var val = [];
$('.raw_checkbox:checked').each(function(i){
val[i] = $(this).val();
});
$('#modal_large').modal('hide');
$.ajax({
type: "post",
url: "<?php echo base_url('login_controller/get_raw_list'); ?>",
data: {rawarray:val},
cache: false,
async: false,
success: function (data) {
$('#selected_raw_list_data').html(data);
}
});
});
});
    
    $(document).on('keyup', '.kg_calculate', function (e) {
    var this_val=$(this).val();
    var id_name = $(this).attr("id");
    var res = id_name.split("_");
    var qty=$('#qty_'+res[1]).val();
    if(this_val)
    {
    var kg=parseInt(this_val) * parseInt(qty);
    $('#kg_'+res[1]).val(kg);
    
    }
    else{$('#kg_'+res[1]).val("0"); }
    });
    
    $(document).on('keyup', '.kg_calculate', function (e) {
    var sum = 0;
    $(".total_kg_product").each(function(){
        sum += +$(this).val();
    });
    $("#total_prod").html(sum);
    });
    
    $(document).on('keyup', '.output_kg_total', function (e) {
    var sum = 0;
    $(".output_kg_total").each(function(){
        sum += +$(this).val();
    });
    $("#total_prod1").html(sum);
    });
    
    var d=1;
    
    $(document).on('click', '.add_denier', function (e) {
        var data='';
        
data='<tr><th><input type="text" class=" form-control dd_'+d+'" name="denier[]" required=""></th><th><select class=" form-control select2" name="tape_type[]" style=" width: 300px;"><option value="">Select</option>\n\
<option value="BLACK">BLACK</option>option value="SU">SU</option><option value="WHITE">WHITE</option><option value="ORANGE">ORANGE</option><option value="RAT BLUE">RAT BLUE</option></select></th>';
data+='<th><input type="text" class=" form-control" name="caco3[]" required=""></th>';
data+='<th><select class=" form-control select2" name="marking[]" style=" width: 300px;">\n\
<option value="">Select</option>\n\
<option value="RED">RED</option>\n\
<option value="RED GREEN">RED GREEN</option>\n\
<option value="RED BLUE">RED BLUE</option>\n\
<option value="BLACK">BLACK</option>\n\
<option value="WHITE">WHITE</option>\n\
</select></th>';
data+='<th><input type="text" class=" form-control output_kg_total" name="output_kg[]" required=""></th>';
data+='<th><label class=" btn btn-primary btn-rounded add_denier">ADD</label></th></tr>';
$('#append_denier_prod').append(data);
$('.dd_'+d).focus();
d++;
$('.select2').select2();
$select.select2("refresh", true);
    });
    
    var b=1;
    $(document).on('click', '.add_break_down', function (e) {
        var data='';
        data='<tr>\n\
<th style=" width: 100px;"><input type="text" class=" form-control time dd_'+b+'" name="hrs[]" style=" width: 100px;" placeholder="hh:mm" ></th>  \n\
<th><textarea type="text" class=" form-control" name="reasons[]" style=" height: 40px;"></textarea></th>\n\
<th><label class=" btn btn-primary btn-rounded add_break_down">ADD</label></th></tr>';
$('#append_break_down').append(data);
$('.dd_'+b).focus();
b++;
$('.select2').select2();
$select.select2("refresh", true);
    });
    
    
    
$(document).on('blur', '.time', function (e) {
var time = $(this).val();
if((time.length)==2)
{var abc=time+":00";   
$(this).closest('tr').find("input.time").val(abc);    
}
else{
var final_time=(time.match(/.{1,2}/g).join(':'));    
$(this).closest('tr').find("input.time").val(final_time);
}
});

   $(document).on('click', '.sect', function (e) {
       var this_val=$(this).val();
       for(var i=1;i<6;i++)
       {if(this_val==i){
        $('#sect_'+this_val).show();}
    else{$('#sect_'+i).hide();}
       }
     });
     
     
     
     $(document).on('click', '#btn1', function (e) {
      var form = document.getElementById('myForm1');
for(var i=0; i < form.elements.length; i++){
if(form.elements[i].value === '' && form.elements[i].hasAttribute('required')){
swal(
'Oops...',
'Please Select ' + form.elements[i].name,
'error'
);
return false;
}
}
$("#loader").show();
$("#loader").fadeOut(2000);

$.ajax({
url:'<?php echo base_url(); ?>login_controller/save_prod_entry_form',
type: 'POST',
async: false,
cache: false,
data: $("#myForm1").serialize(),
success: function(data){
  $(".logsheet_id").val(data);  
  $(".d_type_of_machine").val($('.machine').val());  
  $(".d_shift").val($('.shift').val());  
  $(".d_c_date").val($('.c_date').val());  
$('.c_date_prod').val($('.c_date').val());
$('.machine_prod').val($('.machine').val());
$('.shift_prod').val($('.shift').val());
}
});   
});

$(document).on('click', '#btn2', function (e) {
var form = document.getElementById('myForm2');
for(var i=0; i < form.elements.length; i++){
if(form.elements[i].value === '' && form.elements[i].hasAttribute('required')){
swal(
'Oops...',
'Please Select ' + form.elements[i].name,
'error'
);
return false;
}
}
$("#loader").show();
$("#loader").fadeOut(2000);
$.ajax({
url:'<?php echo base_url(); ?>login_controller/save_tbl_prod_entry_form',
type: 'POST',
async: false,
cache: false,
data: $("#myForm2").serialize(),
success: function(data){

}
});   
});

$(document).on('click', '#btn3', function (e) {
var form = document.getElementById('myForm3');
for(var i=0; i < form.elements.length; i++){
if(form.elements[i].value === '' && form.elements[i].hasAttribute('required')){
swal(
'Oops...',
'Please Select ' + form.elements[i].name,
'error'
);
return false;
}
}
$("#loader").show();
$("#loader").fadeOut(2000);
$.ajax({
url:'<?php echo base_url(); ?>login_controller/save_tbl_winders',
type: 'POST',
async: false,
cache: false,
data: $("#myForm3").serialize(),
success: function(data){

}
});   
});

$(document).on('click', '#btn4', function (e) {
var form = document.getElementById('myForm4');
for(var i=0; i < form.elements.length; i++){
if(form.elements[i].value === '' && form.elements[i].hasAttribute('required')){
swal(
'Oops...',
'Please Select ' + form.elements[i].name,
'error'
);
return false;
}
}
$("#loader").show();
$("#loader").fadeOut(2000);
$.ajax({
url:'<?php echo base_url(); ?>login_controller/save_tbl_row_material',
type: 'POST',
async: false,
cache: false,
data: $("#myForm4").serialize(),
success: function(data){

}
});   
});

$(document).on('click', '#btn5', function (e) {
var form = document.getElementById('myForm5');
for(var i=0; i < form.elements.length; i++){
if(form.elements[i].value === '' && form.elements[i].hasAttribute('required')){
swal(
'Oops...',
'Please Select ' + form.elements[i].name,
'error'
);
return false;
}
}
$("#loader").show();
$("#loader").fadeOut(2000);
$.ajax({
url:'<?php echo base_url(); ?>login_controller/save_tbl_denier',
type: 'POST',
async: false,
cache: false,
data: $("#myForm5").serialize(),
success: function(data){

}
});   
});

$(document).on('click', '#btn6', function (e) {
var form = document.getElementById('myForm6');
for(var i=0; i < form.elements.length; i++){
if(form.elements[i].value === '' && form.elements[i].hasAttribute('required')){
swal(
'Oops...',
'Please Select ' + form.elements[i].name,
'error'
);
return false;
}
}
$("#loader").show();
$("#loader").fadeOut(2000);
$.ajax({
url:'<?php echo base_url(); ?>login_controller/save_tbl_wastage',
type: 'POST',
async: false,
cache: false,
data: $("#myForm6").serialize(),
success: function(data){

}
});   
});

$(document).on('click', '#btn7', function (e) {
var form = document.getElementById('myForm7');
for(var i=0; i < form.elements.length; i++){
if(form.elements[i].value === '' && form.elements[i].hasAttribute('required')){
swal(
'Oops...',
'Please Select ' + form.elements[i].name,
'error'
);
return false;
}
}
$("#loader").show();
$("#loader").fadeOut(2000);
$.ajax({
url:'<?php echo base_url(); ?>login_controller/save_tbl_breakdown',
type: 'POST',
async: false,
cache: false,
data: $("#myForm7").serialize(),
success: function(data){

}
});   
});

$(document).on('change', '.check_data_exist', function (e) {
var shift=$(this).val();
var machine=$('.machine').val();
var c_date=$('.c_date').val();
$("#loader").show();
$("#loader").fadeOut(2000);
$.ajax({
url:'<?php echo base_url(); ?>login_controller/check_data_exist',
type: 'POST',
async: false,
cache: false,
data: {shift:shift,machine:machine,c_date:c_date},
success: function(data){
if(data)
{
swal(
'Oops...',
'Data Already Exists',
'error'
);    
}
    
}
});   
});

$(document).on('click', '.submit_all', function (e) {
var shift=$('.shift').val();
var machine=$('.machine').val();
var c_date=$('.c_date').val();
$("#loader").show();
$("#loader").fadeOut(2000);
$.ajax({
url:'<?php echo base_url(); ?>login_controller/submit_form',
type: 'POST',
async: false,
cache: false,
data: {shift:shift,machine:machine,c_date:c_date},
success: function(data){
   swal({
title: "Great!",
text: "",
type: "success",
showCancelButton: false,
closeOnConfirm: false,
showLoaderOnConfirm: true
},
function(){
setTimeout(function(){
location.reload(true);
}, 1000);
});

    
}
}); 


});

     
     
     

function isNumber(evt) {
evt = (evt) ? evt : window.event;
var charCode = (evt.which) ? evt.which : evt.keyCode;
if (charCode > 31 && (charCode < 48 || charCode > 57)) {
return false;
}
return true;
}
    

</script>



</script>
<script type="text/javascript" src="<?php echo base_url();?>js/select_2.js"></script>
</body>
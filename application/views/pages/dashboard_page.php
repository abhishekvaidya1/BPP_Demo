
    

<?php
$q1="select SUM(rm.kg) as kg_shift_total,es.type_of_machine,GROUP_CONCAT(DISTINCT es.id) as count_mach from tbl_row_material rm "
    . "LEFT JOIN tbl_extrusion_log_sheet es ON es.id=rm.logsheet_id where es.c_date='$s_date' "
    . "group by es.type_of_machine order by es.id ASC";
$d1=$this->db->query($q1)->result();

$q2="select rm.row_material,SUM(rm.kg) as kg from tbl_row_material rm "
        . "LEFT JOIN tbl_extrusion_log_sheet es ON es.id=rm.logsheet_id "
        . "where es.c_date='$s_date' and es.type_of_machine='120mm' group by row_material order by rm.id ASC ";
$d2=$this->db->query($q2)->result();



// Excess 
$excess=array();
$excess1=array();
$excess2=array();
$excess3=array();

$act_cal='';
$act_cal1='';
$act_cal2='';
$act_cal3='';

$date_s='';
for($i=6;$i>-1;$i--)
 {
    $prev_date = date('Y-m-d', strtotime($s_date . - $i . 'day'));
     $date_s .= "'" . $prev_date . "',";
    $q3 = "select SUM(production) as production,SUM(reqd_cal_per) as reqd_cal_per,SUM(cons_kg) as cons_kg,c_date "
            . "from tbl_excess_less where type_of_machine='90mm' and c_date='$prev_date'"
            . "group by c_date order by c_date ASC ";
    $d3 = $this->db->query($q3)->row();
    $q4 = "select SUM(kg) as kg,c_date "
            . "from tbl_row_material where type_of_machine='90mm' and grade='AF' and c_date='$prev_date'"
            . "group by c_date order by c_date ASC ";
    $d4 = $this->db->query($q4)->row();
    if ($d3) {
        $act_cal = $d4->kg * 100 / $d3->production;
        $excess[] = number_format($act_cal - $d3->reqd_cal_per, 2);
    } else {
        $excess[] = 0;
    }


    $q5 = "select SUM(production) as production,SUM(reqd_cal_per) as reqd_cal_per,SUM(cons_kg) as cons_kg,c_date "
            . "from tbl_excess_less where type_of_machine='120mm' and c_date='$prev_date'"
            . "group by c_date order by c_date ASC ";
    $d5 = $this->db->query($q5)->row();
    $q6 = "select SUM(kg) as kg,c_date "
            . "from tbl_row_material where type_of_machine='120mm' and grade='AF' and c_date='$prev_date'"
            . "group by c_date order by c_date ASC ";
    $d6 = $this->db->query($q6)->row();

    if ($d5) {
        $act_cal1 = $d6->kg * 100 / $d5->production;
        $excess1[] = number_format($act_cal1 - $d5->reqd_cal_per, 2);
    } else {
        $excess1[] = 0;
    }


    $q7 = "select SUM(production) as production,SUM(reqd_cal_per) as reqd_cal_per,SUM(cons_kg) as cons_kg,c_date "
            . "from tbl_excess_less where type_of_machine='90mmold' and c_date='$prev_date'"
            . "group by c_date order by c_date ASC ";
    $d7 = $this->db->query($q7)->row();
    $q8 = "select SUM(kg) as kg,c_date "
            . "from tbl_row_material where type_of_machine='90mmold' and grade='AF' and c_date='$prev_date'"
            . "group by c_date order by c_date ASC ";
    $d8 = $this->db->query($q8)->row();

    if ($d7) {
        $act_cal2 = $d8->kg * 100 / $d7->production;
        $excess2[] = number_format($act_cal2 - $d7->reqd_cal_per, 2);
    } else {
        $excess2[] = 0;
    }


    $q9 = "select SUM(production) as production,SUM(reqd_cal_per) as reqd_cal_per,SUM(cons_kg) as cons_kg,c_date "
            . "from tbl_excess_less where type_of_machine='100mm' and c_date='$prev_date'"
            . "group by c_date order by c_date ASC ";
    $d9 = $this->db->query($q9)->row();
    $q10 = "select SUM(kg) as kg,c_date "
            . "from tbl_row_material where type_of_machine='100mm' and grade='AF' and c_date='$prev_date'"
            . "group by c_date order by c_date ASC ";
    $d10 = $this->db->query($q10)->row();

    if ($d9) {
        $act_cal3 = $d10->kg * 100 / $d9->production;
        $excess3[] = number_format($act_cal3 - $d9->reqd_cal_per, 2);
    } else {
        $excess3[] = 0;
    }
}
$excess= implode(",", $excess);
$excess1= implode(",", $excess1);
$excess2= implode(",", $excess2);
$excess3= implode(",", $excess3);


// Excess

// Wastage
$wastage=array();
$wastage1=array();
$wastage2=array();
$wastage3=array();

for($i=6;$i>-1;$i--)
 {
$prev_date = date('Y-m-d', strtotime($s_date . - $i . 'day'));
$a1="SELECT SUM(start_wastage) as start_wastage,SUM(run_wastage) as run_wastage,SUM(dress_wastage) as dress_wastage,type_of_machine  "
. "FROM `tbl_wastage` WHERE c_date='$prev_date' and type_of_machine='90mm' group by c_date";
$b1=$this->db->query($a1)->row();

$a2="SELECT SUM(kg) as kg,type_of_machine,c_date FROM `tbl_row_material` "
. "WHERE c_date='$prev_date' and type_of_machine='90mm' group by c_date ";
$b2=$this->db->query($a2)->row();
if($b2)
{$wastage[] = round(($b1->start_wastage + $b1->run_wastage + $b1->dress_wastage) / $b2->kg * 100,2);}
else {$wastage[] =0.00;}


$a3="SELECT SUM(start_wastage) as start_wastage,SUM(run_wastage) as run_wastage,SUM(dress_wastage) as dress_wastage,type_of_machine  "
. "FROM `tbl_wastage` WHERE c_date='$prev_date' and type_of_machine='120mm' group by c_date";
$b3=$this->db->query($a3)->row();

$a4="SELECT SUM(kg) as kg,type_of_machine,c_date FROM `tbl_row_material` "
. "WHERE c_date='$prev_date' and type_of_machine='120mm' group by c_date ";
$b4=$this->db->query($a4)->row();
if($b4)
{$wastage1[] = round(($b3->start_wastage + $b3->run_wastage + $b3->dress_wastage) / $b4->kg * 100,2);}
else {$wastage1[] =0.00;}

$a5="SELECT SUM(start_wastage) as start_wastage,SUM(run_wastage) as run_wastage,SUM(dress_wastage) as dress_wastage,type_of_machine  "
. "FROM `tbl_wastage` WHERE c_date='$prev_date' and type_of_machine='90mmold' group by c_date";
$b5=$this->db->query($a5)->row();

$a6="SELECT SUM(kg) as kg,type_of_machine,c_date FROM `tbl_row_material` "
. "WHERE c_date='$prev_date' and type_of_machine='90mmold' group by c_date ";
$b6=$this->db->query($a6)->row();
if($b6)
{$wastage2[] = round(($b5->start_wastage + $b5->run_wastage + $b5->dress_wastage) / $b6->kg * 100,2);}
else {$wastage2[] =0.00;}

$a7="SELECT SUM(start_wastage) as start_wastage,SUM(run_wastage) as run_wastage,SUM(dress_wastage) as dress_wastage,type_of_machine  "
. "FROM `tbl_wastage` WHERE c_date='$prev_date' and type_of_machine='100mm' group by c_date";
$b7=$this->db->query($a7)->row();

$a8="SELECT SUM(kg) as kg,type_of_machine,c_date FROM `tbl_row_material` "
. "WHERE c_date='$prev_date' and type_of_machine='100mm' group by c_date ";
$b8=$this->db->query($a8)->row();
if($b8)
{$wastage3[] = round(($b7->start_wastage + $b7->run_wastage + $b7->dress_wastage) / $b8->kg * 100,2);}
else {$wastage3[] =0.00;}


 
}
$wastage= implode(",", $wastage);
$wastage1= implode(",", $wastage1);
$wastage2= implode(",", $wastage2);
$wastage3= implode(",", $wastage3);


// Wastage


// BreakDown
$breakdown1=array();
$breakdown2=array();
$breakdown3=array();
$breakdown4=array();

for($i=6;$i>-1;$i--)
 {
$prev_date = date('Y-m-d', strtotime($s_date . - $i . 'day'));
 $c1="SELECT SUM( MINUTE( hrs ) ) AS minutes, SUM( hrs ) AS hours,c_date,type_of_machine,shift FROM `tbl_breakdown` "
. "WHERE c_date='$prev_date' and type_of_machine='90mm' group by c_date";
$e1=$this->db->query($c1)->row();
if($e1)
{$breakdown1[] = $e1->hours.".".$e1->minutes;}
else{$breakdown1[] = 0.00;}


$c2="SELECT SUM( MINUTE( hrs ) ) AS minutes, SUM( hrs ) AS hours,c_date,type_of_machine,shift FROM `tbl_breakdown` "
. "WHERE c_date='$prev_date' and type_of_machine='120mm' group by c_date";
$e2=$this->db->query($c2)->row();
if($e2)
{$breakdown2[] = $e2->hours.".".$e2->minutes;}
else{$breakdown2[] = 0.00;}

$c3="SELECT SUM( MINUTE( hrs ) ) AS minutes, SUM( hrs ) AS hours,c_date,type_of_machine,shift FROM `tbl_breakdown` "
. "WHERE c_date='$prev_date' and type_of_machine='90mmold' group by c_date";
$e3=$this->db->query($c3)->row();
if($e3)
{$breakdown3[] = $e3->hours.".".$e3->minutes;}
else{$breakdown3[] = 0.00;}

$c4="SELECT SUM( MINUTE( hrs ) ) AS minutes, SUM( hrs ) AS hours,c_date,type_of_machine,shift FROM `tbl_breakdown` "
. "WHERE c_date='$prev_date' and type_of_machine='100mm' group by c_date";
$e4=$this->db->query($c4)->row();
if($e4)
{$breakdown4[] = $e4->hours.".".$e4->minutes;}
else{$breakdown4[] = 0.00;}

 }
$breakdown1= implode(",", $breakdown1);
$breakdown2= implode(",", $breakdown2);
$breakdown3= implode(",", $breakdown3);
$breakdown4= implode(",", $breakdown4);
//print_r($breakdown1);
// BreakDown





$date_s= rtrim($date_s,',');
//echo $date_s;
?>

<div class="col-md-6" >
        <div class="panel panel-default" style="box-shadow: 5px 5px 5px 5px gray; height: 300px;">
            <div id="container" style="height: 300px;"></div>
        </div>
    </div>
<div class="col-md-6">
        <div class="panel panel-default" style="box-shadow: 5px 5px 5px 5px gray; height: 300px;">
            <div id="container1" style="height: 300px;"></div>
        </div>
    </div>
<div class="col-md-6">
        <div class="panel panel-default" style="box-shadow: 5px 5px 5px 5px gray;">
<!--            <div class="panel-heading ui-draggable-handle" style="background-color:  ">
            <div class="panel-title-box" >
                <h3><select class="select form-control excess_change">
                        <option value="90mm">90 mm</option>
                                        <option value="120mm">120 mm</option>
                                        <option value="90mmold">90 mm Old</option>
                                        <option value="100mm">100 mm</option>
                    </select></h3>
            </div>
                </div>-->
            <div id="append" ></div>
        </div>
    </div>
<div class="col-md-6">
        <div class="panel panel-default" style="box-shadow: 5px 5px 5px 5px gray;">
            
            <div id="append2" ></div>
        </div>
    </div>
<div class="col-md-12">
        <div class="panel panel-default" style="box-shadow: 5px 5px 5px 5px gray;">
            
            <div id="append3" ></div>
        </div>
    </div>
<input type="text" class="ss_date" value="<?php echo $s_date;?>" style="display: none;">
<script>
Highcharts.chart('container', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Plant Wise Production (Kg)'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.y:1f} Kg</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.y:1f}'
            }
        }
    },
    series: [{
        name: 'production',
        colorByPoint: true,
        data: [ 
            <?php 
foreach ($d1 as $row) { ?>
            {
           name: '<?php echo $row->type_of_machine;?>',
            y: <?php echo $row->kg_shift_total;?>
        },
<?php } ?>
        ]
    }]
});
</script>
<script>
Highcharts.chart('container1', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: '120mm Raw Material (Kg)'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.y:1f}'
            }
        }
    },
    series: [{
        name: 'kg',
        colorByPoint: true,
        data: [ 
            <?php 
foreach ($d2 as $row) {?>
            {
           name: '<?php echo $row->row_material;?>',
            y: <?php echo $row->kg;?>
        },
<?php } ?>
        ]
    }]
});
</script>
<script>




//$(document).on('change','.excess_change',function(e)
//{var plant=$(this).val();
//
//$("#loader").show();
//$("#loader").fadeOut(1000);
//$.ajax({
//type: "post",
//url: "<?php echo base_url('login_controller/excess_change'); ?>",
//data: {plant:plant,s_date:$('.ss_date').val()},
//cache: false,
//async: false,
//success: function (data) {
//   
//    $("#append").html(data)
//}
//});
//});

//$(document).on('change','.break_change',function(e)
//{var plant=$(this).val();
//
//$("#loader").show();
//$("#loader").fadeOut(1000);
//$.ajax({
//type: "post",
//url: "<?php echo base_url('login_controller/break_change'); ?>",
//data: {plant:plant,s_date:$('.ss_date').val()},
//cache: false,
//async: false,
//success: function (data) {
//   
//    $("#append2").html(data)
//}
//});
//});
//
//$(document).on('change','.wastage_change',function(e)
//{var plant=$(this).val();
//
//$("#loader").show();
//$("#loader").fadeOut(1000);
//$.ajax({
//type: "post",
//url: "<?php echo base_url('login_controller/wastage_change'); ?>",
//data: {plant:plant,s_date:$('.ss_date').val()},
//cache: false,
//async: false,
//success: function (data) {
//   
//    $("#append3").html(data)
//}
//});
//});
//
//function load_excess()
//{var plant="90mm";
////
////$("#loader").show();
////$("#loader").fadeOut(4000);
//$.ajax({
//type: "post",
//url: "<?php echo base_url('login_controller/excess_change'); ?>",
//data: {plant:plant,s_date:$('.ss_date').val()},
//cache: false,
//async: false,
//success: function (data) {
//   $("#append").html(data)
//}
//});
//}
//function load_excess1()
//{var plant="90mm";
////
////$("#loader").show();
////$("#loader").fadeOut(4000);
//$.ajax({
//type: "post",
//url: "<?php echo base_url('login_controller/break_change'); ?>",
//data: {plant:plant,s_date:$('.ss_date').val()},
//cache: false,
//async: false,
//success: function (data) {
//   $("#append2").html(data)
//}
//});
//}
//
//function load_excess2()
//{var plant="90mm";
////
////$("#loader").show();
////$("#loader").fadeOut(4000);
//$.ajax({
//type: "post",
//url: "<?php echo base_url('login_controller/wastage_change'); ?>",
//data: {plant:plant,s_date:$('.ss_date').val()},
//cache: false,
//async: false,
//success: function (data) {
//   $("#append3").html(data)
//}
//});
//}




//Highcharts.chart('append', {
//    chart: {
//        type: 'column'
//    },
//    title: {
//        text: 'Last 7 Days CaCo3 Excess Percentage'
//    },
//    subtitle: {
//        text: ''
//    },
//    xAxis: {
//        categories: [
//            <?php echo $date_s;?>
//        ],
//        crosshair: true
//    },
//    yAxis: {
//        min: 0,
//        title: {
//            text: 'Rainfall (mm)'
//        }
//    },
//    tooltip: {
//        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
//        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
//            '<td style="padding:0"><b>{point.y:.1f}%</b></td></tr>',
//        footerFormat: '</table>',
//        shared: true,
//        useHTML: true
//    },
//    plotOptions: {
//        column: {
//            pointPadding: 0.2,
//            borderWidth: 0
//        }
//    },
//    series: [
//{
//        name: '90 mm',
//        data: [<?php echo $excess;?>]
//
//    },
//{
//        name: '120 mm',
//        data: [<?php echo $excess1;?>]
//
//    },
//{
//        name: '90 mm old',
//        data: [<?php echo $excess2;?>]
//
//    },
//{
//        name: '100 mm',
//        data: [<?php echo $excess3;?>]
//
//    }
//    ]
//});



Highcharts.chart('append', {

    title: {
        text: 'Last 7 Days CaCo3 Excess/Less Percentage (%)'
    },

    subtitle: {
        text: ''
    },
        xAxis: {
        categories: [
            <?php echo $date_s;?>
        ],
        crosshair: true
    },

    yAxis: {
        title: {
            text: 'CaCo3 Excess/Less Percentage (%)'
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        series: {
            label: {
                connectorAllowed: false
            },
            //pointStart: 2010
        }
    },

    series: [
    {
        name: '90 mm',
        data: [<?php echo $excess;?>]

    },
{
        name: '120 mm',
        data: [<?php echo $excess1;?>]

    },
{
        name: '90 mm old',
        data: [<?php echo $excess2;?>]

    },
{
        name: '100 mm',
        data: [<?php echo $excess3;?>]

    }
    ],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});





Highcharts.chart('append2', {

    title: {
        text: 'Last 7 Days Wastage Percentage (%)'
    },

    subtitle: {
        text: ''
    },
        xAxis: {
        categories: [
            <?php echo $date_s;?>
        ],
        crosshair: true
    },

    yAxis: {
        title: {
            text: 'Percentage (%)'
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        series: {
            label: {
                connectorAllowed: false
            },
            //pointStart: 2010
        }
    },

    series: [
    {
        name: '90 mm',
        data: [<?php echo $wastage;?>]

    },
    {
        name: '120 mm',
        data: [<?php echo $wastage1;?>]

    },
    {
        name: '90 mm Old',
        data: [<?php echo $wastage2;?>]

    },
    {
        name: '100 mm',
        data: [<?php echo $wastage3;?>]

    }
    ],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});






Highcharts.chart('append3', {

    title: {
        text: 'Last 7 Days Break Down (HH:MM)'
    },

    subtitle: {
        text: ''
    },
        xAxis: {
        categories: [
            <?php echo $date_s;?>
        ],
        crosshair: true
    },

    yAxis: {
        title: {
            text: '(HH:MM)'
        }
    },
    legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
    },

    plotOptions: {
        series: {
            label: {
                connectorAllowed: false
            },
            //pointStart: 2010
        }
    },

    series: [
    {
        name: '90 mm',
        data: [<?php echo $breakdown1;?>]

    },
    {
        name: '120 mm',
        data: [<?php echo $breakdown2;?>]

    },
    {
        name: '90 mm Old',
        data: [<?php echo $breakdown3;?>]

    },
    {
        name: '100 mm',
        data: [<?php echo $breakdown4;?>]

    }
    ],

    responsive: {
        rules: [{
            condition: {
                maxWidth: 500
            },
            chartOptions: {
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                }
            }
        }]
    }

});




</script>
</body>
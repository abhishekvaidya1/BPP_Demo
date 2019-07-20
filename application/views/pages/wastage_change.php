
<?php


$prev_date = date('Y-m-d', strtotime($ss_date .' -7 day'));
 $q7="SELECT SUM(start_wastage) as start_wastage,SUM(run_wastage) as run_wastage,SUM(dress_wastage) as dress_wastage,type_of_machine  "
         . "FROM `tbl_wastage` WHERE c_date>='$prev_date' and c_date<='$ss_date' and type_of_machine='$plant_nm' group by c_date";
$d7=$this->db->query($q7)->result();

 $q8="SELECT SUM(kg) as kg,type_of_machine,c_date FROM `tbl_row_material` "
         . "WHERE c_date>='$prev_date' and c_date<='$ss_date' and type_of_machine='$plant_nm' group by c_date ";
$d8=$this->db->query($q8)->result();


$excess=array();
$act_cal='';
$date_s='';
$min1='';
for($i=0;$i<count($d8);$i++)
{
    $excess[] = round(($d7[$i]->start_wastage + $d7[$i]->run_wastage + $d7[$i]->dress_wastage) / $d8[$i]->kg * 100,2);
  $date_s.="'".$d8[$i]->c_date."',";
}
 $excess= implode(",", $excess);
$date_s= rtrim($date_s,',');
echo "&nbsp";
?>
<div id="container4" ></div>


<script>
    Highcharts.chart('container4', {
    chart: {
        type: 'column'
    },
    title: {
        text: '7 Days Wastage'
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
        min: 0,
        title: {
            text: 'Rainfall (mm)'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:1f} %</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
        name: '<?php echo $plant_nm;?>',
        data: [<?php echo $excess;?>]

    }]
});
    </script>
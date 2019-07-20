
<?php


$prev_date = date('Y-m-d', strtotime($ss_date .' -7 day'));
 $q5="SELECT SUM( MINUTE( hrs ) ) AS minutes, SUM( hrs ) AS hours,c_date,type_of_machine,shift FROM `tbl_breakdown` "
         . "WHERE c_date>='$prev_date' and c_date<='$ss_date' and type_of_machine='$plant_nm' group by c_date";
$d5=$this->db->query($q5)->result();


$excess=array();
$act_cal='';
$date_s='';
$min1='';
for($i=0;$i<count($d5);$i++)
{
    $excess[] = $d5[$i]->hours.".". $d5[$i]->minutes;
  $date_s.="'".$d5[$i]->c_date."',";
}
$excess= implode(",", $excess);
$date_s= rtrim($date_s,',');

echo "&nbsp";
?>
<div id="container3"></div>


<script>
    Highcharts.chart('container3', {
    chart: {
        type: 'column'
    },
    title: {
        text: '7 Days Break Down'
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
            '<td style="padding:0"><b>{point.y:1f} hh:mm</b></td></tr>',
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
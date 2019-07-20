<?php
$prev_date = date('Y-m-d', strtotime($ss_date .' -7 day'));
 $q3="select SUM(production) as production,SUM(reqd_cal_per) as reqd_cal_per,SUM(cons_kg) as cons_kg,c_date "
        . "from tbl_excess_less where type_of_machine='$plant_nm' and c_date>='$prev_date' and c_date<='$ss_date' "
        . "group by c_date order by c_date ASC ";
$d3=$this->db->query($q3)->result();
$q4="select SUM(kg) as kg,c_date "
        . "from tbl_row_material where type_of_machine='$plant_nm' and grade='AF' and c_date>='$prev_date' and c_date<='$ss_date' "
        . "group by c_date order by c_date ASC ";
$d4=$this->db->query($q4)->result();

$excess=array();
$act_cal='';
$date_s='';
for($i=0;$i<count($d3);$i++)
{
  $act_cal=$d4[$i]->kg * 100 / $d3[$i]->production; 
  $excess[]= number_format($act_cal - $d3[$i]->reqd_cal_per,2);
  $date_s.="'".$d3[$i]->c_date."',";
}
$excess= implode(",", $excess);
$date_s= rtrim($date_s,',');
echo "&nbsp";
?>
<div id="container2"></div>


<script>
    Highcharts.chart('container2', {

    title: {
        text: '7 Dasy CaCo3 Excess/Less Percentage'
    },

    subtitle: {
        text: ''
    },

    yAxis: {
        title: {
            text: ''
        }
    },
    xAxis: {
     //min: 1,
     categories: [<?php echo $date_s;?>]
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
           // pointStart: 2010
        }
    },

    series: [
    {
        name: '<?php echo $plant_nm;?>',
        data: [<?php echo $excess;?>]
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
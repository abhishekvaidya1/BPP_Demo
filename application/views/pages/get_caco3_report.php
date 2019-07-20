 <?php 
 $prodn=array();
 $reqd=array();
 $cons=array();
 
 $t_prodn=array();
 
 $t_s=array();
 $t_r=array();
 $t_d=array();
 
 $t_reqd=array();
 
 $t_kg_af=array();


for($a=0;$a<count($d6);$a++)
{
 $ab= explode(",", $d6[$a]->count_mach);
 for($b=0;$b<count($ab);$b++)
 {
  $t_prodn[]=$d6[$a]->kg_shift_total;   
 }
}

for($a=0;$a<count($d7);$a++)
{
 $ab= explode(",", $d7[$a]->count_mach);
 for($b=0;$b<count($ab);$b++)
 {
  $t_s[]=$d7[$a]->start_wastage;   
  $t_r[]=$d7[$a]->run_wastage;   
  $t_d[]=$d7[$a]->dress_wastage;   
 }
}

for($a=0;$a<count($d8);$a++)
{
 $ab= explode(",", $d8[$a]->count_mach);
 for($b=0;$b<count($ab);$b++)
 {
  $t_reqd[]=$d8[$a]->caco3;   
  
 }
}

for($a=0;$a<count($d9);$a++)
{
 $ab= explode(",", $d9[$a]->count_mach);
 for($b=0;$b<count($ab);$b++)
 {
  $t_kg_af[]=$d9[$a]->kg;   
  
 }
}
//echo "<pre>";print_r($t_kg_af);

    for($j=0;$j<count($d1);$j++)
    { ?>
<table class="table table-bordered">
    <tr>
        <th colspan="14" style=" border: none; "><h2>PLANT <?php echo $d5[$j]->type_of_machine;?></h2></th>
        
    </tr>
    <tr>
        <th>SHIFT : &nbsp;&nbsp;&nbsp;<?php echo $d5[$j]->shift;?></th>
        <th colspan="13" style=" text-align:  center;">OPERATOR : <?php echo $d5[$j]->operator_name;?></th>
    </tr>
    <tr>
        <th style="text-align: center;" rowspan="2">SR.NO.</th>
        <th style="text-align: center;" rowspan="2">DENIER</th>
        <th style="text-align: center;" rowspan="2">PRODN</th>
        <th colspan="5" style="text-align: center;">WASTAGE</th>
        <th style="text-align: center; border-bottom-color:  white;">Reqd Cal.</th>
        <th style="text-align: center; border-bottom-color:  white;">Cons.</th>
        <th style="text-align: center; border-bottom-color:  white;" >Act. Cal.</th>
        <th style="text-align: center; border-bottom-color:  white;">Cons.</th>
        <th style="text-align: center; border-bottom-color:  white;" colspan="2">EXCESS/LESS</th>
<!--        <th style="text-align: center; border-bottom-color:  white;"></th>-->
</tr>
<tr>
   
    
    <th style="text-align: center;">S.</th> 
    <th style="text-align: center;">R.</th> 
    <th style="text-align: center;">D.</th> 
    <th style="text-align: center;">T.W..</th> 
    <th style="text-align: center;">%</th> 
    <th style="text-align: center;">%</th> 
    <th style="text-align: center;">Kg</th> 
    <th style="text-align: center;">%</th> 
    <th style="text-align: center;">Kg</th> 
    <th style="text-align: center;">%</th> 
    <th style="text-align: center;">Kg</th> 
</tr>
        <?php $m=1;for($k=0;$k<count($d2[$j]);$k++)
        {?>
<tr>
<th style="text-align: center;"><?php echo $m;?></th>
<th style="text-align: center;"><?php echo $d2[$j][$k]->denier;?></th>

<th style="text-align: center;"><?php echo $prodn[]=$d2[$j][$k]->output_kg;?></th>
<th colspan="5" rowspan=""></th>

<th style="text-align: center;"><?php echo $reqd[]= number_format($d2[$j][$k]->caco3,2);?></th>
<th style="text-align: center;"><?php echo $cons[]= number_format($d2[$j][$k]->caco3 / 100 * $d2[$j][$k]->output_kg,2);?></th>
        <?php $m++;}  ?></tr>
<tr>
    <th colspan="2">Tot.- Shift &nbsp;&nbsp;&nbsp;<?php echo $d1[$j]->shift;?></th>
    <th style="text-align: center;"><?php echo array_sum($prodn);?></th>
    <?php if(isset($d3)) {?>
    <th style="text-align: center;"><?php echo number_format(@$d3[$j]->start_wastage,2);?></th>
    <th style="text-align: center;"><?php echo number_format(@$d3[$j]->run_wastage,2);?></th>
    <th style="text-align: center;"><?php echo number_format(@$d3[$j]->dress_wastage,2);?></th>
    <th style="text-align: center;"><?php echo number_format(@$d3[$j]->start_wastage + @$d3[$j]->run_wastage + @$d3[$j]->dress_wastage,2);?></th>
    <th style="text-align: center;"><?php echo number_format((@$d3[$j]->start_wastage + @$d3[$j]->run_wastage + @$d3[$j]->dress_wastage) / array_sum($prodn) * 100,2);?></th>
    <?php } ?>
    <th style="text-align: center;"><?php echo number_format(array_sum($reqd),2);?>%</th>
    <th style="text-align: center;"><?php echo number_format(array_sum($cons),2);?></th>
    <?php if(@$d4[$j]->kg_ag_total){?>
    <th style="text-align: center;"><?php echo number_format($d4[$j]->kg_ag_total / array_sum($prodn) * 100 ,2);?>%</th>
    <th style="text-align: center;"><?php echo number_format($d4[$j]->kg_ag_total ,2);?></th>
    <th style="text-align: center;"><?php echo number_format(($d4[$j]->kg_ag_total / array_sum($prodn) * 100) - (array_sum($reqd)),2);?>%</th>
    <th style="text-align: center;"><?php echo number_format(($d4[$j]->kg_ag_total) - (array_sum($cons)),2);?></th>
    <?php } else { ?>
    <th style="text-align: center;">0.00%</th>
    <th style="text-align: center;">0.00</th>
    <th style="text-align: center;">0.00%</th>
    <th style="text-align: center;">0.00</th>
    <?php } ?>
</tr>

<!--<tr>
    <th colspan="2">Tot.Plt.:</th>
    <th style="text-align: center;"><?php echo $t_prodn[$j];?></th>
    <th style="text-align: center;"><?php echo $t_s[$j];?></th>
    <th style="text-align: center;"><?php echo $t_r[$j];?></th>
    <th style="text-align: center;"><?php echo $t_d[$j];?></th>
    <th style="text-align: center;"><?php echo $t_s[$j]+$t_r[$j]+$t_d[$j];?></th>
    <th style="text-align: center;"><?php echo number_format(($t_s[$j]+$t_r[$j]+$t_d[$j]) / $t_prodn[$j] * 100,2);?></th>
    <th style="text-align: center;"><?php echo number_format($t_reqd[$j],2);?>%</th>
    <th style="text-align: center;"><?php echo number_format(($t_reqd[$j] / 100 * $t_prodn[$j]),2);?></th>
    <th style="text-align: center;"><?php echo number_format((@$t_kg_af[$j] / $t_prodn[$j] * 100),2);?>%</th>
    <th style="text-align: center;"><?php echo number_format(@$t_kg_af[$j],2);?></th>
    <th style="text-align: center;"><?php echo number_format(((@$t_kg_af[$j] / $t_prodn[$j] * 100)-$t_reqd[$j]),2);?>%</th>
    <th style="text-align: center;"><?php echo number_format(((@$t_kg_af[$j])-($t_reqd[$j]/ 100 * $t_prodn[$j])),2);?></th>
</tr>-->

</table>
<?php unset($prodn);unset($reqd);unset($cons);} 

?>



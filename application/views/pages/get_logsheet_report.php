
<?php
if($d1) {
    $total=array();
    foreach($d3 as $row) {
$total[]=$row->output_kg;
    }
?>
<table style=" width: 100%;">
    <tr><th colspan="<?php echo count($d2)+1;?>" style=" text-align: center;" align="center"><label style=" font-size: 25px;">BANG POLYPACK<br>EXTRUSION LOG SHEET</label></th></tr>
</table>
<br>
<table class="table table-bordered">
    <tr>
        <th style=" background-color:  lightgray;">Raw Material</th>
        <?php
        foreach($d2 as $row) {?>
        <th style=" text-align: center;"><?php echo $row->row_material;?></th>
<?php } ?>
    </tr>
    <tr>
        <th style=" background-color:  lightgray;">Grade</th>
        <?php
        foreach($d2 as $row) {?>
        <th style=" text-align: center;"><?php echo $row->grade;?></th>
<?php } ?>
    </tr>
    <tr>
        <th style=" background-color:  lightgray;">Bags</th>
        <?php
        foreach($d2 as $row) {?>
        <th style=" text-align: center;"><?php echo $row->bags;?></th>
<?php } ?>
    </tr>
    <tr>
        <th style=" background-color:  lightgray;">Kg</th>
         <?php
        foreach($d2 as $row) {?>
        <th style=" text-align: center;"><?php echo $row->kg;?></th>
<?php } ?>
    </tr>
    <tr>
        <th style=" background-color:  lightgray;">% Mixing</th>
        <?php
        foreach($d2 as $row) {?>
        <th style=" text-align: center;"><?php echo $row->per_mixing;?></th>
<?php } ?>
    </tr>
    <tr>
        <?php
        if($d2[0]->total_production==array_sum($total)) {
        ?>
        <th colspan="<?php echo count($d2)+1;?>" style=" text-align: center; color: green;">Total Production :- &nbsp;&nbsp;<?php echo $d2[0]->total_production;?> Kg</th>
        <?php } else {?>
        <th colspan="<?php echo count($d2)+1;?>" style=" text-align: center; color: red;">Total Production :- &nbsp;&nbsp;<?php echo $d2[0]->total_production;?> Kg</th>
        <?php } ?>
    </tr>
</table>
<table class="table table-bordered">
    <tr style="background-color:  lightgray;">
        <th style=" text-align: center;">Production for Denier</th>
        <th style=" text-align: center;">Type Tape</th>
        <th style=" text-align: center;">CaCo3 %</th>
        <th style=" text-align: center;">Marking</th>
        <th style=" text-align: center;">Output (Kg)</th>
    </tr>
    <?php
    
    foreach($d3 as $row) {?>
    <tr>
        <th style=" text-align: center;"><?php echo $row->denier;?></th>
        <th style=" text-align: center;"><?php echo $row->tape_type;?></th>
        <th style=" text-align: center;"><?php echo $row->caco3;?></th>
        <th style=" text-align: center;"><?php echo $row->marking;?></th>
        <th style=" text-align: center;"><?php echo $row->output_kg;?></th>
    </tr>
    <?php }?>
    <tr>
        <?php
        if($d2[0]->total_production==array_sum($total)) {
        ?>
        <th colspan="5" style=" text-align: center; color: green;">Total Production :- &nbsp;&nbsp;<?php echo array_sum($total);?> Kg</th>
        <?php } else {?>
        <th colspan="5" style=" text-align: center; color: red;">Total Production :- &nbsp;&nbsp;<?php echo array_sum($total);?> Kg</th>
        <?php } ?>
    </tr>
</table>

<table class="table table-bordered">
    <tr style="background-color:  lightgray;">
        <th style=" text-align: center;">Staring</th>
        <th style=" text-align: center;">Running</th>
        <th style=" text-align: center;">Dressing</th>
        <th style=" text-align: center;">Total</th>
        <th style=" text-align: center;">%</th>
    </tr>
    <?php
if($d4) {
?>
    <tr>
        <th style=" text-align: center;"><?php echo $d4->start_wastage;?></th>
        <th style=" text-align: center;"><?php echo $d4->run_wastage;?></th>
        <th style=" text-align: center;"><?php echo $d4->dress_wastage;?></th>
        <th style=" text-align: center;"><?php echo $d4->start_wastage+$d4->run_wastage+$d4->dress_wastage;?></th>
        <th style=" text-align: center;"><?php echo round(($d4->start_wastage+$d4->run_wastage+$d4->dress_wastage) / array_sum($total) * 100,2);?></th>
    </tr>
    <?php } ?>
</table>

<table class="table table-bordered">
    <tr style="background-color:  lightgray;">
        <th style=" text-align: center;">Hrs</th>
        <th style=" text-align: center;">Reason</th>
    </tr>
    <?php
    
    foreach($d5 as $row) {?>
    <tr>
        <th style=" text-align: center;"><?php echo $row->hrs;?></th>
        <th style=" text-align: center;"><?php echo $row->reasons;?></th>
    </tr>
    <?php } ?>
</table>

<?php } ?>
<?php
$raw=array();
$grade=array();

$bags_qty=array();

?>

<table class="table table-bordered">
    <tr>
        <th>Raw Material</th>
        <?php
        foreach($data as $row) {?>
        <th><input type="text" class=" form-control" name="row_material[]" value="<?php echo $row->material_desc;?>" readonly="" style="color: black; width: 100px;"></th>
<?php } ?>
    </tr>
    <tr>
        <th>Grade</th>
        <?php
        foreach($data as $row) {?>
        <th><input type="text" class=" form-control" name="grade[]" value="<?php echo $row->grade;?>" readonly="" style="color: black; width: 100px;"></th>
<?php } ?>
    </tr>
    <tr style=" display: none;">
        <th>Qty</th>
        <?php
        $j=1;
        foreach($data as $row) {?>
        <th><input type="text" class=" form-control"  id="qty_<?php echo $j;?>" value="<?php echo $row->bags_qty;?>" style="color: black; width: 100px;"></th>
<?php $j++;} ?>
    </tr>
    <tr>
        <th>Bags</th>
        <?php
        $i=1;
        foreach($data as $row) {?>
        <th><input type="text" class=" form-control kg_calculate" name="bags[]" id="bags_<?php echo $i;?>"  style="color: black; width: 100px;"></th>
<?php $i++;} ?>
    </tr>
    <tr>
        <th>Kg</th>
        <?php
        $k=1;foreach($data as $row) {?>
        <th><input type="text" class=" form-control total_kg_product" name="kg[]"  id="kg_<?php echo $k;?>" style="color: black; width: 100px;" readonly=""></th>
<?php $k++;} ?>
    </tr>
    <tr>
        <th>% Mixing</th>
        <?php
        foreach($data as $row) {?>
        <th><input type="text" class=" form-control" name="per_mixing[]" value="" style="color: black; width: 100px;"></th>
<?php } ?>
    </tr>
</table>

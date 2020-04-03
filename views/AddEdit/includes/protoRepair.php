<?
    $repairRow = [
        'isHidden' => 'hidden',
        'idProto' => 'protoRepairs',

    ];
if ( !$isRepairProto )
{
    $repairRow = [
        'isHidden' => '',
        'idProto' => '',
    ];
    $repairRow['which'] = $whichRepair ? 'repairsJew' : 'repairs3d';
    $repairRow['whichName'] = $whichRepair ? 'Ремонт Модельера-доработчика №' : '3Д Ремонт №';
    $repairRow['panelColor'] = $whichRepair ? 'panel-success' : 'panel-info';
    $repairRow['panelIcon'] = $whichRepair ? 'wrench' : 'cog';
    $repairRow['descrName'] = $whichRepair ? 'repairs[jew][description][]' : 'repairs[3d][description][]';

    $repairRow['repairsID_name'] = $whichRepair ? 'repairs[jew][id][]' : 'repairs[3d][id][]';
    $repairRow['repairsNum_name'] = $whichRepair ? 'repairs[jew][num][]' : 'repairs[3d][num][]';
    $repairRow['repairsWhich_name'] = $whichRepair ? 'repairs[jew][which][]' : 'repairs[3d][which][]';

    $repairRow['repairsCostName'] = $whichRepair ? 'repairs[jew][cost][]' : 'repairs[3d][cost][]';

    if ( isset($repair['rep_num']) ) $repairRow['number'] = $repair['rep_num'];
    if ( isset($repair['date']) ) $repairRow['date'] = $repair['date'];
    if ( isset($repair['repair_descr']) ) $repairRow['description'] = $repair['repair_descr'];
    if ( isset($repair['id']) ) $repairRow['id'] = $repair['id'];
    if ( isset($repair['cost']) ) $repairRow['cost'] = $repair['cost'];
}
?>
<div id="<?=$repairRow['idProto']?>" class="panel <?=$repairRow['panelColor']?> <?=$repairRow['isHidden']?> <?=$repairRow['which']?>" style="margin-top: 10px;">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-<?=$repairRow['panelIcon']?>" style="color:green;"></span>
        <strong>
            <span class="repairs_name"><?=$repairRow['whichName']?></span><span class="repairs_number"><?=$repairRow['number']?></span>
            от - <span class="repairs_date"><?=date_create( $repairRow['date'] )->Format('d.m.Y')?></span>
        </strong>
        <? if ( !$isView ): ?>
        <button onclick="removeRepairs(this);" class="btn btn-sm btn-default pull-right" style="top:-5px !important; position:relative;" type="button" title="Удалить Ремонт">
            <span class="glyphicon glyphicon-remove"></span>
        </button>
        <?endif;?>
    </div>
    <textarea <?= !$isView ? '' : 'readonly'?> class="form-control repairs_descr" rows="3" name="<?=$repairRow['descrName']?>"><?=$repairRow['description']?></textarea>
    <input type="hidden" class="repairs_id"  name="<?=$repairRow['repairsID_name']?>" value="<?=$repairRow['id']?>"/>
    <input type="hidden" class="repairs_num" name="<?=$repairRow['repairsNum_name']?>" value="<?=$repairRow['number']?>"/>
    <input type="hidden" class="repairs_which" name="<?=$repairRow['repairsWhich_name']?>" value="<?= $whichRepair ? 1 : 0?>"/>

    <? if ( !$isView ): ?>
    <div class="row repairsPayment <?= $whichRepair ? '' : 'hidden'?>" style="margin: 5px 10px 0 10px;">
        <div class="col-xs-4">
            <label for="model_type" class="">
                <span class="glyphicon glyphicon-usd"></span> Стоимость:
                <input type="number" class="form-control repairCost" name="<?=$repairRow['repairsCostName']?>" value="<?=$repairRow['cost']?>">
            </label>
        </div>
        <div class="col-xs-8">
            <br>
            <button onclick="paidRepair(this);" class="btn btn-default pull-right" style="top:-5px !important; position:relative;" type="button">
                <i class="far fa-credit-card"></i> Отметить ремонт оплаченым
            </button>
        </div>
    </div>
    <?endif;?>
</div>
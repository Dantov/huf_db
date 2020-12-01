<?php
/**
 * Date: 28.11.2020
 * Time: 23:14
 */
if ( !isset($whichRepair) ) exit;

switch ($whichRepair)
{
    case 0:
    {
        $repairRow['whichID']     =  'repairs3d';
        $repairRow['whichName']   =  'Ремонт 3Д модели №';
        $repairRow['panelColor']  =  '#5fd7f5';
        $repairRow['panelIcon']   =  'fa-draw-polygon';

        $repairRow['sender']      = 'repairs[3d][sender][]';
        $repairRow['toWhom']      = 'repairs[3d][toWhom][]';
        $repairRow['descrName']   = 'repairs[3d][repair_descr][]';
        $repairRow['descrNeed']   = 'repairs[3d][descrNeed][]';
        $repairRow['status']      = 'repairs[3d][status][]';

        $repairRow['id']    = 'repairs[3d][id][]';
        $repairRow['num']   = 'repairs[3d][num][]';
        $repairRow['which'] = 'repairs[3d][which][]';

        $repairRow['3DIds']      = 'repairs[3d][prices][mp3DIds][]';
        $repairRow['gs3DPoints'] = 'repairs[3d][prices][gs3Dpoints][]';
        $repairRow['gs3DIds']    = 'repairs[3d][prices][gs3Dids][]';
    }
    break;
    case 1:
    {
        $repairRow['whichID']     =  'repairsJew';
        $repairRow['whichName']   =  'Ремонт Мастер модели №';
        $repairRow['panelColor']  =  '#c1b467';
        $repairRow['panelIcon']   =  'fa-screwdriver';

        $repairRow['sender']      = 'repairs[jew][sender][]';
        $repairRow['toWhom']      = 'repairs[jew][toWhom][]';
        $repairRow['descrName']   = 'repairs[jew][repair_descr][]';
        $repairRow['descrNeed']   = 'repairs[jew][descrNeed][]';
        $repairRow['status']      = 'repairs[jew][status][]';

        $repairRow['id']    = 'repairs[jew][id][]';
        $repairRow['num']   = 'repairs[jew][num][]';
        $repairRow['which'] = 'repairs[jew][which][]';

        $repairRow['3DIds']      = 'repairs[jew][prices][mp3DIds][]';
        $repairRow['gs3DPoints'] = 'repairs[jew][prices][gs3Dpoints][]';
        $repairRow['gs3DIds']    = 'repairs[jew][prices][gs3Dids][]';
    }
    break;
    case 2:
    {
        $repairRow['whichID']     =  'repairsProd';
        $repairRow['whichName']   =  'Ремонт модели на производстве №';
        $repairRow['panelColor']  =  '#c2b497';
        $repairRow['panelIcon']   =  'fa-hammer';

        $repairRow['sender']      = 'repairs[prod][sender][]';
        $repairRow['toWhom']      = 'repairs[prod][toWhom][]';
        $repairRow['descrName']   = 'repairs[prod][repair_descr][]';
        $repairRow['descrNeed']   = 'repairs[prod][descrNeed][]';
        $repairRow['status']      = 'repairs[prod][status][]';

        $repairRow['id']    = 'repairs[prod][id][]';
        $repairRow['num']   = 'repairs[prod][num][]';
        $repairRow['which'] = 'repairs[prod][which][]';

        $repairRow['3DIds']      = 'repairs[prod][prices][mp3DIds][]';
        $repairRow['gs3DPoints'] = 'repairs[prod][prices][gs3Dpoints][]';
        $repairRow['gs3DIds']    = 'repairs[prod][prices][gs3Dids][]';
    }
    break;
}

$masterLI = $whichRepair ? $jewelerNameLi : $mod3DLi;
$panelID = "allRepairs_" . $repair['id'];
$collapseID = "repairCollapse_" . $repair['id'];
?>
<div class="panel panel-default <?= $repairRow['whichID'] ?>" id="<?=$panelID?>">
    <div class="panel-heading cursorPointer" style="background-color: <?=$repairRow['panelColor']?>!important;">
        <i class="fas <?=$repairRow['panelIcon']?>"></i>
        <strong>
            <span class="repairs_name"><?=$repairRow['whichName']?></span>
            <span class="repairs_number"><?=$repair['rep_num']?></span>
            от - <span class="repairs_date"><?=date_create( $repair['date'] )->Format('d.m.Y')?></span>
        </strong>
        <button class="btn btn-sm btn-danger pull-right removeRepair" data-repType="<?=$repairRow['whichID']?>" style="top:-5px !important; position:relative;" type="button" title="Удалить Ремонт">
            <span class="glyphicon glyphicon-remove"></span>
        </button>
    </div>
    <div id="<?= $collapseID ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?=$panelID?>" aria-expanded="false" style="height: 0;">
        <ul class="list-group">
            <li class="list-group-item list-group-item-info text-center">
                <i class="far fa-paper-plane"></i> <b><i>Отправитель</i></b>
            </li>
        </ul>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-4">
                    <label for="sender_3dRep" title=""><span class="glyphicon glyphicon-user"></span> Технолог (кто отправил в ремонт):</label>
                    <div class="input-group">
                        <input required="" type="text" class="form-control sender" name="<?=$repairRow['sender']?>" value="<?=$repair['sender']?>">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a elemtoadd="">Дзюба В.М.</a></li>
                                <li><a elemtoadd="">Занин В.А.</a></li>
                                <li><a elemtoadd="">Бондаренко А.</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-4">
                    <label for="toWhom_3dRep" title=""><span class="glyphicon glyphicon-user"></span> Мастер (кто будет делать):</label>
                    <div class="input-group">
                        <input required="" type="text" class="form-control toWhom" name="<?=$repairRow['toWhom']?>" value="<?=$repair['toWhom']?>">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right toWhomList">
                                <?=$masterLI?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <label for="repairs_descr_need" class=""><span class="glyphicon glyphicon-comment"></span> Причина ремонта (что нужно сделать): </label>
                    <textarea class="form-control repairs_descr_need" rows="2" name="<?=$repairRow['descrNeed']?>"><?=$repair['descrNeed']?></textarea>
                    <input type="hidden" class="repairs_id"  name="<?=$repairRow['id']?>" value="<?=$repair['id']?>"/>
                    <input type="hidden" class="repairs_num" name="<?=$repairRow['num']?>" value="<?=$repair['rep_num']?>"/>
                    <input type="hidden" class="repairs_which" name="<?=$repairRow['which']?>" value="<?= $whichRepair ? 1 : 0?>"/>
                </div>
            </div>
        </div>
        <ul class="list-group">
            <li class="list-group-item list-group-item-success text-center">
                <i class="fas fa-bezier-curve"></i> <b><i>Мастер</i></b>
            </li>
            <li class="list-group-item">
                <label for="repairs_descr_done" class=""><span class="glyphicon glyphicon-comment"></span> Описание (что сделано): </label>
                <textarea class="form-control repairs_descr_done" rows="2" name="<?=$repairRow['descrName']?>"><?=$repair['repair_descr']?></textarea>
            </li>
            <li class="list-group-item">
                <i class="fas fa-dollar-sign"></i>
                <strong><i>Стоимость ремонта</i></strong>
                <?php if ( !$this->isCredited($repair['prices']??[], 8) ): ?>
                    <button class="btn btn-sm btn-default pull-right repairPriceAdd" style="top:-5px !important; position:relative;" data-toggle="modal" data-target="#repairPricesModal" type="button" title="Добавить оценку">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                <?php endif; ?>
            </li>
        </ul>
        <table class="table">
            <thead>
                <tr class="thead11">
                    <th>№</th>
                    <th width="30%">Название</th>
                    <th width="30%">Стоимость</th>
                    <th>Статус</th>
                    <th></th>
                </tr>
            </thead>
            <tbody class="<?=$repairRow['whichID']?>">
            <!-- // автозаполнение -->
            <?php $pr_total = 0; $repairPriceStatus = 0; $repairPricePaid = 0; $priceNum = 0; ?>
            <?php foreach ( $repair['prices']??[] as $repairPrice ): ?>
                <?php if ( (int)$repairPrice['is3d_grade'] !== 8 ) continue; ?>
                <tr data-gradeID="<?= $repairPrice['gs_id'] ?>">
                    <td style="width: 30px"><?= ++$priceNum ?></td>
                    <td>
                        <?php $mpTitle = $repairPrice['cost_name']; foreach ( $gradingSystem as $gsRow ) if ( $gsRow['id'] == $repairPrice['gs_id'] ) $mpTitle = $gsRow['description']; ?>
                        <div class="cursorPointer lightUpGSRow" data-toggle="tooltip" data-placement="bottom" title="<?=$mpTitle?>" style="width: 100%">
                            <?=$repairPrice['cost_name'] ?>
                        </div>
                    </td>
                    <td>
                        <?= $repairPrice['value'] ?>
                        <input hidden class="hidden" value="<?= $repairPrice['value'] ?>" name="<?= $repairRow['gs3DPoints'] ?>">
                    </td>
                    <?php $pr_total += $repairPrice['value']; ?>
                    <td>
                        <?php if ( !$this->isCredited($repair['prices'], 8) ): ?>
                            <input hidden class="hidden" value="<?= $repairPrice['id'] ?>"    name="<?= $repairRow['3DIds'] ?>">
                            <input hidden class="hidden" value="<?= $repairPrice['gs_id'] ?>" name="<?= $repairRow['gs3DIds'] ?>">
                        <?php endif; ?>
                        <?php $repairPriceStatus = (int)$repairPrice['status'] ?>
                        <?php $repairPricePaid = (int)$repairPrice['paid'] ?>
                    </td>
                    <td style="width:100px;">
                        <?php if ( !$repairPriceStatus ): ?>
                            <button class="btn btn-sm btn-default repDellGrade" type="button" title="Удалить Оценку">
                                <span class="glyphicon glyphicon-trash"></span>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr class="active text-bold t-total">
                <td style="width: 30px"></td>
                <td>Всего: </td>
                <td><?= $pr_total; ?></td>
                <?php $wholeTotal += $pr_total; ?>
                <td>
                    <?php if ( $priceNum ): ?>
                        <?php if ( $repairPriceStatus === 1 ): ?>
                            <span class="label label-primary ">Зачислено!</span>
                        <?php else: ?>
                            <span class="label label-default ">Не зачислено!</span>
                        <?php endif; ?>
                        <?php if ( $repairPricePaid === 1 ): ?>
                            <span class="label label-success ">Оплачено!</span>
                        <?php else: ?>
                            <span class="label label-default ">Не Оплачено!</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td style="width:100px;"></td>
            </tr>
            </tbody>
        </table>
        <ul class="list-group">
            <li class="list-group-item pt-0"></li>
        </ul>
        <div class="panel-footer">
            <div class="row">
                <div class="col-xs-12 col-sm-2">
                    <span class="pull-right pt-1">
                        <span class="glyphicon glyphicon-ok"></span> <b><i>Статус ремонта: </i></b>
                    </span>
                </div>
                <div class="col-xs-12 col-sm-10">
                    <select class="form-control repairStatus" name="<?=$repairRow['status']?>">
                        <option data-repairFor="id_repair" <?= $repair['status'] == 1 ? 'selected':'' ?> value="1" title="Новый ремонт. Недавно создан.">Новый</option>
                        <option data-repairFor="id_repair" <?= $repair['status'] == 2 ? 'selected':'' ?> value="2" title="Создан. Ожидает принятия в работу.">Ожидает принятия</option>
                        <option data-repairFor="id_repair" <?= $repair['status'] == 3 ? 'selected':'' ?> value="3" title="Принят в работу. Над ним сейчас трудится мастер">В работе</option>
                        <option data-repairFor="id_repair" <?= $repair['status'] == 4 ? 'selected':'' ?> value="4" title="Ремонт завершен">Завершен</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="panel panel-default" style="margin-top: 10px">
    <div class="panel-heading">
        <i class="fas fa-cubes"></i>
        <strong> Материал изделия:</strong> <span id="model_material" class="hidden err-notice"></span>
        <button id="addMats" class="btn btn-sm btn-default pull-right" style="top:-5px !important; position:relative;" type="button" title="Добавить материал">
            <span class="glyphicon glyphicon-plus"></span>
        </button>
    </div>
    <table class="table <?= !count($materials?:[])?'hidden':'' ?>">
        <thead>
            <tr class="thead11">
                <th>Деталь</th><th>Метал</th><th>Проба</th><th>Цвет Метала</th><th>Покрытие</th><th>Площадь</th><th>Цвет Покрытия</th><th>Обработка</th><th></th>
            </tr>
        </thead>
        <tbody id="metals_table" <?php $switchTableRow = "materialsFull"?>>
        <?php foreach ( $materials?:[] as $materialRow ): // автозаполнение если добавляем комплект или редакт модель ?>
            <?php require _viewsDIR_."AddEdit/includes/protoRows.php"?>
        <?php endforeach; ?>
        <? if( isset($materialRow) ) unset($materialRow); ?>
        </tbody>
    </table>
</div>
<?php

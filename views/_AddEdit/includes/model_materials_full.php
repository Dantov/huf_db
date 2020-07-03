<div class="panel panel-default">
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
                <th>Деталь</th>
                <th>Метал</th>
                <th>Проба</th>
                <th class="brr-2-secondary">Цвет Метала</th>
                <th title="Тип покрытия">Покрытие</th>
                <th title="Место куда будет нанесено покрытие">Место</th>
                <th>Цвет Покрытия</th>
                <th title="Дополнительная обработка детали">Обработка</th>
                <th></th>
            </tr>
        </thead>
        <tbody id="metals_table" <?php $switchTableRow = "materialsFull" ?> >
        <?php foreach ( $materials?:[] as $materialRow ): // автозаполнение если добавляем комплект или редакт модель ?>
            <?php require _viewsDIR_."_AddEdit/includes/protoRows.php"?>
        <?php endforeach; ?>
        <?php if( isset($materialRow) ) unset($materialRow); ?>
        </tbody>
    </table>
</div>
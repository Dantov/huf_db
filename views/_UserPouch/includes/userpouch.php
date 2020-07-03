<div class="row">
    <p class="lead text-info text-center"><span class="glyphicon glyphicon-piggy-bank" aria-hidden="true"></span> Кошелёк Работника</p>
    <div class="col-xs-12 stats_table">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="<?= $tab == 'all'? 'active':'' ?>"><a href="/user-pouch/?tab=1">Все Модели</a></li>
            <li role="presentation" class="<?= $tab == 'paid'? 'active':'' ?>"><a href="/user-pouch/?tab=2" >Оплаченные</a></li>
            <li role="presentation" class="<?= $tab == 'notpaid'? 'active':'' ?>"><a href="/user-pouch/?tab=3">Не оплаченные</a></li>
            <li role="presentation" class=""><a href="#statistic" aria-controls="statistic" role="tab" data-toggle="tab">Статистика</a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active in fade" id="allModels">
                <p></p>
                <div class="row pl-3 pr-3">

                    <?php $wholeTotal = 0; ?>
                    <?php foreach ( $stockInfo??[] as $stockModel ): ?>
                        <?php
                        $panelID = "allModels_" . $stockModel['id']; $collapseID = "collapseAllModels_" . $stockModel['id'];
                        $imgPath = $stockModel['number_3d'] . "/" .$stockModel['id'] . "/images/".$stockModel['img_name'];
                        $imgSrc  = file_exists(_stockDIR_ . $imgPath);
                        $imgSrc  =  $imgSrc ? _stockDIR_HTTP_ . $imgPath : _stockDIR_HTTP_."default.jpg";
                        ?>
                        <div class="col-xs-12 col-md-4 pr-0 pl-0">
                            <div class="panel panel-default mb-1">
                                <div class="panel-heading p0" role="tab" id="<?=$panelID?>">
                                    <a class="collapsed panel-title" role="button" data-toggle="collapse" href="#<?=$collapseID?>" aria-expanded="false" aria-controls="<?=$collapseID?>">
                                        <img src="<?= $imgSrc ?>" width="60px" class="thumbnail mb-0 d-inline" />
                                        <?= $stockModel['number_3d'] . "/" . $stockModel['vendor_code'] . " - " . $stockModel['model_type'] ?>
                                    </a>
                                    <a role="button" title="Просмотр модели" class="btn btn-sm btn-info pull-right" href="/model-view/?id=<?=$stockModel['id']?>"><span class="glyphicon glyphicon-eye-open"></span></a>
                                </div>
                                <div id="<?=$collapseID?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?=$panelID?>" aria-expanded="false" style="height: 0;">
                                    <ul class="list-group">
                                        <?php $total = 0; ?>
                                        <?php foreach ( $modelPrices??[] as $modelID => $prices ): ?>
                                            <?php if ( $modelID != $stockModel['id'] ) continue;?>
                                            <?php foreach ( $prices as $price ): ?>
                                                <?php $total += $price['value']; $wholeTotal += $price['value'];?>
                                                <li class="list-group-item">
                                                    <?= $price['cost_name'] . " - " .  $price['value'] . "грн."?><br>
                                                    <?php if ( $price['status'] ):?>
                                                        <span class="label label-primary ">Зачислено!</span>
                                                    <?php else: ?>
                                                        <span class="label label-default ">Не зачислено!</span>
                                                    <?php endif; ?>
                                                    <?php if ( $price['paid'] ):?>
                                                        <span class="label label-success ">Оплачено!</span>
                                                    <?php else: ?>
                                                        <span class="label label-default ">Не Оплачено!</span>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                    <div class="panel-footer text-bold">Всего: <?= $total?> грн. </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="col-xs-12 pl-0 pr-0">
                        <div class="alert alert-info text-bold" role="alert">Всего: <?= $wholeTotal ?></div>
                    </div>
                </div>
            </div>


            <div role="tabpanel" class="tab-pane" id="statistic">
                <p></p>
                <div class="panel panel-default">
                    <div class="panel-heading">Всего</div>
                    <div class="panel-body">
                        <p>Статистика начислений. Сколько начислено, получено, в ожидании и т.д. </p>
                    </div>
                    <ul class="list-group text-bold">
                        <li class="list-group-item list-group-item-success">Доступно к получению: <span class="pull-right"><?= $statistic['notpaid'] ?> грн.</span></li>
                        <li class="list-group-item list-group-item-info">Ожидают зачисления: <span class="pull-right"><?= $statistic['waiting'] ?> грн.</span></li>
                        <li class="list-group-item list-group-item-danger">Получено: <span class="pull-right"><?= $statistic['paid'] ?> грн.</span></li>
                    </ul>
                </div>
            </div>
        </div><!-- end of Tab content -->

        <a class="btn btn-default" type="button" href="<?=$_SESSION['prevPage'];?>"><span class="glyphicon glyphicon-triangle-left"></span> Назад</a>
    </div>
</div>
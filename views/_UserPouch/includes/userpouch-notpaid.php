<div role="tabpanel" class="tab-pane active in fade" id="allModels">
    <p></p>
    <div class="row">
        <div class="col-xs-6 col-md-6">
            <?php foreach ( $stockInfo??[] as $stockModel ): ?>
                <?php
                $panelID = "allModels_" . $stockModel['id']; $collapseID = "collapseAllModels_" . $stockModel['id'];
                $imgPath = $stockModel['number_3d'] . "/" .$stockModel['id'] . "/images/".$stockModel['img_name'];
                $imgSrc  = file_exists(_stockDIR_ . $imgPath);
                $imgSrc  =  $imgSrc ? _stockDIR_HTTP_ . $imgPath : _stockDIR_HTTP_."default.jpg";
                ?>
                <div class="panel panel-default mb-1">
                    <div class="panel-heading p0" role="tab" id="<?=$panelID?>">
                        <a class="collapsed panel-title" role="button" data-toggle="collapse" href="#<?=$collapseID?>" aria-expanded="false" aria-controls="<?=$collapseID?>">
                            <img src="<?= $imgSrc ?>" width="60px" class="thumbnail mb-0 d-inline" />
                            <?= $stockModel['number_3d'] . "/" . $stockModel['vendor_code'] . " - " . $stockModel['model_type'] ?>
                        </a>
                        <a role="button" class="btn btn-sm btn-info pull-right" href="/model-view/?id=<?=$stockModel['id']?>"><span class="glyphicon glyphicon-eye-open"></span> Просмотр модели</a>
                    </div>
                    <div id="<?=$collapseID?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?=$panelID?>" aria-expanded="false" style="height: 0;">
                        <ul class="list-group">
                            <?php $total = 0; ?>
                            <?php foreach ( $modelPrices??[] as $modelID => $prices ): ?>
                                <?php if ( $modelID != $stockModel['id'] ) continue;?>
                                <?php foreach ( $prices as $price ): ?>
                                    <?php $total += $price['value'];?>
                                    <li class="list-group-item">
                                        <?= $price['cost_name'] . " - " .  $price['value'] . "грн."?>
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
            <?php endforeach; ?>
        </div>
    </div>
</div>
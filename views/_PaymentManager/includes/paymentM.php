<?php
    use Views\vendor\libs\classes\URLCrypt;
	$currentWorker = $this->session->getKey('currentWorker'); 
	$tabID = (int)$this->request->get('tab');

	$tabName = '';
    switch ($tab??'all')
    {
        case 'all': $tabName = "Всех моделей"; break;
        case 'paid': $tabName = "Оплаченных"; break;
        case 'notpaid': $tabName = "Не оплаченных"; break;
    }
?>
<div class="row">
    <p class="lead text-info text-center"><span class="glyphicon glyphicon-credit-card" aria-hidden="true"></span> Менеджер Оплат</p>
    <div class="col-xs-12 stats_table">
    	
    	<div class="btn-group pull-right">
			<div class="btn-group btn-group-sm" role="group">
				<button type="button" class="btn btn-default disabled cursorArrow"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Сортировка:</button>
				<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="currentWorkerName"><?= $currentWorker['fio'] ?></span> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="/payment-manager/<?= URLCrypt::encode('pm',["tab"=>$tabID, "worker"=>0, "month"=>$monthID, "year"=>$yearID]) ?>">Все работники</a></li>
					<li><a href="/payment-manager/?tab=<?=$tabID?>&worker=0&month=<?=$monthID?>&year=<?=$yearID?>">Все работники</a></li>
					<li role="separator" class="divider"></li>
					<?php foreach ($usersList as $user): ?>
						<li><a href="/payment-manager/?tab=<?=$tabID?>&worker=<?=$user['id']?>&month=<?=$monthID?>&year=<?=$yearID?>"><?=$user['fio']?></a></li>
					<?php endforeach; ?>
					<!--<li role="separator" class="divider"></li>-->
				</ul>
			</div>
			<div class="btn-group btn-group-sm" role="group">
				<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?= $monthID ? getMonthRu($monthID) : "Все месяцы"?> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="/payment-manager/?tab=<?=$tabID?>&worker=<?=$workerID?>&month=<?=date('n')?>&year=<?=$yearID?>">Текущий месяц</a></li>
					<li><a href="/payment-manager/?tab=<?=$tabID?>&worker=<?=$workerID?>&month=0&year=<?=$yearID?>">Все</a></li>
					<li role="separator" class="divider"></li>
					<?php for ( $m = 1; $m <= 12; $m++ ) : ?>
						<li><a href="/payment-manager/?tab=<?=$tabID?>&worker=<?=$workerID?>&month=<?=$m?>&year=<?=$yearID?>"><?=getMonthRu($m)?></a></li>
				    <?php endfor; ?>
				</ul>
			</div>
			<div class="btn-group btn-group-sm" role="group">
				<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?= $yearID ?: "Текущий год" ?> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="/payment-manager/?tab=<?=$tabID?>&worker=<?=$workerID?>&month=<?=$monthID?>&year=<?=date('Y')?>">Текущий год</a></li>
					<li role="separator" class="divider"></li>
					<?php for( $y = 2020; $y <= date('Y'); $y++ ): ?>
						<li><a href="/payment-manager/?tab=<?=$tabID?>&worker=<?=$workerID?>&month=<?=$monthID?>&year=<?=$y?>"><?=$y?></a></li>
					<?php endfor; ?>
				</ul>
			</div>
		</div>

        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="<?= $tab == 'all'? 'active':'' ?>"><a href="/payment-manager/?tab=1&worker=<?=$currentWorker['id']?>&month=<?=$monthID?>&year=<?=$yearID?>">Все Модели</a></li>
            <li role="presentation" class="<?= $tab == 'paid'? 'active':'' ?>"><a href="/payment-manager/?tab=2&worker=<?=$currentWorker['id']?>&month=<?=$monthID?>&year=<?=$yearID?>" >Оплаченные</a></li>
            <li role="presentation" class="<?= $tab == 'notpaid'? 'active':'' ?>"><a href="/payment-manager/?tab=3&worker=<?=$currentWorker['id']?>&month=<?=$monthID?>&year=<?=$yearID?>">Не оплаченные</a></li>
            <li role="presentation" class=""><a href="#statistic" aria-controls="statistic" role="tab" data-toggle="tab">Статистика</a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active in fade" id="allModels">
                <p></p>
                <div class="row pl-3 pr-3 allmodels">

                    <?php $wholeTotal = 0; $priceIDsAll = ''; ?>
                    <?php foreach ( $stockInfo??[] as $stockModel ): ?>
                        <?php $panelID = "allModels_" . $stockModel['id']; $collapseID = "collapseAllModels_" . $stockModel['id']; $modelIDs .= $stockModel['id'] . ";" ?>
                        <div class="col-xs-12 col-md-4 pr-0 pl-0">
                            <div class="panel panel-default mb-1">
                                <div class="panel-heading p0" role="tab" id="<?=$panelID?>">
                                    <a class="collapsed panel-title modelInfo" role="button" data-toggle="collapse" href="#<?=$collapseID?>" aria-expanded="false" aria-controls="<?=$collapseID?>">
                                        <img src="<?=$stockModel['img_name']?>" width="60px" height="60px" class="thumbnail mb-0 d-inline" />
                                        <?= $stockModel['number_3d'] . "/" . $stockModel['vendor_code'] . " - " . $stockModel['model_type'] ?>
                                    </a>
                                    <a role="button" title="Просмотр модели" class="btn btn-sm btn-info pull-right" href="/model-view/?id=<?=$stockModel['id']?>"><span class="glyphicon glyphicon-eye-open"></span></a>
                                </div>
                                <div id="<?=$collapseID?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="<?=$panelID?>" aria-expanded="false" style="height: 0;">
                                    <ul class="list-group">
                                        <?php $total = 0; ?>
                                        <?php foreach ( $modelPrices??[] as $modelID => $prices ): ?>
                                            <?php if ( $modelID != $stockModel['id'] ) continue; ?>
                                            <?php $priceIDs = ''?>
                                            <?php foreach ( $prices as $price ): ?>
                                                <?php $total += $price['value']; $wholeTotal += $price['value']; $singlePriceIDs=''; ?>
                                                <li class="list-group-item">
                                                    <span class="priceName_value" ><?= $price['cost_name'] . " - " .  $price['value'] . "грн.  -  " . date_create( $price['date'] )->Format('d.m.Y'); ?></span>
                                                    <br>
                                                    <?php if ( $price['status'] ):?>
                                                        <span class="label label-primary ">Зачислено!</span>
                                                    <?php else: ?>
                                                        <span class="label label-default ">Не зачислено!</span>
                                                    <?php endif; ?>
                                                    <?php if ( $price['paid'] ):?>
                                                        <span class="label label-success ">Оплачено!</span>
                                                    <?php else: ?>
                                                        <span class="label label-default ">Не Оплачено!</span>
                                                        <?php if ( $price['status'] ): ?>
                                                            <?php $priceIDs .=  $price['is3d_grade'] == 1 ? $price['ids_3d'] : $price['id'] . ';' ?>
                                                            <?php $priceIDsAll .= $singlePriceIDs .=  $price['is3d_grade'] == 1 ? $price['ids_3d'] : $price['id'] . ';' ?>
                                                            <?php $sPriceID_crypt = URLCrypt::strEncode($singlePriceIDs); ?>
                                                            <?php $modelID_crypt = URLCrypt::strEncode($stockModel['id']); ?>
                                                        	 <button type="button" data-toggle="modal" data-prices="single" data-priceID="<?=$sPriceID_crypt?>" data-posID="<?=$modelID_crypt?>" data-target="#paymentModal" class="btn btn-sm btn-default absolute" style="right: 10px; top: 50%; margin-top: -15px;" title="Оплатить">
                                                                <span class="glyphicon glyphicon-usd"></span>
                                                            </button>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                    <div class="panel-footer text-bold relative">
                                        <span>Всего: <?= $total?> грн.</span>
                                        <?php if ( $tabID === 3 ): ?>
                                            <?php $priceIDs_crypt = URLCrypt::strEncode($priceIDs); ?>
                                            <?php $modelID_crypt = URLCrypt::strEncode($stockModel['id']); ?>
                                            <button type="button" data-toggle="modal" data-prices="allInModel" data-priceID="<?=$priceIDs_crypt?>" data-posID="<?=$modelID_crypt?>" data-target="#paymentModal" class="btn btn-sm btn-success absolute" style="right: 10px; top: 50%; margin-top: -15px;" title="Оплатить Все"><span class="glyphicon glyphicon-usd"></span></button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="col-xs-12 pl-0 pr-0">
                        <div class="alert alert-info text-bold" role="alert">
                        	<span>Всего: <?= $wholeTotal ?> </span>
                        	<?php if ( $tabID === 3 ): ?>
                        		<span class="pull-right">
                                   <?php $priceIDsAll = URLCrypt::strEncode($priceIDsAll); ?>
                                   <?php $modelIDs = URLCrypt::strEncode($modelIDs); ?>
                        			<button type="button" data-toggle="modal" data-prices="all" data-priceID="<?=$priceIDsAll?>" data-posID="<?=$modelIDs?>" data-target="#paymentModal" class="btn btn-sm btn-danger relative" style="top: -4px;" title="Оплатить Все">
                                        <span class="glyphicon glyphicon-usd"></span>
                                        Оплатить Все
                                    </button>
                        		</span>
                        	<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>


            <div role="tabpanel" class="tab-pane" id="statistic">
                <p></p>
                <div class="panel panel-default">
                    <div class="panel-heading text-center text-bold">Всего из <?= $tabName ?> для <?= $currentWorker['fio'] ?></div>
                    <div class="panel-body">
                        <p>Статистика начислений. Сколько начислено, получено, в ожидании и т.д. </p>
                    </div>
                    <ul class="list-group text-bold">
                        <li class="list-group-item list-group-item-success">Возможно оплатить: <span class="pull-right"><?= $statistic['notpaid'] ?> грн.</span></li>
                        <li class="list-group-item list-group-item-info">Не зачисленные: <span class="pull-right"><?= $statistic['waiting'] ?> грн.</span></li>
                        <li class="list-group-item list-group-item-danger">Оплаченные: <span class="pull-right"><?= $statistic['paid'] ?> грн.</span></li>
                    </ul>
                </div>
            </div>
        </div><!-- end of Tab content -->

        <a class="btn btn-default" type="button" href="<?=$_SESSION['prevPage'];?>"><span class="glyphicon glyphicon-triangle-left"></span> Назад</a>
    </div>
</div>

<?php require_once _viewsDIR_ . "_PaymentManager/includes/paymentModal.php" ?>
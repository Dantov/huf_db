<?php
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
                    <li><a href="/payment-manager/?tab=<?=$tabID?>&worker=<?=$workerID?>&month=<?=$monthID?>&year=2019">2019</a></li>
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
                <?php require_once _viewsDIR_ . "_UserPouch/includes/allModelsTab.php"; ?>
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
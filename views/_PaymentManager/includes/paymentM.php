<?php
use Views\vendor\core\HtmlHelper;
use Views\vendor\libs\classes\URLCrypt;
$currentWorker = $this->session->getKey('currentWorker'); 
$tabID = (int)$this->request->get('tab');

HtmlHelper::defineURLParams([
	'tab'    => $tabID,
	'worker' => $workerID,
	'year'   => $yearID,
	'month'  => $monthID,
	'page'   => $page,
]);
//
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
					<li><a href="<?= HtmlHelper::URL('/',['worker'=>0]) ?>">Все работники</a></li>
					<li role="separator" class="divider"></li>
					<?php foreach ($usersList as $user): ?>
						<li><a href="<?=HtmlHelper::URL('/',['worker'=>$user['id']])?>"><?=$user['fio']?></a></li>
					<?php endforeach; ?>
					<!--<li role="separator" class="divider"></li>-->
				</ul>
			</div>
			<div class="btn-group btn-group-sm" role="group">
				<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?= $monthID ? getMonthRu($monthID) : "Все месяцы"?> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="<?=HtmlHelper::URL('/',['month'=>date('n')])?>">Текущий месяц</a></li>
					<li><a href="<?=HtmlHelper::URL('/',['month'=>0])?>">Все</a></li>
					<li role="separator" class="divider"></li>
					<?php for ( $m = 1; $m <= 12; $m++ ) : ?>
						<li><a href="<?=HtmlHelper::URL('/',['month'=>$m])?>"><?=getMonthRu($m)?></a></li>
				    <?php endfor; ?>
				</ul>
			</div>
			<div class="btn-group btn-group-sm" role="group">
				<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?= $yearID ?: "Текущий год" ?> <span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="<?=HtmlHelper::URL('/',['year'=>date('Y')])?>">Текущий год</a></li>
					<li role="separator" class="divider"></li>
					<?php for( $y = 2020; $y <= date('Y'); $y++ ): ?>
						<li><a href="<?=HtmlHelper::URL('/',['year'=>$y])?>"><?=$y?></a></li>
					<?php endfor; ?>
                    <li><a href="<?=HtmlHelper::URL('/',['year'=>2019])?>">2019</a></li>
				</ul>
			</div>
		</div>

        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="<?= $tab == 'all'? 'active':'' ?>"><a href="<?=HtmlHelper::URL('/',['tab'=>1])?>">Все Модели</a></li>
            <li role="presentation" class="<?= $tab == 'paid'? 'active':'' ?>"><a href="<?=HtmlHelper::URL('/',['tab'=>2])?>" >Оплаченные</a></li>
            <li role="presentation" class="<?= $tab == 'notpaid'? 'active':'' ?>"><a href="<?=HtmlHelper::URL('/',['tab'=>3])?>">Не оплаченные</a></li>
            <li role="presentation" class=""><a href="#statistic" aria-controls="statistic" role="tab" data-toggle="tab">Статистика</a></li>
            <li role="presentation" class=""><a href="#help" aria-controls="help" role="tab" data-toggle="tab">Помощь</a></li>
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

            <div role="tabpanel" class="tab-pane" id="help">
                <p></p>
                <div class="panel panel-default">
                    <div class="panel-heading text-center text-bold">Список добавления/зичислений стоимостей</div>
                    <div class="panel-body"></div>
                    <ul class="list-group">
                        <li class="list-group-item">
                            1) Эскиз - создаёт <i>Дизайнер Юв.изделий</i> (Дзюба). <b>Добавляет</b> стоимость за эскиз, дизайнеру (Дзюбе).
                        </li>
                        <li class="list-group-item">
                            2) Утв. эскиза - ставит <i>Худ.совет</i> (Богдан). <b>Зачисляет</b> стоимость за эскиз, дизайнеру (Дзюбе).
                        </li>
                        <li class="list-group-item">
                            3) В работе 3Д  - ставит <i>3д моделлер</i>. <b>Добавляет</b> стоимость дизайнеру за курирование 3д (Дзюбе).
                        </li>
                        <li class="list-group-item">
                            4) На проверке – ставит <i>Технолог 3Д</i> (Быков) <b>Добавляет</b> стоимость технологу 3д. за просмотр 3д модели (Быкову)
                        </li>
                        <li class="list-group-item">
                            5) Подпись технолога – ставит <i>Техн.Юв.изделий</i> (Занин).
                            <b>Добавляет</b> стоимоть технологу за просмотр 3д модели и сопровождение (Занину).
                            А также <b>Зачисляет</b> дизайнеру за курирование 3д. (Дзюбе)
                        </li>
                        <li class="list-group-item">
                            6) Проверено – ставит <i>Технолог 3Д</i> (Быков). <b>Зачисляет</b> стоимость утверждение 3д модели – (Быкову и Занину).
                            <b>Зачисляет</b> за курирование 3д (Дзюбе). А также <b>Зачисляет</b> всю стоимость 3д моделлеру.
                        </li>
                        <li class="list-group-item">
                            7) Поддержки – ставит <i>Оператор 3д принтера</i> (Емельянцевы Слава/Влад).
                            <b>Добавляет</b> стоимость за создание поддержек (Емельянцеву Владу)
                        </li>
                        <li class="list-group-item">
                            8) Выращено - ставит <i>Оператор 3д принтера</i> (Емельянцевы Слава/Влад).
                            <b>Зачисляет</b> стоимость Емельянцеву Владу за поддержки.
                        </li>
                        <li class="list-group-item">
                            9) На сбыте – ставит <i>Участок ПДО</i>. <b>Зачисляет</b> стоимость Техн.Юв.Изделий (Занину) за сопровождение.
                            А так же <b>Зачисляет</b> стоимости модельерам за мастер модель.
                        </li>
                    </ul>
                </div>
            </div>
        </div><!-- end of Tab content -->

        <a class="btn btn-default" type="button" href="<?=$_SESSION['prevPage'];?>"><span class="glyphicon glyphicon-triangle-left"></span> Назад</a>
    </div>
</div>

<?php require_once _viewsDIR_ . "_PaymentManager/includes/paymentModal.php" ?>
<?php
use Views\vendor\core\HtmlHelper;

$tabID = (int)$this->request->get('tab');

HtmlHelper::defineURLParams([
    'ppCount' => $ppCount,
    'tab'   => $tabID,
    'year'  => $yearID,
    'month' => $monthID,
    'page'  => $page,
]);

$tabName = '';
switch ($tab??'all')
{
    case 'all': $tabName = "Всех моделей"; break;
    case 'paid': $tabName = "Оплаченные"; break;
    case 'notpaid': $tabName = "Не оплаченные"; break;
    case 'notCredited': $tabName = "Не Зачисленные"; break;
}
?>
<div class="row">
    <p class="lead text-info text-center"><span class="glyphicon glyphicon-piggy-bank" aria-hidden="true"></span> Кошелёк Работника</p>
    <div class="col-xs-12 stats_table">

        <div class="btn-group pull-right">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-default" id="openAllModels"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span> Развернуть все</button>
                <button type="button" class="btn btn-default dropdown-toggle" title="Кол-во отображаемых позиций" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $ppCount ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <?php for ( $ppc = 18; $ppc <= 120; $ppc = $ppc+6 ) : ?>
                        <li><a href="<?=HtmlHelper::URL('/',['ppCount'=>$ppc])?>"><?=$ppc?></a></li>
                    <?php endfor; ?>
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
                        <li>
                            <a href="<?=HtmlHelper::URL('/',['year'=>$y])?>"><?=$y?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>
        </div>

        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="<?= $tab == 'all'? 'active':'' ?>"><a href="<?=HtmlHelper::URL('/',['tab'=>1])?>">Все Модели</a></li>
            <li role="presentation" class="dropdown <?= in_array($tab,['paid','notpaid','notCredited']) ? 'active':'' ?>">
                <a href="#" id="tabOptions" class="dropdown-toggle cursorPointer" data-toggle="dropdown" aria-controls="tabOptions-contents" aria-expanded="false">
                    <?= in_array($tab,['paid','notpaid','notCredited']) ? $tabName : 'Ещё' ?> <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" aria-labelledby="tabOptions" id="tabOptions-contents">
                    <li role="presentation" class="<?= $tab == 'notCredited'? 'active':'' ?>">
                        <a href="<?=HtmlHelper::URL('/',['tab'=>4])?>" >Не зачисленные</a>
                    </li>
                    <li role="presentation" class="<?= $tab == 'notpaid'? 'active':'' ?>">
                        <a href="<?=HtmlHelper::URL('/',['tab'=>3])?>">Не оплаченные</a>
                    </li>
                    <li role="presentation" class="<?= $tab == 'paid'? 'active':'' ?>">
                        <a href="<?=HtmlHelper::URL('/',['tab'=>2])?>" >Оплаченные</a>
                    </li>
                </ul>
            </li>
            <li role="presentation" class=""><a href="#statistic" aria-controls="statistic" role="tab" data-toggle="tab">Статистика</a></li>
            <li role="presentation" class=""><a href="#help" aria-controls="help" role="tab" data-toggle="tab">Помощь</a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active in fade" id="allModels">
                <?php require_once "allModelsTab.php"; ?>
            </div>

            <div role="tabpanel" class="tab-pane" id="statistic">
                <p></p>
                <div class="panel panel-default">
                    <div class="panel-heading text-center text-bold">
                        Всего из <?= $tabName ?> для <?= $this->session->getKey('user')['fio'] ?>
                    </div>
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

            <div role="tabpanel" class="tab-pane" id="help">
                <p></p>
                <div class="panel panel-default">
                    <div class="panel-heading text-center text-bold"></div>
                    <div class="panel-body">

                    </div>
                </div>
            </div>
        </div><!-- end of Tab content -->

        <a class="btn btn-default" type="button" href="<?=$_SESSION['prevPage'];?>"><span class="glyphicon glyphicon-triangle-left"></span> Назад</a>
    </div>
</div>
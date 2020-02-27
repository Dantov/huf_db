<div id="modalStatuses" data-iziModal-fullscreen="true" data-iziModal-title="Вабрать модели по статусу. Текущий выбор: <b><?=$selectedStatusName?></b>" data-iziModal-icon="icon-home">
    <span id="currentSelectedStatus" class="hidden"><?=$selectedStatusName?></span>
    <div id="modalContent" style="padding: 10px" class="hidden">
        <p>
            <button id="openAll" title="Раскрыть Все" onclick="event.preventDefault()" style="margin-bottom: 10px" class="pull-left btn btn-sm btn-info"><span class="glyphicon glyphicon-menu-left"></span> Раскрыть Все</button>
            <button id="closeAll" title="Закрыть Все" onclick="event.preventDefault()" style="margin-bottom: 10px" class="pull-left hidden btn btn-sm btn-primary"><span class="glyphicon glyphicon-menu-down"></span> Закрыть Все</button>

            <a title="Сбросить" href="<?=_views_HTTP_?>Main/controllers/setSort.php?regStat=none" style="margin-bottom: 10px" class="pull-right btn btn-sm btn-success"><span class="glyphicon glyphicon-remove"></span> Нет</a>
        </p>
        <div class="clearfix"></div>
        <div class="row">
            <?php
                $countWC = count($status?:[]);
                $columns = [
                    0=>'',
                    1=>'',
                    2=>'',
                    3=>'',
                ];
                $c = 0;
                ob_start();
            ?>
            <?php
            /**
             * У людей проблемы с открытием статусов на компах под Win Xp chrome 40 - 49
             */
            $barubina = 'statusesPanelBodyHidden';
            $userAccess = (int)$_SESSION['user']['access'];
            if ( $userAccess > 1 ) $barubina = '';
            ?>
            <?php foreach ( $status?:[] as $wcName => $workingCenter ) :?>
                <div class="panel panel-info" style="position:relative;">
                    <div class="panel-heading">
                        <?=$wcName?>
                        <button title="Раскрыть" onclick="event.preventDefault()" data-status="0" class="btn btn-sm btn-info statusesChevron"><span class="glyphicon glyphicon-menu-left"></span></button>
                    </div>
                    <div class="panel-body pb-0 statusesPanelBody <?=$barubina?>">
                        <?php foreach ( $workingCenter as $subUnit ) :?>
                            <div class="list-group">
                                <a class="list-group-item list-group-item-success"><?=$subUnit['descr']?></a>
                                <?php foreach ( $subUnit['statuses'] as $status ) :?>
                                    <a title="<?=$status['title'];?>" class="list-group-item wc-status-item" href="<?=_views_HTTP_?>Main/controllers/setSort.php?regStat=<?=$status['id']?>">
                                        <span class="glyphicon glyphicon-<?=$status['glyphi']?>"></span>
                                        <span><?=$status['name_ru']?></span>
                                    </a>
                                <?php endforeach; ?>
                                <a title="Ответственный" class="list-group-item list-group-item-danger"><?=$subUnit['user']?></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php $columns[$c] .= ob_get_contents(); ?>
                <?php ob_clean(); ?>
                <?php $c++; ?>
                <?php if ( !($c % 4) ) $c = 0; ?>
            <?php endforeach; ?>

            <?php ob_end_clean(); ?>

            <div class="col-xs-3" style="padding-right: 2px;"><?php echo $columns[0] ?></div>
            <div class="col-xs-3" style="padding: 0 2px 0 2px; "><?php echo $columns[1] ?></div>
            <div class="col-xs-3" style="padding: 0 2px 0 2px; "><?php echo $columns[2] ?></div>
            <div class="col-xs-3" style="padding-left: 2px;"><?php echo $columns[3] ?></div>
        </div>
    </div>
</div>
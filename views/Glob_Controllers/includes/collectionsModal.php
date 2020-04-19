<?
$navBar = $this->navBar;
$coll_silver = $navBar['collectionList']['silver'];
$coll_gold = $navBar['collectionList']['gold'];
$coll_diamond = $navBar['collectionList']['diamond'];
$coll_other = $navBar['collectionList']['other'];
?>
<div id="collectionsModal" data-iziModal-title="Выбрать Коллекцию">
    <div id="modalCollectionsContent" style="padding: 10px" class="hidden">
        <div class="row">

            <div class="col-xs-6 col-md-3" style="padding-right: 2px;">
                <div class="panel panel-default" style="position:relative;">
                    <div class="panel-heading text-bold text-center">Серебро (<?= count($coll_silver)?>)</div>
                    <div class="panel-body pb-0 p1">
                        <div class="list-group pb-1 mb-1 text-bold">
                            <?php foreach ( $coll_silver as $id => $name ) :?>
                                <a class="list-group-item cursorPointer" href="<?=_views_HTTP_?>Main/controllers/setSort.php?coll_show=<?=$id?>"><?=$name?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-6 col-md-3" style="padding: 0 2px 0 2px; ">
                <div class="panel panel-success" style="position:relative;">
                    <div class="panel-heading text-bold text-center">Золото (<?= count($coll_gold)?>)</div>
                    <div class="panel-body pb-0 p1">
                        <div class="list-group pb-1 mb-1 text-bold">
                            <?php foreach ( $coll_gold as $id => $name ) :?>
                                <a class="list-group-item cursorPointer" href="<?=_views_HTTP_?>Main/controllers/setSort.php?coll_show=<?=$id?>"><?=$name?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-6 col-md-3" style="padding: 0 2px 0 2px; ">
                <div class="panel panel-info" style="position:relative;">
                    <div class="panel-heading text-bold text-center">Бриллианты (<?= count($coll_diamond)?>)</div>
                    <div class="panel-body pb-0 p1">
                        <div class="list-group pb-1 mb-1 text-bold">
                            <?php foreach ( $coll_diamond as $id => $name ) :?>
                                <a class="list-group-item cursorPointer" href="<?=_views_HTTP_?>Main/controllers/setSort.php?coll_show=<?=$id?>"><?=$name?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-6 col-md-3" style="padding-left: 2px;">
                <div class="panel panel-warning" style="position:relative;">
                    <div class="panel-heading text-bold text-center">Разные (<?= count($coll_other)?>)</div>
                    <div class="panel-body pb-0 p1">
                        <div class="list-group pb-1 mb-1 text-bold">
                            <a class="list-group-item cursorPointer" href="<?=_views_HTTP_?>Main/controllers/setSort.php?coll_show=-1">Все</a>
                            <?php foreach ( $coll_other as $id => $name ) :?>
                                <a class="list-group-item cursorPointer" href="<?=_views_HTTP_?>Main/controllers/setSort.php?coll_show=<?=$id?>"><?=$name?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
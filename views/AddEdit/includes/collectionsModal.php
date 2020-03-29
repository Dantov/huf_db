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

            <div class="col-xs-3" style="padding-right: 2px;">
                <div class="panel panel-default" style="position:relative;">
                    <div class="panel-heading text-bold text-center">Серебро</div>
                    <div class="panel-body pb-0">
                        <div class="list-group">
                            <?php foreach ( $coll_silver as $id => $name ) :?>
                                <? $goldAI = ''; if ( (int)$id === 22 || (int)$id === 53 )  $goldAI = 'aiblock'; ?>
                                <a class="list-group-item cursorPointer" data-izimodal-close="#collectionsModal" elemToAdd="" coll="" <?=$goldAI?> collid="<?=$id?>"><?=$name?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-3" style="padding: 0 2px 0 2px; ">
                <div class="panel panel-success" style="position:relative;">
                    <div class="panel-heading text-bold text-center">Золото</div>
                    <div class="panel-body pb-0">
                        <div class="list-group">
                            <?php foreach ( $coll_gold as $id => $name ) :?>
                                <? $goldAI = ''; if ( (int)$id === 22 || (int)$id === 53 )  $goldAI = 'aiblock'; ?>
                                <a class="list-group-item cursorPointer" data-izimodal-close="#collectionsModal" elemToAdd="" coll="" <?=$goldAI?> collid="<?=$id?>"><?=$name?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-3" style="padding: 0 2px 0 2px; ">
                <div class="panel panel-info" style="position:relative;">
                    <div class="panel-heading text-bold text-center">Бриллианты</div>
                    <div class="panel-body pb-0">
                        <div class="list-group">
                            <?php foreach ( $coll_diamond as $id => $name ) :?>
                                <? $goldAI = ''; if ( (int)$id === 22 || (int)$id === 53 )  $goldAI = 'aiblock'; ?>
                                <a class="list-group-item cursorPointer" data-izimodal-close="#collectionsModal" elemToAdd="" coll="" <?=$goldAI?> collid="<?=$id?>"><?=$name?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-3" style="padding-left: 2px;">
                <div class="panel panel-warning" style="position:relative;">
                    <div class="panel-heading text-bold text-center">Разные</div>
                    <div class="panel-body pb-0">
                        <div class="list-group">
                            <?php foreach ( $coll_other as $id => $name ) :?>
                                <? $goldAI = ''; if ( (int)$id === 22 || (int)$id === 53 )  $goldAI = 'aiblock'; ?>
                                <a class="list-group-item cursorPointer" data-izimodal-close="#collectionsModal" elemToAdd="" coll="" <?=$goldAI?> collid="<?=$id?>"><?=$name?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php
$navBar = $this->navBar;
$coll_silver = $navBar['collectionList']['silver'];
$coll_gold = $navBar['collectionList']['gold'];
$coll_diamond = $navBar['collectionList']['diamond'];
$coll_other = $navBar['collectionList']['other'];

// Перекинем массив Юзера в JS
$wsUserData = [];
$wsUserData['id'] = $_SESSION['user']['id'];
$wsUserData['fio'] = $_SESSION['user']['fio'];
$wsUserData = json_encode($wsUserData,JSON_UNESCAPED_UNICODE);
$wsUserDataJS = <<<JS
    let wsUserData = $wsUserData;
JS;
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->title ?></title>
    <link rel="icon" href="<?= _rootDIR_HTTP_ ?>favicon.ico?ver=105">
    <link rel="stylesheet" href="<?= _rootDIR_HTTP_ ?>web/css/style_adm.css?ver=<?=time();?>">
    <link rel="stylesheet" href="<?= _rootDIR_HTTP_ ?>web/css/style.css?ver=<?=time();?>">
    <link rel="stylesheet" href="<?= _rootDIR_HTTP_ ?>web/css/bodyImg.css?ver=<?=time();?>">
    <link rel="stylesheet" href="<?= _rootDIR_HTTP_ ?>web/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= _rootDIR_HTTP_ ?>web/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="<?= _rootDIR_HTTP_ ?>web/css/iziModal.min.css">
    <link rel="stylesheet" href="<?= _rootDIR_HTTP_ ?>web/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= _rootDIR_HTTP_ ?>web/fontawesome-free-5.0.6/on-server/css/fontawesome-all.css">
    <script src="<?=_views_HTTP_?>js_lib/jquery-3.2.1.min.js"></script>
    <script src="<?=_views_HTTP_?>js_lib/bootstrap.min.js"></script>
    <script src="<?= _glob_HTTP_ ?>js/const.js?ver=<?=time();?>"></script>
    <script src="<?= _glob_HTTP_ ?>js/pushNotice.js?ver=<?=time();?>"></script>
    <script><?=$wsUserDataJS?></script>
    <script src="<?= _rootDIR_HTTP_ ?>web/js_lib/webSocketConnect.js?ver=<?=time();?>"></script>
</head>
<body id="body" class="<?=$_SESSION['assist']['bodyImg']?>">
	<div id="content"> <!-- нужен что бы скрывать все для показа 3Д -->

        <nav class="navbar navbar-default nav-bar-marg">
            <div class="container-fluid">

                <div class="navbar-header">
                    <a class="navbar-brand"><?= _brandName_ ?></a>
                </div>

                <div class="navbar-collapse">

                    <ul class="nav nav-pills navbar-left inlblock" id="navnav">
                        <li role="presentation" class="<?=$this->varBlock['activeMenu']?>"><a href="<?=_views_HTTP_?>Main/">База</a></li>
                            <? if ( $_SESSION['user']['access'] < 3 ): ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-link topdividervertical dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-menu-hamburger"></span></button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?=_views_HTTP_?>AddEdit/index.php?id=0&component=1<?=$dell;?>"><span class="glyphicon glyphicon-file"></span>&#160; Добавить модель</a>
                                    </li>
                                    <li><a href="<?=_views_HTTP_?>Nomenclature/index.php"><span class="glyphicon glyphicon-list-alt"></span>&#160; Номенклатура</a></li>
                                </ul>
                            </div>
                            <?endif;?>
                        <li role="presentation">
                            <button id="collSelect" onclick="navbar.collectionSelect(this);" type="button" title="Выбрать Коллекцию" style="font-size: 18px; padding: 5px 8px 0 8px;" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-gem"></i>
                            </button>
                        </li>
                    </ul>

                    <form action="<?=_globDIR_?>search.php" method="post" <?=$searchStyle;?> class="navbar-form navbar-left topSearchForm">
                        <div class="input-group">
                        <span class="input-group-btn">
                            <button class="btn btn-link" type="submit" name="search" title="Нажать для поиска">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>
                            <input type="text" class="form-control topSearchInpt" title="Что искать" placeholder="Поиск..." name="searchFor" value="<?=$_SESSION['searchFor'];?>">
                            <div class="input-group-btn">
                                <button type="button" id="searchInBtn" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Где искать">
                                    <span><?=$navBar['searchInStr'];?> </span><span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a type="button" onclick="main.searchIn(1);" title="Поиск во всей Базе">В Базе</a></li>
                                    <li><a type="button" onclick="main.searchIn(2);" title="Поиск в выбраной коллекции">В Коллекции</a></li>
                                </ul>
                            </div><!-- /btn-group -->
                        </div><!-- /input-group -->
                    </form>

                    <form class="navbar-form topuserform navbar-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-link topdividervertical dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="glyphicon glyphicon-<?=$navBar['glphsd']?>"></span>&#160;<?=$navBar['userFio'];?>&#160;<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li class="<?=$navBar['navbarStatsShow'];?>"><a href="<?=$navBar['navbarStatsUrl'];?>"><span class="glyphicon glyphicon-stats"></span>&#160; Статистика</a></li>
                                <li><a href="<?=_views_HTTP_?>Options/index.php"><span class="glyphicon glyphicon-cog"></span>&#160; Опции</a></li>
                                <li class="<?=$navBar['navbarDevShow'];?>"><a href="<?=$navBar['navbarDevUrl'];?>"><span class="glyphicon glyphicon-wrench"></span>&#160; Dev</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="<?=_glob_HTTP_?>exit.php"><span class="glyphicon glyphicon-log-out"></span>&#160; Выход</a></li>
                            </ul>
                        </div>
                    </form>

                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <!-- Блок коллекций -->
        <div id="collection_block" coll_block class="" style="margin-top:70px; left: 50%; margin-right: -50%; transform: translate(-50%); ">
            <div class="row collection_blockRow" coll_block>
                <div class="col-xs-12 col-sm-6 col-md-3" style="max-height: 100%; padding: 0 5px 0 5px;">
                    <div coll_block class=" collItem_TOP">
                        Серебро ( <?= count($coll_silver) ?> )
                    </div>
                    <?php
                    foreach ( $coll_silver as $id => $name )
                    {
                        ?>
                        <a href="<?=_views_HTTP_?>Main/controllers/setSort.php?coll_show=<?=$id?>">
                            <div coll_block class=" collItem"><?= $name ?></div>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3" style="border-left: 1px solid #2F4F4F; max-height: 100%; padding: 0 5px 0 5px;">
                    <div coll_block class=" collItem_TOP">
                        Золото ( <?= count($coll_gold) ?> )
                    </div>
                    <?php
                    foreach ( $coll_gold as $id => $name )
                    {
                        ?>
                        <a href="<?=_views_HTTP_?>Main/controllers/setSort.php?coll_show=<?=$id?>">
                            <div coll_block class=" collItem"><?= $name ?></div>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3" style="border-left: 1px solid #2F4F4F; max-height: 100%; padding: 0 5px 0 5px;">
                    <div coll_block class=" collItem_TOP">
                        Бриллианты ( <?= count($coll_diamond) ?> )
                    </div>
                    <?php
                    foreach ( $coll_diamond as $id => $name )
                    {
                        ?>
                        <a href="<?=_views_HTTP_?>Main/controllers/setSort.php?coll_show=<?=$id?>">
                            <div coll_block class=" collItem"><?= $name ?></div>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3" style="border-left: 1px solid #2F4F4F; max-height: 100%; padding: 0 5px 0 5px;">
                    <div coll_block class=" collItem_TOP" >
                        Разные ( <?= count($coll_other) ?> )
                    </div>
                    <a href="<?=_views_HTTP_?>Main/controllers/setSort.php?coll_show=-1">
                        <div coll_block class="collItem">
                            Все
                        </div>
                    </a>
                    <?php
                    foreach ( $coll_other as $id => $name )
                    {
                        ?>
                        <a href="<?=_views_HTTP_?>Main/controllers/setSort.php?coll_show=<?=$id?>">
                            <div coll_block class=" collItem"><?= $name ?></div>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- END Блок коллекций -->

        <div class="container">
            <?=$content;?>
        </div><!--container-->
    </div><!--content-->
    <div class="container">
        <p class="footer"><i class="pull-right"><a href="<?= _glob_HTTP_ ?>versions.php" title="Список изменений">ver. 1.67</a> &#160; developed by Vadim Bukov</i></p>
        <script src="<?=_rootDIR_HTTP_?>web/js_lib/iziModal.min.js"></script>
        <script src="<?=_glob_HTTP_?>js/NavBar.js"></script>
        <script defer src="<?=_views_HTTP_?>Main/js/main.js?ver=<?=time()?>"></script>
    </div>
</body>
</html>
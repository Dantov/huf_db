<?php
$navBar = $this->navBar;
$coll_silver = $navBar['collectionList']['silver'];
$coll_gold = $navBar['collectionList']['gold'];
$coll_diamond = $navBar['collectionList']['diamond'];
$coll_other = $navBar['collectionList']['other'];
$session = $this->session;
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
    <link rel="icon" href="/web/favicon.ico?ver=106">

    <link rel="stylesheet" href="/web/css/cssFW.css?ver=<?=time();?>">
    <link rel="stylesheet" href="/web/css/style.css?ver=<?=time();?>">
    <link rel="stylesheet" href="/web/css/style_adm.css?ver=<?=time();?>">
    <link rel="stylesheet" href="/web/css/bodyImg.css?ver=<?=time();?>">
    <link rel="stylesheet" href="/web/css/bootstrap.min.css">
    <link rel="stylesheet" href="/web/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="/web/css/iziModal.min.css">
    <link rel="stylesheet" href="/web/css/iziToast.min.css">
    <link rel="stylesheet" href="/web/fontawesome5.9.0/css/all.min.css">
    <script src="../_Globals/js/const.js?ver=<?=time()?>"></script>
    <script><?=$wsUserDataJS?></script>
    <script src="../_Globals/js/webSocketConnect.js?ver=<?=time()?>"></script>
    <? $this->head() ?>
</head>

<body id="body" class="<?=$_SESSION['assist']['bodyImg']?>">
<?php $this->beginBody() ?>
	<div class="wrapper" id="content"> <!-- нужен что бы скрывать все для показа 3Д -->

        <nav class="navbar navbar-default nav-bar-marg">
            <div class="container-fluid">

                <div class="navbar-header">
                    <a class="navbar-brand"><?= _brandName_ ?></a>
                </div>

                <div class="navbar-collapse">

                    <ul class="nav nav-pills navbar-left inlblock" id="navnav">
                        <li role="presentation" class="<?=$this->varBlock['activeMenu']?>"><a href="/main">База</a></li>
                            <?php if ( $_SESSION['user']['access'] < 3 ): ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-link topdividervertical dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-menu-hamburger"></span></button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="/add-edit/?id=0&component=1"><span class="glyphicon glyphicon-file"></span>&#160; Добавить модель</a>
                                    </li>
                                    <li><a href="/nomenclature/"><span class="glyphicon glyphicon-list-alt"></span>&#160; Номенклатура</a></li>
                                </ul>
                            </div>
                            <?php endif;?>
                        <li role="presentation">
                            <button id="collSelect" data-izimodal-open="#collectionsModal" type="button" title="Выбрать Коллекцию" style="font-size: 18px; padding: 5px 8px 0 8px;" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-gem"></i>
                            </button>
                        </li>
                    </ul>

                    <form action="/main/search=<?=$_SESSION['searchFor']?>" method="post" <?=$searchStyle?> class="navbar-form navbar-left topSearchForm">
                        <?php if ( trueIsset( $session->getKey('countAmount') ) ) : ?>
                            <span class="cursorArrow" title="Найдено позиций"><?=$session->getKey('countAmount')?></span>
                        <?php endif; ?>
                        <div class="input-group">
                        <span class="input-group-btn">
                            <?php if ( $session->getKey('searchFor') ): ?>
                                <a href="/main/?search=resetSearch" class="btn btn-link" type="button" name="resetSearch" title="Сбросить поиск"><i class="fas fa-broom"></i></a>
                            <?php endif; ?>
                            <button class="btn btn-link" type="submit" name="search" title="Нажать для поиска">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                        </span>
                            <input type="text" class="form-control topSearchInpt" title="Что искать" placeholder="Поиск..." name="searchFor" value="<?=$_SESSION['searchFor']?>">
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
						<div class="btn-group" id="noticesBadge">
							<button title="Текущие Уведомления" type="button" class="btn btn-link topdividervertical dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="badge pushNoticeBadge"></span>
							</button>
							<ul class="dropdown-menu">
								<li>
									<a title="Показать все уведомления" class="noticeShow"><span class="glyphicon glyphicon-eye-open"></span>&#160; Показать</a>
								</li>
								<li>
									<a title="Спрятать все уведомления" class="noticeHide"><span class="glyphicon glyphicon-eye-close"></span>&#160; Спрятать</a>
								</li>
								<li>
									<a title="" class="noticeCloseAll"><span class="glyphicon glyphicon-remove"></span>&#160; Убрать все</a>
								</li>
							</ul>
						</div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-link topdividervertical dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="glyphicon glyphicon-<?=$navBar['glphsd']?>"></span>&#160;<?=$navBar['userFio'];?>&#160;<span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li class="<?=$navBar['navbarStatsShow'];?>"><a href="<?=$navBar['navbarStatsUrl'];?>"><span class="glyphicon glyphicon-stats"></span>&#160; Статистика</a></li>
                                <li><a href="/options/"><span class="glyphicon glyphicon-cog"></span>&#160; Опции</a></li>
                                <li class="<?=$navBar['navbarDevShow'];?>"><a href="<?=$navBar['navbarDevUrl'];?>"><span class="glyphicon glyphicon-wrench"></span>&#160; Dev</a></li>
                                <li class=""><a href="/help/"><i class="far fa-question-circle"></i>&#160; Помощь</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="/auth/?a=exit"><span class="glyphicon glyphicon-log-out"></span>&#160; Выход</a></li>
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
                        <a href="/main/?coll_show=<?=$id?>">
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
                        <a href="/main/?coll_show=<?=$id?>">
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
                        <a href="/main/?coll_show=<?=$id?>">
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
                    <a href="/main/?coll_show=-1">
                        <div coll_block class="collItem">
                            Все
                        </div>
                    </a>
                    <?php
                    foreach ( $coll_other as $id => $name )
                    {
                        ?>
                        <a href="/main/?coll_show=<?=$id?>">
                            <div coll_block class=" collItem"><?= $name ?></div>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- END Блок коллекций -->

        <?php
            $container = 'container';
            if ( $this->varBlock['container'] === 2 )
            {
                $container = 'containerFullWidth';
            } elseif ( $_SESSION['assist']['containerFullWidth'] == 1 )
            {
                $container = 'containerFullWidth';
            }
        ?>
        <div class="<?=$container?> content">
            <?=$content;?>
        </div><!--container-->

        <?php
        debug($_GET,'$_GET');
        debug($this->getQueryParams(),'QueryParams');
        debug($_SESSION,'$_SESSION');
        debug($_COOKIE,'$_COOKIE');
        ?>

        <footer class="footer" style="box-shadow: 0 -1px 5px rgba(0,0,0,.075)">
            <div class="container">
                <?php if ( $_SESSION['user']['access'] == 1 || $_SESSION['user']['access'] == 2 ):?>
                    <a href="/add-edit/?id=0&component=1" class="btn btn-primary">
                        <span class="glyphicon glyphicon-file"></span>
                        <strong> Добавить модель</strong>
                    </a>
                <?php endif; ?>
                <i class="" style="position: absolute; right: 0; margin-right: 15px; margin-top: 10px"><a href="/versions/" title="Список изменений">ver. <?=$this->currentVersion?></a> &#160; разработка by Vadim Bukov</i>
            </div>
            <script src="/web/js_lib/jquery-3.2.1.min.js"></script>
            <script src="/web/js_lib/bootstrap.min.js"></script>
            <script src="/web/js_lib/iziModal.min.js"></script>
            <script src="/web/js_lib/iziToast.min.js"></script>
			<script defer src="../_Globals/js/NavBar.js?ver=<?=time()?>"></script>
			<?php if ($_SESSION['assist']['PushNotice'] == 1): ?>
				<script defer src="../_Globals/js/pushNotice.js?ver=<?=time() ?>"></script>
			<?php endif; ?>
            <script defer src="../_Main/js/main.js?ver=<?=time()?>"></script>
            <script defer src="../_Main/js/ProgressModal.js?ver=<?=time()?>"></script>

        </footer>

    </div><!--content-->
	<div id="pushNoticeWrapp" class="row"></div>
    <?php if (isset($this->blocks['3DPanels'])) echo $this->blocks['3DPanels']; ?>
    <?php require _globDIR_."includes/CollectionsModal.php" ?>
    <script defer src="../_Globals/js/CollectionsModal.js?ver=<?=time()?>"></script>
    <?php $this->endBody() ?>
</body>
</html>
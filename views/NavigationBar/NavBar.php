<?php require('NavBar_Controller.php');?>
<script><?=$wsUserDataJS?></script>
<script src="<?=_views_HTTP_?>js_lib/jquery-3.2.1.min.js"></script>
<script src="<?=_views_HTTP_?>js_lib/bootstrap.min.js"></script>
<script src="<?= _views_HTTP_ . 'Glob_Controllers\js\pushNotice.js?ver=' . time() ?>"></script>
<script src="<?= _views_HTTP_ . 'js_lib/webSocketConnect.js?ver=' . time() ?>"></script>

<nav class="navbar navbar-default nav-bar-marg">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
		<a class="navbar-brand paddfix"><?= _brandName_ ?></a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="navbar-collapse">
	
    <ul class="nav nav-pills navbar-left inlblock" id="navnav">
		<li role="presentation"><a href="../Main/index.php">База</a></li>
		<li role="presentation" class="<?=$topAddModel;?>">
			<div class="btn-group">
				<button type="button" class="btn btn-link topdividervertical dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-menu-hamburger"></span></button>
				<ul class="dropdown-menu">
					<li>
						<a href="../AddEdit/index.php?id=0&component=1<?=$dell;?>"><span class="glyphicon glyphicon-file"></span>&#160; Добавить модель</a>
					</li>
					<li><a href="../Nomenclature/index.php"><span class="glyphicon glyphicon-list-alt"></span>&#160; Номенклатура</a></li>
				</ul>
			</div>
		</li>
                <li role="presentation">
                    <button id="collSelect" onclick="main.collectionSelect(this);" type="button" title="Выбрать Коллекцию" style="font-size: 18px; padding: 5px 8px 0 8px;" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-gem"></i>
                    </button>
                </li>
    </ul>

	<form action="../Glob_Controllers/search.php" method="post" <?=$searchStyle;?> class="navbar-form navbar-left topSearchForm">
		<div class="input-group">
			<span class="input-group-btn">
				<button class="btn btn-link" type="submit" name="search" title="Нажать для поиска">
					<span class="glyphicon glyphicon-search"></span> 
				</button>
			</span>
			<input type="text" class="form-control topSearchInpt" title="Что искать" placeholder="Поиск..." name="searchFor" value="<?=$_SESSION['searchFor'];?>">
			<div class="input-group-btn">
				<button type="button" id="searchInBtn" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Где искать">
					<span><?=$searchInStr;?> </span><span class="caret"></span>
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
				<?=$glphsd;?>&#160;<?=$userRow['fio'];?>&#160;<span class="caret"></span>
			  </button>
			  <ul class="dropdown-menu">
				<?=$navbarStats;?>
				<li><a href="../Options/index.php"><span class="glyphicon glyphicon-cog"></span>&#160; Опции</a></li>
                 <?=$navbarDev;?>
				<li role="separator" class="divider"></li>
				<li><a href="<?=_glob_HTTP_ . "exit.php"?>"><span class="glyphicon glyphicon-log-out"></span>&#160; Выход</a></li>
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
                                        Серебро ( <?= $collectionList['collectionListSilver_cn']; ?> )
                                </div>
                        <?= $collectionList['collectionListSilver']; ?>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3" style="border-left: 1px solid #2F4F4F; max-height: 100%; padding: 0 5px 0 5px;">
                                <div coll_block class=" collItem_TOP">
                                        Золото ( <?= $collectionList['collectionListGold_cn']; ?> )
                                </div>
                        <?= $collectionList['collectionListGold']; ?></div>
                <div class="col-xs-12 col-sm-6 col-md-3" style="border-left: 1px solid #2F4F4F; max-height: 100%; padding: 0 5px 0 5px;">
                                <div coll_block class=" collItem_TOP">
                                        Бриллианты ( <?= $collectionList['collectionListDiamond_cn']; ?> )
                                </div>
                        <?= $collectionList['collectionListDiamond']; ?>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-3" style="border-left: 1px solid #2F4F4F; max-height: 100%; padding: 0 5px 0 5px;">
                                <div coll_block class=" collItem_TOP" >
                                        Разные ( <?= $collectionList['other_cn']; ?> )
                                </div>
                                <a href="controllers/setSort.php?coll_show=-1">
                                        <div coll_block class="collItem">
                                                Все
                                        </div>
                                </a>
                        <?= $collectionList['other']; ?>
                </div>
        </div>
</div>
<!-- END Блок коллекций -->
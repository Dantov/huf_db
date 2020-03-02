<?php

?>
<script src="<?=_views_HTTP_?>Main/js/trytoload.js?ver=004"> </script>

<div class="row">
	<div class="col-xs-12">
		<div class="btn-group pull-right" role="group" aria-label="...">

			<?php if ( $workCentersSort === true ): ?>
			<div class="btn-group" role="group">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span>Участок: <?= $_SESSION['assist']['wcSort']['name'] ?: 'Нет' ?> </span>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<li role="presentation">
						<a href="controllers/setSort.php?wcSort=none">Нет</a></li>
					<li role="presentation" class="divider"></li>
					<? foreach ( $workingCenters as $wcKey => $workingCenter ) : ?>
						<?
							$wcIDs = '';
							foreach ( $workingCenter as $wcID => $wcArray ) $wcIDs .= $wcID.'-';
							$wcIDs = trim($wcIDs,'-');
						?>
						<li role="presentation">
							<a href="controllers/setSort.php?wcSort=<?=$wcIDs ?>"><?=$wcKey ?></a>
						</li>
					<? endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>

			<div class="btn-group" role="group">
				<button id="statusesSelect" type="button" class="btn btn-default dropdown-toggle trigger" data-izimodal-open="#modalStatuses" data-izimodal-transitionin="fadeInDown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span>Статус:<?=$selectedStatusName ?></span>
					<span class="caret"></span>
				</button>
			</div>

			<div class="btn-group" role="group">
				<button type="button" class="btn btn-default dropdown-toggle" title="<?=$chevTitle; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span style="font-size:9px;">
						<span class="glyphicon glyphicon-<?=$chevron_; ?>"></span></span>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<li>
						<a href="controllers/setSort.php?sortDirect=1" title="По возростанию">
							<span class="glyphicon glyphicon-triangle-top"></span> По возростанию</a></li>
					<li>
						<a href="controllers/setSort.php?sortDirect=2" title="По убыванию">
							<span class="glyphicon glyphicon-triangle-bottom"></span> По убыванию</a></li>
				</ul>
			</div>

			<div class="btn-group" role="group">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<?="Сорт. по ".$showsort; ?>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<li>
						<a href="controllers/setSort.php?reg=date">По Дате</a></li>
					<li>
						<a href="controllers/setSort.php?reg=number_3d">По №3D</a></li>
					<li>
						<a href="controllers/setSort.php?reg=vendor_code">По Артикулу</a></li>
					<li>
						<a href="controllers/setSort.php?reg=status">По Статусу</a></li>
				</ul>
			</div>

			<div class="btn-group" role="group">
				<button type="button" class="btn btn-default dropdown-toggle" title="кол-во отображаемых позиций" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span><?=$_SESSION['assist']['maxPos']; ?></span>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<li>
						<a href="controllers/setSort.php?maxPos=12">12</a></li>
					<li>
						<a href="controllers/setSort.php?maxPos=18">18</a></li>
					<li>
						<a href="controllers/setSort.php?maxPos=24">24</a></li>
					<li>
						<a href="controllers/setSort.php?maxPos=48">48</a></li>
					<li>
						<a href="controllers/setSort.php?maxPos=102">102</a></li>
				</ul>
			</div>

			<div class="btn-group" role="group" aria-label="...">
				<a type="button" href="controllers/setSort.php?row_pos=2" class="btn btn-default <?=$activeList; ?>">
					<span class="glyphicon glyphicon-th-list" title="Разбить по комплектам"></span></a>
				<a type="button" href="controllers/setSort.php?row_pos=1" class="btn btn-default <?=$activeSquer; ?>"><span class="glyphicon glyphicon-th-large" title="Отобразить изделия плиткой"></a>
			</div>
			<div class="btn-group" role="group">
				<button type="button" class="btn btn-default <?=$activeWorkingCenters; ?> <?=$activeWorkingCenters2; ?> dropdown-toggle" title="кол-во отображаемых позиций" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="glyphicon glyphicon-save-file" title="Таблицы Рабочих участков">
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<li>
						<a href="controllers/setSort.php?row_pos=3">
							<span class="glyphicon glyphicon-tasks"></span> Отобразить в таблице Рабочих Участков</a></li>
					<li>
						<a href="controllers/setSort.php?row_pos=4">
							<span class="glyphicon glyphicon-menu-hamburger"></span> Отобразить по Рабочим Центрам</a></li>
				</ul>
			</div>

			<div class="btn-group <?=$toggleSelectedGroup; ?>" id="selectedGroup" role="group">
				<button type="button" class="btn btn-default dropdown-toggle" title="Выделенные модели" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="glyphicon glyphicon-screenshot"></span>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu dropdown-menu-right">
					<li role="presentation">
						<a href="controllers/selectionController.php?selectedModels=show">Показать</a></li>
					<li role="presentation">
						<a href="../Edit">Проставить статус</a></li>
					<li role="presentation" class="divider"></li>
					<?=$selectedModelsByLi; ?>
				</ul>
			</div>
			<div class="btn-group" role="group" aria-label="...">
				<a type="button" id="selectMode" onclick="selects.toggleSelectionMode(this);" class="btn btn-default <?=$variables['activeSelect']; ?>">
					<span class="glyphicon glyphicon-edit" title="Режим выделения"></span></a>
			</div>
		</div>

		<!-- Кнопки Xlsx PDF -->
		<div class="btn-group pull-left" role="group">
			<?php $drawBy_ = $_SESSION['assist']['drawBy_']; ?>
			<?php
			if ( $drawBy_ == 3)
				: ?>
			<a onclick="main.sendXLS()"  id="sendXLS" class="btn btn-link" style="font-size: 18px; padding: 5px 8px 0 8px;" type="button" title="Записать коллекцию в Excel" >
				<i class="far fa-file-excel"></i>
			</a>
			<?php elseif( $drawBy_ < 5 ): ?>
			<a onclick="main.sendPDF()"  id="sendPDF" class="btn btn-link" style="font-size: 18px; padding: 5px 8px 0 8px;" type="button" title="Записать коллекцию в PDF" >
				<i class="far fa-file-pdf"></i>
			</a>
			<?php endif; ?>
			<?php
			if ( $drawBy_ == 3 )
				: ?>
			<a id="expiredButon" href="controllers/setSort.php?row_pos=5" class="btn btn-link" style="font-size: 18px; padding: 5px 8px 0 8px;" type="button" title="Таблица просроченных" >
				<i class="far fa-clock"></i>
			</a>
			<?php endif; ?>
			<?php
			if ( $drawBy_ == 5 )
				: ?>
			<a id="wCentersButon" href="controllers/setSort.php?row_pos=3" class="btn btn-link" style="font-size: 18px; padding: 5px 8px 0 8px;" type="button" title="Таблица Рабочих Участков" >
				<i class="fas fa-tasks"></i>
			</a>
			<?php endif; ?>
		</div>

		<div class="pull-left">
			<h3 style="margin: 0 0 0 15px; padding-top:4px;">
				<a type="button" title="<?=$collectionName ?>" href="controllers/setSort.php?coll_show=<?=$_SESSION['assist']['collection_id']?>">
					<span id="collectionName"><?=$collectionName?></span>
				</a>
			</h3>
		</div>
	</div><!-- end col -->
</div><!-- /row -->
<div class="clearfix"></div>
<hr/>
<div class="row loading_cont" id="loadeding_cont">
	<?php if ( !isset($_SESSION['nothing']) ): ?>
		<?php if ( $wholePos == 0 ): ?>
			<img src="<?=_rootDIR_HTTP_ ?>web/picts/web1.png" width="10%"/>
			В этой коллекции изделий нет.
		<?php endif; ?>
	<?php else: ?>
		<img src="<?=_rootDIR_HTTP_ ?>web/picts/web1.png" width="10%"/>
		<?
			$showModels .= $_SESSION['nothing'];
			unset($_SESSION['nothing']); 
		?>
	<?php endif; ?>
	<?=$showModels ?>
</div>
<center><!-- paggination -->
	<span class="statsbuttom"><?=$statsbottom?></span>
	<?=$pagination?>
</center>

<?php include('includes/modal.php'); ?>
<?php include('includes/progressModal.php'); ?>
<script src="<?=_views_HTTP_?>Main/js/Selects.js?ver=<?=time(); ?>"></script>
<? if ($_SESSION['assist']['PushNotice'] == 1): ?>
	<? include_once _globDIR_.'includes/pushNotice.php' ?>
 	<script src="<?=_glob_HTTP_ ?>js/PushNotice.js?ver='.time().'"></script>
<? endif; ?>
<div id="blackCover"></div>
<!-- progress bar -->
<div id="pdf_result" class="alert alert-success">
	<div id="progressStatus" style="font-weight:600;"></div>
	<div class="progress">
		<div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
	</div>
</div>
<!-- end progress bar -->
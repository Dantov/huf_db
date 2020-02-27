<?php
include('../Glob_Controllers/sessions.php');
if( !isset( $_SESSION['access'] ) || $_SESSION['access'] != true ){
	header("location: ../index.php");
} else {
	include('controllers\stat_Controller.php');
?>
<!DOCTYPE HTML>
<html>
<head><?php include('../Glob_Controllers/head_adm.php');?></head>
<body class="<?=$_SESSION['assist']['bodyImg'];?>">
	<?php include('../NavigationBar/NavBar.php');?>
	<div class="container">
		<div class="row">
			<p class="lead text-info text-center">Статистика</p>
			<div class="col-xs-12 stats_table">
      
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab">Посетители</a></li>
					<li role="presentation"><a href="#tab2" role="tab" data-toggle="tab">Модели</a></li>
					<li role="presentation"><a href="#tab3" role="tab" data-toggle="tab">Общее</a></li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active in fade" id="tab1">
						<center><h4>Сейчас на сайте</h4></center>
						<table class="table table-hover">
							<thead>
								<tr>
									<th>№</th>
									<th>ФИО</th>
									<th>Дата последнего визита</th>
									<th>IP адрес</th>
								</tr>
							</thead>
							<tbody>
							<?php
								for ( $i = 0; $i < count($users); $i++ ) {
									$datearr = explode(' ', $users[$i]['date']);
									$userDate = date_create( $datearr[0] )->Format('d.m.Y');
									$userTime = $datearr[1];
							?>
								<tr>
									<td><?=$i+1;?></td>
									<td><?=$users[$i]['fio'];?></td>
									<td><?=$userDate.' - '.$userTime;?></td>
									<td><?=$users[$i]['ip'];?></td>
								</tr>
							<?php
								}
							?>
								<tr class="warning">
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div> <!-- end of panel 1 -->
			
					<div role="tabpanel" class="tab-pane fade" id="tab2">
						<center><h4>Коллекции в базе</h4></center>
						<table class="table table-hover">
							<thead>
								<tr>
									<th>№</th>
									<th>Коллекция</th>
									<th>Комплектов</th>
									<th>Изделий</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$complAll = 0;
								$modelsAll = 0;
								for ( $i = 0; $i < count($models); $i++ ) {
									$complAll += $models[$i]['wholeCompl'];
									$modelsAll += $models[$i]['wholePos'];
							?>
								<tr>
									<td><?=$i+1;?></td>
									<td><a href="../Main/controllers/setSort.php?sCollId=<?=$models[$i]['id'];?>" id="collection"><?=$models[$i]['name'];?></a></td>
									<td><?=$models[$i]['wholeCompl'];?></td>
									<td><?=$models[$i]['wholePos'];?></td>
								</tr>
							<?php
								}
							?>
								<tr class="warning">
									<td></td>
									<td><b>Всего:</b></td>
									<td><b><?=$complAll;?></b></td>
									<td><b><?=$modelsAll;?></b></td>
								</tr>
							</tbody>
						</table>
						<center><h4>Топ 10 лайков/дизлайков</h4></center>
						<div class="row">
							<div class="col-xs-6">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>№</th>
											<th>Номер 3Д / Арт.</th>
											<th>Лайки</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$i=1;
										foreach($likes['likes'] as $key => $value) {
											if ( $i > 10 ) break;
											$arr = explode(';',$key);
									?>
										<tr>
											<td><?=$i++;?></td>
											<td><a href="../ModelView/index.php?id=<?=$arr[0];?>" id="collection"><?=$arr[1]?></a></td>
											<td><?=$value;?></td>
										</tr>
									<?php
										}
									?>
										<tr class="warning">
											<td></td>
											<td><b></b></td>
											<td><b></b></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-xs-6">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>№</th>
											<th>Номер 3Д / Арт.</th>
											<th>Дизлайки</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$i=1;
										foreach($likes['dislikes'] as $key => $value) {
											if ( $i > 10 ) break;
											$arr = explode(';',$key);
									?>
										<tr>
											<td><?=$i++;?></td>
											<td><a href="../ModelView/index.php?id=<?=$arr[0];?>" id="collection"><?=$arr[1]?></a></td>
											<td><?=$value;?></td>
										</tr>
									<?php
										}
									?>
										<tr class="warning">
											<td></td>
											<td><b></b></td>
											<td><b></b></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div> <!-- end of panel 2 -->
			
					<div role="tabpanel" class="tab-pane fade" id="tab3">
						<div class="row">
							<div class="col-xs-6">
								<center><h5>Модельеры сделали</h5></center>
								<table class="table table-hover">
									<thead>
										<tr>
											<th>№</th>
											<th>Имя</th>
											<th>Кол-во комплектов</th>
											<th>Кол-во моделей</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$complAll = 0;
										$modelsAll = 0;
										for ( $i = 0; $i < count($modelsBy3Dmodellers); $i++ ) {
											$complAll += $modelsBy3Dmodellers[$i]['wholeCompl'];
											$modelsAll += $modelsBy3Dmodellers[$i]['wholePos'];
									?>
										<tr>
											<td><?=$i+1;?></td>
											<td>
												<form action="../Glob_Controllers/search.php" method="post" >
													<input type="hidden" name="searchFor" value="<?=$modelsBy3Dmodellers[$i]['name'];?>">
													<input class="btn btn-link" type="submit" name="search" value="<?=$modelsBy3Dmodellers[$i]['name'];?>">
												</form>
											</td>
											<td><?=$modelsBy3Dmodellers[$i]['wholeCompl'];?></td>
											<td><?=$modelsBy3Dmodellers[$i]['wholePos'];?></td>
										</tr>
									<?php
										}
									?>
										<tr class="warning">
											<td></td>
											<td><b>Всего</b></td>
											<td><b>Комплектов: <?=$complAll;?></b></td>
											<td><b>Моделей: <?=$modelsAll;?></b></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-xs-6">
								<center><h5>За Авторством</h5></center>
								<table class="table table-hover">
									<thead>
										<tr>
											<th>№</th>
											<th>Имя</th>
											<th>Кол-во комплектов</th>
											<th>Кол-во моделей</th>
										</tr>
									</thead>
									<tbody>
									<?php
										$complAll = 0;
										$modelsAll = 0;
										for ( $i = 0; $i < count($modelsByAuthors); $i++ ) {
											$complAll += $modelsByAuthors[$i]['wholeCompl'];
											$modelsAll += $modelsByAuthors[$i]['wholePos'];
									?>
										<tr>
											<td><?=$i+1;?></td>
											<td>
												<form action="../Glob_Controllers/search.php" method="post" >
													<input type="hidden" name="searchFor" value="<?=$modelsByAuthors[$i]['name'];?>">
													<input class="btn btn-link" type="submit" name="search" value="<?=$modelsByAuthors[$i]['name'];?>">
												</form>
											</td>
											<td><?=$modelsByAuthors[$i]['wholeCompl'];?></td>
											<td><?=$modelsByAuthors[$i]['wholePos'];?></td>
										</tr>
									<?php
										}
									?>
										<tr class="warning">
											<td></td>
											<td><b>Всего</b></td>
											<td><b>Комплектов: <?=$complAll;?></b></td>
											<td><b>Моделей: <?=$modelsAll;?></b></td>
										</tr>
									</tbody>
									</tbody>
								</table>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6">
								<center><h5>Место на диске</h5></center>
								<table class="table table-hover">
									<thead>
										<tr>
											<th>№</th>
											<th>Имя</th>
											<th>Кол-во</th>
											<th>Размер</th>
										</tr>
									</thead>
									<tbody>
									<?php
										
									?>
										<tr>
											<td>1</td>
											<td>Картиники</td>
											<td><?=$fileSizes['imgFileCounts'];?> шт.</td>
											<td><?=$fileSizes['imgFileSizes'];?></td>
										</tr>
										<tr>
											<td>2</td>
											<td>Стл файлы</td>
											<td><?=$fileSizes['stlFileCounts'];?> шт.</td>
											<td><?=$fileSizes['stlFileSizes'];?></td>
										</tr>
									<?php
										
									?>
										<tr class="warning">
											<td></td>
											<td><b>Всего</b></td>
											<td><b><?=$fileSizes['overalCounts'];?> шт.</b></td>
											<td><b><?=$fileSizes['overalSizes'];?></b></td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="col-xs-6">
							</div>
						</div>
						
					</div> <!-- end of panel 3 -->
				
				</div><!-- end of Tab content -->
			</div>
			
			<a class="btn btn-default" type="button" href="<?=$_SESSION['prevPage'];?>"><span class="glyphicon glyphicon-triangle-left"></span> Назад</a>
		</div><!--row-->
	
		<?php include('../Glob_Controllers/bottomScripts_adm.php');?>
	</div><!--container-->
	
	<script src="../Main/js/main.js?ver=<?=time();?>"></script>
</body>
</html>
<?php
	} //ELSE
?>
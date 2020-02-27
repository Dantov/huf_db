<?php
include('../Glob_Controllers/sessions.php');
include('controllers/opt_Controller.php');
?>
<!DOCTYPE HTML>
<html>
<head><?php include('../Glob_Controllers/head_adm.php');?></head>
<body class="<?=$_SESSION['assist']['bodyImg'];?>">
	<?php include('../NavigationBar/NavBar.php');?>
	<div class="container">
		<div class="row">
			<p class="lead text-info text-center">Опции</p>
			<div class="col-xs-12 stats_table">
      
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab">Общие</a></li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active in fade" id="tab1">
						<center><h4></h4></center>
						<p><label for="PN_control" style="cursor:pointer;">Показывать уведомления</label> &nbsp;<input id="PN_control" type="checkbox" <?=$PushNoticeCheck;?>></p>
						<p>Цвета фона:
							<div class="row">
							<?php
								for( $i = 0; $i < count($bgsImg); $i++ ) {
							?>
								<div class="col-xs-6 col-sm-4 col-md-2 status123">
									<input id="bg_img<?=$i;?>" <?=$bgsImg[$i]['checked'];?> data-class="<?=$bgsImg[$i]['body'];?>" name="bg_img" type="radio" class="bg_img">
									<label for="bg_img<?=$i;?>" style="cursor:pointer;">
										<div class="<?=$bgsImg[$i]['prev'];?> img-responsive" style="width:165px; height:92px;"></div>
									</label>
								</div>
							<?php
								}
							?>
							</div>
						</p>
					</div> <!-- end of panel 1 -->
					
				</div><!-- end of Tab content -->
			</div>
			
			<a class="btn btn-default" type="button" href="<?=$_SESSION['prevPage'];?>"><span class="glyphicon glyphicon-triangle-left"></span> Назад</a>
		</div><!--row-->
	
		<?php include('../Glob_Controllers/bottomScripts_adm.php');?>
	</div><!--container-->
	
	<script src="../Main/js/main.js?ver=<?=time();?>"></script>
	<script src="js/options.js?ver=<?=time();?>"></script>
</body>
</html>
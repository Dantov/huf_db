<?php
include('../Glob_Controllers/sessions.php');
include('controllers/edit_Controller.php');
?>
<!DOCTYPE HTML>
<html>
	<head><?php include('../Glob_Controllers/head_adm.php');?></head>
	<body class="<?=$_SESSION['assist']['bodyImg'];?>">
		<?php include('../NavigationBar/NavBar.php');?>
		<div class="container">
		
			<!-- заголовок -->
			<div class="row">
				<div class="col-xs-12 col-sm-2">
					<a class="btn btn-sm btn-info pull-left" href="<?=$prevPage;?>" role="button">
						<span class="glyphicon glyphicon-triangle-left"></span> 
						Назад
					</a>
				</div><!--end col -->
				<div class="col-xs-12 col-sm-8">
					<center>
						<h4 class="text-warning" id="topName" style="margin: 5px 0 0 0;"><?=$header;?></h4>
					</center>
				</div><!--end col -->
                <div class="col-xs-12 text-right">
                    <p id="complects">
                        <?=$strModels;?>
                    </p>
                </div><!--end col -->
			</div><!--end row-->
			<!-- конец заголовка -->
			
                <hr />

                <!--MAIN FORM-->
                <form method="post" id="editform" enctype = "multipart/form-data">

                <div class="row">


                    <!-- Statuses -->
                    <div class="col-xs-12 status" id="workingCenters">
                        <button id="openAll" title="Раскрыть Все" onclick="event.preventDefault()" style="margin-bottom: 10px" class="btn btn-sm btn-warning"><span class="glyphicon glyphicon-option-vertical"></span> Раскрыть Все</button>
                        <button id="closeAll" title="Закрыть Все" onclick="event.preventDefault()" style="margin-bottom: 10px" class="hidden btn btn-sm btn-primary"><span class="glyphicon glyphicon-option-horizontal"></span> Закрыть Все</button>
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
                                                <a class="list-group-item active"><?=$subUnit['descr']?></a>
                                                <?php foreach ( $subUnit['statuses'] as $status ) :?>
                                                    <a class="list-group-item">
                                                        <input type="radio" <?=$status['check'];?> name="status" id="<?=$status['name_en'];?>" aria-label="..." value="<?=$status['id'];?>">
                                                        <label for="<?=$status['name_en'];?>" title="<?=$status['title'];?>">
                                                            <span class="glyphicon glyphicon-<?=$status['glyphi'];?>"></span>
                                                            <?=$status['name_ru'];?>
                                                        </label>
                                                    </a>
                                                <?php endforeach; ?>
                                                <a title="Ответственный" class="list-group-item list-group-item-success"><?=$subUnit['user']?></a>
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
                    <!-- END Statuses -->


                </div><!--end row-->
                <hr />
    
                <input type="hidden" name="save" value="1"/>
                <input type="hidden" name="date" value="<?=date('Y-m-d'); ?>" />
				
                </form>
                    <div class="row">
                        
                        <div class="col-xs-12">
                            <center id="tosubmt">
                                <button class="btn btn-default"  onclick="submitForm();" >
                                    <span class="glyphicon glyphicon-floppy-disk"></span> 
                                    Сохранить
                                </button>
                            </center>
                        </div><!--end col-xs-6-->
                       
                    </div><!--end row-->
                <p></p>
		
                <img id="imageBoxPrev" width="200px" class="img-thumbnail hidden"/>
                <?php include('../AddEdit/includes/resultModal.php');?>
                <script src="js/edit.js?ver=<?=time();?>"></script>

                <?php include('../Glob_Controllers/bottomScripts_adm.php');?>
			
		</div><!--container-->
        <div class="AddEditSideButtons" id="AddEditSideButtons">
            <div class="btn-group-vertical" role="group" aria-label="...">
                <button type="button" class="btn btn-info hidden" title="Вверх" onclick="pageUp();"><span class="glyphicon glyphicon-chevron-up"></span></button>
                <button type="button" class="btn btn-success" title="Сохранить" onclick="submitForm();"><span class="glyphicon glyphicon-floppy-disk"></span></button>
                <button type="button" class="btn btn-info" title="Вниз" onclick="pageDown();"><span class="glyphicon glyphicon-chevron-down"></span></button>
            </div>
        </div>
	</body>
</html>
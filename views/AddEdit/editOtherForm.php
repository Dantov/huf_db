<?php
include('sessions.php');

if( !isset( $_SESSION['access'] ) || $_SESSION['access'] != true ){
header("location: ../index.php");
} else { ?>
<!DOCTYPE HTML>
<html>

<head>
  <?php include('head_adm.php'); ?>
</head>

<body>
<?php
	include('navBar_adm.php');
	include('functs.php');
	
	$id = $_GET['id'];

	$dop_vc = mysqli_query($connection, "  SELECT * FROM vc_links WHERE pos_id='$id' ");
	$img = mysqli_query($connection, "  SELECT * FROM images WHERE pos_id='$id' ");
	
	$result = mysqli_query($connection, " SELECT * FROM stock WHERE id='$id' ");
	$row = mysqli_fetch_assoc($result);
	
	// автозаполнение для добавления комплекта
	$_SESSION['general_data']['id']             = $id;
	$_SESSION['general_data']['number_3d']      = $row['number_3d'];
	$_SESSION['general_data']['vendor_code']    = $row['vendor_code'];
	$_SESSION['general_data']['collection'] 	= $row['collections'];
	$_SESSION['general_data']['author']         = $row['author'];
	$_SESSION['general_data']['modeller3d']     = $row['modeller3D'];
	$_SESSION['general_data']['model_weight']   = $row['model_weight'];
	$_SESSION['general_data']['model_covering'] = $row['model_covering'];
	$_SESSION['general_data']['model_material'] = $row['model_material'];
	$_SESSION['general_data']['model_type']     = $row['model_type'];
	$_SESSION['general_data']['description']    = $row['description'];
	
	$VCcreater = 'disabled';
	$VCcreaterDis = ''; 
	if ( (int)$_SESSION['user_access'] === 4 ) {
		$VCcreater = '';
		$VCcreaterDis = 'disabled'; 
	}
?>

<div class="container">

	<!-- заголовок -->
    <div class="row">
		<div class="col-xs-12 col-sm-2">
			<?php
				$thisPage = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				if ( $thisPage !== $_SERVER["HTTP_REFERER"] ) {
					$_SESSION['prevPage'] = $_SERVER["HTTP_REFERER"];
				}
			?>
			<a class="btn btn-sm btn-info pull-left" href="<?=$_SESSION['prevPage'];?>" role="button">
				<span class="glyphicon glyphicon-triangle-left"></span> 
				Назад
			</a>
		<?php 
			echo "
				<a style=\"margin: 0 0 0 7px;\" class=\"btn btn-sm btn-info pull-left\" href=\"show_pos_adm.php?id=$id\" role=\"button\">
				   Просмотр
				</a>
			";
		?>
	    </div><!--end col -->
		<div class="col-xs-12 col-sm-8">
			<center>
			  <h4 class="text-warning" id="topName" style="margin: 5px 0 0 0;">
				<?php 
					printHeaderEditAddForm('true',$_SESSION['general_data']['number_3d'],$connection);
				?>
			  </h4>
			</center>
		</div><!--end col -->
		<div class="col-xs-12 col-sm-2">

	    </div><!--end col -->
	</div><!--end row-->
	<!-- конец заголовка -->
    <hr />

  <form method="post" id="addform">
	<div class="row">
	   <div class="col-sm-6">
        <div class="form-group" title="'000' вводить не обязательно. По нему формируются комплекты">
           <label for="number_3d"><span class="glyphicon glyphicon-question-sign"></span> номер 3D:</label>
           <input disabled id="num3d" type="text" name="number_3d" class="form-control" value="<?=$_SESSION['general_data']['number_3d'];?>">
        </div>
		</div>
	    <div class="col-sm-6">
			<div class="form-group" title="Добавляется во все изделия в комплекте (если там было пусто)">
				<label for="shortName">
					<span class="glyphicon glyphicon-question-sign" ></span> 
					Фабричный артикул:
				</label>
				<input <?=$VCcreater;?> id="vendor_code" type="text" name="vendor_code" class="form-control" value="<?php echo $_SESSION['general_data']['vendor_code']; ?>">
			</div>
		</div>
	</div> <!--end row-->
	  
		<div class="form-group">
          <label for="collection"><i class="fas fa-gem"></i> Коллекция:</label>
	       <input disabled id="collection" type="text" name="collection" class="form-control" value="<?=$_SESSION['general_data']['collection'];?>">
        </div>
		
	<div class="row">
		
	  <div class="col-sm-6">
		<label for="author"><span class="glyphicon glyphicon-user"></span> Автор:</label>
		  <input disabled type="text" class="form-control" aria-label="..." name="author" value="<?=$_SESSION['general_data']['author'];?>" >
	  </div>
  
		<div class="col-sm-6">
			<label for="modeller3d"><span class="glyphicon glyphicon-user"></span> 3Д модельер:</label>
			  <input disabled type="text" class="form-control" aria-label="..." name="modeller3d" value="<?=$_SESSION['general_data']['modeller3d'];?>">
		</div>
		
		<div class="col-xs-12">
			<br/>
		</div>
		<div class="col-xs-3">   
			<label for="model_type">
				<span class="glyphicon glyphicon-eye-open"></span> 
				Вид модели:
			</label>
			<input disabled type="text" id="modelType" class="form-control" aria-label="..." name="model_type" value="<?=$row['model_type'];?>" />
		</div>
				
			<div class="col-xs-3">
				<label for="model_weight">
					<i class="fab fa-quinscape"></i>
					Размерный Ряд:
				</label>
				<input <?=$VCcreaterDis;?> type="text" class="form-control" id="size_range" name="size_range" value="<?=$row['size_range'];?>" />
			</div>
				
			<div class="col-xs-3">
				<label for="model_weight">
					<span class="glyphicon glyphicon-scale"></span> 
					Вес 3D:
				</label>
				<input disabled step="0.05" type="number" class="form-control" aria-label="..." required name="model_weight" value="<?=$_SESSION['general_data']['model_weight']; ?>">
			</div>
			
			<div class="col-xs-3">
				<?php
					if ( (int)$_SESSION['user_access'] === 3 ) {
				?>
						<label for="model_weight">
							<span class="glyphicon glyphicon-usd"></span>
							Стоимость печати:
						</label>
						<input type="text" class="form-control" id="print_cost" name="print_cost" value="<?=$row['print_cost'];?>" />
				<?php	
					}
				?>
			</div>
				<?php
					if ( isset($_SESSION['general_data']['model_material']) ) {
							$material = setMaterial($_SESSION['general_data']['model_material']);
					} else {
						$material['metall_silv'] = "checked";
					}
				?>
				<?php if ( (int)$_SESSION['user_access'] === 4 ) { ?>
				<div class="col-xs-6" id="material">
				
					<label for=""></label>
					<p></p>
					<span class="model_material"><span class="glyphicons-cube"></span> <b>Материал изделия:</b></span>

					<input type="radio" <?=$material['metall_silv'];?> name="model_material" id="sivler" class="radio" value="Серебро">
					<label for="sivler" class="sivler">
						<span class="redSilverLabel"> Серебро 925</span>
					</label>
						
					<input type="radio" <?=$material['metall_gold'];?> name="model_material" id="gold" class="radio" value="Золото">
					<label for="gold" class="gold">
						<span class="redGoldLabel">	Золото</span>
					</label>
						
					<div class="goldsample">
					
						<input type="radio" <?=$material['probe585'];?> name="samplegold" id="sample585" class="radio" value="585">
						<label for="sample585" class="sample">
							<span class="">585&#176;  &nbsp;&nbsp;</span>
						</label>
						<input type="radio" <?=$material['probe750'];?> name="samplegold" id="sample750" class="radio" value="750">
						<label for="sample750" class="sample">
							<span class="">750&#176;</span>
						</label>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="checkbox" <?=$material['gold_white'];?> name="whitegold" id="whitegold" class="whitegChk radio" value="Белое">
						<label for="whitegold" class="sample">
							<span class="">Белое&nbsp;&nbsp;</span>
						</label>
						<input type="checkbox" <?=$material['gold_red'];?> name="redgold" id="redgold" class="redgChk radio" value="Красное">
						<label for="redgold" class="sample">
							<span class="">Красное&nbsp;&nbsp;</span>
						</label>
						<input type="checkbox" <?=$material['gold_yellow'];?> name="eurogold" id="eurogold" class="eurogChk radio" value="Желтое(евро)">
						<label for="eurogold" class="sample">
							<span class="">Желтое(евро)</span>
						</label>
							
					</div>

				</div>
				
				<?php
					if ( isset($_SESSION['general_data']['model_covering']) ) {
							$covering = setCovering($_SESSION['general_data']['model_covering']);
					} else {
						$covering['rhodium'] = "checked";
						$covering['full'] = "checked";
					}
				?>
				
				<div class="col-xs-6" id="covering">
				<label for=""></label>
				<p></p>
						<span class="model_material"><i class="fas fa-cube fasL"></i> <b>Покрытие:</b></span>

						<input type="checkbox" <?=$covering['rhodium'];?> name="rhodium" id="rhodium" class="rhodium radio" value="Родирование">
						<label for="rhodium" class="sivler rhodiumed">
							<span class="redSilverLabel">Родирование</span>
						</label>
						
						<input type="checkbox" <?=$covering['golding'];?> name="golding" id="golding" class="radio" value="Золочение">
						<label for="golding" class="gold golded">
							<span class="redGoldLabel golding">Золочение</span>
						</label>
						&nbsp;&nbsp;&nbsp;
						<input type="checkbox" <?=$covering['blacking'];?> name="blacking" id="blacking" class="radio" value="Чернение">
						<label for="blacking" class="gold blackeg">
							<span class="redGoldLabel blacking">Чернение</span>
						</label>
						
						<div class="rhodium">
						
							<input type="radio" <?=$covering['full'];?> name="rhodium_fill" id="rhodium_full" class="radio" value="Полное">
							<label for="rhodium_full" class="sample">
								<span class="">Полное  &nbsp;&nbsp;</span>
							</label>
							
							<input type="radio" <?=$covering['onPartical'];?> name="rhodium_fill" id="rhodium_part" class="radio rhod_part" value="Частичное">
							<label for="rhodium_part" class="sample">
								<span class="">Частичное</span>
							</label>
							
							<div class="rhodium_parts">
							
								<input type="checkbox" <?=$covering['onProngs'];?> name="onProngs" id="onProngs" class="radio" value="По крапанам">
								<label for="onProngs" class="sample">
									<span class="">По крапанам &nbsp;&nbsp;</span>
								</label>
								
								<input type="checkbox" <?=$covering['parts'];?> name="onParts" id="onParts" class="onParts radio" value="Отдельные части">
								<label for="onParts" class="sample">
									<span class="">Отдельные части</span>
								</label>
								<input type="text" name="rhodium_PrivParts" class="mytextinput rhodium_PrivParts" value="<?=$covering['partsStr'];?>">
							</div>
							
						</div>
				</div>
				<?php } ?>
	
	</div><!-- /.row -->
	
	<hr />
	<?php
		if ( isset($img) ) {
			while( $row_img = mysqli_fetch_assoc($img) ) {
				if ( $row_img['main'] == 1 ) {
	?>
					<div class="col-xs-6 col-sm-3 col-md-2 image_row">
						<div class="ratio img-thumbnail">
							<div class="ratio-inner ratio-4-3">
								<div class="ratio-content">
									<img src="<?=$uploaddir."/".$row_img['img_name'];?>" class="dopImg img-responsive "/>
								</div>
							</div>
						</div>
					</div>
	<?php   
				}
			}
		}
	?>
	
	<div class="row">
        <div class="col-xs-12">
		
		<?php if ( (int)$_SESSION['user_access'] === 4 ) { ?>

		   <div class="panel panel-default">
            <div class="panel-heading">
				<span class="glyphicon glyphicon-link"></span>
				<strong> Ссылки на другие артикулы:</strong>
				<button class="btn btn-sm btn-default pull-right" style="top:-5px !important; position:relative;" type="button" onclick="addLinksRow(this);" title="Добавить отсылку">
					<span class="glyphicon glyphicon-plus"></span>
				</button>
			</div>
			
			<table class="table">
              <thead>
               <tr class="thead11">
                 <th>№</th><th>Название</th><th>Артикул / Номер 3D</th><th>Описание</th><th></th>
               </tr>
              </thead>
              <tbody id="dop_vc_table">
				<?php
					
					$num_c = 1;
					$row_vc_countr = 0;
					$row_vc_countrForInpt = 0;
				
					while( $row_dop_vc = mysqli_fetch_assoc($dop_vc) ) {
				?>
					<tr class="dop_vc_row">
						<td><?=$num_c++;?></td>
						<td><?php include('DopArticl_names_input.php'); ?></td>
						<td>
							<?php 
								if (isset($isSchwenze) && !empty($isSchwenze)) {
									include('schwenze.php');
									unset($isSchwenze);
								} else {
							?>
								<input type="text" class="form-control" name="num3d_vc_[]" value="<?=$row_dop_vc['vc_3dnum']; $_SESSION['vc_links']['vc_3dnum'][] = $row_dop_vc['vc_3dnum'];?>">
							<?php	
								}
							?>
						</td>
						<td>
							<input type="text" class="form-control" name="descr_dopvc_[]" value="<?=$row_dop_vc['descript']; $_SESSION['vc_links']['descript'][] = $row_dop_vc['descript'];?>">
						</td>
					</tr>
				<?php 
						$row_vc_countr++; 
						$row_vc_countrForInpt++; 
					}
					unset($row_vc_countr,$row_dop_vc,$num_c);
				?>
				<tr style="display:none;"></tr>
              </tbody>
            </table>
		   </div><!-- end panel dopArticls-->
		<?php } ?>
		
		    <label for="descr"><span class="glyphicon glyphicon-comment"></span> Примечания:</label>
            <textarea id="descr" class="form-control" rows="3" name="description" style="margin:0px 0 15px 0 !important;"><?php 
			echo $_SESSION['general_data']['description'];
		  ?></textarea>
		</div> <!--col-xs-12-->
	
    </div><!--row-->
	
	<?php if ( (int)$_SESSION['user_access'] === 3 ) { ?>
    <hr />
    <div class="row">
	<?php 
			if ( isset($row['status']) && !empty($row['status']) ) {
				$stts = trim($row['status']);
				if ( $stts == "На проверке" )   $chec_onVerifi   = "checked";
				if ( $stts == "В росте" )       $chec_onPrint    = "checked";
				if ( $stts == "Готовая ММ" )    $chec_MMdone     = "checked";
				if ( $stts == "Вышел сигнал!" ) $chec_signalDone = "checked";
				if ( $stts == "В работе" )      $chec_wip        = "checked";
				if ( $stts == "В работе(Монтировка)" ) $chec_wipM = "checked";
				if ( $stts == "В ремонте" )     $chec_onrep      = "checked";
				if ( $stts == "Отложено" )      $chec_defer      = "checked";
			}else {
				$chec_bydef = "checked";
			}
	?>
	  <div class="col-xs-12 status">
	    <p>
			<b><span class="glyphicon glyphicon-ok"></span> &#160;Статус:</b>
		</p>
		<div class="row">
		
		  <div class="col-xs-12 col-sm-6 col-md-4">
		    <input disabled type="radio" <?=$chec_onVerifi,$chec_bydef;?> name="status" id="onVerifi" aria-label="..." value="На проверке">
			<label for="onVerifi" style="cursor:not-allowed;background-color:#DCDCDC;">
				<span class="">	На проверке</span>
			</label>
		  </div><!--end col-xs-6-->
		  
		  <div class="col-xs-12 col-sm-6 col-md-4">
		    <input type="radio" <?=$chec_onPrint;?> name="status" id="onPrint" aria-label="..." value="В росте">
			<label for="onPrint" class="">
				<span class="">	В росте</span>
			</label>
		  </div><!--end col-xs-6-->
		  
		  <div class="col-xs-12 col-sm-6 col-md-4">
		    <input disabled type="radio" <?=$chec_MMdone;?> name="status" id="MMdone" aria-label="..." value="Готовая ММ">
			<label for="MMdone" style="cursor:not-allowed;background-color:#DCDCDC;">
				<span class="">	Готовая ММ</span>
			</label>
		  </div><!--end col-xs-6-->
		  
		  <div class="col-xs-12 col-sm-6 col-md-4">
		    <input disabled type="radio" <?=$chec_signalDone;?> name="status" id="signalDone" aria-label="..." value="Вышел сигнал!">
			<label for="signalDone" style="cursor:not-allowed;background-color:#DCDCDC;">
				<span class="">	Вышел сигнал!</span>
			</label>
		  </div><!--end col-xs-6-->
		  
		  <div class="col-xs-12 col-sm-6 col-md-4">
		    <input disabled type="radio" <?=$chec_wip;?> name="status" id="wip" aria-label="..." value="В работе">
			<label for="wip" style="cursor:not-allowed;background-color:#DCDCDC;">
				<span class="">	В работе 3D</span>
			</label>
		  </div><!--end col-xs-6-->
		  
		  <div class="col-xs-12 col-sm-6 col-md-4">
		    <input disabled type="radio" <?=$chec_wipM;?> name="status" id="wipM" aria-label="..." value="В работе(Монтировка)">
			<label for="wipM" style="cursor:not-allowed;background-color:#DCDCDC;">
				<span class="">	В работе (Монтировка)</span>
			</label>
		  </div><!--end col-xs-6-->
		  
		  <div class="col-xs-12 col-sm-6 col-md-4">
		    <input type="radio" <?=$chec_onrep;?> name="status" id="onRepaire" aria-label="..." value="В ремонте">
			<label for="onRepaire" class="">
				<span class="">	В ремонте</span>
			</label>
		  </div><!--end col-xs-6-->
		  
		  <div class="col-xs-12 col-sm-6 col-md-4">
		    <input disabled type="radio" <?=$chec_defer;?> name="status" id="defer" aria-label="..." value="Отложено">
			<label for="defer" style="cursor:not-allowed;background-color:#DCDCDC;">
				<span> Отложено</span>
			</label>
		  </div><!--end col-xs-6-->
		  
		  </div><!--end row-->

	  </div><!--end col-xs-5-->
	</div><!--end row-->
	<?php } ?>
	
	<hr />
	<div class="row">
	  <div class="col-xs-4">
		
		<a class="btn btn-default pull-left" role="button" href="<?=$_SESSION['prevPage'];?>">
			<span class="glyphicon glyphicon-triangle-left"></span> 
			Назад
		</a>
	  </div><!--end col-xs-6-->

	  <div class="col-xs-4">
		<center id="tosubmt">
			<button class="btn btn-default" type="submit" >
				<span class="glyphicon glyphicon-floppy-disk"></span> 
				Сохранить
			</button>
		</center>
	  </div><!--end col-xs-6-->
	  
	  <div class="col-xs-4">
	  </div><!--end col-xs-6-->
	</div><!--end row-->
		
		<input type="hidden" name="save" value="1"/>
		<input type="hidden" name="id" value="<?=$id;?>" id="thisId"/>
		<input type="hidden" name="num3d" value="<?=$row['number_3d'];?>"/>
	    <input type="hidden" name="date" value="<?php echo date('Y-m-d'); ?>" />
  </form>
	
	<p></p>
	
<!-- RESULT WINDOW -->
<div id="blackCover"></div>
<div id="saved_form_result" class="alert alert-success">
	<div id="progressStatus" style="font-weight:600;"></div>
	<div class="progress">
	  <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
	</div>
</div>
<!-- END RESULT WINDOW -->

<!-- прототип швенз -->
<?php include('schwenze.php');?>
<!-- END прототип швенз -->

<!-- прототип строки доп. артикулов -->
	<table class="hidden">
		<tr style="display:none;" id="protoArticlRow" >
		  <td></td>
		  <td><?php include('DopArticl_names_input.php'); ?></td>
		  <td><input type="text" class="form-control" name="num3d_vc_[]" value=""></td>
		  <td><input type="text" class="form-control" name="descr_dopvc_[]" value=""></td>
		</tr>
	</table>
<!-- END прототип строки доп. артикулов -->

<!-- image Box prev-->
<img id="imageBoxPrev" width="200px" class="img-thumbnail img-responsive hidden" style="position:absolute; z-index:100;" />
<!-- END image Box prev-->

<?php	
	include('bottomScripts_adm.php');
?>
<script src="../js/editOtherForm.js?ver<?=time();?>"></script>
</div><!--container-->
</body>
</html>
<?php 
	mysqli_close($connection);
} //END ELSE 
?>
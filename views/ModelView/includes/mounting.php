<div id="status_cover" class="popup hidden">
	<div id="status_blackCover"></div>
	<div id="status_window" class="status_window alert alert-success">
		<form method="post" id="saveVC_mounting" >
		<center><h4><b>Изменить Статус:</b></h4></center>
		<p>
			Текущий статус: &#160;&#160;
			<span class="glyphicon glyphicon-<?=$glyphi;?>"></span>
			<b><i><?=$stat_name;?></b></i>&#160;&#160; &#8212; &#160;&#160;
			<small title="Дата последнего изменения статуса" style="cursor:default;">
				<?=$stat_date;?>
			</small>
		<?php 
			$chckS = '';
			if ( $stat_name == 'Вышел сигнал!' ) $chckS = 'checked'; 
			if ( $stat_name == 'В работе (Монт.)' ) $chckW = 'checked';
			if ( $stat_name == 'В ремонте' ) $chckRem = 'checked'; 
		?>
		<br/><br/>
		<div class="row status">
		  <div class="col-xs-12 col-sm-4" style="text-align: center;">
		    <input <?=$chckS;?> type="radio" <?=$chec_signalDone;?> name="status" id="signalDone" aria-label="..." value="Вышел сигнал!">
			<label for="signalDone" class="">
				<span class="">	Вышел сигнал!</span>
				<span class="glyphicon glyphicon-thumbs-up"></span>
			</label>
		  </div><!--end col-xs-6-->
		  
		  <div class="col-xs-12 col-sm-4" style="text-align: center;">
		    <input <?=$chckW;?> type="radio" <?=$chec_wip;?> name="status" id="wipM" aria-label="..." value="В работе (Монт.)">
			<label for="wipM" class="">
				<span class="">	В работе (Монтировка)</span>
				<span class="glyphicon glyphicon-cog"></span>
			</label>
		  </div><!--end col-xs-6-->
		  
		  <div class="col-xs-12 col-sm-4" style="text-align: center;">
		    <input <?=$chckRem;?> type="radio" <?=$chec_onrep;?> name="status" id="onRepaire" aria-label="..." value="В ремонте">
			<label for="onRepaire" class="">
				<span class="">	В ремонте</span>
				<span class="glyphicon glyphicon-wrench"></span>
			</label>
		  </div><!--end col-xs-6-->
  
		  </div><!--end row-->
		</p>
		<p id="status_description" class="hidden">
			Причина отправки в ремонт:
			<textarea id="" class="form-control" rows="3" name="mounting_descr"><?php 
			echo $row['mounting_descr'];
			?></textarea>
		</p>
		<br/>
		<center>
			<input type="button" class="btn btn-warning" name="change_mounting" value="Изменить" onclick="sendNewStatus();" />&#160;&#160;
			<input type="button" class="btn btn-default" value="Отмена" onclick="close_status_window(this);" />
			<input type="hidden" name="id" value="<?=$id;?>" />
			<input type="hidden" name="n3d" value="<?=$row['number_3d'];?>" />
			<input type="hidden" name="mountVC" value="<?=$row['vendor_code'];?>" />
		</center>
		</form>
	</div>
</div>
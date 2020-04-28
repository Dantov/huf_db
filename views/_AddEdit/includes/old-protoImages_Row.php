<div class="col-xs-6 col-sm-3 col-md-2 image_row_proto">
	<input class="hidden" type="file" name="upload_images[]" accept="image/jpeg,image/png,image/gif"/>
	<div class="ratio img-thumbnail">
		<div class="ratio-inner ratio-4-3">
			<div class="ratio-content">
				<img src="" class="img-responsive img-aligned" />
			</div>
			<div class="img_dell">
				<button class="btn btn-default" type="button" onclick="dellImgPrew(this);">
					<span class="glyphicon glyphicon-remove"></span> 
				</button>
			</div>
		</div>
	</div>
	<div class="img_inputs">
		<div class="input-group">
			<input required type="text" readonly class="form-control" aria-label="..." name="imgFor[]" value="<?=;?>" >
			<div class="input-group-btn">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					
				</ul>
			</div>
		</div>
		<!--
		<div>
			<input type="radio" name="mainImg" id="mainImg" value=""/>
			<label for="mainImg"> Главния</label>	
		</div>
		<div>
			<input type="radio" name="onBodyImg" id="onBodyImg" value=""/>
			<label for="onBodyImg"> На теле</label>
		</div>
		<div>
			<input type="radio" name="sketchImg" id="sketchImg" value=""/>
			<label for="sketchImg">	Эскиз</label>
		</div>
		<div>
			<input type="radio" name="detailImg" id="detailImg" value=""/>
			<label for="detailImg">	Деталировка</label>
		</div>
		-->
	</div>
	<input type="hidden" name="upload_images_word[]" value="" />
</div>

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
			<input type="hidden" class="notVis" name="" value="" />
			<input required type="text" readonly class="form-control vis" aria-label="..." value="" />
			<div class="input-group-btn">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<li title="Будет помещено на главной странице, в паспорте и бегунке, а так же в коллекции."><a data-imgFor="1" elemToAdd>Главная</a></li>
					<li title="Будет помещено в паспорте и бегунке."><a data-imgFor="2" elemToAdd>На теле</a></li>
					<li title="Будет помещено в паспорте."><a data-imgFor="3" elemToAdd>Эскиз</a></li>
					<li title="Будет печатать в коллекции как доп. картинка."><a data-imgFor="4" elemToAdd>Деталировка</a></li>
					<li title="Будет помещено в бегунке на последней странице."><a data-imgFor="5" elemToAdd>Схема сборки</a></li>
					<li title="Будет видно на странице показа модели."><a data-imgFor="0" elemToAdd>Нет</a></li>
				</ul>
			</div>
		</div>
	</div>
	<input type="hidden" name="upload_images_word[]" value="" />
</div>

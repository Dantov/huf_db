<div class="input-group">
    <input type="text" class="form-control" name="dop_vc_name_[]" value="<?=$row_dop_vc[$j]['vc_names'];?>"/>
    <div class="input-group-btn">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu dropdown-menu-right">
			<?=$vc_namesLI;?>
		</ul>
    </div>
</div>


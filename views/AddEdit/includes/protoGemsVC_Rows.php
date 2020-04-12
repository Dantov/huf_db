<table class="hidden">
	<!-- прототип строки доп. артикулов -->
	<tr style="display:none;" id="protoArticlRow" >
	  <td></td>
	  <td><?php include('DopArticl_names_input.php'); ?></td>
	  <td><input type="text" class="form-control" name="num3d_vc_[]" value=""></td>
	  <td><input type="text" class="form-control" name="descr_dopvc_[]" value=""></td>
	  <td>
		<button class="btn btn-sm btn-default" type="button" onclick="duplicateRow(this);" title="дублировать строку">
			<span class="glyphicon glyphicon-duplicate"></span>
		</button>
		<button class="btn btn-sm btn-default" type="button" onclick="deleteRow(this);" title="удалить строку">
			<span class="glyphicon glyphicon-trash"></span>
		</button>
	  </td>
	</tr>
	<!-- END прототип строки доп. артикулов -->

	<!-- прототип строки камней -->
	<tr style="display:none;" id="protoGemRow">
	  <td></td>
	  <td><?php include('gems_diametr_input.php'); ?></td>
	  <td><input type="number" class="form-control gems_value_input" name="gemsVal[]" value=""></td>
	  <td><?php include('gems_cut_input.php'); ?></td>
	  <td><?php include('gems_input.php'); ?></td>
	  <td><?php include('gems_color_input.php'); ?></td>
	  <td style="width:100px;">
		<button class="btn btn-sm btn-default" type="button" onclick="duplicateRow(this);" title="дублировать строку">
			<span class="glyphicon glyphicon-duplicate"></span>
		</button>
		<button class="btn btn-sm btn-default" type="button" onclick="deleteRow(this);" title="удалить строку">
			<span class="glyphicon glyphicon-trash"></span>
		</button>
	  </td>
	</tr>
	<!-- END прототип строки камней -->

    <!-- прототип строки коллекций -->
    <tr style="display:none;" id="protoCollectionRow">
        <td style="width: 30px"></td>
        <td><?php include('collections_input.php'); ?></td>
        <td style="width:100px;">
            <button class="btn btn-sm btn-default" type="button" onclick="deleteRow(this);" title="удалить строку">
                <span class="glyphicon glyphicon-trash"></span>
            </button>
        </td>
    </tr>
    <!-- END прототип строки коллекций -->

    <!-- прототип строки Материалов -->
	<? $switchTableRow = "materialsFull"; require _viewsDIR_."AddEdit/includes/protoRows.php" ?>
    <!-- END прототип строки Материалов -->
</table>

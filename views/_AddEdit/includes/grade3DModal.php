<!-- grade3DModal -->
<div class="modal fade" id="grade3DModal" tabindex="-1" role="dialog" aria-labelledby="grade3DModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="grade3DModalLabel">Выбрать оценку 3Д</h4>
      </div>
      <div class="modal-body">
        <select class="form-control add3DGrade">
          <option value="">---</option>
      <?php foreach ( $gradingSystem3D??[] as $gs3DRow ): ?>
        <option data-workName="<?=$gs3DRow['work_name']?>" data-points="<?=$gs3DRow['points']?>" value="<?=$gs3DRow['id']?>" title="<?=$gs3DRow['description']?>" >
          <?= $gs3DRow['work_name'] . " - ". $gs3DRow['points']?>
          <?php
            $descr = $gs3DRow['description'];
            if ( mb_strlen( $descr, "UTF-8" ) > 50 ) $descr = mb_substr($gs3DRow['description'], 0, 50, "UTF-8") . "...";
          ?>
          <?= ": " . $descr ?>
        </option>
      <?php endforeach; ?>
      </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
      </div>
    </div>
  </div>
</div>

<table class="hidden">
<tr class="gs_proto3DRow">
  <td style="width: 30px"></td>
  <td></td>
  <td></td>
  <td></td>
  <td style="width:100px;">
    <button class="btn btn-sm btn-default ma3DgsDell" type="button" onclick="deleteRow(this);" title="Удалить Оценку">
      <span class="glyphicon glyphicon-trash"></span>
    </button>
  </td>
</tr>
</table>
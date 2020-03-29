<div class="input-group">
    <input required readonly type="text" name="collection[]" class="form-control collection" value="<?=$_SESSION['general_data']['collection'][$i], $_SESSION['fromWord_data']['collection'];?>">
    <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <?=$collLi;?>
        </ul>
    </div>
</div>
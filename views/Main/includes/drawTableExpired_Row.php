<tr class="">
    <td title="" style="text-align: right!important;"><?=$workingCenter['name']?:''?> :</td>
    <td title="" style="text-align: left!important;"><?=$workingCenter['descr']?:''?></td>
    <td title="Кол-во моделей из выбранных принадлежат этому участку">
        <?php if ( $workingCenter['countAll'] ): ?>
            <?php $countAllIds = implode(',',$workingCenter['ids']) ?>
            <a href="<?=_views_HTTP_ . "/Main/controllers/setSort.php?countedIds=".$countAllIds ?>"><b><?=$workingCenter['countAll']?></b></a>
        <?php else: ?>
            0
        <?php endif; ?>
    </td>
    <td title="">
        <?php if ( $workingCenter['expired'] ): ?>
            <?php $expiredIds = implode(',',$workingCenter['expiredIds']) ?>
            <a href="<?=_views_HTTP_ . "/Main/controllers/setSort.php?countedIds=".$expiredIds ?>"><b><?=$workingCenter['expired']?></b></a>
        <?php else: ?>
            0
        <?php endif; ?>
    </td>
    <td title="<?= $wcUser['fullFio'] ?>"><?= $wcUser['fio'] ?></td>
</tr>
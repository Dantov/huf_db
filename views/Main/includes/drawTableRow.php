<tr <?= $trFill ? 'class="danger" title=""' : '' ?> onclick="window.location.href = _URL_ + '/Views/ModelView/index.php?id=<?=$row['id']?>'">
    <td title="Артикул / №3Д"><?=$row['vendor_code'] ?: $row['number_3d']?></td>
    <td></td>
    <td title="<?=$row['size_range']?>"><?=$sizeRange?></td>
    <?php foreach ( $wCenters as $wCenterL ):?>

        <?php $startNameRu = isset($wCenterL['start']['status']['name_ru'])?$wCenterL['start']['status']['name_ru']:'' ?>
        <?php $endNameRu = isset($wCenterL['end']['status']['name_ru'])?$wCenterL['end']['status']['name_ru']:'' ?>

        <?php $startDate = isset($wCenterL['start']['date'])?$wCenterL['start']['date']:'' ?>

        <td title="<?= $startNameRu ?>"><?= $startDate ?></td>

        <?php $endDate = $wCenterL['end']['date']?>
        <td <?=$endDate===-1 ? 'style="background: #3c510c; color: #fff"' :'' ?> title="<?=$endNameRu?>"><?=$endDate!==-1?$endDate: 'Просрочено'?></td>
    <?php endforeach; ?>
</tr>
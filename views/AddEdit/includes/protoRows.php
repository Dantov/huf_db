<?php
    switch ( $switchTableRow )
    {
        case "dopVC": //прототип строки доп. артикулов
?>
        <tr <?php if ( !isset($vc_link) ) echo 'class="hidden protoRow" id="protoArticlRow"'; ?> >
            <td>
                <div class="input-group">
                    <input type="hidden" class="rowID" name="dopvc[id][]" value="<?=$vc_link['id']?>">
                    <input type="text" class="form-control" name="dopvc[name][]" value="<?=$vc_link['vc_names']?>"/>
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <?php foreach ($dataTables['vc_names'] as $tName) : ?>
                                <li style="position:relative;">
                                    <a elemToAdd VCTelem><?= $tName['name'] ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="text" class="form-control" name="dopvc[num3dvc][]" value="<?=$vc_link['vc_3dnum']?>">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                        </ul>
                    </div>
                </div>
            </td>
            <td><input type="text" class="form-control" name="dopvc[descr][]" value="<?=$vc_link['descript']?>"></td>
            <td>
                <?php if ( $hiddens['hide'] ): ?>
                    <button class="btn btn-sm btn-default" type="button" onclick="duplicateRow(this);" title="дублировать строку">
                        <span class="glyphicon glyphicon-duplicate"></span>
                    </button>
                    <button class="btn btn-sm btn-default" type="button" onclick="deleteRow(this);" title="удалить строку">
                        <span class="glyphicon glyphicon-trash"></span>
                    </button>
                <?php endif;?>
            </td>
        </tr>
<?php
        break;
        case "gems": //прототип строки камней
?>
            <tr <?= !isset($gem) ? 'class="hidden protoRow" id="protoGemRow"':'' ?> >
                <td>
                    <input type="hidden" class="rowID" name="gems[id][]" value="<?=$gem['id']?>">
                    <?php include('gems_diametr_input.php'); ?>
                </td>
                <td><input type="number" min="1" class="form-control gems_value_input" name="gems[val][]" value="<?=$gem['value'];?>"></td>
                <td><?php include('gems_cut_input.php'); ?></td>
                <td><?php include('gems_input.php'); ?></td>
                <td><?php include('gems_color_input.php'); ?></td>
                <td style="width:100px;">
                    <?php if ( $hiddens['hide'] ): ?>
                        <button class="btn btn-sm btn-default" type="button" onclick="duplicateRow(this);" title="дублировать строку">
                            <span class="glyphicon glyphicon-duplicate"></span>
                        </button>
                        <button class="btn btn-sm btn-default" type="button" onclick="deleteRow(this);" title="удалить строку">
                            <span class="glyphicon glyphicon-trash"></span>
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
<?php
        break;
        case 'materialsFull': //прототип строки материалов
?>
            <tr <?= !isset($materialRow) ? 'class="hidden protoRow" id="protoMaterialsRow"':'' ?>>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="hidden" class="rowID" name="mats[id][]" value="<?=$materialRow['id']?>">
                        <input type="text" class="form-control" name="mats[part][]" value="<?=$materialRow['part']?>">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li style="position:relative;">
                                    <a elemToAdd>Шинка</a>
                                    <a elemToAdd>Каст</a>
                                    <a elemToAdd>Накладка</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="mats[type][]" value="<?=$materialRow['type']?>">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php foreach ( $materials['names'] as $type ) : ?>
                                    <li style="position:relative;">
                                        <a elemToAdd><?=$type?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="mats[probe][]" value="<?=$materialRow['probe']?>">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php foreach ( $materials['probes'] as $probes ) : ?>
                                    <?php foreach ( $probes as $probe ) : ?>
                                        <li style="position:relative;">
                                            <a elemToAdd><?=$probe?></a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="mats[metalColor][]" value="<?=$materialRow['metalColor']?>">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php foreach ( $materials['colors'] as $color ) : ?>
                                    <li style="position:relative;">
                                        <a elemToAdd><?=$color?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="mats[covering][]" value="<?=$materialRow['covering']?>">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php foreach ( $coverings['names'] as $type ) : ?>
                                    <li style="position:relative;">
                                        <a elemToAdd><?=$type?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="mats[area][]" value="<?=$materialRow['area']?>">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php foreach ( $coverings['areas'] as $area ) : ?>
                                    <li style="position:relative;">
                                        <a elemToAdd><?=$area?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="mats[covColor][]" value="<?=$materialRow['covColor']?>">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php foreach ( $materials['colors'] as $color ) : ?>
                                    <li style="position:relative;">
                                        <a elemToAdd><?=$color?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="mats[handling][]" value="<?=$materialRow['handling']?>">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php foreach ( $handlings as $type ) : ?>
                                    <li style="position:relative;">
                                        <a elemToAdd><?=$type['name']?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </td>
                <td style="width:100px;">
                    <button class="btn btn-sm btn-default" type="button" onclick="duplicateRow(this);" title="дублировать строку">
                        <span class="glyphicon glyphicon-duplicate"></span>
                    </button>
                    <button class="btn btn-sm btn-default" type="button" onclick="deleteRow(this);" title="удалить строку">
                        <span class="glyphicon glyphicon-trash"></span>
                    </button>
                </td>
            </tr>
<?php
            break;
        case 'repair':
?>
            <div <?=!isset($repair) ? 'id="protoRepairs"':''?> class="panel panel-danger <?= !isset($repair) ? 'hidden':'repairs protoRow'?>">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-wrench" style="color:green;"></span>
                    <strong>
                        Ремонт №<span class="repairs_number"> <?=$repair['rep_num']?></span>
                        от - <span class="repairs_date"><?=date_create( $repair['date'] )->Format('d.m.Y');?></span>
                    </strong>
                    <button onclick="removeRepairs(this);" class="btn btn-sm btn-default pull-right" style="top:-5px !important; position:relative;" type="button" title="Удалить Ремонт">
                        <span class="glyphicon glyphicon-remove"></span>
                    </button>
                </div>
                <input type="hidden" class="repairs_id" name="repairs[id][]" value="<?=$repair['id']?>">
                <textarea class="form-control repairs_descr" rows="3" name="repairs[descr][]"><?=$repair['repair_descr']?></textarea>
                <input type="hidden" class="repairs_num" name="repairs[num][]" value="<?=$repair['rep_num']?>"/>
                <input type="hidden" class="date_repair" name="repairs[date][]" value="<?=$repair['date']?>"/>
            </div>
<?php
            break;
        case 'dropImage':
?>
            <div class="col-xs-6 col-sm-3 col-md-2 <?=$protoImgRow?'hidden':'image_row'?>" <?=$protoImgRow ? 'id="proto_image_row"': ''?> >
                <div class="ratio img-thumbnail">
                    <div class="ratio-inner ratio-4-3">
                        <div class="ratio-content">
                            <img src="<?=$protoImgRow ? '': $image['imgPath']?>" class="imgThumbs" />
                        </div>
                        <div class="img_dell">
                            <button class="btn btn-default" type="button" <? if ( !$protoImgRow ): ?>
                                    onclick="dell_fromServ(<?=$id?>, '<?=$image['imgName']?>', false, false, this)"
                                    <?endif;?>
                                    >
                                <span class="glyphicon glyphicon-remove"></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="img_inputs">
                    <input type="hidden" class="rowID" <?=$protoImgRow ? '': 'name="image[id][]"'?> value="<?=!$protoImgRow && $component === 3 ? '': $image['id']?>">
                    <select class="form-control input-sm" <?=$protoImgRow ? '': 'name="image[imgFor][]" onchange="handlerFiles.onSelect(this)"'?>>
                        <?php $statusImgArray = $protoImgRow ? $dataArrays : $image ?>
                        <?php foreach ( $statusImgArray['imgStat']?:[] as $statusImg ): ?>
                            <option <?=(int)$statusImg['selected'] === 1 ?'selected':''?> data-imgFor="<?=$statusImg['id']?>" value="<?=$statusImg['id']?>" title="<?=$statusImg['title']?>"><?=$statusImg['name']?></option>
                        <?php endforeach; ?>
                    </select>
                    <? if ( !$protoImgRow && $component === 3) : ?>
                        <input type="hidden" name="image[img_name][sketch]" value="<?=$row['number_3d'].'#'.$row['id'].'#'.$image['imgName']?>">
                    <?endif;?>
                </div>
            </div>
<?php
            break;
    }
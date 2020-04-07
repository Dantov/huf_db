<div class="row">
    <div class="col-xs-12 col-sm-2">
        <a class="btn btn-sm btn-info pull-left" href="<?=$prevPage;?>" role="button">
            <span class="glyphicon glyphicon-triangle-left"></span>
            Назад
        </a>
        <? if ( $component === 2 ): ?>
            <a style="margin: 0 0 0 7px;" class="btn btn-sm btn-info pull-left" href="<?=_views_HTTP_?>ModelView/index.php?id=<?=$id;?>" role="button">
                Просмотр
            </a>
        <? endif; ?>
    </div><!--end col -->
    <div class="col-xs-12 col-sm-8">
        <h4 class="text-warning text-center" id="topName" style="margin: 5px 0 0 0;">
            <? if ( $component === 1 ): ?>
                <strong>
                    <span>&#160;Добавить новую модель</span>
                </strong>
            <? else: ?>
                <?=$header;?>
            <?endif;?>
        </h4>
    </div><!--end col -->
    <div class="col-xs-12 col-sm-2">
        <? if ( $component === 2 ): ?>
            <? if ( $permittedFields['addComplect'] === true ): ?>
                <a class="btn btn-sm btn-info pull-right" href="<?=_views_HTTP_?>AddEdit/index.php?id=<?=$id;?>&component=3" role="button">
                    Добавить комплект
                </a>
            <? endif; ?>
        <? endif; ?>
    </div><!--end col -->
</div><!--end row-->
<!-- конец заголовка -->


<hr />


<!--MAIN FORM-->
<form method="POST" id="addform" enctype="multipart/form-data">

    <div class="row">
        <?php if ( $permittedFields['number_3d'] ): ?>
            <div class="col-sm-6">
                <div class="form-group" title="По нему формируются комплекты. '000' вводить не обязательно.">
                    <label for="number_3d">
                        <span class="glyphicon glyphicon-question-sign"></span>
                        номер 3D:
                    </label>
                    <input required id="num3d" type="text" name="number_3d" class="form-control" value="<?=$_SESSION['general_data']['number_3d'], $_SESSION['fromWord_data']['number3D'];?>">
                </div>
            </div>
        <?php endif; ?>

        <?php if ( $permittedFields['vendor_code'] ): ?>
            <div class="col-sm-6">
                <div class="form-group" title="Добавляется во все изделия в комплекте (если там было пусто)">
                    <label for="shortName">
                        <span class="glyphicon glyphicon-question-sign" ></span>
                        Фабричный артикул:
                    </label>
                    <input id="vendor_code" type="text" name="vendor_code" class="form-control" value="<?=$_SESSION['general_data']['vendor_code'];?>" />
                </div>
            </div>
        <?php endif; ?>

    </div> <!--end row-->

    <?php if ( $permittedFields['collections'] ): ?>
        <div class="form-group">

            <div class="panel panel-default">
                <div class="panel-heading" title="Коллекции к которым будет принадлежать данное изделие">
                    <i class="fas fa-gem"></i>
                    <strong> Коллекции:</strong>
                    <button id="addCollection" class="btn btn-sm btn-default pull-right" style="top:-5px !important; position:relative;" type="button" title="Добавить коллекцию">
                        <span class="glyphicon glyphicon-plus"></span>
                    </button>
                </div>
                <table class="table <?=$collections_len ? "" : "hidden"?>">
                    <thead>
                    <tr class="thead11">
                        <th>№</th><th>Название</th><th></th>
                    </tr>
                    </thead>
                    <tbody id="collections_table">
                    <!-- // автозаполнение если добавляем комплект или редакт модель -->
                    <?php $i = 0; foreach ( $collections_len as $collection ) : ?>
                        <tr>
                            <td style="width: 30px"><?=++$i?><?php --$i; ?></td>
                            <td><?php include('includes/collections_input.php'); $i++?></td>
                            <td style="width:100px;">
                                <button class="btn btn-sm btn-default" type="button" onclick="deleteRow(this);" title="удалить строку">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div><!-- end panel gems-->
        </div>
    <?php endif; ?>


    <div class="row">
        <?php if ( $permittedFields['author'] ): ?>
            <div class="col-sm-4 ">
                <label for="author"><span class="glyphicon glyphicon-user"></span> Автор:</label>
                <div class="input-group">
                    <input required type="text" class="form-control" aria-label="..." name="author" value="<?=$_SESSION['general_data']['author'], $_SESSION['fromWord_data']['author'];?>" >
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <?=$authLi;?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>


        <?php if ( $permittedFields['modeller3d'] ): ?>
            <div class="col-sm-4 ">
                <label for="modeller3d"><span class="glyphicon glyphicon-user"></span> 3Д модельер:</label>
                <div class="input-group">
                    <input required type="text" class="form-control" aria-label="..." name="modeller3d" value="<?=$_SESSION['general_data']['modeller3d'], $_SESSION['fromWord_data']['mod3D']; ?>">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <?=$mod3DLi;?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( $permittedFields['jewelerName'] ): ?>
            <div class="col-sm-4 ">
                <label for="jewelerName"><span class="glyphicon glyphicon-user"></span> Доработчик:</label>
                <div class="input-group">
                    <input type="text" class="form-control" aria-label="..." name="jewelerName" value="<?=$_SESSION['general_data']['jewelerName']?>">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <?=$jewelerNameLi;?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-xs-12">
            <br/>
        </div>

        <?php if ( $permittedFields['model_type'] ): ?>
            <div class="col-xs-3">
                <label for="model_type" class=""><span class="glyphicon glyphicon-eye-open"></span> Вид модели:</label>
                <div class="input-group ">
                    <input required  type="text" id="modelType" class="form-control" aria-label="..." name="model_type" value="<?=$row['model_type'], $_SESSION['fromWord_data']['modType'];?>" />
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <?=$modTypeLi;?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( $permittedFields['size_range'] ): ?>
            <div class="col-xs-3">
                <label for="model_weight"><i class="fab fa-quinscape"></i> Размерный Ряд:</label>
                <input type="text" class="form-control" name="size_range" value="<?=$row['size_range'];?>" />
            </div>
        <?php endif; ?>

        <?php if ( $permittedFields['model_weight'] ): ?>
            <div class="col-xs-2 ">
                <label for="model_weight"><span class="glyphicon glyphicon-scale"></span> Вес 3D:</label>
                <input step="0.01" type="number" class="form-control" aria-label="..." required name="model_weight" value="<?=$_SESSION['general_data']['model_weight'], $_SESSION['fromWord_data']['weight']; ?>">
            </div>
        <?php endif; ?>

        <?php if ( $permittedFields['print_cost'] ): ?>
            <div class="col-xs-2 ">
                <label for="model_weight"><span class="glyphicon glyphicon-usd"></span>	Стоимость печати:</label>
                <input type="text" class="form-control" name="print_cost" value="<?=$row['print_cost'];?>" />
            </div>
        <?php endif; ?>

        <?php if ( $permittedFields['model_cost'] ): ?>
            <div class="col-xs-2 ">
                <label for="work_cost"><span class="glyphicon glyphicon-usd"></span> Стоимость доработки:</label>
                <input type="text" class="form-control" name="model_cost" value="<?=$row['model_cost'];?>" />
            </div>
            <div class="col-xs-12 "><br></div>
        <?php endif; ?>


        <?php if ( $permittedFields['material'] ): ?>
            <div class="col-xs-6" id="material">
                <label for=""></label>
                <p></p>
                <span class="model_material"><span class="glyphicons-cube"></span> <b>Материал изделия:</b></span>

                <input type="radio" <?=$material['metall_silv'];?> name="model_material" id="sivler" class="radio" value="Серебро">
                <label for="sivler" class="sivler">
                    <span class="redSilverLabel"> Серебро 925</span>
                </label>

                <input type="radio" <?=$material['metall_gold'];?> name="model_material" id="gold" class="radio" value="Золото">
                <label for="gold" class="gold">
                    <span class="redGoldLabel">	Золото</span>
                </label>

                <div class="goldsample">
                    <input type="radio" <?=$material['probe585'];?> name="samplegold" id="sample585" class="radio" value="585">
                    <label for="sample585" class="sample">
                        <span class="">585&#176;  &nbsp;&nbsp;</span>
                    </label>
                    <input type="radio" <?=$material['probe750'];?> name="samplegold" id="sample750" class="radio" value="750">
                    <label for="sample750" class="sample">
                        <span class="">750&#176;</span>
                    </label>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" <?=$material['gold_white'];?> name="whitegold" id="whitegold" class="whitegChk radio" value="Белое">
                    <label for="whitegold" class="sample">
                        <span class="">Белое&nbsp;&nbsp;</span>
                    </label>
                    <input type="checkbox" <?=$material['gold_red'];?> name="redgold" id="redgold" class="redgChk radio" value="Красное">
                    <label for="redgold" class="sample">
                        <span class="">Красное&nbsp;&nbsp;</span>
                    </label>
                    <input type="checkbox" <?=$material['gold_yellow'];?> name="eurogold" id="eurogold" class="eurogChk radio" value="Желтое(евро)">
                    <label for="eurogold" class="sample">
                        <span class="">Желтое(евро)</span>
                    </label>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( $permittedFields['covering'] ): ?>
            <div class="col-xs-6" id="covering">
                <label for=""></label>
                <p></p>
                <span class="model_material"><i class="fas fa-cube fasL"></i> <b>Покрытие:</b></span>

                <input type="checkbox" <?=$covering['rhodium'];?> name="rhodium" id="rhodium" class="rhodium radio" value="Родирование">
                <label for="rhodium" class="sivler rhodiumed">
                    <span class="redSilverLabel">Родирование</span>
                </label>

                <input type="checkbox" <?=$covering['golding'];?> name="golding" id="golding" class="radio" value="Золочение">
                <label for="golding" class="gold golded">
                    <span class="redGoldLabel golding">Золочение</span>
                </label>
                &nbsp;&nbsp;&nbsp;
                <input type="checkbox" <?=$covering['blacking'];?> name="blacking" id="blacking" class="radio" value="Чернение">
                <label for="blacking" class="gold blackeg">
                    <span class="redGoldLabel blacking">Чернение</span>
                </label>

                <div class="rhodium">

                    <input type="radio" <?=$covering['full'];?> name="rhodium_fill" id="rhodium_full" class="radio" value="Полное">
                    <label for="rhodium_full" class="sample"><span class="">Полное&nbsp;&nbsp;&nbsp;</span></label>

                    <input type="radio" <?=$covering['onPartical'];?> name="rhodium_fill" id="rhodium_part" class="radio rhod_part" value="Частичное">
                    <label for="rhodium_part" class="sample"><span class="">Частичное</span></label>

                    <div class="rhodium_parts">
                        <input type="checkbox" <?=$covering['onProngs'];?> name="onProngs" id="onProngs" class="radio" value="По крапанам">
                        <label for="onProngs" class="sample"><span class="">По крапанам &nbsp;&nbsp;</span></label>
                        <input type="checkbox" <?=$covering['parts'];?> name="onParts" id="onParts" class="onParts radio" value="Отдельные части">
                        <label for="onParts" class="sample"><span class="">Отдельные части</span></label>
                        <input type="text" name="rhodium_PrivParts" class="mytextinput rhodium_PrivParts" value="<?=$covering['partsStr'];?>">
                    </div>

                </div>
            </div>
        <?php endif; ?>

    </div><!-- /.row -->




    <?php if ( $permittedFields['stl'] ): ?>
        <!-- STL Block -->
        <hr class=""/>
        <div class="row">
            <div class="col-xs-12">
                <div><span style="position:relative; top: -6px; font-size:13px;">* Просьба, перед загрузкой stl файлов применять к моделям метод Triangle Reduction в Magics с параметром 0,0025. Для уменьшения размера файлов. </span></div>
                <div class="haveStl <?=$haveStl;?>">
                    <span><b>STL:&nbsp;&nbsp;</b><?=$stl_file['stl_name'];?></span>
                    <button type="button" id="dellStl" class="btn btn-default" onclick="dell_fromServ( <?=$id.',\''.$stl_file['stl_name'].'\'';?>, 1, false )" title="Удалить">
                        <span class="glyphicon glyphicon-trash"></span>
                    </button>
                </div>
                <div class="noStl <?=$noStl;?>">
                    <span><b>STL: &nbsp;</b></span>
                    <input class="hidden" type="file" multiple id="fileSTL" name="fileSTL[]" accept=".stl"/>
                    <span id="stlSelect" class="stlSelect" title="Можно выбрать несколько">Добавить файлы STL</span>
                    <button type="button" id="removeStl" class="btn btn-default hidden" onclick="" title="Убрать">
                        <span class="glyphicon glyphicon-remove"></span>
                    </button>
                </div>
            </div>
        </div><!-- /.row -->
        <hr class=""/>
        <!-- STL Block END -->
    <?php endif; ?>


    <?php if ( $permittedFields['ai'] ): ?>
        <!-- AI Block -->
        <div class="row AIBlock <?=$ai_hide;?>">
            <div class="col-xs-12">
                <div class="haveStl <?=$haveAi;?>">
                    <span><b>Накладка:&nbsp;&nbsp;</b><?=$ai_file['name'];?></span>
                    <button type="button" id="dellAi" class="btn btn-default" onclick="dell_fromServ( <?=$id.',\''.$ai_file['name'].'\'';?>, 2, false )" title="Удалить">
                        <span class="glyphicon glyphicon-trash"></span>
                    </button>
                </div>
                <div class="noStl <?=$noAi;?>">
                    <span><b>Накладка: &nbsp;</b></span>
                    <input class="hidden" type="file" multiple id="fileAi" name="fileAi[]" accept=".ai"/>
                    <span id="aiSelect" class="stlSelect" title="Можно выбрать несколько">Добавить файл накладки</span>
                    <button type="button" id="removeAi" class="btn btn-default hidden" onclick="" title="Убрать">
                        <span class="glyphicon glyphicon-remove"></span>
                    </button>
                </div>
            </div>
        </div><!-- /.row -->
        <hr class=" AIBlockHR <?=$ai_hide;?>"/>
        <!-- Ai Block END -->
    <?php endif; ?>


    <?php if ( $permittedFields['images'] ): ?>
        <div class="row" id="picts">
            <span id="imgFor" class="help-block hidden err-notice"></span>
            <?php for ( $i = 0; $i < $imgLen; $i++ ): ?>
                <div class="col-xs-6 col-sm-3 col-md-2 image_row">
                    <? if ( $component === 3 ):?>
                        <input class="hidden" type="file" name="upload_images[]" accept="image/jpeg,image/png,image/gif">
                    <? endif;?>
                    <div class="ratio img-thumbnail">
                        <div class="ratio-inner ratio-4-3">
                            <div class="ratio-content">
                                <img src="<?=_stockDIR_HTTP_.$_SESSION['general_data']['number_3d'].'/'.$_SESSION['general_data']['id'].'/images/'.$imgPath[$i];?>" class="dopImg img-responsive "/>
                            </div>
                            <? if ( $component === 3 ):?>
                                <div class="img_dell">
                                    <button class="btn btn-default" type="button" onclick="dellImgPrew(this);">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </button>
                                </div>
                            <? else: ?>
                                <a class="btn btn-sm btn-default img_dell" role="button" onclick="dell_fromServ(<?=$id?>, '<?=$imgPath[$i]?>', false, false, this)">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            <? endif;?>
                        </div>
                    </div>
                    <div class="img_inputs">
                        <div class="input-group">
                            <input type="hidden" class="notVis" name="imgFor[]" value="<?=$imgStat[$i]['id'];?>" />
                            <input required type="text" readonly class="form-control vis" aria-label="..." value="<?=$imgStat[$i]['name'];?>" />
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
                </div>
            <?php endfor; ?>

            <div class="col-xs-12">
                <div id="drop-area" title="Загрузить картинку">
                    <p>Загрузить картинки можно перетащив их в эту область</p>
                    <button type="button" id="addImageFiles" class="button"><i class="far fa-images"></i> Выбрать изображения</button>
                </div>
            </div>

            <div class="col-xs-6 col-sm-3 col-md-2 prj" id="add_bef_this">
                <div class="ratio" id="add_img">
                    <div class="ratio-inner ratio-4-3">
                        <div class="ratio-content">
                            <span class="add_img">
                                <span class="glyphicon glyphicon-picture"></span>
                            </span>
                            <div class="add_img_text">
                                Загрузить картинку
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!--Picts row-->
        <hr />
    <?php endif; ?>




    <div class="row">
        <?php if ( $permittedFields['gems'] ): ?>
            <div class="col-sm-12">
                <div id="stonesFromWord" class="<?=$stonesFromWord;?>"><?=$_SESSION['fromWord_data']['stones'];?></div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="far fa-gem"></i>
                        <strong> Вставки:</strong>
                        <button id="addGem" class="btn btn-sm btn-default pull-right" style="top:-5px !important; position:relative;" type="button" title="Добавить камень">
                            <span class="glyphicon glyphicon-plus"></span>
                        </button>
                    </div>
                    <table class="table <?=$gs_len ? "" : "hidden"?>">
                        <thead>
                        <tr class="thead11">
                            <th>№</th><th>Ø(Размер мм)</th><th>Кол-во шт.</th><th>Огранка</th><th>Сырьё</th><th>Цвет</th><th></th>
                        </tr>
                        </thead>
                        <tbody id="gems_table">
                        <?php for ( $i = 0; $i < $gs_len; $i++ ): // автозаполнение если добавляем комплект или редакт модель?>
                            <tr>
                                <td><?=$i+1;?></td>
                                <td><?php include('includes/gems_diametr_input.php');?></td>
                                <td><input type="number" class="form-control gems_value_input" name="gemsVal[]" value="<?=$row_gems[$i]['value'];?>"></td>
                                <td><?php include('includes/gems_cut_input.php'); ?></td>
                                <td><?php include('includes/gems_input.php'); ?></td>
                                <td><?php include('includes/gems_color_input.php'); ?></td>
                                <td style="width:100px;">
                                    <button class="btn btn-sm btn-default " type="button" onclick="duplicateRow(this);" title="дублировать строку">
                                        <span class="glyphicon glyphicon-duplicate"></span>
                                    </button>
                                    <button class="btn btn-sm btn-default " type="button" onclick="deleteRow(this);" title="удалить строку">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>
                                </td>
                            </tr>
                        <?php endfor; ?>
                        </tbody>
                    </table>
                </div><!-- end panel gems-->
            </div>
        <?php endif; ?>

        <div class="col-xs-12">

            <?php if ( $permittedFields['vc_links'] ): ?>
                <?=$vcDopFromWord;?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <span class="glyphicon glyphicon-link"></span>
                        <strong> Ссылки на другие артикулы:</strong>
                        <button id="addVC" class="btn btn-sm btn-default pull-right " style="top:-5px !important; position:relative;" type="button" title="Добавить отсылку">
                            <span class="glyphicon glyphicon-plus"></span>
                        </button>
                    </div>
                    <table class="table <?=$vc_Len ? "" : "hidden"?>">
                        <thead>
                        <tr class="thead11">
                            <th>№</th><th>Название</th><th>Артикул / Номер 3D</th><th>Описание</th><th></th>
                        </tr>
                        </thead>
                        <tbody id="dop_vc_table">
                        <?php for ( $j = 0; $j < $vc_Len; $j++ ): ?>
                            <tr>
                                <td><?=$j+1; ?></td>
                                <td><?php include('includes/DopArticl_names_input.php'); ?></td>
                                <td><?php include('includes/num3dVC_input.php'); ?></td>
                                <td><input type="text" class="form-control" name="descr_dopvc_[]" value="<?=$row_dop_vc[$j]['descript'];?>"></td>
                                <td>
                                    <button class="btn btn-sm btn-default " type="button" onclick="duplicateRow(this);" title="дублировать строку">
                                        <span class="glyphicon glyphicon-duplicate"></span>
                                    </button>
                                    <button class="btn btn-sm btn-default " type="button" onclick="deleteRow(this);" title="удалить строку">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div><!-- end panel dopArticls-->
            <?php endif; ?>

            <?php if ( $permittedFields['description'] ): ?>
                <label for="descr" class=""><span class="glyphicon glyphicon-comment"></span> Примечания:</label>
                <textarea id="descr" class="form-control" rows="3" name="description" style="margin:0px 0 15px 0 !important;"><?php
                    echo $_SESSION['general_data']['description']; echo $_SESSION['fromWord_data']['descr'];
                    ?></textarea>
            <?php endif; ?>

        </div> <!--col-xs-12-->


        <!--РЕМОНТЫ-->
        <?php if ( $permittedFields['repairs'] ): ?>
            <div class="col-xs-12" id="repairsBlock">
                <?php if ( $permittedFields['repairs3D'] ): ?>
                <?
                    $isRepairProto = false;
                    for ( $i = 0; $i < count($repairs?:[]); $i++ )
                    {
                        $repair = $repairs[$i];
                        if ( $whichRepair = $repair['which'] ? true : false ) continue; // пропустим ремонты модельеров, у них 1
                        require "includes/protoRepair.php";
                    }
                ?>
                <button data-repair="3d" style="margin-top:10px;" class="btn btn-info addRepairs"><span class="glyphicon glyphicon-cog"></span> Добавить ремонт 3Д</button>
                <?php endif; ?>

                <?php if ( $permittedFields['repairsJew'] ): ?>
                <?
                    for ( $i = 0; $i < count($repairs?:[]); $i++ )
                    {
                        $repair = $repairs[$i];
                        if ( !$whichRepair = $repair['which'] ? true : false ) continue; // пропустим ремонты 3д, у них 0
                        require "includes/protoRepair.php";
                    }
                    if (isset($whichRepair)) unset($whichRepair);
                ?>
                <button data-repair="jeweler" style="margin-top:10px;" class="btn btn-success addRepairs"><span class="glyphicon glyphicon-wrench"></span> Добавить ремонт Модельера-доработчика</button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <!--END РЕМОНТЫ-->

    </div><!--row-->

    <hr />

    <div class="row">

        <?php if ( $permittedFields['labels'] ): ?>
            <!-- Labels-->
            <div class="col-xs-12 lables">
                <p><b><span class="glyphicon glyphicon-tags"></span> &#160;Метки:</b></p>
                <div class="row">
                    <?php for ( $i = 0; $i < count($labels); $i++ ):?>
                        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3" title="<?=$labels[$i]['info']?>">
                            <input type="checkbox" <?=$labels[$i]['check'];?> name="labels[<?=$labels[$i]['id']?>]" id="<?=$labels[$i]['id']?>" aria-label="..." value="<?=$labels[$i]['name'];?>">
                            <label for="<?=$labels[$i]['id'];?>">
                            <span class="label <?=$labels[$i]['class'];?> lables-bottom">
                                <span class="glyphicon glyphicon-tag"></span> <?=$labels[$i]['name'];?></span>
                            </label>
                        </div>
                    <?php endfor; ?>
                </div>
                <hr />
            </div>
            <!--end Labels-->
        <?php endif; ?>


        <?php if ( $permittedFields['statuses'] ): ?>
            <!-- Statuses -->
            <div class="col-xs-12 status" id="workingCenters">
                <p title="Текущий статус" style="cursor: default;">
                    <span>
                        <span class="glyphicon glyphicon-ok"></span>
                        &#160;Текущий статус:
                    </span>
                    <span class="label label-warning" style="font-weight: bold;font-size: medium;" title="<?=$row['status']['title']?>" >
                        <span style="color: #1C1C1C" class="glyphicon glyphicon-<?=$row['status']['glyphi']?>"></span>
                        <span id="currentStatus" ><?=$row['status']['name_ru']?></span>
                    </span>
                    <button id="openAll" title="Раскрыть Все" onclick="event.preventDefault()" style="margin-bottom: 10px" class="pull-right btn btn-sm btn-info"><span class="glyphicon glyphicon-menu-left"></span> Раскрыть Все</button>
                    <button id="closeAll" title="Закрыть Все" onclick="event.preventDefault()" style="margin-bottom: 10px" class="pull-right hidden btn btn-sm btn-primary"><span class="glyphicon glyphicon-menu-down"></span> Закрыть Все</button>
                <div class="clearfix"></div>
                </p>
                <div class="row">
                    <?php
                    $countWC = count($status?:[]);
                    $columns = [
                        0=>'',
                        1=>'',
                        2=>'',
                        3=>'',
                    ];
                    $c = 0;
                    ob_start();
                    /**
                     * У людей проблемы с открытием статусов на компах под Win Xp chrome 40 - 49
                     */
                    $barubina = 'statusesPanelBodyHidden';
                    $userAccess = (int)$_SESSION['user']['access'];
                    if ( $userAccess > 1 ) $barubina = '';
                    ?>
                    <?php foreach ( $status?:[] as $wcName => $workingCenter ) :?>
                        <div class="panel panel-info" style="position:relative;">
                            <div class="panel-heading">
                                <?=$wcName?>
                                <button title="Раскрыть" onclick="event.preventDefault()" data-status="0" class="btn btn-sm btn-info statusesChevron"><span class="glyphicon glyphicon-menu-left"></span></button>
                            </div>
                            <div class="panel-body pb-0 statusesPanelBody <?=$barubina?>">
                                <?php foreach ( $workingCenter as $subUnit ) :?>
                                    <div class="list-group">
                                        <a class="list-group-item active"><?=$subUnit['descr']?></a>
                                        <?php foreach ( $subUnit['statuses'] as $status ) :?>
                                            <a class="list-group-item">
                                                <input type="radio" <?=$status['check'];?> name="status" id="<?=$status['name_en'];?>" aria-label="..." value="<?=$status['id'];?>">
                                                <label for="<?=$status['name_en'];?>" title="<?=$status['title'];?>">
                                                    <span class="glyphicon glyphicon-<?=$status['glyphi'];?>"></span>
                                                    <?=$status['name_ru'];?>
                                                </label>
                                            </a>
                                        <?php endforeach; ?>
                                        <a title="Ответственный" class="list-group-item list-group-item-success"><?=$subUnit['user']?></a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php $columns[$c] .= ob_get_contents(); ?>
                        <?php ob_clean(); ?>
                        <?php $c++; ?>
                        <?php if ( !($c % 4) ) $c = 0; ?>
                    <?php endforeach; ?>

                    <?php ob_end_clean(); ?>

                    <div class="col-xs-3" style="padding-right: 2px;"><?php echo $columns[0] ?></div>
                    <div class="col-xs-3" style="padding: 0 2px 0 2px; "><?php echo $columns[1] ?></div>
                    <div class="col-xs-3" style="padding: 0 2px 0 2px; "><?php echo $columns[2] ?></div>
                    <div class="col-xs-3" style="padding-left: 2px;"><?php echo $columns[3] ?></div>
                </div>
            </div>
            <!-- END Statuses -->
        <?php endif; ?>

    </div><!--end row-->
    <hr />

    <input type="hidden" name="save" value="1"/>
    <?php if ( !$permittedFields['number_3d'] ): ?>
        <input type="hidden" name="number_3d" value="<?=$_SESSION['general_data']['number_3d'];?>"/>
    <?php endif;?>
    <input type="hidden" name="id" value="<?=$id?>"/>
    <input type="hidden" name="edit" id="edit" value="<?=$component;?>"/>
    <input type="hidden" name="date" value="<?=date('Y-m-d'); ?>" />
</form>

<div class="row">
    <div class="col-xs-4">
        <a class="btn btn-default pull-left" role="button" href="<?=$prevPage;?>">
            <span class="glyphicon glyphicon-triangle-left"></span>
            Назад
        </a>
    </div><!--end col-xs-6-->
    <div class="col-xs-4">
        <center id="tosubmt">
            <button class="btn btn-default"  onclick="submitForm();" >
                <span class="glyphicon glyphicon-floppy-disk"></span>
                Сохранить
            </button>
        </center>
    </div><!--end col-xs-6-->
    <div class="col-xs-4">
        <? if ( $component === 2 && $_SESSION['user']['access'] < 3 ): ?>
        <a type="button" class="btn btn-danger pull-right" onclick="dell_fromServ(<?=$id;?>, false, false, 1);">
            <span class="glyphicon glyphicon-remove"></span>
            Удалить
        </a>
        <? endif; ?>
    </div><!--end col-xs-6-->
</div><!--end row-->

<p><?//debug($this->navBar,'1',1)?></p>

<img id="imageBoxPrev" width="200px" class="img-thumbnail hidden"/>
<?php include('includes/resultModal.php');?>
<?php include('includes/collectionsModal.php');?>
<?php include('includes/deleteModal.php');?>
<?php include('includes/num3dVC_input_Proto.php');?>
<?php include('includes/protoGemsVC_Rows.php');?>
<?php include('includes/protoImages_Row.php');?>
<?php $isRepairProto = true; include('includes/protoRepair.php');?>

<script defer src="<?=_views_HTTP_?>AddEdit/js/ResultModal.js?ver=<?=time();?>"></script>
<script defer src="<?=_views_HTTP_?>AddEdit/js/CollectionsModal.js?ver=<?=time();?>"></script>
<script defer src="<?=_views_HTTP_?>AddEdit/js/deleteModal.js?v=<?=time();?>"></script>
<script defer src="<?=_views_HTTP_?>AddEdit/js/add_edit.js?ver=<?=time();?>"></script>
<?=$stonesScript;?>

<div class="AddEditSideButtons" id="AddEditSideButtons">
    <div class="btn-group-vertical" role="group" aria-label="...">
        <button type="button" class="btn btn-info hidden" title="Вверх" onclick="pageUp();"><span class="glyphicon glyphicon-chevron-up"></span></button>
        <button type="button" class="btn btn-success" title="Сохранить" onclick="submitForm();"><span class="glyphicon glyphicon-floppy-disk"></span></button>
        <button type="button" class="btn btn-info" title="Вниз" onclick="pageDown();"><span class="glyphicon glyphicon-chevron-down"></span></button>
    </div>
</div>

<?
$this->title .= $row['number_3d'] ." ". $row['model_type'];
$imgEncode = json_encode($images,JSON_UNESCAPED_UNICODE);
?>
<div class="row" id="middleRow">

    <!-- images block start-->
    <div class="col-xs-12 col-sm-6 pl-0 pr-1" id="images_block">

        <ul class="nav nav-tabs" style="margin-left: -15px;">
            <li role="presentation" class="active" title="Картинки 3Д модели"><a href="#images3d" role="tab" data-toggle="tab">Рендеры 3Д</a></li>
            <? if ($button3D): ?>
            <li role="presentation" title="Доступен 3D просмотр">
                <a href="#" role="tab" id="butt3D" data-toggle="tab" ><span class="button-3D-pict"></span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Просмотр</a>
                <form method="post" id="extractform" class="hidden">
                    <input type="hidden" name="zip_name" value="<?= $row['number_3d'].'/'.$row['id'].'/stl/'.$button3D ?>" />
                    <input type="hidden" name="zip_path" value="<?= $row['number_3d'].'/'.$row['id'].'/stl/' ?>" />
                </form>
                <form method="post" id="dellstlform" class="hidden"></form>
            </li>
            <? endif; ?>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active in fade pt-1" id="images3d">
                <div class="row">
                    <div class="col-xs-12 pl-0">
                        <div class="panel mb-1">
                            <div class="mainImage cursorLoupe" data-id="<?=$mainImg['id']?>" style="background-image: url(<?=$mainImg['src']?>);"></div>
                        </div>
                    </div>
                </div>
                <div class="row dopImages">
                    <? foreach ( $images as $image ) :?>
                        <? $borderDopImg = $image['main'] == 1 ? 'border-primary-1': 'border-secondary-1' ?>
                        <div class="col-xs-6 col-sm-3 pl-0 pr-2 mb-1">
                            <div class="imageSmall cursorPointer border-radius-1 <?=$borderDopImg?> <?=$image['main']==1?'activeImage':''?>" data-id="<?=$image['id']?>" style="background-image: url(<?= $image['img_name'] ?>); height: 10rem;"></div>
                        </div>
                    <? endforeach; ?>
                </div>
            </div>
        </div>

    </div>
    <!-- END images block-->


    <div class="col-xs-12 col-sm-6 pl-1 pr-0" id="descr">

        <ul class="nav nav-tabs">
            <li role="presentation" class="active" title="Общая информация о изделии"><a href="#info" role="tab" data-toggle="tab">Информация</a></li>
            <li role="presentation" title="История статусов"><a href="#history" role="tab" data-toggle="tab">История</a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active in fade pt-1" id="info">

                <div class="panel mb-1 descriptionPanel">
                    <div class="panel-heading <?=$stat_class;?> cursorArrow mb-2" title="<?=$stat_title;?>"><span class="<?=$stat_glyphi?>"></span> <?=$stat_name;?><span title="Дата последнего изменения статуса"><?=$stat_date?" - " . $stat_date:''?></span></div>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <span class="badge badge-lg" id="num3d"><?=$row['number_3d']?></span>
                            <i class="fas fa-hashtag"></i> Номер 3D:
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-lg" id="articl"><?=$stillNo?></span>
                            <i class="fas fa-industry"></i> Фабричный Артикул:
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-lg" id="complects"><?=$complStr?></span>
                            <i class="fas fa-object-group"></i> В Комплекте:
                        </li>
                        <li class="list-group-item">
                            <?php foreach ( $coll_id as $coll ) : ?>
                                <span class="badge badge-lg"><i><a href="<?=_views_HTTP_?>Main/controllers/setSort.php?sCollId=<?=$coll['id']?>" id="collection"><?=$coll['name']?></a></i></span>
                            <?php endforeach;?>
                            <i class="fas fa-gem"></i> Коллекции:
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-lg"><?=$row['author']?></span>
                            <i class="fas fa-user-tie"></i> Автор:
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-lg"><?=$row['modeller3D']?></span>
                            <i class="fas fa-user-edit"></i> 3D модельер:
                        </li>
                        <?php if ( $row['jewelerName'] ): ?>
                        <li class="list-group-item">
                            <span class="badge badge-lg"><?=$row['jewelerName']?></span>
                            <i class="fas fa-user-cog"></i> Модельер-доработчик:
                        </li>
                        <?endif;?>
                        <li class="list-group-item">
                            <span class="badge badge-lg" id="modelType"><?=$row['model_type']?></span>
                            <span class="glyphicon glyphicon-eye-open"></span> Вид модели:
                        </li>
                        <li class="list-group-item">
                            <span class="badge badge-lg"><?=$row['model_weight']." гр."?></span>
                            <span class="glyphicon glyphicon-scale"></span> Вес в 3D:
                        </li>
                        <?php if ( isset($row['size_range']) && !empty($row['size_range']) ) : ?>
                            <li class="list-group-item">
                                <span class="badge badge-lg"><?=$row['size_range']?></span>
                                <i class="fab fa-quinscape"></i> Размерный Ряд:
                            </li>
                        <?php endif; ?>
                        <?php
                        if ( isset($row['print_cost']) && !empty($row['print_cost']) && $session['user']['access'] > 0 ) : ?>
                            <li class="list-group-item">
                                <span class="badge badge-lg"><?=$row['print_cost']." гр."?></span>
                                <span class="glyphicon glyphicon-usd"></span> Печать:
                            </li>
                        <?php endif; ?>
                        <?php if (  is_array($ai_file) ) : ?>
                            <li class="list-group-item" title="загрузить файл накладки">
                                <span class="badge badge-lg"><a href="<?= _stockDIR_HTTP_.$modelView->number_3d.'/'.$id.'/ai/'.$ai_file['name'] ?>">Скачать</a></span>
                                <span class="glyphicon glyphicon-floppy-save"></span> Накладка:
                            </li>
                        <?php endif; ?>
                        <?php if ( !empty($labels) ) : ?>
                            <li class="list-group-item text-right">
                                <span class="pull-left"><span class="glyphicon glyphicon-tags"></span>&nbsp;&nbsp;Метки:</span>
                                <?php foreach ( $labels as $label ) : ?>
                                    <span class="label <?=$label['class']?>"><span class="glyphicon glyphicon-tag"></span> <?=$label['name']?></span>
                                <?php endforeach; ?>
                            </li>
                        <?php endif; ?>
                        <?php if ( !empty($row['description']) ) :?>
                        <li class="list-group-item">
                            <span class="glyphicon glyphicon-comment"></span><strong> Примечания:</strong> &nbsp;
                            <span><?=$row['description'];?></span>
                        </li>
                        <?php endif; ?>
                        <li class="list-group-item">
                            <span class="pull-left" title="Дата добавления модели в базу"><span class="glyphicon glyphicon-calendar"></span>&nbsp;&nbsp;Дата создания:</span>
                            <span title="Создатель" class="badge"><?=$row['creator_name'];?></span>
                            <span title="Дата создания" class="badge"><?=date_create( $row['date'] )->Format('d.m.Y');?></span>
                            <div class="clearfix"></div>
                        </li>
                        <? if ( $editBtn ): ?>
                            <li class="list-group-item">
                                <span ><span class="glyphicon glyphicon-print"></span>&nbsp;&nbsp;Печать:</span>
                                <span>
                                    <a class="btn btn-sm btn-info" onclick="getPDF('passport');" role="button">
                                        <span> Пасспорт</span>
                                    </a>
                                    <a class="btn btn-sm btn-info" onclick="getPDF('runner');" role="button">
                                        <span> Бегунок</span>
                                    </a>
                                </span>
                                <a href="<?=_views_HTTP_?>AddEdit/index.php?id=<?=$id?>&component=2" class="btn btn-default pull-right" style="color: #0f0f0f!important;">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                    Редактировать
                                </a>
                                <div class="clearfix"></div>
                            </li>
                        <?endif;?>
                    </ul>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane in fade pt-1" id="history">
                <div class="list-group">
                    <?php for ( $i = 0; $i < count($statuses); $i++ ) :?>
                        <a href="#" class="list-group-item" title="<?=$statuses[$i]['title']?>">
                            <?=$statuses[$i]['date']." &#160;&#160;"?>
                            <span class="glyphicon glyphicon-<?=$statuses[$i]['glyphi']?>"></span>
                            <?=$statuses[$i]['status']." &#160;&#160; ( ".$statuses[$i]['name']." )"?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-lg-6 pl-1 pr-0">
        <div class="panel mb-1 panel-success">
            <div class="panel-heading"><i class="fab fa-codepen"></i> <b>Материалы:</b></div>
            <ul class="list-group">
                <?php foreach ( $matsCovers?:[] as $material ) : ?>
                <li class="list-group-item brb-2-secondary bg-info-light">
                    <span class="badge badge-lg"><?=$material['probe']."&deg;"?></span>
                    <span class="badge badge-lg"><?=$material['metalColor']?></span>
                    <span class="badge badge-lg"><?=$material['type']?></span>
                    <b><?=$material['part']?:"&#160;"?></b>
                </li>
                <li class="list-group-item p0">
                    <table class="table table-condensed table-striped mb-0 text-small text-center">
                        <thead>
                            <tr class="text-muted">
                                <td>Покрытие</td><td>Площадь</td><td>Цвет Покрытия</td><td>Обработка</td>
                            </tr>
                        </thead>
                        <tbody class="brb-2-success">
                            <tr>
                                <th class="border-right-1 text-center"><?=$material['covering'];?></th>
                                <th class="border-right-1 text-center"><?=$material['area'];?></th>
                                <th class="border-right-1 text-center"><?=$material['covColor'];?></th>
                                <th class="text-center"><?=$material['handling'];?></th>
                            </tr>
                        </tbody>
                    </table>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <?php if ( !empty($gemsTR) ) : ?>
        <div class="col-xs-12 col-sm-12 col-lg-6 pl-1 pr-0">
            <div class="panel mb-1 panel-info">
                <div class="panel-heading"><i class="far fa-gem"></i> <b>Вставки 3D:</b></div>
                <table class="table table-condensed mb-0 text-small text-center table_gems">
                    <thead>
                    <tr class="text-muted">
                        <td>Размер</td><td>Кол-во</td><td>Огранка</td><td>Сырьё</td><td>Цвет</td>
                    </tr>
                    </thead>
                    <tbody class="tbody_gems">
                    <?php foreach ( $gemsTR?:[] as $gem ) : ?>
                        <tr class="text-bold">
                            <td><?=$gem['gem_size']?></td>
                            <td><?=$gem['gem_value']?></td>
                            <td><?=$gem['gem_cut']?></td>
                            <td><?=$gem['gem_name']?></td>
                            <td><?=$gem['gem_color']?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
    <?php if ( !empty($dopVCTr) ) : ?>
        <div class="col-xs-12 col-sm-12 col-lg-6 pl-1 pr-0">
            <div class="panel mb-1 panel-warning">
                <div class="panel-heading"><span class="glyphicon glyphicon-link"></span> <b>Ссылки на другие артикулы:</b></div>
                <table class="table table-condensed mb-0 text-small text-center table_gems table_vc_links">
                    <thead>
                    <tr class="text-muted">
                        <td>Название</td><td>№3D/Арт.</td><td>Описание</td>
                    </tr>
                    </thead>
                    <tbody class="tbody_vc_links">
                    <?php foreach ( $dopVCTr?:[] as $vcLink  ) : ?>
                        <tr class="text-bold">
                            <td><?=$vcLink['vc_names']?></td>
                            <td><?=$vcLink['vc_link'];?></td>
                            <td><?=$vcLink['vc_descript']?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div><!-- end Middle Row-->
<?= $repairs3D ?>
<?= $repairsJew ?>

<script defer id="imageViewerJs" src="<?=_views_HTTP_.$this->controllerName?>/js/imageViewer.js?ver=<?=time()?>"></script>
<script><?=
    <<<JS
    document.getElementById('imageViewerJs').onload = function() {
        new ImageViewer($imgEncode).init();
    };
JS;
?></script>
<script defer src="<?=_views_HTTP_.$this->controllerName?>/js/show_pos_scrpt.js?ver=<?=time()?>"></script>

<? if($dopBottomScripts): ?>
    <script src="<?= _webDIR_HTTP_ ?>js_lib/three.min.js"></script>
    <script src="<?= _webDIR_HTTP_ ?>js_lib/OrbitControls.js"></script>
    <script src="<?= _webDIR_HTTP_ ?>js_lib/TrackballControls.js"></script>
    <script src="<?= _webDIR_HTTP_ ?>js_lib/TransformControls.js"></script>
    <script src="<?= _webDIR_HTTP_ ?>js_lib/STLLoader.js"></script>
    <?php
        $this->startBlock('3DPanels');
        include_once _viewsDIR_.$this->controllerName."/includes/3DWievPanels.php";
        $this->endBlock();
    ?>
<? endif; ?>

<!-- lond cut div -->
<div id="longTD" class="longTD hidden"></div>
<img id="imageBoxPrev" style="max-height:250px; max-width:200px;" class="img-thumbnail hidden"/>

<? include_once _viewsDIR_.$this->controllerName."/includes/imageWrapper.php" ?>
<? include_once _globDIR_. 'includes/progressModal.php' ?>

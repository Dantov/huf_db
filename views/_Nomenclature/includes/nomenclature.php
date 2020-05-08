<div class="row">
    <p class="lead text-info text-center">Списки Наименований</p>

    <div class="col-xs-12 stats_table">
        <ul class="nav nav-tabs" role="tablist" id="tablist">
            <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab">Коллекции</a></li>
            <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab">Камни</a></li>
            <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab">Общие данные</a></li>
        </ul>
        <div class="tab-content">

            <!-- КОЛЛЕКЦИИ -->
            <div role="tabpanel" class="tab-pane active in fade" id="tab1">
                <br/>
                <table class="table table-hover">
                    <thead>
                    <tr class="thead11">
                        <th width="5%">№</th>
                        <th>Имя</th>
                        <th>Дата создания</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody data-coll="collections">
                    <?php foreach ($collections as $i => $row_coll) : ?>
                        <tr class="collsRow">
                            <td><?=$i;?></td>
                            <td>
                                <input type="text" class="form-control" data-coll="collections" data-id="<?=$row_coll['id'];?>" value="<?=$row_coll['name'];?>">
                            </td>
                            <td><?=date_create( $row_coll['date'] )->Format('d.m.Y');?></td>
                            <td>
                                <a class="btn btn-sm btn-default" type="button" role="button">
                                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php require _viewsDIR_.'_Nomenclature/includes/plus.php'?>
                    <tr class="warning">
                        <td></td>
                        <td>Всего коллекций: <?=$i;?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div> <!-- end of КОЛЛЕКЦИИ -->

            <!-- start of panel 2 -->
            <div role="tabpanel" class="tab-pane fade" id="tab2">
                <br/>
                <div class="row">

                    <div class="col-xs-6">

                        <!-- start КАМНИ -->
                        <table class="table table-hover">
                            <thead>
                            <tr class="thead11">
                                <th>№</th>
                                <th>Сырьё</th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody data-coll="gems_names">
                            <?php foreach ($gems_names as $i => $gems_names_row) : ?>
                                <tr class="collsRow">
                                    <td><?=$i;?></td>
                                    <td>
                                        <input type="text" class="form-control inpt" data-coll="gems_names" data-id="<?=$gems_names_row['id'];?>" value="<?=$gems_names_row['name'];?>">
                                    </td>
                                    <td></td>
                                    <td>
                                        <a class="btn btn-sm btn-default" type="button" role="button">
                                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php require _viewsDIR_.'_Nomenclature/includes/plus.php'?>
                            <tr class="warning">
                                <td></td>
                                <td>Всего: <?=$i;?></td>
                                <td></td>
                                <td></td>
                            </tr>

                            </tbody>
                        </table>

                        <!-- gems КАМНИ ОГРАНКА -->
                            <table class="table table-hover">
                                <thead>
                                <tr class="thead11">
                                    <th>№</th>
                                    <th>Огранка</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody data-coll="gems_cut">
                                <?php foreach ($gems_cut as $i => $gems_cut_row) : ?>
                                    <tr class="collsRow">
                                        <td><?=$i;?></td>
                                        <td>
                                            <input type="text" class="form-control" data-coll="gems_cut" data-id="<?=$gems_cut_row['id'];?>" value="<?=$gems_cut_row['name']; ?>">
                                        </td>
                                        <td></td>
                                        <td>
                                            <a class="btn btn-sm btn-default" type="button" role="button">
                                                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php require _viewsDIR_.'_Nomenclature/includes/plus.php'?>
                                <tr class="warning">
                                    <td></td>
                                    <td>Всего: <?=$i;?></td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                </tbody>
                            </table> <!-- end gems cut -->


                            <!-- gems КАМНИ ЦВЕТА -->
                            <table class="table table-hover">
                                <thead>
                                <tr class="thead11">
                                    <th>№</th>
                                    <th>Цвета</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody data-coll="gems_color">
                                <?php foreach ($gems_color as $i => $gems_color_row) : ?>
                                    <tr class="collsRow">
                                        <td><?=$i; ?></td>
                                        <td>
                                            <input type="text" class="form-control" data-coll="gems_color" data-id="<?=$gems_color_row['id'];?>" value="<?=$gems_color_row['name']; ?>">
                                        </td>
                                        <td></td>
                                        <td>
                                            <a class="btn btn-sm btn-default" type="button" role="button">
                                                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php require _viewsDIR_.'_Nomenclature/includes/plus.php'?>
                                <tr class="warning">
                                    <td></td>
                                    <td>Всего: <?=$i; ?></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table> <!-- end gems color -->
                    </div><!-- end of col-xs-6 -->


                    <div class="col-xs-6">

                        <!-- gems sizes start -->
                        <table class="table table-hover">
                            <thead>
                            <tr class="thead11">
                                <th>№</th>
                                <th>Размеры</th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody data-coll="gems_sizes">
                            <?php foreach ($gems_size as $i => $gems_size_row) : ?>
                                <tr class="collsRow">
                                    <td><?=$i;?></td>
                                    <td>
                                        <input type="text" class="form-control" data-coll="gems_sizes" data-id="<?=$gems_size_row['id'];?>" value="<?=$gems_size_row['name'];?>">
                                    </td>
                                    <td></td>
                                    <td>
                                        <a class="btn btn-sm btn-default" type="button" role="button">
                                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php require _viewsDIR_.'_Nomenclature/includes/plus.php'?>
                            <tr class="warning">
                                <td></td>
                                <td>Всего: <?php echo $i; ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table><!-- end gems sizes -->
                    </div><!-- end of col-xs-6 -->

                </div><!-- end row -->

            </div> <!-- end of panel 2 -->


            <!-- start of panel 3 -->
            <div role="tabpanel" class="tab-pane fade" id="tab3">

                <!-- authors start -->
                <div class="col-xs-12 col-sm-6">
                    <table class="table table-hover">
                        <thead>
                        <tr class="thead11">
                            <th>№</th>
                            <th>Авторы</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody data-coll="author">
                        <?php foreach ($gems_author as $i => $gems_author_row) : ?>
                            <tr class="collsRow">
                                <td><?=$i;?></td>
                                <td>
                                    <input type="text" class="form-control" data-coll="author" data-id="<?=$gems_author_row['id'];?>" value="<?=$gems_author_row['name'];?>">
                                </td>
                                <td></td>
                                <td>
                                    <a class="btn btn-sm btn-default" type="button" role="button">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php require _viewsDIR_.'_Nomenclature/includes/plus.php'?>
                        <tr class="warning">
                            <td></td>
                            <td>Всего: <?=$i;?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div><!-- authors end -->

                <!-- 3d modellers start -->
                <div class="col-xs-12 col-sm-6">
                    <table class="table table-hover">
                        <thead>
                        <tr class="thead11">
                            <th>№</th>
                            <th>3D модельеры</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody data-coll="modeller3d">
                        <?php foreach ($gems_modeller3D as $i => $gems_modeller3D_row) : ?>
                            <tr class="collsRow">
                                <td><?php echo $i; ?></td>
                                <td>
                                    <input type="text" class="form-control" data-coll="modeller3d" data-id="<?=$gems_modeller3D_row['id'];?>" value="<?=$gems_modeller3D_row['name'];?>">
                                </td>
                                <td></td>
                                <td>
                                    <a class="btn btn-sm btn-default" type="button" role="button">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php require _viewsDIR_.'_Nomenclature/includes/plus.php'?>
                        <tr class="warning">
                            <td></td>
                            <td>Всего: <?= $i; ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>

                    </table>
                </div>
                <!-- 3d modellers end -->

                <!-- Jeweler start -->
                <div class="col-xs-12 col-sm-6">
                    <table class="table table-hover">
                        <thead>
                        <tr class="thead11">
                            <th>№</th>
                            <th>Модельеры-доработчики</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody data-coll="jeweler_names">
                        <?php foreach ($jeweler as $i => $jeweler_row) : ?>
                            <tr class="collsRow">
                                <td><?php echo $i; ?></td>
                                <td>
                                    <input type="text" class="form-control" data-coll="jeweler_names" data-id="<?=$jeweler_row['id'];?>" value="<?=$jeweler_row['name'];?>">
                                </td>
                                <td></td>
                                <td>
                                    <a class="btn btn-sm btn-default" type="button" role="button">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php require _viewsDIR_.'_Nomenclature/includes/plus.php'?>
                        <tr class="warning">
                            <td></td>
                            <td>Всего: <?= $i; ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>

                    </table>
                </div>
                <!-- Jeweler end -->

                <!-- model type start -->
                <div class="col-xs-12 col-sm-6">
                    <table class="table table-hover">
                        <thead>
                        <tr class="thead11">
                            <th>№</th>
                            <th>Вид модели</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody data-coll="model_type">
                        <?php foreach ($gems_model_type as $i => $gems_model_type_row) : ?>
                            <tr class="collsRow">
                                <td><?=$i;?></td>
                                <td>
                                    <input type="text" class="form-control" data-coll="model_type" data-id="<?=$gems_model_type_row['id'];?>" value="<?php echo $gems_model_type_row['name'];?>">
                                </td>
                                <td></td>
                                <td>
                                    <a class="btn btn-sm btn-default" type="button" role="button">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php require _viewsDIR_.'_Nomenclature/includes/plus.php'?>
                        <tr class="warning">
                            <td></td>
                            <td>Всего: <?php echo $i; ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- model type end -->

                <!-- dop articles start -->
                <div class="col-xs-12 col-sm-6">
                    <table class="table table-hover">
                        <thead>
                        <tr class="thead11">
                            <th>№</th>
                            <th>Доп. Артикулы</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody data-coll="vc_names">
                        <?php foreach ($gems_vc_names as $i => $gems_vc_names_row) : ?>
                            <tr class="collsRow">
                                <td><?=$i;?></td>
                                <td>
                                    <input type="text" class="form-control" data-coll="vc_names" data-id="<?=$gems_vc_names_row['id'];?>" value="<?php echo $gems_vc_names_row['name'];?>">
                                </td>
                                <td></td>
                                <td>
                                    <a class="btn btn-sm btn-default" type="button" role="button">
                                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php require _viewsDIR_.'_Nomenclature/includes/plus.php'?>
                        <tr class="warning">
                            <td></td>
                            <td>Всего: <?=$i;?></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>

                </div>
                <!-- model type end -->

            </div>
            <!-- end of panel 3 -->

        </div><!-- end of Tab content -->

    </div><!--row-->

</div>

<?php //include('includes/nom_incl.php');?>
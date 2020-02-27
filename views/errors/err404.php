<?php
	header("location:" ._views_HTTP_ . "Main/index.php" );
?>
<!DOCTYPE html>
<html lang="ru">
    <head><?php include(_globDIR_.'head_adm.php');?></head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-xs-2">
                </div>
				<div class="col-xs-8">
                    <p></p>
                    <div class="alert alert-danger" role="alert">
                        <h2 class="center-block">Страница не найдена: что-то пошло не так.</h2>
                    </div>
                    <div class="alert alert-info" role="alert">
                        <h4>
                            <b><a href="<?=_views_HTTP_?>Main/index.php">На главную</a></b>.<br />
                        </h4>
                    </div>
                </div>
                <div class="col-xs-2">
                </div>
            </div>
            <?php include(_globDIR_.'bottomScripts_adm.php');?>
        </div>
    </body>
</html>
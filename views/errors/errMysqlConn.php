<?php
	$errno = $_GET['errno'];
    $errtext = $_GET['errtext'];
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
                        <h2 class="center-block">Ошибка: Невозможно установить соединение с MySQL.</h2>
                        <p>Код: <?=$errno;?></p>
                        <p>Описание: <?=$errtext;?></p>
                    </div>
                    <div class="alert alert-info" role="alert">
                        <h4>
                            Попробейте вернутья на <b><a href="<?=_views_HTTP_?>Main/index.php">главную страницу</a></b>.<br />
                            Узнайте включен ли сервер.
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
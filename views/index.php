<?php
session_start();
if( isset($_SESSION['access']) && $_SESSION['access'] === true ) header("location: /Main/index.php" );

$access = 0;

if ( isset($_POST['submit']) ) 
{
	require_once "Glob_Controllers/db.php";
	
	$login = htmlspecialchars( trim($_POST['login']), ENT_QUOTES );
	$login  = mysqli_real_escape_string($connection, $login);
	$result_log = mysqli_query($connection, " SELECT * FROM users WHERE login='$login' ");
	mysqli_close($connection);

	$userRow = mysqli_fetch_assoc($result_log);

	if ( $userRow ) {
		$access = 1; //правильный логин
		
		$pass = trim($_POST['pass']);
		
		if ( $pass === $userRow['pass'] ) {
			$access = 2; //правильный пароль
		} else {
			$wrongPass = 'Пароль не верен!';
		}
	} else {
		$wrongLog = 'Логин не верен!';
	}
}
	
if ( $access === 2 ) {
	
	session_start();
	
	$_SESSION['assist']['memeMe'] = isset($_POST['memeMe']) ? 1 : 0;
	$_SESSION['assist']['access'] = $access;
	
	$_SESSION['user']['id'] 	= $userRow['id'];
	$_SESSION['user']['access'] = $userRow['access'];
	$_SESSION['user']['fio']	= $userRow['fio'];
	
	header("location: Glob_Controllers/enter.php");
	
} else {
	//переходим на главную если есть куки
	if ( isset($_COOKIE['meme_sessA']) ) header("location: /Main/index.php" );
?>

<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>HUF 3d models base</title>
	<link rel="icon" href="../web/favicon.ico?ver=<?=time()?>">
	<link rel="stylesheet" href="<?= _webDIR_HTTP_ ?>css/style.css?ver=<?=time()?>">
	<link rel="stylesheet" href="<?= _webDIR_HTTP_ ?>css/style_adm.css?ver=<?=time()?>">
	<link rel="stylesheet" href="<?= _webDIR_HTTP_ ?>css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= _webDIR_HTTP_ ?>css/bootstrap-theme.min.css">
</head>
<body>

<div class="container">
    <div class="col-xs-12">
        <br />
        <form id="auth_form" method="post" action="index.php">
            <fieldset>
                <legend><span class="glyphicon glyphicon-lock"></span> Вход в базу 3D моделей ХЮФ</legend>
                <div class="form-group">
                    <label for="inputLogin">Логин</label><span style="color: red;"> <?=$wrongLog; ?></span>
                    <input type="text" name="login" id="Login" required placeholder="Введите логин" class="form-control" value="<?=$login; ?>" />
                </div>
                <div class="form-group">
                    <label for="inputPassword">Пароль</label><span style="color: red;"> <?=$wrongPass; ?>
                    <input type="password" name="pass" id="Pass" required placeholder="Введите пароль" class="form-control">
                </div>
                <div class="form-group">
                    <input type="checkbox" name="memeMe" id="memeMe"/>
                    <label for="memeMe" class="memeMe">&#160;Запомнить меня</label>
                    <br>
                    <button type="submit" name="submit" class="btn btn-default"><span class="glyphicon glyphicon-log-in"></span>&#160; Войти</button>
                </div>
            </fieldset>
        </form>
    </div>
</div>

</body>
</html>
<?php
	} // ELSE
?>
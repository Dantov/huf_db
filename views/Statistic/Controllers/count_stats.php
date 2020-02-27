<?php include '../inc/db.php'; ?>

<h2>Статистика</h2>

<p><a href="?interval=1">За сегодня</a></p>
<p><a href="?interval=7">За последнюю неделю</a></p>

<table style="border: 1px solid silver;">

<tr>
    <td style="border: 1px solid silver;">Дата</td>
    <td style="border: 1px solid silver;">Уникальных посетителей</td>
    <td style="border: 1px solid silver;">Просмотров</td>
</tr>

<?php
// Если в массиве GET есть элемент interval (т.е. был клик по одной из ссылок выше)
if ($_GET['interval'])
{
	$interval = $_GET['interval'];
    
    // Если в качестве параметра передано не число
    if (!is_numeric ($interval))
    {
        echo '<p><b>Недопустимый параметр!</b></p>';        
    }
    
    // Указываем кодировку, в которой будет получена информация из базы 
    @mysqli_query ($db, 'set character_set_results = "utf8"');
    
    // Получаем из базы данные, отсортировав их по дате в обратном порядке в количестве interval штук
	$res = mysqli_query($db, "SELECT * FROM `visits` ORDER BY `date` DESC LIMIT $interval");    
    
    // Формируем вывод строк таблицы в цикле
	while ($row = mysqli_fetch_assoc($res))
    {
		echo '<tr>
			     <td style="border: 1px solid silver;">' . $row['date'] . '</td>
			     <td style="border: 1px solid silver;">' . $row['hosts'] . '</td>
			     <td style="border: 1px solid silver;">' . $row['views'] . '</td>
			 </tr>';
	}
}
?>

</table>
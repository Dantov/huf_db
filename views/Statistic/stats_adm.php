<?php
session_start();

if( !isset( $_SESSION['access'] ) || $_SESSION['access'] != true ){
header("location: ../stanadmin.php");
}
else{ ?>
<!DOCTYPE HTML>
<html>

<head>
  <?php include('head_adm.php'); ?>
</head>

<body>
<?php
  include('navBar_adm.php');
?>
<script>
"use strict"
document.getElementById('navnav').children[4].setAttribute('class','active');
</script>

<div class="container">
  <div class="row">
    <p class="lead text-info text-center">Статистика посещений</p>
      <div class="col-xs-12 stats_table">
      <p> За все время: <br />
      <?php
        include('../inc/db_.php');
        include('../db.php');
        $date = date('Y-m-d');
        
        $res = mysqli_query($db_stats, " SELECT views, hosts FROM visits ") or die ("Проблема при подключении к БД");
        $summV = 0;
        $summH = 0;
        while ($row = mysqli_fetch_assoc($res)) {
            $summV += $row['views'];
            $summH += $row['hosts'];
        }
        echo 'Уникальных посетителей: <i>' . $summH . '</i><br/>';
        echo ' Общее кол-во просмотров всех страниц: <i>' . $summV . '</i>';
                  
      ?>
      </p>
      
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab">За сутки</a></li>
        <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab">За неделю</a></li>
        <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab">За месяц</a></li>
        <li role="presentation"><a href="#tab4" role="tab" data-toggle="tab">За год</a></li>
      </ul>
      <div class="tab-content">
      
        <div role="tabpanel" class="tab-pane active in fade" id="tab1">
          <table class="table table-hover">
            <thead>
               <tr>
                <th>Дата</th>
                <th>Уникальных посетителей</th>
                <th>Просмотры главной страницы</th>
                <th>Просмотры страницы Прайс-Лист</th>
                <th>Все Клики</th>
              </tr>
            </thead>
            <tbody>
         <?php
	    $res = mysqli_query($db_stats, "SELECT * FROM visits ORDER BY date DESC LIMIT 1");
        
        $hosts_summ = 0;
         $views_summ = 0;
         $indx_summ = 0;
         $price_summ = 0; 

        while ($row = mysqli_fetch_assoc($res)) {
            
            $formatedDate = date_create( $row['date'] )->Format('d.m.Y');
            
		  echo '<tr>
			     <td>' . $formatedDate . '</td>
			     <td>' . $row['hosts'] . '</td>
                 <td>' . $row['index_visited'] . '</td>
                 <td>' . $row['price_visited'] . '</td>
                 <td>' . $row['views'] . '</td>
			    </tr>';
	         }
         ?>
            </tbody>
          </table>
          <hr />
      <p class="text-info"><strong>Просмотры по позициям:</strong></p>
        <table class="table table-hover">
            <thead>
               <tr>
                <th>Имя</th>
                <th>просмотры</th>
              </tr>
            </thead>
            <tbody>
              <?php

        $result_names = mysqli_query($connection, " SELECT id, short_name FROM stock ");
        $result_views = mysqli_query($db_stats, " SELECT pos_string FROM posviews WHERE date='$date' ");
        
        $pieces = array();
        
        while( $row_views = mysqli_fetch_assoc($result_views) ) {
            $string_counter = $row_views['pos_string'];
            $arr_temp = explode(";", $string_counter);
            $pieces = array_merge($pieces, $arr_temp);
            
         }
         //print_r($pieces);

        $wholeSumm = 0;
        while($row = mysqli_fetch_assoc($result_names))
        { ?> 
              <tr>
                <td><?php echo $row['short_name']; ?></td>
                <td><?php
                       $summ = 0;
                       for( $i = 0; $i < count($pieces); $i++ ) {
                         if ( $row['id'] == $pieces[$i] ) {
                           $summ++;
                         }
                       }
                       $wholeSumm += $summ;
                       echo $summ;
             ?></td>
             </tr>
             <?php }  ?>
              <tr class="warning">
                <td>Всех:</td>
                <td><?php echo $wholeSumm; ?></td>
              </tr>
            </tbody>
          </table>
        </div> <!-- end of panel 1 -->
        
        <div role="tabpanel" class="tab-pane fade" id="tab2">
          <table class="table table-hover">
            <thead>
               <tr>
                <th>Дата</th>
                <th>Уникальных посетителей</th>
                <th>Просмотры главной страницы</th>
                <th>Просмотры страницы Прайс-Лист</th>
                <th>Все Клики</th>
              </tr>
            </thead>
            <tbody>
         <?php
	    $res = mysqli_query($db_stats, " SELECT * FROM visits
                                         WHERE date > DATE_SUB(CURDATE(), INTERVAL (DAYOFWEEK(CURDATE()) -1) DAY)
                                         AND date < DATE_ADD(CURDATE(), INTERVAL (9 - DAYOFWEEK(CURDATE())) DAY)
                                         ORDER BY date DESC ");
        
         $hosts_summ = 0;
         $views_summ = 0;
         $indx_summ = 0;
         $price_summ = 0;

        while ($row = mysqli_fetch_assoc($res)) {
            $hosts_summ += $row['hosts'];
            $views_summ += $row['views'];
            $indx_summ += $row['index_visited'];
            $price_summ += $row['price_visited'];
            $formatedDate = date_create( $row['date'] )->Format('d.m.Y');
            
		  echo '<tr>
			     <td>' . $formatedDate . '</td>
			     <td>' . $row['hosts'] . '</td>
                 <td>' . $row['index_visited'] . '</td>
                 <td>' . $row['price_visited'] . '</td>
                 <td>' . $row['views'] . '</td>
			    </tr>';
	         }
         ?>
            <tr class="warning">
			     <td>Всего:</td>
			     <td><?php echo $hosts_summ; ?></td>
                 <td><?php echo $indx_summ; ?></td>
                 <td><?php echo $price_summ; ?></td>
                 <td><?php echo $views_summ; ?></td>
			</tr>
            </tbody>
          </table>
          <hr />
        <p class="text-info"><strong>Просмотры по позициям:</strong></p>
        <table class="table table-hover">
            <thead>
               <tr>
                <th>Имя</th>
                <th>просмотры</th>
              </tr>
            </thead>
            <tbody>
              <?php

        $result_names = mysqli_query($connection, " SELECT id, short_name FROM stock ");
        $result_views = mysqli_query($db_stats, " SELECT pos_string FROM posviews 
                                                  WHERE date > DATE_SUB(CURDATE(), INTERVAL (DAYOFWEEK(CURDATE()) -1) DAY)
                                                  AND date < DATE_ADD(CURDATE(), INTERVAL (9 - DAYOFWEEK(CURDATE())) DAY) 
                                                  ");
        
        $pieces_week = array();                                                                            
         while( $row_views = mysqli_fetch_assoc($result_views) ) {
            $string_counter = $row_views['pos_string'];
            $arr_temp = explode(";", $string_counter);
            $pieces_week = array_merge($pieces_week, $arr_temp);
            
         }
        $wholeSumm = 0;
        while($row = mysqli_fetch_assoc($result_names))
        { ?> 
              <tr>
                <td><?php echo $row['short_name']; ?></td>
                <td><?php
                       $summ = 0;
                       for( $i = 0; $i < count($pieces_week); $i++ ) {
                         if ( $row['id'] == $pieces_week[$i] ) {
                           $summ++;
                         }
                       }
                       $wholeSumm += $summ;
                       echo $summ;
             ?></td>
             </tr>
             <?php }  ?>
              <tr class="warning">
                <td>Всех:</td>
                <td><?php echo $wholeSumm; ?></td>
              </tr>
            </tbody>
          </table>
        </div> <!-- end of panel 2 -->
        
        <div role="tabpanel" class="tab-pane fade" id="tab3">
          <table class="table table-hover">
            <thead>
               <tr>
                <th>Дата</th>
                <th>Уникальных посетителей</th>
                <th>Просмотры главной страницы</th>
                <th>Просмотры страницы Прайс-Лист</th>
                <th>Все Клики</th>
              </tr>
            </thead>
            <tbody>
         <?php
	    $res = mysqli_query($db_stats, " SELECT * FROM visits
                                         WHERE date > LAST_DAY(CURDATE()) + INTERVAL 1 DAY - INTERVAL 1 MONTH
                                         AND date < DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY) 
                                         ORDER BY date DESC ");
        
         $hosts_summ = 0;
         $views_summ = 0;
         $indx_summ = 0;
         $price_summ = 0;

        while ($row = mysqli_fetch_assoc($res)) {
            $hosts_summ += $row['hosts'];
            $views_summ += $row['views'];
            $indx_summ += $row['index_visited'];
            $price_summ += $row['price_visited'];
            $formatedDate = date_create( $row['date'] )->Format('d.m.Y');
            
		  echo '<tr>
			     <td>' . $formatedDate . '</td>
			     <td>' . $row['hosts'] . '</td>
                 <td>' . $row['index_visited'] . '</td>
                 <td>' . $row['price_visited'] . '</td>
                 <td>' . $row['views'] . '</td>
			    </tr>';
	         }
         ?>
            <tr class="warning">
			     <td>Всего:</td>
			     <td><?php echo $hosts_summ; ?></td>
                 <td><?php echo $indx_summ; ?></td>
                 <td><?php echo $price_summ; ?></td>
                 <td><?php echo $views_summ; ?></td>
			</tr>
            </tbody>
          </table>
          <hr />
        <p class="text-info"><strong>Просмотры по позициям:</strong></p>
        <table class="table table-hover">
            <thead>
               <tr>
                <th>Имя</th>
                <th>просмотры</th>
              </tr>
            </thead>
            <tbody>
              <?php

        $result_names = mysqli_query($connection, " SELECT id, short_name FROM stock ");
        $result_views = mysqli_query($db_stats, " SELECT pos_string FROM posviews 
                                                  WHERE date > LAST_DAY(CURDATE()) + INTERVAL 1 DAY - INTERVAL 1 MONTH
                                                  AND date < DATE_ADD(LAST_DAY(CURDATE()), INTERVAL 1 DAY)
                                                  ");
        
        $pieces_week = array();                                                                            
         while( $row_views = mysqli_fetch_assoc($result_views) ) {
            $string_counter = $row_views['pos_string'];
            $arr_temp = explode(";", $string_counter);
            $pieces_week = array_merge($pieces_week, $arr_temp);
            
         }
        $wholeSumm = 0;
        while($row = mysqli_fetch_assoc($result_names))
        { ?> 
              <tr>
                <td><?php echo $row['short_name']; ?></td>
                <td><?php
                       $summ = 0;
                       for( $i = 0; $i < count($pieces_week); $i++ ) {
                         if ( $row['id'] == $pieces_week[$i] ) {
                           $summ++;
                         }
                       }
                       $wholeSumm += $summ;
                       echo $summ;
             ?></td>
             </tr>
             <?php }  ?>
              <tr class="warning">
                <td>Всех:</td>
                <td><?php echo $wholeSumm; ?></td>
              </tr>
            </tbody>
          </table>
        </div> <!-- end of panel 3 -->
        
        <div role="tabpanel" class="tab-pane fade" id="tab4">
        <table class="table table-hover">
            <thead>
               <tr>
                <th>Дата</th>
                <th>Уникальных посетителей</th>
                <th>Просмотры главной страницы</th>
                <th>Просмотры страницы Прайс-Лист</th>
                <th>Все Клики</th>
              </tr>
            </thead>
            <tbody>
         <?php
	    $res = mysqli_query($db_stats, "SELECT * FROM visits WHERE date > '2016-12-31' ORDER BY date DESC ");
         $hosts_summ = 0;
         $views_summ = 0;
         $indx_summ = 0;
         $price_summ = 0;

        while ($row = mysqli_fetch_assoc($res)) {
            $hosts_summ += $row['hosts'];
            $views_summ += $row['views'];
            $indx_summ += $row['index_visited'];
            $price_summ += $row['price_visited'];
            $formatedDate = date_create( $row['date'] )->Format('d.m.Y');
            
		  echo '<tr>
			     <td>' . $formatedDate . '</td>
			     <td>' . $row['hosts'] . '</td>
                 <td>' . $row['index_visited'] . '</td>
                 <td>' . $row['price_visited'] . '</td>
                 <td>' . $row['views'] . '</td>
			    </tr>';
	         }
         ?>
            <tr class="warning">
			     <td>Всего:</td>
			     <td><?php echo $hosts_summ; ?></td>
                 <td><?php echo $indx_summ; ?></td>
                 <td><?php echo $price_summ; ?></td>
                 <td><?php echo $views_summ; ?></td>
			</tr>
            </tbody>
          </table>
          <hr />
        <p class="text-info"><strong>Просмотры по позициям:</strong></p>
        <table class="table table-hover">
            <thead>
               <tr>
                <th>Имя</th>
                <th>просмотры</th>
              </tr>
            </thead>
            <tbody>
              <?php

        $result_names = mysqli_query($connection, " SELECT id, short_name FROM stock ");
        $result_views = mysqli_query($db_stats, " SELECT pos_string FROM posviews 
                                                  WHERE date > '2016-12-31'
                                                  ");
        
        $pieces_week = array();                                                                            
         while( $row_views = mysqli_fetch_assoc($result_views) ) {
            $string_counter = $row_views['pos_string'];
            $arr_temp = explode(";", $string_counter);
            $pieces_week = array_merge($pieces_week, $arr_temp);
            
         }
        $wholeSumm = 0;
        while($row = mysqli_fetch_assoc($result_names))
        { ?> 
              <tr>
                <td><?php echo $row['short_name']; ?></td>
                <td><?php
                       $summ = 0;
                       for( $i = 0; $i < count($pieces_week); $i++ ) {
                         if ( $row['id'] == $pieces_week[$i] ) {
                           $summ++;
                         }
                       }
                       $wholeSumm += $summ;
                       echo $summ;
             ?></td>
             </tr>
             <?php }  ?>
              <tr class="warning">
                <td>Всех:</td>
                <td><?php echo $wholeSumm; ?></td>
              </tr>
            </tbody>
          </table>
          
         </div><!-- end of panel 3 -->
         
        </div><!-- end of Tab content -->
      </div>
     <a class="btn btn-default" type="button" href="index_adm.php">
     <span class="glyphicon glyphicon-triangle-left"></span> Назад</a>
<hr />

</div><!--row-->
</div><!--container-->

<?php
	include('bottomScripts_adm.php');
    mysqli_close($db_stats);
    mysqli_close($connection);
?>
</body>

</html>
<?php } ?>
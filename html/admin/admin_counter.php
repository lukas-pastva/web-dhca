<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "pocitadlo") ) {
  header("location: index.php");
  die;
}
/*********************************************************************************************/
?>
<h3>Počítadlo prístupov</h3>
<center>
  <?


  if (! $_GET['limit']) { 
    $limit = 25; 
  } else {
    $limit = $_GET['limit'];
  }
  define("COUNTER_POCET_ZOBRAZOVANYCH" ,25);
  $counter = psw_mysql_query('SELECT * FROM counter ORDER BY nr DESC');
  $pocet_zaznamov = psw_mysql_fetch_array( psw_mysql_query('SELECT count(*) AS x FROM counter') );
  $pocet_zaznamov = $pocet_zaznamov['x'];
  ?>

   <br>
    <div class="clanok_autor">
     Zoznam navstev stranky...asi nie priliz potrebna vec...
    </div>
   <br>
   <div class="admin_msg">
    <div class="clanok_autor">
     <center>
     
     <?
      for ($x = COUNTER_POCET_ZOBRAZOVANYCH; ($x-COUNTER_POCET_ZOBRAZOVANYCH) < $pocet_zaznamov; $x+=COUNTER_POCET_ZOBRAZOVANYCH){
        $nr+=1;
        if ( $limit != $x ){
          echo "\r\n\t<a href=\"index.php?file=admin_counter.php&amp;limit=".$x."\"><b>".$nr."</b></a>";
        } else{
          echo "\r\n\t[".$nr."]";
        }
      }
     ?>
     </center>
    </div>
   </div>
   <br>
   <?
   for ($i = 0; $i < $limit; $i++){
     if ($counter_array = psw_mysql_fetch_array($counter)){
       if ($i >= ($limit - COUNTER_POCET_ZOBRAZOVANYCH)){
       	?>
         <div class="admin_msg_left">
          <b><? echo $counter_array['nr']; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;cas: <b><? echo date('d.n.Y H:i:s', ($counter_array['time'])); ?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ip: <b><? echo $counter_array['ip']; ?></b>&nbsp;&nbsp;&nbsp;&nbsp;</b>
          <br>
          pripojenie: <b><? echo gethostbyaddr($counter_array['ip']); ?></b><br>
         </div>
         <br>
        <?
       }
     } 
   }
   
   ?>
   
</center>

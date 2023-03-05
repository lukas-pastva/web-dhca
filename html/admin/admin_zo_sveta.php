<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "zo_sveta") ) {
  header("location: index.php");
  die;
}
/*********************************************************************************************/
?>
<h3>Zo sveta</h3>
<center>
  <?
  
/*****************************************************************************************************************/
  //Ak sa ide editovat sprava
  if ($_GET['x'] == "1"){
  	$id    = strip_tags($_POST['id']   , '');
  	$href  = $_POST['href'];
  	$text  = $_POST['text'];
  	
  	if (! psw_mysql_query('UPDATE zo_sveta SET href="'.$href.'", text="'.$text.'" WHERE id = "'.$id.'" ') ){
  		alert(mysql_error());
  	}
  }
/*****************************************************************************************************************/
  
/*****************************************************************************************************************/
  //Ak sa ide mazat sprava
  if ($_GET['x'] == "2"){
  	$id = strip_tags($_POST['id'], '');
    psw_mysql_query('DELETE FROM zo_sveta WHERE id = "'.$id.'" ');
  }
/*****************************************************************************************************************/
    
/*****************************************************************************************************************/
  //Ak sa ide vkladat sprava
  if ($_GET['x'] == "3"){
  	$href  = $_POST['href'];
  	$text  = $_POST['text'];
  	$time  = time();

 	if ( $href && $text ){
	  psw_mysql_query('INSERT INTO zo_sveta ( time, href, text ) VALUES ('.$time.', "'.$href.'", "'.$text.'")');
 	}
  }
/*****************************************************************************************************************/
  
  
  if (! $_GET['limit']) { 
    $limit = ZO_SVETA_POCET_SPRAV_NA_STRANU;
  } else {
    $limit = $_GET['limit'];
  }

  $all_text=psw_mysql_query('SELECT * FROM zo_sveta ORDER BY id DESC');
  $pocet_zaznamov = psw_mysql_fetch_array( psw_mysql_query('SELECT count(*) AS x FROM zo_sveta') );
  $pocet_zaznamov = $pocet_zaznamov['x'];
  
  ?>
  
  <div class="clanok_autor">
    Adminovanie sprav zo sveta..mozes upravit spravu kliknutim na uprav spravu.<br>
    Pozor!!! Je potrebne zadavat kompletnu adresu daneho odkazu, cize aj http://....
  </div>
  <br>
  <hr>
   <div class="admin_msg_left">
    <form action="index.php?file=admin_zo_sveta.php&amp;x=3" method="post">
     Adresa: <input type="text" name="href" size="130"><br><br>
     Text:   <input type="text" name="text" size="130"><br><br>
      <input type="submit" value="vloz novinku zo sveta">
    </form>
   </div>
  <hr><br>
  <?
  //Vypis zoznamov stranok
  ?>
  
   <div class="admin_msg">
    <div class="clanok_autor">
     <center>
     <?
      $actual_limit;
      for ($x = ZO_SVETA_POCET_SPRAV_NA_STRANU; ($x-ZO_SVETA_POCET_SPRAV_NA_STRANU) < $pocet_zaznamov; $x+=ZO_SVETA_POCET_SPRAV_NA_STRANU){
       $nr+=1;
       if ( $limit != $x ){
         echo "\r\n\t<a href=\"index.php?file=admin_zo_sveta.php&amp;limit=".$x."\"><b>".$nr."</b></a>";
       } else{
         echo "\r\n\t[".$nr."]";
         $actual_limit = $limit;
       }
     }
     ?>
     
     </center>
    </div>
   </div>
   <br>
  <?
/*****************************************************************************************************************/
   for ($i = 0; $i < $limit; $i++){
     if ($sprava = psw_mysql_fetch_array($all_text)){
       if ($i >= ($limit - ZO_SVETA_POCET_SPRAV_NA_STRANU)){
         if ( $_GET['edit'] == $sprava['id'] ){
       	   ?>
           <a name="message"></a>
           <div class="admin_msg_left">
            <form action="index.php?file=admin_zo_sveta.php&amp;x=1" method="post">
             Adresa:
             <input type="text" name="href" size="130" value="<? echo $sprava['href']; ?>"><br><br>
             Text:
             <input type="text" name="text" size="130" value="<? echo $sprava['text']; ?>"><br><br>
             <input type="hidden" name="id" value="<? echo $sprava['id']; ?>">
             <input type="submit" value="Uprav spravu">
            </form>
            <br />
            <form action="index.php?file=admin_zo_sveta.php&amp;x=2" method="post">
             <input type="hidden" name="id" value="<? echo $sprava['id']; ?>">
             <input type="submit" value="Vymaz spravu">
            </form>
           </div>
           <br>
           <?
         } else {
           ?>
         
           <div class="admin_msg_left">
             <b><? echo date('j.n.Y G:i:s', ($sprava['time'])); ?></b><br><br>
             <b>Adresa:</b>      <? echo $sprava['href']; ?><br><br>
             <b>Text:</b>      <? echo $sprava['text']; ?><br><br>
             <input type="submit" value="uprav spravu" onclick="window.open('index.php?file=admin_zo_sveta.php&amp;limit=<? echo $actual_limit; ?>&edit=<? echo $sprava['id']; ?>#message', '_self');">
           </div>
           <br>
           <?
         }
       }
     }
   }
/*****************************************************************************************************************/
?>
</center>

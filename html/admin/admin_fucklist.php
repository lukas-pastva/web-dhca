<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "fucklist") ) {
  header("location: index.php");
  die;
}
/*********************************************************************************************/
?>

<h3>Fuck list</h3>
<center>
 <div class="clanok_autor">
   V tejto mozes casti cenzúrovať nadávky(fucks).
 </div>
 <br>
 <?
/***************************************************************************************************************/
 
/***************************************************************************************************************/
  //Ak sa ide vkladat fuck
  if($_GET['x'] == "1"){
  	$fuck    = $_POST['fuck'];
  	$censure = $_POST['censure'];
  	psw_mysql_query('INSERT INTO fucklist (fuck, censure) VALUES ("' .$fuck. '", "' .$censure. '" )');
  }
/***************************************************************************************************************/
  
/***************************************************************************************************************/
  //Ak sa ide mazat fuck
  if($_GET['x'] == "2"){
  	$fuck_id = $_POST['fuck_id'];
  	psw_mysql_query('DELETE FROM fucklist WHERE fuck_id = "' .$fuck_id. '" ');
  }
/***************************************************************************************************************/
  
/***************************************************************************************************************/
  //Ponukne sa moznost vlozit fuck
  ?>
  	
   <form action="index.php?file=admin_fucklist.php&amp;x=1" method="post">
  	<b>Nadavka: </b><input type="text" name="fuck">&nbsp;<b>cenzura: </b><input type="text" name="censure">
  	<input type="submit" value="vlož cenzúru">
   </form>
  	
  <?
/***************************************************************************************************************/
 
/***************************************************************************************************************/
  //vypisem vsetky fucks
  $allFucks = psw_mysql_query('SELECT * FROM fucklist ORDER BY fuck_id');
  echo '
  <br /><br />
  <table class="data_table" style="width: 400px;">
   <tr class="even">
    <td><b>Nadávka</b></td>
    <td><b>Cenzúra</b></td>
    <td>&nbsp;</td>
   </tr>';
  while($fuck = psw_mysql_fetch_array($allFucks)){
  	?>
  	
  	  <form action="index.php?file=admin_fucklist.php&amp;x=2" method="post">
  	   <input type="hidden" name="fuck_id" value="<? echo $fuck['fuck_id']; ?>">
  	    <tr>
  	     <td><? echo $fuck['fuck']; ?></td>
  	     <td><? echo $fuck['censure']; ?></td>
  	     <td><input type="submit" value="Zmaž"></td>
  	    </tr>  	     
  	  </form>
  	
  	<?
  }
  echo '</table>';
/***************************************************************************************************************/
 ?>
</center>
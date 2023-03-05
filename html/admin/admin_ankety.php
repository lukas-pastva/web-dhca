<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "ankety") ) {
  header("location: index.php");
  die;
}
/*********************************************************************************************/
?>

<h3>Ankety</h3>
<center>

<?
  	
/*****************************************************************************************************************/  	
  	
  	//Ak sa odosielal hlas
  	if ( $_GET['x'] == "1" ){
  	  $id_ankety = strip_tags( $_POST['id_ankety'] );
  	  $hlas      = strip_tags( $_POST['hlas'] );

  	  psw_mysql_query('INSERT INTO ankety_data (id_ankety, hodnota) VALUES ("'.$id_ankety.'", "'.$hlas.'")');
  	}
  	
/*****************************************************************************************************************/ 

/*****************************************************************************************************************/

  	//Ak sa mazalo
  	if($_GET['x'] == "2"){
  	  $id_ankety = strip_tags($_POST['id_ankety']);
  	  psw_mysql_query('DELETE FROM ankety WHERE id_ankety = "'.$id_ankety.'"');
  	  psw_mysql_query('DELETE FROM ankety_data WHERE id_ankety = "'.$id_ankety.'"');
  	}

/*****************************************************************************************************************/

/*****************************************************************************************************************/

  	//Ak sa menil nazov stlpca
  	if($_GET['x'] == "3"){
  	  $id_ankety   = strip_tags($_POST['id_ankety']);
  	  $hlas        = strip_tags($_POST['hlas']);
  	  $meno_stlpca = strip_tags($_POST['meno_stlpca']);
  	  psw_mysql_query('UPDATE ankety SET meno_hodnoty_'.$hlas.' = "'.$meno_stlpca.'" WHERE id_ankety = "'.$id_ankety.'" ');
  	}

/*****************************************************************************************************************/
 
/*****************************************************************************************************************/

  	//Ak sa menil nazov ankety
  	if($_GET['x'] == "4"){
  	  $id_ankety = strip_tags($_POST['id_ankety']);
  	  $meno_ankety_new = strip_tags($_POST['meno_ankety_new']);

  	  psw_mysql_query('UPDATE ankety SET meno_ankety = "'.$meno_ankety_new.'" WHERE id_ankety = "'.$id_ankety.'" ');  	 
  	}
  	
/*****************************************************************************************************************/
  	//Ak sa Vytvarala nova anketa
  	if($_GET['x'] == "5"){
  	  $meno_ankety    = strip_tags($_POST['meno_ankety']);
      $meno_hodnoty_0 = strip_tags($_POST['meno_hodnoty_0']);
      $meno_hodnoty_1 = strip_tags($_POST['meno_hodnoty_1']);
      $meno_hodnoty_2 = strip_tags($_POST['meno_hodnoty_2']);
      $meno_hodnoty_3 = strip_tags($_POST['meno_hodnoty_3']);
      $meno_hodnoty_4 = strip_tags($_POST['meno_hodnoty_4']);
      $meno_hodnoty_5 = strip_tags($_POST['meno_hodnoty_5']);
      $meno_hodnoty_6 = strip_tags($_POST['meno_hodnoty_6']);
      $meno_hodnoty_7 = strip_tags($_POST['meno_hodnoty_7']);
      $meno_hodnoty_8 = strip_tags($_POST['meno_hodnoty_8']);
      $meno_hodnoty_9 = strip_tags($_POST['meno_hodnoty_9']);

      if ( ( $meno_ankety != "") && $meno_ankety ){
        $dotaz = psw_mysql_query('INSERT INTO ankety (meno_ankety, meno_hodnoty_0, meno_hodnoty_1, meno_hodnoty_2, meno_hodnoty_3, meno_hodnoty_4, meno_hodnoty_5, meno_hodnoty_6, meno_hodnoty_7, meno_hodnoty_8, meno_hodnoty_9) VALUES ("'.$meno_ankety.'","'.$meno_hodnoty_0.'","'.$meno_hodnoty_1.'","'.$meno_hodnoty_2.'","'.$meno_hodnoty_3.'","'.$meno_hodnoty_4.'","'.$meno_hodnoty_5.'","'.$meno_hodnoty_6.'","'.$meno_hodnoty_7.'","'.$meno_hodnoty_8.'","'.$meno_hodnoty_9.'")');
      } else {
      	alert("Nezadal si meno ankety");
      }
  	}
/*****************************************************************************************************************/
   ?>

    <div class="clanok_autor">
     Tu mas moznost vytvorit novu anketu, musis akurat zadat jej nazov a niektore z moznosti(podla potreby)
    </div>
    <br>
   
   <div class="admin_msg">
    <br>
    <form action="index.php?file=admin_ankety.php&amp;x=5" method="post">
     <b>Meno Ankety:</b>
     <input type="text" name="meno_ankety" size="50"><br><br>
     Volba 01:
     <input tyle="text" name="meno_hodnoty_0" size="40"><br>
     Volba 02:
     <input tyle="text" name="meno_hodnoty_1" size="40"><br>
     Volba 03:
     <input tyle="text" name="meno_hodnoty_2" size="40"><br>
     Volba 04:
     <input tyle="text" name="meno_hodnoty_3" size="40"><br>
     Volba 05:
     <input tyle="text" name="meno_hodnoty_4" size="40"><br>
     Volba 06:
     <input tyle="text" name="meno_hodnoty_5" size="40"><br>
     Volba 07:
     <input tyle="text" name="meno_hodnoty_6" size="40"><br>
     Volba 08:
     <input tyle="text" name="meno_hodnoty_7" size="40"><br>
     Volba 09:
     <input tyle="text" name="meno_hodnoty_8" size="40"><br>
     Volba 10:
     <input tyle="text" name="meno_hodnoty_9" size="40"><br><br>
     <input type="submit" value="Vytvor anketu">
    </form>
   </div>
   <?
/*****************************************************************************************************************/
   //Ak sa meni viditelnost ankety
   if($_GET['x'] == "6"){
   	$visible = $_POST['visible'];
   	$id_ankety = $_POST['id_ankety'];
   	if (!psw_mysql_query('UPDATE ankety SET visible = "' .$visible. '" WHERE id_ankety = "' .$id_ankety. '" ')){
   		alert(mysql_error());
   	}
   }
   
/*****************************************************************************************************************/
   ?>
   
   <br>
   <div class="clanok_autor">
    Tu su vypisane vsetky ankety, mas moznost mazat ich, menit ich nazov, mena hlasovacich moznosti a takisto hlasovat bez obmedzenia.<br>
   </div>
   
   <?
   
     //Este raz
     $query1 = psw_mysql_query('SELECT * FROM ankety ORDER BY id_ankety DESC');
     while ( $fetch1 = psw_mysql_fetch_array($query1) ){
     	
       $pocet_hlasujucich = psw_mysql_fetch_array( psw_mysql_query('SELECT count(*) AS pocet_hlasujucich FROM ankety_data WHERE id_ankety = "'. $fetch1['id_ankety'] .'" ') );
       $pocet_hlasujucich = $pocet_hlasujucich['pocet_hlasujucich'];
     	?>
     	
     	<br>
      	<table border="1" bordercolor="black">
      	 <tr>
      	  <td width="100">
      	  </td>
      	  <td width="230">
      	  </td>
      	  <td width="170">
      	  </td>
      	 </tr>
      	 <tr align="center">
      	  <td colspan="3">
      	   <form action="index.php?file=admin_ankety.php&amp;x=4" method="post">
            <input type="text" name="meno_ankety_new" size="50" value="<? echo $fetch1['meno_ankety']; ?>" />
            <input type="hidden" name="id_ankety" value="<? echo $fetch1['id_ankety']; ?>" />
            <input type="submit" value="premenuj anketu" />
           </form>
           
           <form action="index.php?file=admin_ankety.php&amp;x=2" method="post">
      	    <input type="hidden" name="id_ankety" value="<? echo $fetch1['id_ankety']; ?>" />
      	    <input type="submit" value="vymaž aknetu" onClick="if(!confirm('Ste si istý, že chcete zmazať anketu?')){return false;}"  />
      	   </form>
      	   
      	   <form onchange="submit()" action="index.php?file=admin_ankety.php&amp;x=6" method="post">
      	    <input type="hidden" name="id_ankety" value="<? echo $fetch1['id_ankety']; ?>">
      	    <select name="visible">
      	     <option value="1" <? if($fetch1['visible'] == "1"){echo "selected";} ?>>Zobrazit  </option>
      	     <option value="0" <? if($fetch1['visible'] == "0"){echo "selected";} ?>>Nezobrazit</option>
      	    </select>
      	   </form>
     	  </td>
     	 </tr>
     	 <?
     	 
     	 for ($i = 0; $i < 10; $i++){
     	 	if ( ($fetch1['meno_hodnoty_'.$i] != "") && ($fetch1['meno_hodnoty_'.$i]) ){
     	 	  $pocet_hlasov = psw_mysql_fetch_array( psw_mysql_query('SELECT count(*) AS pocet_hlasov FROM ankety_data WHERE id_ankety = "' .$fetch1['id_ankety']. '" AND hodnota = "' .$i. '" ') );
              $pocet_hlasov = $pocet_hlasov['pocet_hlasov'];
     	 	  ?>
       	      	
       	       <tr>
     	       
       	         <td align="center">
       	          <form action="index.php?file=admin_ankety.php&amp;x=1" method="post">
       	          <input type="hidden" name="id_ankety" value="<? echo $fetch1['id_ankety']; ?>" />
       	          <input type="hidden" name="hlas" value="<? echo $i; ?>" />
       	          <input type="submit" value="zahlasuj" />
       	          </form>
                  </td>
                 
                 
                  <td align="center">
                  <form action="index.php?file=admin_ankety.php&amp;x=3" method="post">
       	           <input type="text" name="meno_stlpca" size="20" value="<? echo $fetch1['meno_hodnoty_'.$i]; ?>" />
       	           <input type="hidden" name="id_ankety" value="<? echo $fetch1['id_ankety']; ?>" />
       	           <input type="hidden" name="hlas" value="<? echo $i; ?>" />
       	           <input type="submit" value="premenuj" />
       	           </form>
       	          </td>
       	         
       	         <td align="left">
       	         <? echo "hlasy:".$pocet_hlasov; 
       	           if ($pocet_hlasujucich > 0){
       	            $percenta = ceil(($pocet_hlasov / $pocet_hlasujucich)*100);
       	            if ($percenta > 0){
       	      	      $sirka_palicky = $percenta;
   	      	          echo "<img src=\"../pics/anketa.png\" width=\"". $sirka_palicky ."\" height=\"15\"> ".$percenta." % ";
       	            }
       	            ?>
       	            
       	         </td>
       	        </tr>
       	            <?
       	           }
     	 	}
     	 }
     	 ?>
     	 
      	 <tr>
      	  <td colspan="3" align="center">
      	   <? echo "<b>Pocet hlaujucich:</b> ".$pocet_hlasujucich; ?>
      	   
      	  </td>
      	 </tr>
      	</table><br><br><br>
     	<?
     }
  	?>
  	
</center>
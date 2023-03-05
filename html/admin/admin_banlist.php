<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "banlist") ) {
  header("location: index.php");
  die;
}
/*********************************************************************************************/
?>
<h3>Ban List</h3>
<center>
    <div class="clanok_autor">
     V tejto casti mas moznost udelit ban neakej ip adrese/uzivatelovi. Da sa vyberat z typu banov. 
     <br>A sice forum - uzivatel nebude moct pridavat spravy do fora a total ban - uzivatelovi sa ani stranka nenacita.
     Odporucam ku kazdemu banu pisat odovodnenie, zobrazi sa danemu uzivatlovi ako dovod banu.
    </div>
    <br>
    
 <?
/*****************************************************************************************************************/
 
  //hmmm
  
  //Ak sa ide vytvarat ban
  if ($_GET['x'] == "1"){
  	$ip       = $_POST['ip'];
  	$ban_type = $_POST['ban_type'];
  	$reason   = $_POST['reason'];

    if ( $ban_type && $ip ){
      psw_mysql_query('INSERT INTO banlist (`ip`, `ban_type`, `reason`) VALUES ("' .$ip. '", "' .$ban_type. '", "' .$reason. '") ');
    }
  }
  
  //Ak sa ide mazat ban
  if ($_GET['x'] == "2"){
  	$ban_id = $_POST['ban_id'];
    psw_mysql_query('DELETE FROM banlist WHERE ban_id = "' .$ban_id. '" ');
  }
 
/*****************************************************************************************************************/
  ?> 
  
  <form action="index.php?file=admin_banlist.php&amp;x=1" method="post">
   <table border="1" bordercolor="black">
    <tr>
     <td width="110">
     </td>
     <td width="110">
     </td>
     <td width="220">
     </td>
    </tr>
    <tr>
     <td colspan="3" align="center">
      <b>Pridaj uzivatela do banlistu</b>
     </td>
    </tr>
    <tr>
     <td align="center">
      <b>Ip adresa</b>
     </td>
     <td align="center">
      <b>Typ banu</b>
     </td>
     <td align="center">
      <b>Dovod banu</b>
     </td>
    </tr>
    <tr>
     <td align="center">
      <input type="text" name="ip" size="15">
     </td>
     <td align="center">
      <select size="1" name="ban_type">
       <option value="0"></option>
       <option value="1">Forum</option>
       <option value="2">Cela stranka</option>
      </select>
     </td>
     <td align="center">
      <input type="text" name="reason" size="35">
     </td>
    </tr>
    <tr>
     <td align="center" colspan="3">
      <input type="submit" value="Vytvor ban">
     </td>
    </tr>
   </table>
  </form>
   
   <br><br><br>
   
  
   <table border="1" bordercolor="black">
    <tr>
     <td width="110">
     </td>
     <td width="110">
     </td>
     <td width="220">
     </td>
     <td width="100">
     </td>
    </tr>
    <tr>
     <td colspan="4" align="center">
      <b>Zoznam banov</b>
     </td>
    </tr>
    <tr>
     <td align="center">
      <b>Ip adresa</b>
     </td>
     <td align="center">
      <b>Typ banu</b>
     </td>
     <td align="center">
      <b>Dovod banu</b>
     </td>
     <td align="center">
      <b>Zmaz ban</b>
     </td>
    </tr>
    <?
    
     $query = psw_mysql_query('SELECT * FROM banlist ORDER BY ban_id DESC');
     while ( $fetch = psw_mysql_fetch_array($query) ){
       ?>
       
        <form action="index.php?file=admin_banlist.php&amp;x=2" method="post">
         <tr>
          <td align="center">
           <? echo $fetch['ip']."\n"; ?>
          </td>
          <td align="center">
           <?
             if ($fetch['ban_type'] == 1){
               echo "Forum";
             }
             else if ($fetch['ban_type'] == 2){
               echo "Cela stranka";
             }
             else {
               echo "Ziadny";
             }
           ?>
           
          </td>
          <td align="center">
           <? echo $fetch['reason']."\n"; ?>
          </td>
          <td align="center" colspan="3">
           <input type="hidden" value="<? echo $fetch['ban_id']; ?>" name="ban_id">
           <input type="submit" value="Zmaz">
          </td>
         </tr>
        </form>
       <?
     }
    ?>

   </table>
  <?
/*****************************************************************************************************************/
 ?>
  	
</center>
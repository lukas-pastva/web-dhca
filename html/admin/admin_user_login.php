<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "userlogin") ) {
  header("location: index.php");
  die;
}
/*********************************************************************************************/
  ?>
 <table align="left">
  <tr>
   <td align="left" width="120">
    <b>nick:</b>
   </td>
   <td align="left" width="220">
    <b>time:</b>
   </td>
   <td align="left" width="120">
    <b>ip:</b>
   </td>
  </tr>
  <?
/*****************************************************************************************************************/
  $all_user_logins = psw_mysql_query('SELECT L.time, L.ip, U.nick FROM user_login L, user U WHERE L.user_id = U.id ORDER BY L.time DESC');
  while($user_login = psw_mysql_fetch_array($all_user_logins)){
  	?>
  	 <tr>
  	  <td><?=$user_login['nick'];?></td>
  	  <td><?=date('d.m.Y / G:i:s', $user_login['time']);?></td>
  	  <td><?=$user_login['ip'];?></td>
  	 </tr>
  	<?
  }
  ?>
 </table>

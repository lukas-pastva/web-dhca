<?
	session_start();
	ob_start();
	include_once('admin_functions.php');
?>
<!doctype html public "-//w3c//dtd html 4.01 transitional//en">
<html>
 <head>
  <link rel="stylesheet" type="text/css" href="admin_style.css">
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>A.D.M.I.N. [<? echo SITENAME; ?>]</title>
 </head>
 <body>
  <div class="sajtah">
   <div class="top"></div>
   <div style="position: absolute; top: 170px; left: 300px; color: white; cursor: pointer; width: 140px;" onclick="window.open('../index.php', '_self');"><b>Verejne rozhranie</b></div>
   <div class="site">
    <center>
<?php



  $nick = $_POST["nick"];
  $nick = strip_tags($nick,'');
  $pass = $_POST["pass"];
  $pass = strip_tags($pass,'');


  if (! login($nick, $pass) ){
 	?>
 	
 	 <form action="admin_login.php" method="post"> 
      Najskor sa musis prihlasit.
      <h2>Login</h2>
        meno:<br><input type="text" name="nick" ><br>
        heslo:<br><input type="password" name="pass"><br><br><br>
        <input type="submit" value="Prihlasit sa">
     </form>
     
 	<?
  } else {
    header("location: index.php");
  }
?>
     </center>
    </div>
  </div>
 </body>
</html>
<?ob_end_flush();?>
<?php
/*********************************************************************************************/
if (! userGetAccess($_SESSION['meno_uzivatela'], "mailinglist") ) {
	header("location: index.php");
	die;
}
/*********************************************************************************************/
?>
<h3>Mailing List</h3>
<center><?

/*****************************************************************************************************************/

//Ak sa vkladal mail
if ( $_GET['x'] == "1" ){
	$nick = $_REQUEST['nick'];
	$mail = $_REQUEST['mail'];

	if ($mail && $nick){
		if (! psw_mysql_fetch_array( psw_mysql_query('SELECT * FROM mailing_list WHERE nick="' .$nick. '" AND mail="' .$mail. '" LIMIT 1' ) ) ){
			psw_mysql_query('INSERT INTO mailing_list (nick, mail) VALUES ("'.$nick.'", "'.$mail.'")');
		} else {
			alert("Takýto mail už existuje!");
		}
	}

}

/*****************************************************************************************************************/

/*****************************************************************************************************************/

//Ak sa mazal mail
if ( $_GET['x'] == "2" ){
	$nick = $_REQUEST['nick'];
	$mail = $_REQUEST['mail'];

	psw_mysql_query('DELETE FROM mailing_list WHERE nick="' .$nick. '" AND mail="' .$mail. '" ');
}

/*****************************************************************************************************************/

/*****************************************************************************************************************/

//Ak sa upravoval mail
if ( $_GET['x'] == "3" ){
	$old_nick = $_REQUEST['old_nick'];
	$old_mail = $_REQUEST['old_mail'];
	$new_nick = $_REQUEST['new_nick'];
	$new_mail = $_REQUEST['new_mail'];

	psw_mysql_query('UPDATE mailing_list SET nick = "' .$new_nick. '", mail ="'.$new_mail.'" WHERE nick = "' .$old_nick. '" AND mail = "' .$old_mail. '" ');


}

/*****************************************************************************************************************/

?> 
<div class="clanok_autor">Tu sa vkladaju, mazu, resp menia maily, ktore
sa zobrazuju v kontakte.</div>
<br>

<div class="admin_msg"><br>
<form action="index.php?file=admin_mailinglist.php&x=1" method="post">
Nick: <input type="text" name="nick">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Mail:<b></b> <input type="text" name="mail"> <input
	type="submit" value="Vloz mail"></form>
</div>
<br>

<?

$all_mails = psw_mysql_query('SELECT * FROM mailing_list ORDER BY nick ASC');
while ($one_mail = psw_mysql_fetch_array($all_mails)){
	if( ($_REQUEST['x'] == "4") && ($_REQUEST['nick'] == $one_mail['nick']) && ($_REQUEST['mail'] == $one_mail['mail']) ){
		?>
<div class="admin_msg">
<form action="index.php?file=admin_mailinglist.php&x=3" method="post"><input
	type="hidden" name="old_nick" value="<?=$one_mail['nick']?>"> <input
	type="hidden" name="old_mail" value="<?=$one_mail['mail']?>"> Nick:<input
	type="text" name="new_nick" value="<?=$one_mail['nick']?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Mail:<input type="text" name="new_mail" value="<?=$one_mail['mail']?>">&nbsp;&nbsp;&nbsp;
<input type="submit" value="zmen mail"></form>
<form action="index.php?file=admin_mailinglist.php&x=2" method="post"><input
	type="hidden" name="nick" value="<?=$one_mail['nick']?>"> <input
	type="hidden" name="mail" value="<?=$one_mail['mail']?>"> <input
	type="submit" value="zmaz mail"></form>
</div>
<br>
		<?
	} else {
		?>
<div class="admin_msg">
<form action="index.php?file=admin_mailinglist.php&x=4" method="post">
Nick: <b><?=$one_mail['nick']?></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Mail:
<b><?=$one_mail['mail']?></b>&nbsp;&nbsp;&nbsp; <input type="hidden"
	name="nick" value="<?=$one_mail['nick']?>"> <input type="hidden"
	name="mail" value="<?=$one_mail['mail']?>"> <input type="submit"
	value="edituj"></form>
</div>
<br>
		<?
	}

}

?></center>

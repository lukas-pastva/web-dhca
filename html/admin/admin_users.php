<?php
/*********************************************************************************************/
if (! $_SESSION['meno_uzivatela'] ) {
	header("location: index.php");
	die;
}
/*********************************************************************************************/
?>
<h3>Správa užívateľa</h3>
<center><?
/***************************************************************************************************************/

//Ak sa ide menit heslo
if ( $_GET['x'] == "1" ){
	$nick     = strip_tags($_POST['nick'], '');
	$pass_old = md5( strtolower( strip_tags($_POST['pass_old'], '') ) );
	$pass_1   = strtolower( strip_tags($_POST['pass_1'], '') );
	$pass_2   = strtolower( strip_tags($_POST['pass_2'], '') );

	if ($pass_1 != $pass_2){
		alert("Nove heslo musi byt vyplnene v oboch kolonkach rovnako!");
	} else {
		if ( ! $pass_1 ){
			alert("Nezadal si nove heslo!!!");
		} else{
			$pass_new = md5($pass_1);
			$pass = psw_mysql_fetch_array(psw_mysql_query('SELECT pass FROM user WHERE nick = "'. $nick .'" '));
			$pass = $pass['pass'];
			if ( $pass != $pass_old ){
				alert("Nezadal si spravne stare heslo!!!");
			} else {
				if ( psw_mysql_query('UPDATE user SET pass = "' . $pass_new . '" WHERE nick = "' .$nick. '" ') ){
					alert("Heslo uspesne zmenene!");
				}
			}
		}
	}
}
/***************************************************************************************************************/

/***************************************************************************************************************/

//Ak sa ide vytvarat uzivatel
if ( $_GET['x'] == "2" ){
	$nick = strtolower( strip_tags($_POST['nick'], '') );
	$pass_1 = strtolower( strip_tags($_POST['pass_1'], '') );
	$pass_2 = strtolower( strip_tags($_POST['pass_2'], '') );

	if ( ! $nick ) {
		alert("Nezadal si meno uzivatela!");
	} else {
		if ( $pass_1 != $pass_2 ){
			alert("Obe zadane hesla musia byt rovnake!!!");
		} else {
			if ( ! $pass_1 ){
				alert("Nezadal si heslo!");
			} else {
				$pass = md5($pass_1);
				if( psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM user WHERE nick = "'. $nick .'" ')) ){
					alert("Uzivatel s menom '". $nick ."' uz existuje! ");
				} else{
					//Samotne vytvaranie uzivatela

					$fields = mysql_list_fields(SQL_DBNAME, "user");
					$atributy;
					$values;
					for( $i=3; $i<mysql_num_fields($fields); $i++ ){
							
						$atributy = $atributy.'`'.mysql_field_name($fields, $i).'`';
						if (! (($i+1)==mysql_num_fields($fields)) ){
							$atributy = $atributy.', ';
						}

						if( $_REQUEST[mysql_field_name($fields, $i)] == "on" ){
							$values = $values.' "1" ';
						} else {
							$values = $values.' "0" ';
						}
						if (! (($i+1)==mysql_num_fields($fields)) ){
							$values = $values.', ';
						}

					}

					if (psw_mysql_query('INSERT INTO user (nick, pass, '. $atributy .') VALUES ("'.$nick.'", "'.md5($pass_1).'", '. $values .') ')){
						alert("Uzivatel vytvoreny.");
					} else {
						alert(mysql_error());
					}
				}
			}
		}
	}
}
/***************************************************************************************************************/

/***************************************************************************************************************/
//Ak sa ide mazat uzivatel
if ( $_GET['x'] == "3" ){
	$id = $_POST['id'];

	if ( (psw_mysql_query('DELETE FROM user WHERE id = "'. $id .'"') ) && ( psw_mysql_query('DELETE FROM user_login WHERE user_id = "'. $id .'" ') ) ){
		alert("Uzivatel vymazany.");
	}
}
/***************************************************************************************************************/

/***************************************************************************************************************/
//Ak sa menili prava uzivatela
if ( $_GET['x'] == "5" ){
	$id = $_POST['id'];


	//Teraz si zistim vsetky polozky ktore maju prava a zmenim tak pomocou SQL prikazu prava...:(((
	$fields = mysql_list_fields(SQL_DBNAME, "user");
	$SQLdotaz;
	for( $i=3; $i<mysql_num_fields($fields); $i++ ){
		$SQLdotaz = $SQLdotaz.'`'.mysql_field_name($fields, $i).'`';

		if( $_REQUEST[mysql_field_name($fields, $i)] == "on" ){
			$SQLdotaz = $SQLdotaz.' = "1" ';
		} else {
			$SQLdotaz = $SQLdotaz.' = "0" ';
		}

		if (! (($i+1)==mysql_num_fields($fields)) ){
			$SQLdotaz = $SQLdotaz.', ';
		}
	}

	if (psw_mysql_query($sql = 'UPDATE user SET '. $SQLdotaz  .' WHERE id = "' .$id. '" ')){
		alert("Prava zmenene.");
	} else {
		alert(mysql_error());
	}
}
/***************************************************************************************************************/

?>

<div class="clanok_autor">V tejto casti mas moznost vytvarat novych
uzivatelov(ak mas prava), a to tak, ze najprv im urci prava a potom daj
vytvorit uzivatela. <br>
Mozes si tu zmenit svoje heslo, len treba zadat stare heslo. Odporucam
si hesla pametat, lebo su kryptovane a nedaju sa ziskat spat.</div>

<br>
<br>


<table border="1" bordercolor="black"
	style="border-width: 2px; border-color: black; border-style: solid; background-color: grey;">
	<tr>
		<td colspan="4" align="center"><b>Zmen si heslo</b></td>
	</tr>
	<tr>
		<td align="center"><b>meno</b></td>
		<td align="center"><b>Stare heslo</b></td>
		<td align="center"><b>nove heslo</b></td>
		<td align="center"><b>nove heslo znova</b></td>
	</tr>
	<form action="index.php?file=admin_users.php&amp;x=1" method="post">
	
	
	<tr>
		<td align="center"><input type="hidden" name="nick"
			value="<? echo $_SESSION['meno_uzivatela']; ?>"> <? echo $_SESSION['meno_uzivatela']; ?>
		</td>
		<td align="center"><input type="password" name="pass_old"></td>
		<td align="center"><input type="password" name="pass_1" /></td>
		<td align="right"><input type="password" name="pass_2" /> <input
			type="submit" value="zmen si heslo" /></td>
	</tr>
	</form>
</table>




<?
/**********************************************************************************************/
/**********************************************************************************************/
if ( userGetAccess($_SESSION['meno_uzivatela'], "uzivatelia" )){
	?> <br>
<br>
<h3>Administračné rozhranie pre správu užívateľov</h3>

<table border="1" bordercolor="black"
	style="border-width: 2px; border-color: black; border-style: solid; background-color: grey;">
	<tr>
	<?
	//V tejto casti sa vypisu mena stlpcov z user okrem nick a pass :(((
	$fields = mysql_list_fields(SQL_DBNAME, "user");
	for( $i=0; $i<mysql_num_fields($fields); $i++ ){
		if( (mysql_field_name($fields, $i) != "id") && (mysql_field_name($fields, $i) != "pass") ){
			?>
		<td align="center">&nbsp;<b><? echo mysql_field_name($fields, $i); ?></b>&nbsp;
		</td>
		<?
		}
	}
	?>
		<td colspan="2"></td>
	</tr>
	<?
	$all_users = psw_mysql_query('SELECT * FROM user ORDER BY nick ASC');
	while($one_user = psw_mysql_fetch_array($all_users)){
		?>
	<tr>
		<form action="index.php?file=admin_users.php&amp;x=5" method="post"><input
			type="hidden" name="id" value="<? echo $one_user['id']; ?>"> <?
			for( $i=0; $i<mysql_num_fields($fields); $i++ ){
				if( (mysql_field_name($fields, $i) != "id") && (mysql_field_name($fields, $i) != "pass") ){
					if(mysql_field_name($fields, $i) != "nick"){
						if($one_user[mysql_field_name($fields, $i)] == "0"){
							?>
		
		
		<td align="center"><input type="checkbox"
			name="<? echo mysql_field_name($fields, $i); ?>"></td>
			<?
						} else {
							?>
		<td align="center"><input type="checkbox"
			name="<? echo mysql_field_name($fields, $i); ?>" checked></td>
			<?
						}
					} else {
						?>
		<td align="center">&nbsp;<i><? echo $one_user[mysql_field_name($fields, $i)]; ?></i>&nbsp;
		</td>
		<?
					}
				}
			}
			?>
		<td><input type="submit" value="zmen prava"></td>
		</form>
		<? if ($_SESSION['meno_uzivatela'] != $one_user['nick']){?>
		<form action="index.php?file=admin_users.php&amp;x=3" method="post">
		
		
		<td><input type="hidden" name="id" value="<? echo $one_user['id']; ?>">
		<input type="submit" value="vymaz uzivatela" onClick="if(!confirm('Ste si istý, že chcete zmazať užívateľa?')){return false;}"  /></td>
		</form>
		<?}?>
	</tr>
	<?
	}
	?>
</table>

<br>
<br>
<b>Vytvor noveho uzivatela</b>
<form action="index.php?file=admin_users.php&amp;x=2" method="post">
<table border="1" bordercolor="black"
	style="border-width: 2px; border-color: black; border-style: solid; background-color: grey;">
	<tr>
		<td align="center"><b>meno</b></td>
		<td align="center"><b>heslo</b></td>
		<td align="center"><b>heslo znova</b></td>
		<?
		//V tejto casti sa vypisu mena stlpcov z user okrem nick a pass :(((
		$fields = mysql_list_fields(SQL_DBNAME, "user");
		for( $i=0; $i<mysql_num_fields($fields); $i++ ){
			if( (mysql_field_name($fields, $i) != "id") && (mysql_field_name($fields, $i) != "pass") && (mysql_field_name($fields, $i) != "nick") ){
				?>
		<td align="center">&nbsp;<b><? echo mysql_field_name($fields, $i); ?></b>&nbsp;
		</td>
		<?
			}
		}
		?>
		<td></td>
	</tr>
	<tr>
		<td align="center"><input type="text" name="nick" value="zadaj meno">
		</td>
		<td align="center"><input type="password" name="pass_1"></td>
		<td align="right"><input type="password" name="pass_2"></td>
		<?
		for( $i=0; $i<mysql_num_fields($fields); $i++ ){
			if( (mysql_field_name($fields, $i) != "id") && (mysql_field_name($fields, $i) != "pass") && (mysql_field_name($fields, $i) != "nick") ){
				?>
		<td align="center"><input type="checkbox"
			name="<? echo mysql_field_name($fields, $i); ?>"></td>
			<?
			}
		}
		?>
		<td><input type="submit" value="vytvor uzivatela"></td>
	</tr>
</table>
</form>
		<?
}
?></center>

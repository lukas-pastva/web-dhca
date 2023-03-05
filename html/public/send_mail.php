<?php
if ($_REQUEST['x'] == "1"){

	$adresa  = strip_tags($_REQUEST['adresa' ]);
	$predmet = strip_tags($_REQUEST['predmet']);
	$from    = strip_tags($_REQUEST['mailer' ]);
	$text    = strip_tags($_REQUEST['text'   ]);

	if ($adresa && $predmet && $text && $from ){
		$text = "OD: ".$from." text spravy: ".$text;
			
		if (mail($adresa, $predmet, $text)){
			alert("Mail úspešne odoslaný.");
		} else {
			alert("Error :(");
		}
	}
	else if ( ! $adresa ){
		alert("Nezadal si adresu!");
	}
	else if ( ! $predmet ){
		alert("Nezadal si predmet!");
	}
	else if ( ! $from ){
		alert("Nezadal si svoje meno!");
	}
	else if ( ! $text ){
		alert("Nezadal si text mailu!");
	}
}



echo '
<br />
<br />
<br />
<div style="text-align: center;">
<form action="'.$path.'sekcia/kontakt" method="post">
<table align="center"	style="width: 100%; font-family: Verdana; font-size: 11px;">
	<tr>
		<td colspan="2" align="center"><b>Pošli E-mail niektorému členovi Sewer.sk crew</b></td>
	</tr>
	<tr>
		<td align="right" width="35%">Adresa:</td>
		<td><select style="font-size: 9px; width: 130px;" name="adresa"	size="1">
			<option value=""></option>';

			$query = psw_mysql_query('SELECT * FROM mailing_list ORDER BY id ASC');
			while ($fetch = psw_mysql_fetch_array($query) ){
				echo "\r\n\t<option value=\"" .$fetch['mail']. "\">" .$fetch['nick']."</option>";
			}

			echo '
		</select></td>
	</tr>
	<tr>
		<td align="right">Predmet:</td>
		<td><input style="font-size: 9px; width: 130px;" type="text"
			name="predmet" value="'.$_REQUEST['predmet'].'"></td>
	</tr>
	<tr>
		<td align="right">Od koho:</td>
		<td><input style="font-size: 9px; width: 130px;" type="text"
			name="mailer" value="'.$_REQUEST['mailer'].'"></td>
	</tr>
	<tr>
		<td align="center" colspan="2">Text:<br />
		<textarea style="font-family: Verdana; font-size: 11px;" cols="60"
			rows="6" name="text">'.$_REQUEST['text'].'</textarea></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
		 <input type="hidden" name="x" value="1">
		 <input style="font-size: 9px;"
			type="submit" value="Odošli mail"></td>
	</tr>
</table>
</form>
</div>';
			
			
?>

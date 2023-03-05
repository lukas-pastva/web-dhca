<?
$path;

if ($_REQUEST['id_ankety']) {
    include_once ("../admin/admin_functions.php");
    $path = getPath();
}

$uzHlasoval = false;
/**
 * **************************************************************************************************************
 */
// Ak sa odosielal hlas
if ($_REQUEST['id_ankety']) {

    $id_ankety = strip_tags($_GET['id_ankety']);
    $hlas = strip_tags($_GET['hlas']);
    $ip = $_SERVER["REMOTE_ADDR"];

    // Ak je hlas v rozsahu 0 az 9
    if (($hlas >= 0) && ($hlas < 10)) {
        // Ak dana anketa existuje
        if (psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM ankety WHERE id_ankety = "' . $id_ankety . '" '))) {
            // Ak este dany uzivatel nehlasoval do danej ankety
            if (! psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM ankety_data WHERE ip = "' . $ip . '" AND id_ankety = "' . $id_ankety . '" '))) {
                // A konecne sa zahlasuje
                psw_mysql_query('INSERT INTO ankety_data (id_ankety, hodnota, ip) VALUES ("' . $id_ankety . '", "' . $hlas . '", "' . $ip . '")');
            } else {
                $uzHlasoval = true;
            }
        }
    }
}
/**
 * **************************************************************************************************************
 */
// Vyber dat z ankety a vsetko okolo toho

$result = psw_mysql_query('SELECT * FROM ankety WHERE visible = "1" ORDER BY id_ankety');
while ($fetch1 = $result->fetch_assoc()) {

    
    
    $result = psw_mysql_query('SELECT count(*) AS pocet_hlasujucich FROM ankety_data WHERE id_ankety = "' . $fetch1['id_ankety'] . '" ');
    $pocet_hlasujucich = $result->fetch_assoc();
    
    
    $pocet_hlasujucich = $pocet_hlasujucich['pocet_hlasujucich'];

    echo '
		<table class="anketa" >
		 <tr>
		  <td colspan="3" align="left">
		   <b>' . $fetch1['meno_ankety'] . '</b>
		  </td>
		 </tr>
		 <tr>
		  <td>
		    &nbsp;
		  </td>
	   </tr>';

    for ($i = 0; $i < 10; $i ++) {
        if (($fetch1['meno_hodnoty_' . $i] != "") && ($fetch1['meno_hodnoty_' . $i])) {
            
            
            
            $result = psw_mysql_query('SELECT count(*) AS pocet_hlasov FROM ankety_data WHERE id_ankety = "' . $fetch1['id_ankety'] . '" AND hodnota = "' . $i . '" ');
            $pocet_hlasov = $result->fetch_assoc();
            
            
            $pocet_hlasov = $pocet_hlasov['pocet_hlasov'];

            echo '
			  <tr>
		     <td width="45%">
		      <span class="a" onClick="anketaHlasuj(\'' . $fetch1['id_ankety'] . '\',\'' . $i . '\');" title="Hlasuj!!!" ><b>' . $fetch1['meno_hodnoty_' . $i] . '</b></span>		      
				 </td>
		     <td width="10%">' . $pocet_hlasov . 'x</td>
		     <td width="45%">';

            if ($pocet_hlasujucich > 0) {
                $percenta = ceil(($pocet_hlasov / $pocet_hlasujucich) * 100);
                if ($percenta > 0) {
                    $sirka_palicky = ceil($percenta / 1.6);
                    echo '<img src="' . $path . 'pics/anketa.png" width="' . $sirka_palicky . '" height="10" /> ' . ceil($percenta) . '%';
                }
                echo '</td>
	           </tr>';
            }
        }
    }
    echo '
    <tr>
	   <td colspan="3" align="center">
	    <br />
	    Počet hlasujúcich: <b>' . $pocet_hlasujucich . '</b>
	   </td>
    </tr>
	<tr>
		  <td>
		    &nbsp;
		  </td>
	</tr>
   </table>';
}

echo ($uzHlasoval ? '<b>Už si hlasoval.</b>' : '');
?>
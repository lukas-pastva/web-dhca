<?
if(is_file("../admin/admin_functions.php")){
	include_once("../admin/admin_functions.php");
}



$path;
if(!isset($_REQUEST['path'])){
	$path = getPath();
} else {
	$path = $_REQUEST['path'];
}

$galeryId = $_REQUEST['galeryId'];
$section_id = $galeryId;
$pocet_obrazkov_na_riadok = POCET_OBRAZKOV_NA_RIADOK;
$pocet_obrazkov_na_stranu = POCET_OBRAZKOV_NA_STRANU;

//Limit odkial sa budu zobrazovat fotky
if ($_REQUEST['limit']){
	$limit = $_REQUEST['limit'];
} else {
	$limit = 0;
}

echo '<script type="text/javascript">
    var GB_ROOT_DIR = "'.$path.'greybox/";
  </script>
  <script type="text/javascript" src="'.$path.'greybox/AJS.js"></script>
  <script type="text/javascript" src="'.$path.'greybox/AJS_fx.js"></script>
  <script type="text/javascript" src="'.$path.'greybox/gb_scripts.js"></script>';

$section = psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM section WHERE section_id = "' .$section_id. '" '));
$destination = ''.$path.'foto/'.$section['main_section']."/".$section['sub_section']."/thumbs/";
$destinationBig = ''.$path.'foto/'.$section['main_section']."/".$section['sub_section']."/";

$pocet = psw_mysql_fetch_array(psw_mysql_query('SELECT count(*) AS pocet FROM picture WHERE section_id = "' .$section_id. '" '));
$pocet_stran = ceil($pocet['pocet'] / $pocet_obrazkov_na_stranu);
$aktualna_strana = ceil(( $limit/$pocet_obrazkov_na_stranu )+1);

$foto = getTableRows('picture', ' AND section_id = "' .$section_id. '"', 'date');

if ($limit+$pocet_obrazkov_na_stranu > count($foto)){
	$pokial_zobrazovat = count($foto);
}else{
	$pokial_zobrazovat = $limit+$pocet_obrazkov_na_stranu;
}

echo '<div class="fotoalbum">
        <b>'.$section['section_name'].'</b><br />
        &nbsp;'.$aktualna_strana.'/'.$pocet_stran.'&nbsp;&nbsp;(počet fotografií: ' .$pocet['pocet']. ')<br /><br />';

if ($limit > 0){
	echo '&nbsp;<b>|</b>&nbsp;
	      <span class="a" onClick="fotoalbumPrepni(\''.$_REQUEST['galeryId'].'\', \''.($limit - $pocet_obrazkov_na_stranu).'\', \''.$path.'\');" >
	       Predchádzajúca strana
	      </span>
	      &nbsp;<b>|</b>&nbsp;';
}

if ($limit+$pocet_obrazkov_na_stranu < count($foto)){
	echo '&nbsp;<b>|</b>&nbsp;
				<span class="a" onClick="fotoalbumPrepni(\''.$_REQUEST['galeryId'].'\', \''.($limit + $pocet_obrazkov_na_stranu).'\', \''.$path.'\');" >
				 Nasledujúca strana
				</span>
				&nbsp;<b>|</b>&nbsp;';
}

echo '<div style="display: none;">';
for ($i=0; $i<$limit; $i++){
	echo '<a href="'.$destinationBig.$foto[$i]['filename'].'" rel="gb_imageset[foto]" title="'.validateForm($section['section_name'].($foto[$i]['text']?': '.$foto[$i]['text']:'')). '" >'.$i.'</a>';
}
echo '</div>';

for ($i=$limit; $i<$pokial_zobrazovat; ){
	echo '
        <table>
	       <tr>';

	for($j=0; $j<$pocet_obrazkov_na_riadok; $j++){
		if( ($i<count($foto)) && ( $i < $pokial_zobrazovat) ){
			echo '
				<td>
				 <a href="'.$destinationBig.$foto[$i]['filename'].'" rel="gb_imageset[foto]" title="'.validateForm($section['section_name'].($foto[$i]['text']?': '.$foto[$i]['text']:'')). '" >
				  <img src="'.$destination.$foto[$i]['filename'].'" border="1" />
				 </a>
				</td>';
			$i++;
		}
	}
	echo ' </tr>
        </table>';
}
echo '<div style="display: none;">';
for ($i=$pokial_zobrazovat; $i<count($foto); $i++){
	echo '<a href="'.$destinationBig.$foto[$i]['filename'].'" rel="gb_imageset[foto]" title="'.validateForm($section['section_name'].($foto[$i]['text']?': '.$foto[$i]['text']:'')). '" >'.$i.'</a>';
}
echo '</div></div>';


?>
<?

include_once("db.inc.php");
mysql_pconnect(SQL_HOST, SQL_USERNAME, SQL_PASSWORD) or die("Nelze se poipojit k MySQL: " . mysql_error());
mysql_select_db(SQL_DBNAME) or die("Nelze vybrat databazi: ". mysql_error());


//zobrazi foto, hehe
if(0 < ($_REQUEST['j'])){
	$id = $_REQUEST['j'];
}else{
	die;
}

if(0 < ($_REQUEST['i'])){
	
	if($_REQUEST['i']=='1'){
		$img = psw_mysql_fetch_array(psw_mysql_query('SELECT image FROM clanok WHERE clanok_id = ' .$id));
		$img = $img['image'];
		header("Content-type: image/jpeg");
		echo base64_decode($img);
	}
	
	if($_REQUEST['i']=='2'){
		$img = psw_mysql_fetch_array(psw_mysql_query('SELECT image FROM odkaz WHERE odkaz_id = ' .$id));
		$img = $img['image'];
		header("Content-type: image/jpeg");
		echo base64_decode($img);
	}
	if($_REQUEST['i']=='3'){
		$img = psw_mysql_fetch_array(psw_mysql_query('SELECT thumb FROM partylist WHERE partylist_id = ' .$id));
		$img = $img['thumb'];
		header("Content-type: image/jpeg");
		echo base64_decode($img);
	}
	if($_REQUEST['i']=='4'){
		$img = psw_mysql_fetch_array(psw_mysql_query('SELECT poster FROM partylist WHERE partylist_id = ' .$id));
		$img = $img['poster'];
		header("Content-type: image/jpeg");
		echo base64_decode($img);
	}
	
} else {
	die;
}

?>
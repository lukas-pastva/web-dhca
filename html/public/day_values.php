<?
/*
//zistim si z kolko ludi ludi je online teraz
//podla toho kolko ludi je online poovnam s hodnotou z tabulky daneho dna a ak je vecsia pepisem, tyjo este dam aj cas :)
$midnight = (time() - ((date('G')*60*60)+(date('i')*60)+(date('s'))));

	 
	$online_users = psw_mysql_fetch_array( psw_mysql_query('SELECT count(*) AS online_users FROM counter WHERE logged="1"') );
	$online_users = $online_users['online_users'];

	$max_day_online_users = psw_mysql_fetch_array( psw_mysql_query('SELECT MAX(max_day_users) FROM day_values ') );
	$max_day_online_users_time = psw_mysql_fetch_array( psw_mysql_query('SELECT time FROM day_values WHERE max_day_users = "' .$max_day_online_users[0]. '" ORDER BY id DESC') );
	$max_day_online_users_time = $max_day_online_users_time['time'];

	if(! psw_mysql_fetch_array( psw_mysql_query('SELECT * FROM day_values WHERE max_day_href = 0 AND time > "' .date('Y-m-d H:i:s', $midnight). '" ' ) ) ){
		if($online_users>$max_day_online_users[0]){
			psw_mysql_query('INSERT INTO day_values SET max_day_users = ' .$online_users);
			$max_day_online_users = $online_users;
			$max_day_online_users_time = date('Y-m-d H:i:s');
		}
	} else {
		if($online_users>$max_day_online_users[0]){
			psw_mysql_query('UPDATE day_values SET max_day_users = ' .$online_users. ', time="' .date('Y-m-d H:i:s'). '" WHERE time = ' .$max_day_online_users_time. ' AND max_day_href = 0 ');
			$max_day_online_users = $online_users;
			$max_day_online_users_time = date('Y-m-d H:i:s');
		}
	}

	$max_day_href = psw_mysql_fetch_array( psw_mysql_query('SELECT max_day_href FROM day_values WHERE max_day_users = 0 AND time > "' .date('Y-m-d H:i:s', $midnight). '" ' ) );
	$max_day_href = $max_day_href['max_day_href'];

*/
?>
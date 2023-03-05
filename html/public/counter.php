<?
/*****************************************************************************************************************/
$ip = $_SERVER['REMOTE_ADDR'];
$time = time();
$start_time = microtime(true);

//nebude sa pocitat ak sa neda prelozit ip na hosta...hacker?

if(

strpos(gethostbyaddr($ip), 'crawl.yahoo') ||
strpos(gethostbyaddr($ip), 'cuill.com') ||
strpos(gethostbyaddr($ip), 'googlebot.com')



){
	$all_users = 0;
	$month_users = 0;
	$week_users = 0;
	$day_users = 0;
	$online_users = 0;
} else {

	//Ak nieje nalogovany, zapocitame ip.
	if (! psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM counter WHERE ip = "' .$ip. '" AND logged = "1"'))){
		psw_mysql_query('INSERT INTO counter (time, ip, logged) VALUES ("' .$time. '", "' .$ip. '", "1")');
		//Ak je nalogovany, tak mu prepiseme cas akoze sa nalogoval prave v tomto case
	} else {
		psw_mysql_query('UPDATE counter SET time = "' .time(). '" WHERE ip = "' .$ip. '" AND logged ="1"');
	}
	psw_mysql_query('UPDATE counter SET logged = "0" WHERE logged = "1" AND time < "'.( time() - 600 ).'" ');
	/*****************************************************************************************************************/

	$all_users = psw_mysql_fetch_array( psw_mysql_query('SELECT count(*) AS all_users FROM counter') );
	$all_users = $all_users['all_users'];

	$time = time();
	$month_time = ( $time - 2592000);
	$month_users = psw_mysql_fetch_array( psw_mysql_query('SELECT count(*) AS month_users FROM counter WHERE time > '.$month_time.'') );
	$month_users = $month_users['month_users'];

	$week_time = ( $time - 604800);
	$week_users = psw_mysql_fetch_array( psw_mysql_query('SELECT count(*) AS week_users FROM counter WHERE time > '.$week_time.'') );
	$week_users = $week_users['week_users'];


	$time_in_secs = ( (date("G",time())*3600) + (date("i" ,time())*60) + date("s" ,time()) );

	$day_time = ( $time - $time_in_secs);
	$day_users = psw_mysql_fetch_array( psw_mysql_query('SELECT count(*) AS day_users FROM counter WHERE time > '.$day_time.'') );
	$day_users = $day_users['day_users'];

	$online_users = psw_mysql_fetch_array( psw_mysql_query('SELECT count(*) AS online_users FROM counter WHERE logged="1"') );
	$online_users = $online_users['online_users'];

	$midnight = (time() - ((date('G')*60*60)+(date('i')*60)+(date('s'))));
	$pocetNavstevDnes = mysql_num_rows(psw_mysql_query('SELECT count(*) FROM counter WHERE time > "' .$midnight. '" GROUP BY ip'));

	$pocetNavstevDnesUzivatel = psw_mysql_fetch_array(psw_mysql_query('SELECT count(*) FROM counter WHERE ip = "' .$_SERVER['REMOTE_ADDR']. '" AND time > "' .$midnight. '" '));
	$pocetNavstevDnesUzivatel = $pocetNavstevDnesUzivatel[0];
}

echo '  <table class="counter">
         <tr>
          <td>
              Všetky Návštevy:
          </td>
          <td>
           <b>' .($all_users+340000). '</b>
          </td>
         </tr>
         <tr>
          <td>
           Návštevy za mesiac:
          </td>
          <td>
           <b>' .($month_users+0). '</b>
          </td>
         </tr>
         <tr>
          <td>
           Návštevy za týždeň:
          </td>
          <td>
           <b>' .$week_users. '</b>
          </td>
         </tr>
         <tr>
          <td>
           Návštevy dnes:
          </td>
          <td>
           <b>' .$day_users. '</b>
          </td>
         </tr>
         <tr>
          <td>
           Uživatelia dnes:
          </td>
          <td>
           <b>' .$pocetNavstevDnes. '</b>
          </td>
         </tr>
         <tr>
          <td>
           Užívatelia teraz:
          </td>
          <td>
           <b>' .$online_users. '</b>
          </td>
         </tr>
        </table>';


?>
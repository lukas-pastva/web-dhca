<?

//otvorim subor
if (file_exists('./admin2/data/comments.txt')){

	$comments_file = fopen('./admin2/data/comments.txt', 'rb');
	$comments_file_data;
	while (!feof($comments_file)) {
		$comments_file_data .= fread($comments_file, 1024);
	}
	fclose($comments_file);

	//nacitam ho po riadkoch
	$comments_pole = Array();
	$comments_pole = explode("\n", $comments_file_data);

	//vytvorim si pole, kde kluce budu id clankov
	$pole_clankov = Array();
	foreach($comments_pole as $key => $value){

		$name = mb_substr($value, 0, 10);
		$text = '|' . mb_substr($value, 12);

		//vylucim clanky bez komentarov
		if(strlen($text)>2){
			$pole_clankov[$name] = $text;
		}

	}


	//najdem v kazdom riadku najnovsi komentar - posledny
	foreach($pole_clankov as $key => $value){

		$value = strrev($value);
		$last = strpos($value, '||', 1);
		$value = strrev($value);
		$last_comment = mb_substr($value, (strlen($value)-$last));
		$pole_clankov[$key] = null;
		$temp = explode('|', $last_comment);
		$pole_clankov[$key]['time'] = mb_substr($last_comment, 0, 9);
		$pole_clankov[$key]['clanok'] = $key;
		$pole_clankov[$key]['autor'] = $temp[1];
		$pole_clankov[$key]['text'] = $temp[4];

	}

	//zoradit pole podla casu
	asort($pole_clankov);
	$pole_clankov = array_reverse($pole_clankov);
	//echo count($pole_clankov);
	//print_r($pole_clankov);
}


?>

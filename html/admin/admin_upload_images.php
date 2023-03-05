<?

include_once('admin_functions.php');

if ($_REQUEST['section_id']){

	foreach($_FILES as $filee){
		$section = psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM section WHERE section_id = "' .$section_id. '" '));
		//Ak uz dany obrazok existuje
		if (psw_mysql_fetch_array(psw_mysql_query('SELECT * FROM picture WHERE filename  = "' .normalizeFilename($filee['name']). '" AND section_id = "' .$_REQUEST['section_id']. '" '))){
			//alert("Takato foto uz existuje");
		} else {

			//Cesta k fotkam
			$destination = "../foto/".$section['main_section']."/".$section['sub_section']."/";
			$filename_norm  = $destination.normalizeFilename($filee['name']);
			$filename_thumb = $destination."thumbs/".normalizeFilename($filee['name']);

			//Ak sa nepodari nahrat subor
			if ( ! (move_uploaded_file($filee['tmp_name'],$filename_norm) || (copy($filename_norm,$filename_thumb)) ) ){
				//die;
			}

			//Zmena rozlisenia obrazku
			$src_img  = imagecreatefromjpeg($filename_norm);
			$size_img = getimagesize($filename_norm);

			//Praca s thumbnailom
			$thumb_width = $size_img[0] / ( $size_img[1] /THUMB_PICTURE_HEIGHT );
			$dst_img_thumb = imageCreateTrueColor($thumb_width,THUMB_PICTURE_HEIGHT);
			imagecopyresampled($dst_img_thumb, $src_img, 0, 0, 0, 0, $thumb_width, THUMB_PICTURE_HEIGHT, $size_img[0], $size_img[1]);
			imagejpeg($dst_img_thumb, $filename_thumb, THUMB_PICTURE_QUALITY);

			//otestujem ci sa ide resizovat a rozvetvim
			if($_REQUEST['resize'] == 'on'){
				if( (!0 < ($_REQUEST['width_1'])) || (!0 < ($_REQUEST['width_2'])) ){
					$_REQUEST['width_1'] = BIG_PICTURE_WIDTH;
					$_REQUEST['width_2'] = BIG_PICTURE_WIDTH_2;
				} else {
					
					//zistim orientaciu
					$naSirku = false;
					if( ($size_img[0]/$size_img[1])>1 ){
						$naSirku = true;
					}


					if($naSirku && ($size_img[0]>$_REQUEST['width_1'])){
						
						//na sirku
						$big_height = $size_img[1] / ( $size_img[0] / $_REQUEST['width_1'] );
						$dst_img_big = imageCreateTrueColor($_REQUEST['width_1'], $big_height);
						imagecopyresampled($dst_img_big, $src_img, 0, 0, 0, 0, $_REQUEST['width_1'], $big_height, $size_img[0], $size_img[1]);
						imagejpeg($dst_img_big, $filename_norm, BIG_PICTURE_QUALITY);

					} else if(!$naSirku && ($size_img[0]>$_REQUEST['width_2'])){
						
						//na vysku
						$big_height = $size_img[1] / ( $size_img[0] / $_REQUEST['width_2'] );
						$dst_img_big = imageCreateTrueColor($_REQUEST['width_2'], $big_height);
						imagecopyresampled($dst_img_big, $src_img, 0, 0, 0, 0, $_REQUEST['width_2'], $big_height, $size_img[0], $size_img[1]);
						imagejpeg($dst_img_big, $filename_norm, BIG_PICTURE_QUALITY);

					} else {
						
						//nerisajzuje sa
						$dst_img_big = imageCreateTrueColor($size_img[0], $size_img[1]);
						imagecopyresampled($dst_img_big, $src_img, 0, 0, 0, 0, $size_img[0], $size_img[1], $size_img[0], $size_img[1]);
						imagejpeg($dst_img_big, $filename_norm, BIG_PICTURE_QUALITY);
						
					}
				}
			} else {
				$dst_img_big = imageCreateTrueColor($size_img[0], $size_img[1]);
				imagecopyresampled($dst_img_big, $src_img, 0, 0, 0, 0, $size_img[0], $size_img[1], $size_img[0], $size_img[1]);
				imagejpeg($dst_img_big, $filename_norm, BIG_PICTURE_QUALITY);
			}

			psw_mysql_query('INSERT INTO picture (section_id, filename, date) VALUES ( "' .$_REQUEST['section_id']. '", "' .normalizeFilename($filee['name']). '", "'.time().'" ) ');
		}
	}
}

?>
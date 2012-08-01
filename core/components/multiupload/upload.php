<?php
include('../../config/config.inc.php');
mysql_connect ($database_server, $database_user, $database_password);
mysql_set_charset($database_connection_charset);
mysql_select_db ($dbase);
if(isset($_REQUEST['aid']) && is_numeric($_REQUEST['aid'])) {
  $aid =$_REQUEST['aid'];
}
else $aid = 0;
if (isset($_REQUEST['file'])) {
  
  function image_resize($source, $newpath, $width, $height)
  {
      $filename = $source;
      $parametr = getimagesize($filename);
      list($width_orig, $height_orig) = getimagesize($filename);
      $ratio_orig = $width_orig / $height_orig;
  
      if ($width / $height > $ratio_orig) {
	  $width = $height * $ratio_orig;
      } else {
	  $height = $width / $ratio_orig;
      }
      // Resample
      $image_p = imagecreatetruecolor($width, $height);
      switch ( $parametr[2] ) {
	case IMAGETYPE_GIF: $image = imagecreatefromgif($filename);
	break;
	case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($filename);
	break;
	case IMAGETYPE_PNG: 
	$image = imagecreatefrompng($filename);
	 imageAlphaBlending($image_p, false);
	
	
	$transparent = imagecolorallocatealpha($image_p, 0, 0, 0, 127);
	imagefill($image_p, 0, 0, $transparent);
	imageSaveAlpha($image_p, true);
       
	break;
      }
      imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
      // Output
    switch ( $parametr[2] ) {
	case IMAGETYPE_GIF: 
	  imagegif($image_p, $newpath);
	break;
	case IMAGETYPE_JPEG: 
	  imagejpeg($image_p, $newpath, 100);
	break;
	case IMAGETYPE_PNG: 
	  imagepng($image_p, $newpath, 0);
	break;
    }
  }
  $imgpath = $modx_base_path."assets/uploads/images/goods/";
  $query = "INSERT INTO `{$table_prefix}site_content` VALUES ()";
  mysql_query($query);
  $gid = mysql_insert_id();
  $query = "INSERT INTO `{$table_prefix}ms_modGoods` SET `gid`={$gid}";
  mysql_query($query);
  $newImgFullName = $gid."_".rand(1,9999999)."_".$_FILES['Filedata']['name'];
  $newImgFullPath = $imgpath.$newImgFullName;
  
  if(!copy($_FILES['Filedata']['tmp_name'], $newImgFullPath)) {
		exit("Ошибка загрузки файла");
		
     } else {
    
	    $params = getimagesize($newImgFullPath);
	    switch ( $params[2] ) {
		case IMAGETYPE_GIF: 
		  $ext = 'gif';
		break;
		case IMAGETYPE_JPEG: 
		  $ext = 'jpg';
		break;
		case IMAGETYPE_PNG: 
		  $ext = 'png';
		break;
	      
	     
	    }
       //image_resize($newImgFullPath, $newImgFullPath."_tb.".$ext, 170, 170);
       //image_resize($newImgFullPath, $newImgFullPath."_hit.".$ext, 350, 350);        
	
	    mysql_query("
			UPDATE 
				`{$table_prefix}ms_modGoods` 
			SET 
				`img`='assets/components/spgoods/images/{$newImgFullName}'"
		);
     }
	 
  $category = "<select name='cat[]'><option disabled>Выберите категорию</option>";
  $query = "SELECT * FROM `{$table_prefix}site_content` WHERE `template`=3 AND `isfolder`=1";
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result)) {
		if ($aid == $row['id']) $selected = " selected"; else $selected="";
		$category.= "<option value='".$row['id']."' ".$selected.">".$row['pagetitle']."</option>";    
    }
  $category.="</select>";
  $warehouse = "<select name='wid[]'><option disabled>Выберите склад</option>";
  $query = "SELECT * FROM `{$table_prefix}ms_modWarehouse`";
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result)) {
		if ($aid == $row['id']) $selected = " selected"; else $selected="";
		$warehouse.= "<option value='".$row['id']."' ".$selected.">".$row['pagetitle']."</option>";    
    }
  $warehouse.="</select>";
  $types = "<select name='types[]'><option disabled>Выберите тип</option>";
  $query = "SELECT * FROM `{$tbl_full_prefix}goods_types`";
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result)) {
		if ($aid == $row['ID']) $selected = " selected"; else $selected="";
		$types.= "<option value='".$row['ID']."' ".$selected.">".$row['NAME']."</option>";    
    }
  $types.="</select>";
  echo "<td rowspan='2'>
<img title='".$newImgFullName."' alt='".$newImgFullName."' src='/assets/uploads/images/goods/".$newImgFullName."_tb.".$ext."'>
</td><td>
Название<br /><input type='text' name='name[]' value='' />
<br />
Описание <br />
<textarea name='desc[]' rows='4' cols='60'/>
</textarea>
<br />
Цена <input type='text' name='price[]' value='' />   
Артикул <input type='text' name='sku[]' value='' /><br />
Каталог 
".$category."<br />
<input type='hidden' name='gid[]' value='".$gid."' />
</td>

";
}
if(isset($_REQUEST['info'])) {

  if (isset($_REQUEST['name'])) {
						$names = $_REQUEST['name'];
					}
    if (isset($_REQUEST['sku'])) {
						$skus = $_REQUEST['sku'];
					}
		
     if (isset($_REQUEST['desc'])) {
						$descs = $_REQUEST['desc'];
					}
	if (isset($_REQUEST['price'])) {
						$prices = $_REQUEST['price'];
					}				
  					
	if (isset($_REQUEST['gid'])) {
						$gids = $_REQUEST['gid'];
					}
	if (isset($_REQUEST['alb'])) {
		$albs = $_REQUEST['alb'];
	}
					
	for ($i=0;$i<count($gids);$i++) {
		if (is_numeric($prices[$i])) {
		$price = $prices[$i];
		}
		else $price=0;
		$query = "UPDATE `{$tbl_full_prefix}goods` SET `NAME`='".$names[$i]."', `DESC`='".$descs[$i]."', `SKU`='".$skus[$i]."',
		`PRICE`=".$price.", `ALB_ID`=".$albs[$i]." WHERE `ID`=".$gids[$i];
		mysql_query($query);
	}
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta http-equiv="refresh" content="0; url=/multiupload.html?aid='.$aid.'" >';

	}

?>
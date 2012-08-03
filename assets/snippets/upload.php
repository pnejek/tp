<?php
if(!isset($_SESSION['modx.user.contextTokens']['mgr'])) die('Доступ запрещен.');
include('./core/config/config.inc.php');
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
  $imgpath = MODX_BASE_PATH."assets/uploads/images/goods/";
  $query = "INSERT INTO `{$table_prefix}site_content` (`uri`) VALUES ('".rand(1,9999999)."')";
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
				`img`='assets/uploads/images/goods/{$newImgFullName}'
			WHERE 
				`gid`={$gid}"
		);
     }
	 
  $category = "<select name='cat[]' class='category'><option disabled>Выберите категорию</option>";
  $query = "SELECT * FROM `{$table_prefix}site_content` WHERE `template`=3 AND `isfolder`=1";
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result)) {
		if ($aid == $row['id']) $selected = " selected"; else $selected="";
		$category.= "<option value='".$row['id']."' ".$selected.">".$row['pagetitle']."</option>";    
    }
  $category.="</select>";
  $warehouse = "<select name='wid[]' class='warehouse'><option disabled>Выберите склад</option><option selected value='0'>На все</option>";
  $query = "SELECT * FROM `{$table_prefix}ms_modWarehouse`";
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result)) {
		$warehouse.= "<option value='".$row['id']."'>".$row['name']."</option>";    
    }
  $warehouse.="</select>";
  $types = "<select name='type[]' class='type'><option disabled>Выберите тип</option>";
  $query = "SELECT * FROM `{$tbl_full_prefix}goods_types`";
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result)) {
		if ($tid == $row['ID']) $selected = " selected"; else $selected="";
		$types.= "<option value='".$row['ID']."' ".$selected.">".$row['NAME']."</option>";    
    }
  $types.="</select>";
  echo $modx->getChunk('multiuploadDataForm', array(
  	'CATEGORY' => $category,
  	'WAREHOUSE' => $warehouse,
  	'TYPES' => $types,
  	'IMAGENAME' => $newImgFullName,
  	'GOODID' => $gid
  	));
}
if(isset($_REQUEST['info'])) {

  	if (isset($_REQUEST['pagetitle'])) {
		$pagetitles = $_REQUEST['pagetitle'];
	}
	if (isset($_REQUEST['longtitle'])) {
		$longtitles = $_REQUEST['longtitle'];
	}
   
 	if (isset($_REQUEST['alias'])) {
		$aliases = $_REQUEST['alias'];
 	}					
	if (isset($_REQUEST['gid'])) {
		$gids = $_REQUEST['gid'];
	}
	if (isset($_REQUEST['cat'])) {
		$cats = $_REQUEST['cat'];
	}
	if (isset($_REQUEST['type'])) {
		$typess = $_REQUEST['type'];
	}
	if (isset($_REQUEST['wid'])) {
		$wids = $_REQUEST['wid'];
	}		
	echo "Добавленные товары: ";
	for ($i=0;$i<count($gids);$i++) {
		if(empty($aliases[$i])) {
			$aliases[$i]=$pagetitles[$i];
		}
		$document = $modx->getObject('modDocument', array('id'=>$gids[$i]));
		
		$document->set('pagetitle', $pagetitles[$i] );
		$document->set('longtitle', $longtitles[$i] );
		$document->set('alias', $aliases[$i] );
		$document->set('published', 1 );
		$document->set('template', 2 );
		$document->set('parent', $cats[$i] );
		$document->save();
		$document->set('uri',$modx->makeUrl($gids[$i]));
		$document->save();
		if($wid[$i]==0) {
			$reswh = mysql_query("SELECT `id` FROM `{$table_prefix}ms_modWarehouse`");
			$j = 0;
			while($whs = mysql_fetch_array($reswh)){
				if ($j == 0) {
					$query = "UPDATE `{$table_prefix}ms_modGoods` SET `wid`={$whs['id']}, 
					`type`={$typess[$i]} WHERE `gid`=".$gids[$i];
					mysql_query($query);
					$j++;
					continue;
				}
				$query = "INSERT INTO `{$table_prefix}ms_modGoods` SET `wid`={$whs['id']}, 
				`type`={$typess[$i]} WHERE `gid`=".$gids[$i];
				mysql_query($query);
				$j++;
			}
	
		}
		else {
			$query = "UPDATE `{$table_prefix}ms_modGoods` SET `wid`={$wid[$i]}, 
					`type`={$typess[$i]} WHERE `gid`=".$gids[$i];
					mysql_query($query);
		}
		$imgres = mysql_query("SELECT `img` FROM `{$table_prefix}ms_modGoods` WHERE `gid`=".$gids[$i]);
		$img = mysql_fetch_array($imgres);
		
		echo "<br /><a target='_blank' href='/service/productmanagement.html?step=2&action=view&id={$gids[$i]}'>
					{$pagetitles[$i]}<img width='200' src='/{$img['img']}' title='{$pagetitles[$i]}' alt='{$pagetitles[$i]}'/></a>";
	}
	echo "<br /><a href='/service/multiupload.html'>Продолжить загрузки</a>";
	/*echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta http-equiv="refresh" content="0; url=/service/multiupload.html" >';*/

}
<?php
if(!isset($_SESSION['modx.user.contextTokens']['mgr'])) die('Доступ запрещен.');
include('./core/config/config.inc.php');
mysql_connect ($database_server, $database_user, $database_password);
mysql_set_charset($database_connection_charset);
mysql_select_db ($dbase);
if(isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) ) {
	$id = $_REQUEST['id'];
	$document = $modx->getObject('modDocument', $id);//получаем товар
	$goodName = $document->get('pagetitle');//получаем название товара
} 
if(isset($_REQUEST['step']) && is_numeric($_REQUEST['step']) )
  $step = $_REQUEST['step'];
if(isset($_REQUEST['action']))
  $action = $_REQUEST['action'];
else $action = '';
$output = '';  
switch ($step) {
	case '1': //задание основных значений товара
	  switch ($action) {
	  	case 'view' :
	  		$output.="Этап 1. Задание основных значений";
			$category = "<select name='cat' class='category'><option disabled>Выберите категорию</option>";
			$query = "SELECT * FROM `{$table_prefix}site_content` WHERE `template`=3 AND `isfolder`=1";
			$result = mysql_query($query);
			while($row = mysql_fetch_array($result)) {
				if ($row['id']==125) $selected = " selected"; else $selected="";
				$category.= "<option value='".$row['id']."' ".$selected.">".$row['pagetitle']."</option>";    
			}
			$category.="</select>";
			$types = "<select name='type' class='type'><option disabled>Выберите тип</option>";
			$query = "SELECT * FROM `{$tbl_full_prefix}goods_types`";
			$result = mysql_query($query);
			while($row = mysql_fetch_array($result)) {
				if ($row['ID']==2) $selected = " selected"; else $selected="";
				$types.= "<option value='".$row['ID']."' ".$selected.">".$row['NAME']."</option>";    
			}
			$types.="</select>";
			$warehouse = "<select name='wid[]' class='warehouse'><option disabled>Выберите склад</option><option selected value='0'>На все</option>";
			$query = "SELECT * FROM `{$table_prefix}ms_modWarehouse`";
			$result = mysql_query($query);
			while($row = mysql_fetch_array($result)) {
				$warehouse.= "<option value='".$row['id']."'>".$row['name']."</option>";    
			}
			$warehouse.="</select>";
			$output.="<script type='text/javascript'>
			function checkfirst(form){
				if (form.pagetitle.value==''){
					alert( 'Введите название!' );
   					form.pagetitle.focus();
    				return false ;
				}
				return true;
			}
			</script>
			<form id='first' action='/service/goodsrush.html' method='POST' onsubmit='return checkfirst(this);'>
			Название: <input type='text' name='pagetitle' value='' /><br />
			Длинное название: <input type='text' name='longtitle' value='' />(Если не задано - будет название)<br />
			Псведоним: <input type='text' name='alias' value='' /> (Если не задано - будет название)<br />
			Категория: {$category}<br />
			Тип: {$types}<br />
			Склад: {$warehouse}<br />
			<input type='hidden' name='action' value='add' />
			<input type='hidden' name='step' value='1' />
			<input type='submit' name='submit' value='Далее' />
			</form>";
	  	break;
	  	case 'add' :
	  		if (isset($_REQUEST['pagetitle']) && !empty($_REQUEST['pagetitle'])) {
				$pagetitle = mysql_real_escape_string(trim($_REQUEST['pagetitle']));
			} else {
				die('Название не введено!');
			}
			if (isset($_REQUEST['longtitle']) && !empty($_REQUEST['longtitle'])) {
				$longtitle = mysql_real_escape_string(trim($_REQUEST['longtitle']));
			}
			else {
		 		$longtitle = $pagetitle;	
		 	}
		 	if (isset($_REQUEST['alias']) && !empty($_REQUEST['alias'])) {
				$alias= mysql_real_escape_string(trim($_REQUEST['alias']));
		 	}
		 	else {
		 		$alias = $pagetitle;	
		 	}
		 	if (isset($_REQUEST['cat'])) {
				$cat = $_REQUEST['cat'];
			}
			if (isset($_REQUEST['type'])) {
				$type = $_REQUEST['type'];
			}
			if (isset($_REQUEST['wid']) && is_numeric($_REQUEST['wid'])) {
				$wid = $_REQUEST['wid'];
			} else {
				$wid = 0;
			}
			
	  		$query = "INSERT INTO `{$table_prefix}site_content` (`uri`) VALUES ('".rand(1,9999999)."')";
			mysql_query($query);
			$id = mysql_insert_id();
			if($wid==0) {
				$reswh = mysql_query("SELECT `id` FROM `{$table_prefix}ms_modWarehouse`");
				while($whs = mysql_fetch_array($reswh)){
					$query = "INSERT INTO `{$table_prefix}ms_modGoods` SET `wid`={$whs['id']}, 
					`type`={$type}, `gid`=".$id;
					mysql_query($query) or die("fffffuuu ".mysql_error());
				}
			} else {
				$query = "INSERT INTO `{$table_prefix}ms_modGoods` SET `wid`={$wid}, 
					`type`={$type}, `gid`=".$id;
				mysql_query($query) or die("fffffuuu2 ".mysql_error());
			}
			$document = $modx->getObject('modDocument', $id);
			$document->set('pagetitle', $pagetitle );
			$document->set('longtitle', $longtitle );
			$document->set('alias', $alias );
			$document->set('published', 1 );
			$document->set('template', 2 );
			$document->set('parent', $cat );
			$document->save();
			$document->set('uri',$modx->makeUrl($id));
			$document->save();
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta http-equiv="refresh" content="0; url=/service/goodsrush.html?step=2&action=search&id='.$id.'" >';
	  	break;
	  }
		
	break;
	case '2': //поиск и добавление картинки
		switch ($action) {
			case 'search' :
				function getimages($query, $id){
				    $i =1;
				    $html = "<script type='text/javascript'>
					    function valButton(btn) {
						    var cnt = -1;
						    for (var i=btn.length-1; i > -1; i--) {
						        if (btn[i].checked) {cnt = i; i = -1;}
						    }
						    if (cnt > -1) return cnt;
						    else return -1;
						}
						function checkpict(form){
							var btn = valButton(form.maincheck);
							if (btn == -1){
								alert('Вы забыли выбрать картинку!' );
			    				return false;
							}
							return true;
						}
						function showsubmit() {
							$('.radiobuttonclick').click(function(e) {
								var x =e.pageX + 5;
								var y =e.pageY + 2;
        						$('#pictsubmit').offset({top:y, left:x});
    						});
						}
					</script>
				    <form id='pictures' method='POST' action='/service/goodsrush.html' onsubmit='return checkpict(this);'>
				    <table border=1>";
				    for ($j=0; $j<32; $j+=8){
						$body = file_get_contents('http://ajax.googleapis.com/ajax/services/search/images?v=1.0&rsz=large&imgsz=large|xlarge|xxlarge&start='.$j.'&q='.urlencode($query));
						$json = json_decode($body);
						$html.= "<tr>";
						foreach ($json->responseData->results as $result)
						{
							$html.= "
							<td>
							
							<img id='image{$i}' src='".$result->tbUrl."' alt='".$result->title."' height='".($result->tbHeight)."' width='".($result->tbWidth)."' /><br />
							<a target='_blank' href='".$result->unescapedUrl."'>".$result->width."x".$result->height."</a><br />
									Выбрать<input class='radiobuttonclick' type='radio' name='maincheck' value='{$i}' onchange='showsubmit();'/><br />
									".$result->content."
									<input type='hidden' name='url{$i}' value='".$result->url."'>
									<input type='hidden' name='width{$i}' value='".$result->width."'>
								    <input type='hidden' name='height{$i}' value='".$result->height."'>
								    <script type='text/javascript'>
								$('#image{$i}').tooltip({	
									delay: 0,
									top: -15,
									left: 5,
									showURL: false,	
									bodyHandler: function() {		
										return $('<img/>').attr('src', '{$result->unescapedUrl}');	
									}
								});
							</script>
								</td>
							";
							$i++;
						}
						$html.= "</tr>";
				    }
				    $html .= "</table>
				    <input type='hidden' name='action' value='add' />
					<input type='hidden' name='step' value='2' />
					<input type='hidden' name='id' value='{$id}' />
				    <input id='pictsubmit' type='submit' name='submit' value='Сохранить'>
				    </form>";
					return $html;
				}
				$document = $modx->getObject('modDocument', $id);
				$goodName = $document->get('pagetitle');
				if(isset($_REQUEST['search'])) {
					$search=$_REQUEST['search'];
				} else {
					$search = $goodName;
				}
				echo "Продукт \"{$goodName}\" <br />";
				echo "<form action='/service/goodsrush.html' method='POST'>
					  <input type='text' name='search' value='{$search}' /><br />
					  <input type='hidden' name='action' value='search' />
					  <input type='hidden' name='step' value='2' />
					  <input type='hidden' name='id' value='{$id}' />
					  <input type='submit' name='submit' value='Поискать' />
					  </form>
				";
				echo getimages($search, $id);
			break;
			case 'add' :
				if (isset($_REQUEST['maincheck'])) {
					$imgID = $_REQUEST['maincheck'];
					$img = $_REQUEST['url'.$imgID];
					$ext = substr($img, -3);
				} else {
					die("Картинка не задана.");
				}
				$imgpath = MODX_BASE_PATH."assets/uploads/images/goods/";
				$newImgFullName = $id."_".rand(1,999999999).".".$ext;
				$newImgFullPath = $imgpath.$newImgFullName;
				if(!copy($img, $newImgFullPath)) {
					exit("Ошибка загрузки файла");
			     } else {
				    mysql_query("
						UPDATE 
							`{$table_prefix}ms_modGoods` 
						SET 
							`img`='assets/uploads/images/goods/{$newImgFullName}'
						WHERE 
							`gid`={$id}"
					);
					echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta http-equiv="refresh" content="0; url=/service/productmanagement.html?step=2&action=view&id='.$id.'" >';
			     }
			break;
		}
		
	break;
}
echo $output;
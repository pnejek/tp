<?php
if(!isset($_SESSION['modx.user.contextTokens']['mgr'])) die('Доступ запрещен.');

include('./core/config/config.inc.php');
mysql_connect ($database_server, $database_user, $database_password);
mysql_set_charset($database_connection_charset);
mysql_select_db ($dbase);
if(isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) )
  $id = $_REQUEST['id'];
if(isset($_REQUEST['step']) && is_numeric($_REQUEST['step']) )
  $step = $_REQUEST['step'];
if(isset($_REQUEST['tid']) && is_numeric($_REQUEST['tid']) )
  $tid = $_REQUEST['tid'];
if(isset($_REQUEST['action']))
  $action = $_REQUEST['action'];
else $action = '';
if(isset($_REQUEST['mode']))
  $mode = $_REQUEST['mode'];
$output='';
$output.='<script>function addMSvalue(id, string) {
  $(string).insertBefore($("#multiselect-attr"+id+" #btnaddMS"));
}
function deleteMSsel (attrid) {
	var select = $("#multiselect-attr"+attrid+" select:last");
	var value = $(select).find("option:selected").val();
	$.post("/service/productmanagement.html",
	  {
	    step: 2,
	    action: "deletemssel",
	    attrid: attrid,
	    id: '.$id.',
	    value: value
	  }	  
	);
	$(select).remove();	
}
function saveandreload() {
	$("#savereload").val(1);
	$("form").submit();
}
function showavvalform(xrid) {
	    $("#addavval"+xrid).show();
	    $("#addavval"+xrid+" input:first").focus();
}
function closeavvalform	(xrid) {
	$("#addavval"+xrid).hide();
}
function addavval(xrid){
	var value = $("#addavval"+xrid+" input:first").val(); 
	$.post("/service/typesmanagement.html", 
		{
			value: value,
			table: "avval",
			action: "new",
			xrid: xrid
		}	
	);
	$("#addavval"+xrid).hide();
}

</script>';
if (!isset($modx->miniShop) || !is_object($modx->miniShop)) {
	$modx->miniShop = $modx->getService('minishop','miniShop', $modx->getOption('core_path').'components/minishop/model/minishop/', $scriptProperties);
	if (!($modx->miniShop instanceof miniShop)) return '';
}
$good = $modx->getObject('ModGoods', array('gid'=>$id));
$arr = $good->toArray();
$tid = $arr['type'];
if (!$tid) {
	die("Тип не определен!");
}
$goodimg ='/'.$arr['img'];
$document = $modx->getObject('modDocument', $id);
$goodName = $document->get('pagetitle');
switch ($step) {
	case 2 :
		switch ($action) {
			
			case 'view' :
				$output.="<h3>Создание товара. Этап 2. Статичные атрибуты</h3><br />Товар: {$goodName} <br />
				<img src='{$goodimg}' alt='Изображение товара, если его не видно - это плохо!' width='50' />
				";
				$resattrs = mysql_query("
					SELECT `{$tbl_full_prefix}type_attr_xref`.*, 
						   `{$tbl_full_prefix}attributes`.`NAME`, 
					       `{$tbl_full_prefix}attributes`.`ID` as atid  
					FROM `{$tbl_full_prefix}type_attr_xref` 
					RIGHT JOIN `{$tbl_full_prefix}attributes` 
					   ON `{$tbl_full_prefix}attributes`.`ID`=`{$tbl_full_prefix}type_attr_xref`.`ATTR_ID` 
					WHERE `{$tbl_full_prefix}type_attr_xref`.`TYPE_ID` = {$tid} AND 
						  `{$tbl_full_prefix}type_attr_xref`.`STATIC`=1 
				    ORDER BY `{$tbl_full_prefix}type_attr_xref`.`SORTORDER` ASC
				") or die(mysql_error());
				if (mysql_num_rows($resattrs)>0) {
					$output.="<script> </script>
					
					<form action='/service/productmanagement.html' method='POST' id='staticform' enctype='multipart/form-data'>
					<input type='hidden' name='MAX_FILE_SIZE' value='3000000' />
					<table border='0'><caption>Статичные атрибуты</caption>";
					while($attrs = mysql_fetch_array($resattrs)) { 
						if (!empty($attrs['CSS'])) {
							$class = $attrs['CSS'];
						} else {
							$class = '';
						}
						$resprv = mysql_query("SELECT `attr{$attrs['atid']}` as pratval 
											   FROM `{$tbl_full_prefix}producttype_{$tid}_static` 
											   WHERE `PID`={$id}");
						if (mysql_num_rows($resprv)>0) {
							$prv = mysql_fetch_array($resprv);
							if ($attrs['MANUAL'])
							{
								if($attrs['FILE']) {
									$output.="<tr id='{$attrs['atid']}' class='{$class}'><td>{$attrs['NAME']} <input type='hidden' name='attrs[]' value='attr{$attrs['atid']}' /></td>";
									$output.="<td><input name='attr{$attrs['atid']}' type='file' id='manual{$attrs['atid']}'/>";
									if($attrs['FILE_BEHAVIOR']=='mainimage' || $attrs['FILE_BEHAVIOR']=='otherimage') {
										$output.="<br /><a href='/{$prv['pratval']}' target='_blank' ><img src='/{$prv['pratval']}' height='50' /></a>";
									} else {
										$output.="<br /><a href='/{$prv['pratval']}' target='_blank' >{$prv['pratval']}</a>";
									}
									$output.="</td></tr><tr bgcolor='#aaa' style='height: 22px;'><td> </td><td> </td></tr>";									
								} else {
									$output.="<tr id='{$attrs['atid']}' class='{$class}'><td>{$attrs['NAME']} <input type='hidden' name='attrs[]' value='attr{$attrs['atid']}' /></td>";
									$output.="<td><textarea name='attr{$attrs['atid']}' id='manual{$attrs['atid']}'>{$prv['pratval']}</textarea>
									</td></tr><tr bgcolor='#aaa' style='height: 22px;'><td> </td><td> </td></tr>";
							    }
							}
							else {
								$output.="<tr id='{$attrs['atid']}' class='{$class}'><td>{$attrs['NAME']} <input type='hidden' name='attrs[]' value='attr{$attrs['atid']}' />
								&nbsp;&nbsp;<a title='Добавить возможных значений' href='javascript:showavvalform({$attrs['ID']});'>[+]</a>
								<div id='addavval{$attrs['ID']}' style='display:none; background-color: #ccc; border: 1px solid black; width: 260px; '>
							      <input type='text' size='15' name='value' value='' />
							      <input type='button' name='add' value='Добавить' onclick='addavval({$attrs['ID']});'/>
							      &nbsp;<a href='javascript:closeavvalform({$attrs['ID']})' title='Закрыть'>[x]</a>
								</div>
								</td>";
								$output.="<td>";
								$dir = '';
								if ($attrs['SORTDIR'] == 1) {
									$dir = ' ASC';
								}
								if ($attrs['SORTDIR'] == -1) {
									$dir = ' DESC';
								}
						  		if($attrs['DBTYPE']=='DIGIT') {
						  			$orderby = '*1 '.$dir;
						  		}
						  		else{
						  			$orderby = $dir;
						  		}
								if (!$attrs['MULTISELECT']) {
									$resvalues = mysql_query("SELECT * FROM `{$tbl_full_prefix}available_values` 
															  WHERE `TA_XREF_ID`={$attrs['ID']} 
															  ORDER BY `VALUE`{$orderby}");
									$output.="
									<select name='attr{$attrs['atid']}'><option value=''>Не задано</option>";
									while($values = mysql_fetch_array($resvalues)){
										if ($values['ID'] == $prv['pratval']) $checked =" selected"; else $checked='';
										$output.="<option value='{$values['ID']}'{$checked}>{$values['VALUE']}</option>";
									}
									$output.="</select></td></tr><tr bgcolor='#aaa' style='height: 22px;'><td> </td><td> </td></tr>";
								} else {
									//вывод мультиселекта с выбранными значениями
									//формируем селект
									$resvalues = mysql_query("SELECT * FROM `{$tbl_full_prefix}available_values` 
															  WHERE `TA_XREF_ID`={$attrs['ID']} 
															  ORDER BY `VALUE`{$orderby}");
									$select="<select name='attr{$attrs['atid']}[]'><option value=''>Не задано</option>";
									while($values = mysql_fetch_array($resvalues)){
										$select.="<option value='{$values['ID']}'>{$values['VALUE']}</option>";
									}
									$select.="</select>";
									//конец формирования селекта
									//вывод атрибута
									$output.="<div id='multiselect-attr{$attrs['atid']}'>";
									//получаем значения атрибута
									$resMSvalues = mysql_query("SELECT `VALUE` 
													FROM `{$tbl_full_prefix}static_multivalues` 
													WHERE `PID`={$id} AND `ATTRID`={$attrs['atid']}
									");
									$i = 0;
									while ($MSvalues = mysql_fetch_array($resMSvalues)) {
										$output.="<div style='display:inline;' id='MS-{$i}-{$attrs['atid']}'>";
										$output.=$select;
										//выделяем значение
										$output.="<script>
											$('#multiselect-attr{$attrs['atid']} #MS-{$i}-{$attrs['atid']} option[value={$MSvalues['VALUE']}]').attr('selected', 'selected');
										</script></div>";
										$i++;
									}
									
									$output.='<button id="btnaddMS" onclick="addMSvalue('.$attrs['atid'].',\''.
									addslashes($select).'\' ); return false;">Добавить значение</button>';
									$output.="<a title='Удалить последнее значение' 
									href='javascript:deleteMSsel({$attrs['atid']})'>[X]</a>
									</div></td></tr><tr bgcolor='#aaa' style='height: 22px;'><td> </td><td> </td></tr>";
									//конец вывода атрибута
								}
							}
						}
						else {
							if ($attrs['MANUAL'])
							{
								if($attrs['FILE']) {
									$output.="<tr id='{$attrs['atid']}' class='{$class}'><td>{$attrs['NAME']} <input type='hidden' name='attrs[]' value='attr{$attrs['atid']}' /></td>";
									$output.="<td><input name='attr{$attrs['atid']}' type='file' id='manual{$attrs['atid']}'/>
									</td></tr><tr bgcolor='#aaa' style='height: 22px;'><td> </td><td> </td></tr>";									
								} else {
									$output.="<tr id='{$attrs['atid']}' class='{$class}'><td>{$attrs['NAME']} <input type='hidden' name='attrs[]' value='attr{$attrs['atid']}' /></td>";
									$output.="<td><textarea name='attr{$attrs['atid']}' id='manual{$attrs['atid']}'></textarea>
									</td></tr><tr bgcolor='#aaa' style='height: 22px;'><td> </td><td> </td></tr>";
							    }
							}
							else {
								$output.="<tr id='{$attrs['atid']}' class='{$class}'><td>{$attrs['NAME']} <input type='hidden' name='attrs[]' value='attr{$attrs['atid']}' />
								&nbsp;&nbsp;<a title='Добавить возможных значений' href='javascript:showavvalform({$attrs['ID']});'>[+]</a>
								<div id='addavval{$attrs['ID']}' style='display:none; background-color: #ccc; border: 1px solid black; width: 260px; '>
							      <input type='text' size='15' name='value' value='' />
							      <input type='button' name='add' value='Добавить' onclick='addavval({$attrs['ID']});'/>
							      &nbsp;<a href='javascript:closeavvalform({$attrs['ID']})' title='Закрыть'>[x]</a>
								</div>
								</td>";
								$output.="<td>";
								$dir = '';
								if ($attrs['SORTDIR'] == 1) {
									$dir = ' ASC';
								}
								if ($attrs['SORTDIR'] == -1) {
									$dir = ' DESC';
								}
						  		if($attrs['DBTYPE']=='DIGIT') {
						  			$orderby = '*1 '.$dir;
						  		}
						  		else{
						  			$orderby = $dir;
						  		}
								$select = '';
								$resvalues = mysql_query("SELECT * FROM `{$tbl_full_prefix}available_values` 
														  WHERE `TA_XREF_ID`={$attrs['ID']} 
														  ORDER BY `VALUE`{$orderby}");
								$select.="<select name='attr{$attrs['atid']}[]'><option value=''>Не задано</option>";
								while($values = mysql_fetch_array($resvalues)){
									$select.="<option value='{$values['ID']}' >{$values['VALUE']}</option>";
								}
								$select.="</select> ";
								
								if ($attrs['MULTISELECT']) {
									$output.="<div id='multiselect-attr{$attrs['atid']}'>";
									$output.=$select.'<button id="btnaddMS" onclick="addMSvalue('.$attrs['atid'].',\''.addslashes($select).'\' ); return false;">Добавить значение</button>';
									$output.="  <a title='Удалить последнее значение' href='javascript:deleteMSsel({$attrs['atid']})'>[X]</a></div>"; //кнопка удаления селекта, если нам не нужны лишние значения
								}
								else {
									$output.=$select;
								}
								$output.="</td></tr><tr bgcolor='#aaa' style='height: 22px;'><td> </td><td> </td></tr>";
							}
						}
					}
					$output.="</table>
					<input type='hidden' name='step' value='2' />
					<input type='hidden' name='action' value='edit' />
					<input type='hidden' name='id' value='{$id}' />
					<input type='hidden' name='tid' value='{$tid}' />
					<input type='submit' name='submitform' value='Сохранить' />
					<input type='hidden' id='savereload'  name='save' value='0' />
					<input type='button' name='savereload' value='Сохранить и обновить' onclick='saveandreload();' /> 
					</form>
					
					
					";
				}
			break;
			case 'edit':
		  	  $new = 1;
		  	  $rescheck = mysql_query("SELECT `ID` FROM `{$tbl_full_prefix}producttype_{$tid}_static` WHERE `PID`={$id}");
			  if(mysql_num_rows($rescheck)>0) {
			    $new=0;
			  }
			  
			  if ($new) {
			   $qi = "INSERT INTO `{$tbl_full_prefix}producttype_{$tid}_static` SET `PID`={$id}";
				    foreach ($_REQUEST['attrs'] as $attr) {
				    	$attrid = substr($attr, 4);
				    	$resattr = mysql_query("SELECT `MULTISELECT`, `FILE`, `FILE_BEHAVIOR` FROM `{$tbl_full_prefix}type_attr_xref`
				    	WHERE `TYPE_ID`={$tid} AND `ATTR_ID`={$attrid}");
				    	$attrMS = mysql_fetch_array($resattr);
				    	if ($attrMS['MULTISELECT']) {
				    		if(!empty($_REQUEST[$attr])){
					    		//echo "{$_REQUEST[$attr]} - array";
					    		$qi.=", `{$attr}`=''";
					    		foreach($_REQUEST[$attr] as $attrMV) {
					    			$value = mysql_real_escape_string(trim($attrMV));
					    			mysql_query("INSERT INTO `{$tbl_full_prefix}static_multivalues` 
					    						 SET `PID`={$id}, `ATTRID`={$attrid}, `VALUE`='{$value}'");
					    		}
				    		}
				    	} else {
				    		if (is_array($_REQUEST[$attr])){
				    			$value = implode('', $_REQUEST[$attr]);
				    		} else {
				    			$value = $_REQUEST[$attr];
				    		}
				    		if($attrMS['FILE']){
				    			if(!empty($_FILES[$attr]['tmp_name'])) {
				    				$ext = substr($_FILES[$attr]['name'], -3);
				    				$filepath ="assets/uploads/files/attributes/";
									$newFileFullName = $id."_".$attr."_".rand(1,999999999).".".$ext;
									$newFileFullPath = $filepath.$newFileFullName;
				    				if(!copy($_FILES[$attr]['tmp_name'], "./".$newFileFullPath)) {
										echo("Ошибка загрузки файла ".$_FILES[$attr]['name']." для атрибута".$attr);
     								}
				    			}
				    			
				    		}
				    		$qi.=", `{$attr}`='".mysql_real_escape_string(trim($value))."'";	
				    	}                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          
					    
				    }
				    mysql_query($qi) or die(mysql_error()." ".$qi);
				    if($_REQUEST['save']) {
				    	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
							<meta http-equiv="refresh" content="0; url=/service/productmanagement.html?step=2&action=view&id='.$id.'" >';
						die();
				    }
			    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta http-equiv="refresh" content="0; url=/service/productmanagement.html?step=3&id='.$id.'" >';

			  }
			  else {
			   $i = 0;
			   $qi = "UPDATE `{$tbl_full_prefix}producttype_{$tid}_static` SET `PID`={$id}";
				    foreach ($_REQUEST['attrs'] as $attr) {
						$attrid = substr($attr, 4);
				    	$resattr = mysql_query("SELECT `MULTISELECT` FROM `{$tbl_full_prefix}type_attr_xref`
				    	WHERE `TYPE_ID`={$tid} AND `ATTR_ID`={$attrid}");
				    	$attrMS = mysql_fetch_array($resattr);
				    	if ($attrMS['MULTISELECT']) {
				    		if(!empty($_REQUEST[$attr])){
				    			mysql_query("DELETE FROM `{$tbl_full_prefix}static_multivalues` WHERE `PID`={$id} AND `ATTRID`={$attrid}");
					    		$qi.=", `{$attr}`=''";
					    		foreach($_REQUEST[$attr] as $attrMV) {
					    			mysql_query("INSERT INTO `{$tbl_full_prefix}static_multivalues` 
					    						 SET `PID`={$id}, `ATTRID`={$attrid}, `VALUE`='{$attrMV}'");
					    		}			
				    		}
				    	} else {
				    		if (is_array($_REQUEST[$attr])){
				    			$value = implode('', $_REQUEST[$attr]);
				    		} else {
				    			$value = mysql_real_escape_string($_REQUEST[$attr]);
				    		}
				    		if($attrMS['FILE']){
				    			if(!empty($_FILES[$attr]['tmp_name'])) {
				    				$ext = substr($_FILES[$attr]['name'], -3);
				    				$filepath = "assets/uploads/files/attributes/";
									$newFileFullName = $id."_".$attr."_".rand(1,999999999).".".$ext;
									$newFileFullPath = $filepath.$newFileFullName;
				    				if(!copy($_FILES[$attr]['tmp_name'], "./".$newFileFullPath)) {
										echo("Ошибка загрузки файла ".$_FILES[$attr]['name']." для атрибута".$attr);
     								}
				    			}
				    			
				    		}
				    		$qi.=", `{$attr}`='".mysql_real_escape_string(trim($value))."'";	
				    	} 
				    }
				    $qi.=" WHERE `PID`={$id}";
				    mysql_query($qi) or die(mysql_error()." ".$qi);
				    if($_REQUEST['save']) {
				    	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
							<meta http-equiv="refresh" content="0; url=/service/productmanagement.html?step=2&action=view&id='.$id.'" >';
						die();
				    }
			    /*echo "<h1>Изменения сохранены. Окно закроется само.</h1>
				<script type='text/javascript'>
				window.setTimeout(function () {window.parent.Ext.getCmp('windowgrid2').close()}, 1000);
				</script>";*/
				echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					  <meta http-equiv="refresh" content="0; url=/service/productmanagement.html?step=3&id='.$id.'" >';
			  }
		  
			break;
			case 'deletemssel' :
				if(isset($_REQUEST['value'])) {
					$value = mysql_real_escape_string(trim($_REQUEST['value']));
				}
				if(isset($_REQUEST['attrid']) && is_numeric($_REQUEST['attrid'])) {
					$attrid = $_REQUEST['attrid'];
				}
				mysql_query("DELETE FROM `{$tbl_full_prefix}static_multivalues` 
    						 WHERE `PID`={$id} AND `ATTRID`={$attrid} AND `VALUE`='{$value}'");
			break;
		}
	
	
	break;
	
	case 3 :
		switch ($action) {
		  	case 'add' :
		  	  $qattrs = mysql_query("SELECT `ATTR_ID`,`FILE`  
						FROM `{$tbl_full_prefix}type_attr_xref` 
						WHERE `STATIC`=0");
			  while ($attrs = mysql_fetch_array($qattrs)) {
			    $attrsarray['attr'.$attrs['ATTR_ID']] = $attrs['ATTR_ID'];
			    $attrsfile['attr'.$attrs['ATTR_ID']] = $attrs['FILE'];
			  }
		  
			  $qc = "SELECT `ID` FROM `{$tbl_full_prefix}producttype_{$tid}` WHERE `PID`={$id}";
			  foreach ($_REQUEST['attrs'] as $attr) {
			    if(isset($attrsarray[$attr])) {
			      if($attrsfile[$attr]==1){
			      	continue;
			      } else {
				    $qc.=" AND `{$attr}`='{$_REQUEST[$attr]}'";
			      }
			    }
			  }
		  	  $resqc = mysql_query($qc) or die(mysql_error()." ".$qc);
		  	  if(mysql_num_rows($resqc)==0) {
		  	  	$reswh = mysql_query("SELECT `id` FROM `modx_ms_modWarehouse`");
		  	  	while ($wh = mysql_fetch_array($reswh)){
			  	  	$qi = "INSERT INTO `{$tbl_full_prefix}producttype_{$tid}` SET `PID`={$id}, `WID`={$wh['id']}";
				    foreach ($_REQUEST['attrs'] as $attr) {
			    		if($attrsfile[$attr]==1){
				    		if(!empty($_FILES[$attr]['tmp_name'])) {
			    				$ext = substr($_FILES[$attr]['name'], -3);
			    				$filepath = "assets/uploads/files/attributes/";
								$newFileFullName = $id."_".$attr."_".rand(1,999999999).".".$ext;
								$newFileFullPath = $filepath.$newFileFullName;
			    				if(!copy($_FILES[$attr]['tmp_name'], "./".$newFileFullPath)) {
									echo("Ошибка загрузки файла ".$_FILES[$attr]['name']." для атрибута".$attr);
	 							}
	 							$qi.=", `{$attr}`='{$newFileFullPath}'";
			    			}
			    		} else {
			    			$qi.=", `{$attr}`='{$_REQUEST[$attr]}'";
			    		}
					    
				    }
				    mysql_query($qi) or die(mysql_error()." ".$qi);  
		  	  	} 
			    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		    		<meta http-equiv="refresh" content="0; url=/service/productmanagement.html?step=3&id='.$id.'" >';
			  }
			  else {
			      echo 'Данный вариант уже присутствует. Вы будете перенаправлены через 3 секунды.';
			      echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
					<meta http-equiv="refresh" content="3; url=/service/productmanagement.html?step=3&id='.$id.'" >';
			  }
			break;
			
			case 'delete' :
			    $vid = is_numeric($_REQUEST['vid'])?$_REQUEST['vid']:0;
			    /*находим атрибуты с файлами, чтобы удалить содержимое*/
				$resfattr = mysql_query("SELECT `ATTR_ID`, `STATIC` FROM `{$tbl_full_prefix}type_attr_xref` WHERE `TYPE_ID`={$tid} AND `FILE`=1 AND `STATIC`=0");
				if(mysql_num_rows($resfattr)>0) {
					while ($fattr = mysql_fetch_array($resfattr)) {
					  $resffrompid = mysql_query("SELECT `attr{$fattr['ATTR_ID']}` FROM `{$tbl_full_prefix}producttype_{$tid}` WHERE `ID`={$vid}");
					  if(mysql_num_rows($resffrompid)>0) {
					    while($filefrompid = mysql_fetch_array($resffrompid)) {
						  if(!empty($filefrompid['attr'.$fattr['ATTR_ID']])) {
							unlink(MODX_BASE_PATH.$filefrompid['attr'.$fattr['ATTR_ID']]);
						  }
						}
					  }
					}
				}
			    mysql_query("DELETE FROM `{$tbl_full_prefix}producttype_{$tid}` WHERE `ID`={$vid}");
			    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		    		<meta http-equiv="refresh" content="0; url=/service/productmanagement.html?step=3&id='.$id.'" >';
			break;
			default:
				echo "<h2>Этап 3. Варианты товаров</h2><br />Товар: {$goodName}";
				$resattrs = mysql_query("SELECT `{$tbl_full_prefix}type_attr_xref`.*, `{$tbl_full_prefix}attributes`.`NAME`, 
				`{$tbl_full_prefix}attributes`.`ID` as atid  
				FROM `{$tbl_full_prefix}type_attr_xref` 
				RIGHT JOIN `{$tbl_full_prefix}attributes` ON `{$tbl_full_prefix}attributes`.`ID`=`{$tbl_full_prefix}type_attr_xref`.`ATTR_ID` 
				WHERE `{$tbl_full_prefix}type_attr_xref`.`TYPE_ID` = {$tid} AND `{$tbl_full_prefix}type_attr_xref`.`STATIC`=0 
				ORDER BY `{$tbl_full_prefix}type_attr_xref`.`SORTORDER` ASC
				") or die(mysql_error());
				if (mysql_num_rows($resattrs)>0) {
					$output.="<form enctype='multipart/form-data'  action='/service/productmanagement.html' method='POST'>
					<input type='hidden' name='MAX_FILE_SIZE' value='1000000' />
					<table border='0'><caption>Атрибуты</caption>";
					while($attrs = mysql_fetch_array($resattrs)) { 
						$output.="<tr id='{$attrs['atid']}'><td>{$attrs['NAME']} <input type='hidden' name='attrs[]' value='attr{$attrs['atid']}' /></td>";
						if ($attrs['MANUAL'])
						{
							if($attrs['FILE']) {
								$output.="<td><input name='attr{$attrs['atid']}' type='file' id='manual{$attrs['atid']}'/>";									
							} else {
								$output.="<td><textarea name='attr{$attrs['atid']}' id='manual{$attrs['atid']}'></textarea>";
						    }
							$output.="</td></tr>";
						}
						else {
							$dir = '';
							if ($attrs['SORTDIR'] == 1) {
								$dir = ' ASC';
							}
							if ($attrs['SORTDIR'] == -1) {
								$dir = ' DESC';
							}
					  		if($attrs['DBTYPE']=='DIGIT') {
					  			$orderby = '*1 '.$dir;
					  		}
					  		else{
					  			$orderby = $dir;
					  		}
							$resvalues = mysql_query("SELECT * FROM `{$tbl_full_prefix}available_values` 
							WHERE `TA_XREF_ID`={$attrs['ID']} ORDER BY `VALUE`{$orderby}");
							$output.='<td>
							<table border="0"><tr>';
							while($values = mysql_fetch_array($resvalues)){
								$output.="<td id='{$values['ID']}' style='border: solid #5e0472 1px; background-color: #fff;' >
								<input type='radio' name='attr{$attrs['atid']}' value='{$values['ID']}' />{$values['VALUE']}</td>";
							}
							$output.="</tr></table></td></tr>";
						}
					}
					$output.="</table>
					<input type='hidden' name='step' value='3' />
					<input type='hidden' name='action' value='add' />
					<input type='hidden' name='id' value='{$id}' />
					<input type='hidden' name='tid' value='{$tid}' />
					<input type='submit' name='submit' value='Создать' /></form>";
				
				}
		  		$qats=mysql_query("SELECT `{$tbl_full_prefix}attributes`.*,  
							  `{$tbl_full_prefix}type_attr_xref`.`MANUAL`, `{$tbl_full_prefix}type_attr_xref`.`FILE`, `{$tbl_full_prefix}type_attr_xref`.`FILE_BEHAVIOR` 
						   FROM `{$tbl_full_prefix}attributes`, 
							`{$tbl_full_prefix}type_attr_xref` 
				WHERE `{$tbl_full_prefix}attributes`.`ID` = `{$tbl_full_prefix}type_attr_xref`.`ATTR_ID` AND
				`{$tbl_full_prefix}type_attr_xref`.`TYPE_ID`={$tid} AND 
				`{$tbl_full_prefix}type_attr_xref`.`STATIC`=0");
				while ($ats = mysql_fetch_array($qats)) {
					$atids[] = $ats['ID'];
					$atnames[] = $ats['NAME'];
				  	$atmanual[] = $ats['MANUAL'];
				  	$atfile[]=$ats['FILE'];
				  	$atfileb[]=$ats['FILE_BEHAVIOR'];
				}
		  		$resproducts = mysql_query("SELECT * FROM `{$tbl_full_prefix}producttype_{$tid}` WHERE `PID`=".$id);
				if(mysql_num_rows($resproducts)>0) {
				  $output.="<script type='text/javascript'>
					      function deletevariant(vid) {
						    if (window.XMLHttpRequest)
						      {// code for IE7+, Firefox, Chrome, Opera, Safari
							      xmlhttp=new XMLHttpRequest();
						      }
						    else
						      {// code for IE6, IE5
						      xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
						      }
						    xmlhttp.onreadystatechange=function()
						      {	
							if (xmlhttp.readyState==4 && xmlhttp.status==200)
							{
							  document.getElementById('pr'+vid).parentNode.removeChild(document.getElementById('pr'+vid));
							  var table = document.getElementById('products');
							  var trs=table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
							  if(trs.length == 1)
							  {
								  table.parentNode.removeChild(table);
							  }
							}	
						      }
						    xmlhttp.open('GET', 
						      '/service/productmanagement.html?step=3&action=delete&vid='+vid+'&id={$id}'+'&tid={$tid}',true);
						    xmlhttp.send();
	      
					      }		   
				      </script>
					      <table id='products' border='1'><caption>Существующие варианты продукта</caption><tr>";
				  foreach ($atnames as $atname) {
				    $output.="<td>{$atname}</td>"; 
				  }
				  $output.="<td style='visibility:hidden;'></td></tr>";
				  while ($products = mysql_fetch_array($resproducts)) {
				    $output.="<tr id='pr{$products['ID']}'>";
				    foreach ($atids as $key=>$attrid) {
				      if($atmanual[$key]!=1) {
					$resavval = mysql_query("SELECT `VALUE` FROM `{$tbl_full_prefix}available_values` 
					  WHERE `ID`=".$products['attr'.$attrid]);
					if(mysql_num_rows($resavval)!=0){
					  $avval = mysql_fetch_array($resavval);
					  $output.="<td>".nl2br($avval['VALUE'])."</td>";
					}
				      }
				      else {
				      	if($atfile[$key]==1 && !empty($products['attr'.$attrid])) {
				      		if(($atfileb[$key]=='mainimage' || $atfileb[$key]=='otherimage')) {
				      			$output.="<td><img src='/{$products['attr'.$attrid]}' height='40' /></td>";
				      		} else {
				      			$output.="<td><a href='/{$products['attr'.$attrid]}' height='40' target='_blank'>Файл</a></td>";
				      		}
				      	} else {
				      		$output.="<td>".nl2br($products['attr'.$attrid])."</td>";
				      	}
					  	
				      }
				    }
				    $output.="<td><span style='cursor:pointer;' onclick='deletevariant(".$products['ID'].")'>
					      <img src='/assets/templates/main/img/deleteButton.png' title='Удалить' alt='Удалить'/>
					      </span></td></tr>";
				  }
				  $output.="</table>";
				}
				$output.="<p><a href='/service/goodsrush.html?step=1&action=view'><<-- Rush-режим. Этап 1.</a></p>";
		    
			break;
			
		}
	break;
}
echo $output;
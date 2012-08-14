<?php
if(!isset($_SESSION['modx.user.contextTokens']['mgr'])) die('Доступ запрещен.');
include('./core/config/config.inc.php');
mysql_connect ($database_server, $database_user, $database_password);
mysql_set_charset($database_connection_charset);
mysql_select_db ($dbase);
if(isset($_REQUEST['tid']) && is_numeric($_REQUEST['tid']) )
  $tid = $_REQUEST['tid'];
if(isset($_REQUEST['atid']) && is_numeric($_REQUEST['atid']) )
  $atid = $_REQUEST['atid'];
if(isset($_REQUEST['xrid']) && is_numeric($_REQUEST['xrid']) )
  $xrid = $_REQUEST['xrid'];
else $xrid = 0;
if(isset($_REQUEST['vid']) && is_numeric($_REQUEST['vid']) )
  $vid = $_REQUEST['vid'];
if(isset($_REQUEST['action']))
  $action = $_REQUEST['action'];
if(isset($_REQUEST['table']))
  $table = $_REQUEST['table'];
else {
  $table = '';
}
if(isset($_REQUEST['mode']))
  $mode = $_REQUEST['mode'];
$output='';
$dirselect = "<br /><div id='dirselect'>Направление сортировки: <select name='DIR'><option value='-1'>По убыванию</option><option value='1'>По возрастанию</option></select></div>";
$filebehaviorselect = "<br /><div id='fbs' style='display:none;'> Роль файла: <select name='fbehavior'><option value='mainimage'>Главное изображение</option><option value='otherimage'>Дополнительное изображение</option><option value='download'>Скачиваемый файл</option></select></div>";
switch ($table) {
  case 'type':
    switch ($action) {      
      	  case 'view':
			switch($mode) {
			  case 'new':
				  $output.="Введите наименование типа:<br /><form action='/service/typesmanagement.html' method='POST'>
					<input type='text' name='name' />
					<input type='hidden' name='table' value='type' />
					<input type='hidden' name='action' value='new' />
					<input type='submit' name='submit' value='Создать' /></form>
					<a href='/service/typesmanagement.html'>Назад</a>
				  ";
			  break;	      
			  case 'old':
				$resinfo = mysql_query("SELECT * FROM `{$tbl_full_prefix}goods_types` WHERE `ID`=".$tid);
				$info = mysql_fetch_array($resinfo);
				$output.="Наименование типа:<br /><form action='/service/typesmanagement.html' method='POST'>
				<input type='text' name='name' value='".$info['NAME']."' />
				<input type='hidden' name='table' value='type' />
				<input type='hidden' name='action' value='edit' />
				<input type='hidden' name='tid' value='{$tid}' />
				<input type='submit' name='submit' value='Изменить' /></form>
				<a href='/service/typesmanagement.html'>Назад</a>			
				";
				$resattrs = mysql_query("SELECT * FROM `{$tbl_full_prefix}attributes` WHERE `ID` NOT IN (SELECT `ATTR_ID` FROM `{$tbl_full_prefix}type_attr_xref` WHERE `TYPE_ID`={$tid})");
				$option = "<select name='atid'><option disabled selected>Выберите атрибут</option>";
				while ($attrs = mysql_fetch_array($resattrs)) {
					$option.="<option value='".$attrs['ID']."'>".$attrs['NAME']."</option>";
				}
				$option.="</select>";
				$output.="<script type='text/javascript'>
				  function deletexref (tid, atid) {	
					  $.post(
						  '/service/typesmanagement.html',
						  {
						  	table: 'xref',
						    action: 'delete',
						    tid: tid,
						    atid: atid
						  },
						  function(data) {
						  	window.location.reload();
						  }
					  );
					}
					function togglechman () {  // переключение чекбокса Ручной ввод
		    			var check = $('#chman').attr('checked');
		    			if (check == 'checked') {
		    				$('#chms').attr('disabled', 'disabled');
		    				$('#chms').removeAttr('checked');
		    				$('#dirselect').hide();
		    			} else {
		    				$('#chms').removeAttr('disabled');
		    				$('#dirselect').show();
		    			}
		    		}
		    		function togglechms () {  //переключение чекбокса Мультиселект
		    			var check = $('#chms').attr('checked');
		    			if (check == 'checked') {
		    				$('#chman').removeAttr('checked');
		    				$('#chman').attr('disabled', 'disabled');
		    				$('#chstatic').attr('checked', 'checked');
		    				$('#chstatic').attr('readonly', 'readonly');
		    			} else {
		    				$('#chman').removeAttr('disabled');
		    				$('#chstatic').removeAttr('readonly');
		    				$('#chstatic').removeAttr('checked');
		    			}
		    		}
		    		function togglechfile () {  //переключение чекбокса Файл
		    			var check = $('#chfile').attr('checked');
		    			if (check == 'checked') {
		    				$('#chman').attr('checked', 'checked');
		    				$('#chman').attr('readonly', 'readonly');
		    				$('#chms').removeAttr('checked');
		    				$('#chms').attr('disabled', 'disabled');
		    				$('#fbs').show();
		    			} else {
		    				$('#chms').removeAttr('disabled');
		    				$('#chman').removeAttr('checked');
		    				$('#chman').removeAttr('readonly');
		    				$('#fbs').hide();
		    			}
		    		}
					function addemptyxref (tid) {
						var table = document.getElementById('attrs');
						var tbody = table.getElementsByTagName('tbody')[0];
						var tr = document.createElement('tr');
						tr.innerHTML = \"<td><form action='/service/typesmanagement.html' method='POST'>".$option." \" +
						\" <br />Ручной ввод?: <input type='checkbox' id='chman' name='manual' value='1' onchange='togglechman()'/> \" +
				    	\" <div id='multiselect'>Мультивыбор?: \" +
				    	\"<input type='checkbox' id='chms' name='multiselect' value='1' onchange='togglechms()'/></div>\" +
				    	\" ".$dirselect."<br /> Статичный?: <input type='checkbox' id='chstatic' name='static' value='1' />\" +
				    	\" <br />Является файлом? <input type='checkbox' id='chfile' name='fileflag' value='1' onchange='togglechfile()'/> ".$filebehaviorselect."\" +
				    	\"<br /><select name='type'><option disabled>Тип поля</option><option value='TEXT'>Текстовое</option><option value='DIGIT'>Числовое</option></select><br />Css-класс<input type='text' name='css' /><input type='hidden' name='action' value='add' /><input type='hidden' name='table' value='xref' /><input type='hidden' name='tid' value='\"+tid+\"' /><br /><input type='submit' name='submit' value='Сохранить' /></form><a href='javascript:window.location.reload()'>Отменить</a></td>\";
						tbody.appendChild(tr);
					}
					function changeorder (tid, atid, value, staticattr) {
						$.post(
						  '/service/ajaxwithjson.html',
						  {
						    action: 'changeorder',
						    tid: tid,
						    atid: atid,
						    value: value,
						    st: staticattr
						  },
						  changeordersuccess,
						  'json'
						);
					}
					function changeordersuccess(data)
					{ 
					  // Здесь мы получаем данные, отправленные сервером и выводим их на экран.
					  if(data.result == 'success') {
					    window.location.reload();
					  }
					  if(data.result == 'failure') {
					  	alert(obj.error);
					  }
					}
				</script>";
				$output.="<table id='attrs' border='1'><tbody>";
				$resdata = mysql_query("SELECT `{$tbl_full_prefix}type_attr_xref`.*, `{$tbl_full_prefix}attributes`.`NAME` 
					FROM `{$tbl_full_prefix}type_attr_xref` 
					RIGHT JOIN `{$tbl_full_prefix}attributes` on `{$tbl_full_prefix}attributes`.`ID`=`{$tbl_full_prefix}type_attr_xref`.`ATTR_ID` 
					WHERE `{$tbl_full_prefix}type_attr_xref`.`TYPE_ID`=".$tid." 
					ORDER BY `{$tbl_full_prefix}type_attr_xref`.`SORTORDER` ASC ");
				if(mysql_num_rows($resdata)>0){
				  $output2="<tr><td><table border='0'><caption>Атрибуты для вариантов</caption>";
				  $output1="<td><table border='0'><caption>Статичные атрибуты <br /> Порядок сортировки должен быть >0!</caption>";
					while($data = mysql_fetch_array($resdata)) {
						if($data['STATIC']) {
						  $output1.="<tr><td >".$data['NAME']."</td>
						  <td width='120'>
						  	<a href='javascript:changeorder({$tid}, {$data['ATTR_ID']}, -1, 1);'>&darr;</a>
						  	<input type='text' size='1' name='sortorder' id='sortorder{$data['ATTR_ID']}' value='{$data['SORTORDER']}' 
						  	  onchange='changeorder({$tid}, {$data['ATTR_ID']}, this.value, 1);'/>
						  	<a href='javascript:changeorder({$tid}, {$data['ATTR_ID']}, 0, 1);'>&uarr;</a>

						  </td>
						  <td>";
						  if(!$data['MANUAL']) {
							$output1.="<a href='/service/typesmanagement.html?table=avval&action=view&tid=".$tid."&atid=".$data['ATTR_ID']."'>Допустимые значения</a>";  
						  }
							$output1.="<a href='javascript:deletexref(".$tid.",".$data['ATTR_ID'].")'><img src='/assets/templates/main/img/deleteButton.png' title='Удалить привязку' alt='Удалить привязку'/></a>
							</td></tr>";
						}
						else {
						  $output2.="<tr><td>".$data['NAME']."</td>
						  <td width='120'>
						  	<a href='javascript:changeorder({$tid}, {$data['ATTR_ID']}, -1, 0); return false;'>&darr;</a>&nbsp;
						  	<input type='text' size='1' name='sortorder' id='sortorder{$data['ATTR_ID']}' value='{$data['SORTORDER']}' 
						  	  onchange='changeorder({$tid}, {$data['ATTR_ID']}, this.value, 0);'/>&nbsp;
						  	<a href='javascript:changeorder({$tid}, {$data['ATTR_ID']}, 0, 0); return false;'>&uarr;</a>&nbsp;

						  </td>
						  <td>";
						  if(!$data['MANUAL']) {
							$output2.="<a href='/service/typesmanagement.html?table=avval&action=view&tid=".$tid."&atid=".$data['ATTR_ID']."'>Допустимые значения</a>";  
						  }
							$output2.="<a href='javascript:deletexref(".$tid.",".$data['ATTR_ID'].")'>
								<img src='/assets/templates/main/img/deleteButton.png' title='Удалить привязку' alt='Удалить привязку'/></a>
							</td></tr>";
						}
					}
					$output1.="</table></td></tr></table>";
					$output2.="</table></td>";
					$output.=$output2.$output1;			
				}
				$output.="</tbody></table><br /><a href='javascript:addemptyxref(".$tid.");'>Добавить привязку</a>
				<a href='/service/typesmanagement.html?table=attr&action=view&mode=new&tid=".$tid."'>Создать новый атрибут</a>
				";
			  break;	      
			}
  	  break;
	  case 'new':
		mysql_query ("INSERT INTO `{$tbl_full_prefix}goods_types` SET `NAME`='".$_REQUEST['name']."'");
		$tid = mysql_insert_id();
		mysql_query("CREATE TABLE `{$tbl_full_prefix}producttype_".$tid."` (
		`ID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
		`PID` INT(11) UNSIGNED NOT NULL,
		`WID` INT(11) UNSIGNED NOT NULL) TYPE=MyISAM;");
		mysql_query("CREATE TABLE `{$tbl_full_prefix}producttype_".$tid."_static` (
		`ID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
		`PID` INT(11) UNSIGNED NOT NULL) TYPE=MyISAM;");
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="refresh" content="0; url=/service/typesmanagement.html?table=type&action=view&mode=old&tid='.$tid.'" >';
  	  break;
      case 'edit':
		if(isset($_REQUEST['name'])) {
			mysql_query("UPDATE `{$tbl_full_prefix}goods_types` SET `NAME`='".$_REQUEST['name']."' WHERE `ID`=".$tid);
		}
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="refresh" content="0; url=/service/typesmanagement.html?table=type&action=view&mode=old&tid='.$tid.'" >';
  	  break;
      case 'delete':
		mysql_query("DELETE FROM `{$tbl_full_prefix}goods_types` WHERE `ID`=".$tid);
		mysql_query("DROP TABLE `{$tbl_full_prefix}producttype_".$tid."`");
		mysql_query("DROP TABLE `{$tbl_full_prefix}producttype_".$tid."_static`");
		$resxref = mysql_query("SELECT `ID` FROM `{$tbl_full_prefix}type_attr_xref` WHERE `TYPE_ID`=".$tid);
		if(mysql_num_rows($resxref)>0) {
		  while($xref = mysql_fetch_array($resxref)) {
		  if(!$xref['MANUAL']) {
		    mysql_query("DELETE FROM `{$tbl_full_prefix}available_values` WHERE `TA_XREF_ID`=".$xref['ID']);
		  }
		    mysql_query("DELETE FROM `{$tbl_full_prefix}type_attr_xref` WHERE `ID`=".$xref['ID']." AND `TYPE_ID`=".$tid);
		  }
		}
  	  break;
    }
  break;
  case 'attr':
  	switch ($action) {
      case 'view':
	    $output.= "
		<script>
		function checkattralias(string){
		  	var re = /[\w\s\d.]+/i;
			var index = string.search (re);
			if (index > -1) {
				
				$('#aliaserror').html('Недопустимые символы в строке. <br /> Разрешены: латинский алфавит, \'.\', \' \', \'_\'.');
				$('#aliaserror').show();
			} else {
				$('#aliaserror').hide();
			}		
		}
		</script>
		";
	  	switch($mode) {
		  	case 'new':
				$output.="<form action='/service/typesmanagement.html' method='POST'>
				<table border='0' id='aliastable'><tr>
				<td>Введите наименование атрибута:</td><td><input type='text' name='name' /></td></tr>
				<tr><td>Алиас (краткое название в английской раскладке):</td><td><input type='text' name='alias' onchange='checkattralias(this.value)'/><div id='aliaserror' style='display:none;'></div></td></tr>
				</table>
				<input type='hidden' name='table' value='attr' />
				<input type='hidden' name='action' value='new' />";
		  if($tid) {
		    $output.="
		    		<script>
					function togglechman () {  // переключение чекбокса Ручной ввод
		    			var check = $('#chman').attr('checked');
		    			if (check == 'checked') {
		    				$('#chms').attr('disabled', 'disabled');
		    				$('#chms').removeAttr('checked');
		    				$('#dirselect').hide();
		    			} else {
		    				$('#chms').removeAttr('disabled');
		    				$('#dirselect').show();
		    			}
		    		}
		    		function togglechms () {  //переключение чекбокса Мультиселект
		    			var check = $('#chms').attr('checked');
		    			if (check == 'checked') {
		    				$('#chman').removeAttr('checked');
		    				$('#chman').attr('disabled', 'disabled');
		    				$('#chstatic').attr('checked', 'checked');
		    				$('#chstatic').attr('readonly', 'readonly');
		    			} else {
		    				$('#chman').removeAttr('disabled');
		    				$('#chstatic').removeAttr('readonly');
		    				$('#chstatic').removeAttr('checked');
		    			}
		    		}
		    		function togglechfile () {  //переключение чекбокса Файл
		    			var check = $('#chfile').attr('checked');
		    			if (check == 'checked') {
		    				$('#chman').attr('checked', 'checked');
		    				$('#chman').attr('readonly', 'readonly');
		    				$('#chms').removeAttr('checked');
		    				$('#chms').attr('disabled', 'disabled');
		    				$('#fbs').show();
		    			} else {
		    				$('#chms').removeAttr('disabled');
		    				$('#chman').removeAttr('checked');
		    				$('#chman').removeAttr('readonly');
		    				$('#fbs').hide();
		    			}
		    		}
		    		</script>
				    <br />Ручной ввод?: <input type='checkbox' id='chman' name='manual' value='1' onchange='togglechman()'/>
				    	<div id='multiselect'>Мультивыбор?: 
				    	<input type='checkbox' id='chms' name='multiselect' value='1' onchange='togglechms()'/></div>
				    	{$dirselect}<br /> Статичный?: <input type='checkbox' id='chstatic' name='static' value='1' />
				    	<br />Является файлом? <input type='checkbox' id='chfile' name='fileflag' value='1' onchange='togglechfile()'/>{$filebehaviorselect}
					<br /><select name='type'><option disabled>Тип поля</option>
					<option value='TEXT'>Текстовое</option><option value='DIGIT'>Числовое</option><option value='BOOL'>Чекбокс</option>
					</select><br />
					<input type='text' name='css' />
			";
		    $output.="<input type='hidden' name='tid' value='{$tid}' />";
		  }
				$output.="<input type='submit' name='submit' value='Создать' /></form>
				<a href='/service/typesmanagement.html'>Назад</a>
		  		";
			break;
		
			case 'old':
				$resinfo = mysql_query("SELECT * FROM `{$tbl_full_prefix}attributes` WHERE `ID`=".$atid);
				$info = mysql_fetch_array($resinfo);   
				$output.="<form action='/service/typesmanagement.html' method='POST'>
				<table border='0' id='aliastable'><tr>
				<td>Наименование атрибута:</td><td><input type='text' name='name' value='".$info['NAME']."' /></td><div id='aliaserror' style='display:none;'></div></tr>
				<tr><td>Алиас (краткое название в английской раскладке):</td>
				<td><input type='text' name='alias' value='".$info['ALIAS']."'/></td></tr>
				</table>
				
				<input type='hidden' name='table' value='attr' />
				<input type='hidden' name='action' value='edit' />
				<input type='hidden' name='atid' value='{$atid}' />
				<input type='submit' name='submit' value='Изменить' /></form>
				<a href='/service/typesmanagement.html'>Назад</a>
				";
		}
  	  break;
	  case 'new':
		$name = mysql_real_escape_string($_REQUEST['name']);
		$alias = mysql_real_escape_string($_REQUEST['alias']);
  		mysql_query ("INSERT INTO `{$tbl_full_prefix}attributes` SET `NAME`='{$name}', `ALIAS`='{$alias}'");
		$atid = mysql_insert_id();
		if($tid) {
		  $manual ='';
		  $static ='';
		  $type = '';
		  $css = '';
		  $index = '';
		  $typeq='';
		  $cssq='';
		  $multiselect = '';
		  $file = '';
		  $fbh = '';
		  $fileq='';
		  if (isset($_REQUEST['fileflag']) && $_REQUEST['fileflag']==1) {
		  	$file = 1;
		  	$fbh = $_REQUEST['fbehavior'];
		  	$fileq= ", `FILE`=1, `FILE_BEHAVIOR`='{$fbh}'";
		  }
		  if (isset($_REQUEST['css'])) {
		    $css = mysql_real_escape_string($_REQUEST['css']);
		    $cssq = ", `CSS`='{$css}'";
		  }
		  if (isset($_REQUEST['manual'])) {
		     $manual = ", `MANUAL`=".$_REQUEST['manual'];
		  }
		  if (isset($_REQUEST['static'])) {
		     $static = ", `STATIC`=".$_REQUEST['static'];
		  }
		  if (isset($_REQUEST['multiselect'])) {
		     $multiselect = ", `MULTISELECT`=".$_REQUEST['multiselect'];
		  }
		  if ($_REQUEST['type']=='TEXT') {
		    $typeq= ", `DBTYPE`='TEXT'";
		    $type = 'TEXT';
		  }
		  if ($_REQUEST['type']=='DIGIT') {
		    $type = 'VARCHAR(255)';
		    $typeq= ", `DBTYPE`='DIGIT'";
		  }
		  if ($_REQUEST['type']=='BOOL') {
		    $type = 'INT(1)';
		    $typeq= ", `DBTYPE`='BOOL'";
		  }
		  if (isset($_REQUEST['DIR'])) {
		     $dir = ", `SORTDIR`=".$_REQUEST['DIR'];
		  }
		  mysql_query("INSERT INTO `{$tbl_full_prefix}type_attr_xref` SET `TYPE_ID`={$tid}, `ATTR_ID`={$atid}{$manual}{$static}{$typeq}{$cssq}{$multiselect}{$dir}{$fileq}");
		  if ($_REQUEST['static']) {
			  mysql_query("ALTER TABLE `{$tbl_full_prefix}producttype_{$tid}_static` ADD COLUMN `attr{$atid}` {$type} NOT NULL DEFAULT  ''");
			  mysql_query("ALTER TABLE `{$tbl_full_prefix}producttype_{$tid}_static` ADD FULLTEXT (`attr{$atid}`)");
		  }
		  else {
			  mysql_query("ALTER TABLE `{$tbl_full_prefix}producttype_{$tid}` ADD COLUMN `attr{$atid}` {$type} NOT NULL DEFAULT  ''");
			  mysql_query("ALTER TABLE `{$tbl_full_prefix}producttype_{$tid}` ADD FULLTEXT (`attr{$atid}`)");
		  }
		  if($_REQUEST['manual']) {
			  echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			  <meta http-equiv="refresh" content="0; url=/service/typesmanagement.html?table=type&mode=old&action=view&tid='.$tid.'" >';
		  }
		  else {
			   echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			  <meta http-equiv="refresh" content="0; url=/service/typesmanagement.html?table=avval&action=view&tid='.$tid.'&atid='.$atid.'" >';
		  }	
		}
	  	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="refresh" content="0; url=/service/typesmanagement.html" >';
  	  break;
      case 'edit':
      	$name = mysql_real_escape_string($_REQUEST['name']);
		$alias = mysql_real_escape_string($_REQUEST['alias']);
  		mysql_query("UPDATE `{$tbl_full_prefix}attributes` SET `NAME`='{$name}', `ALIAS`='{$alias}' WHERE `ID`=".$atid);
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="refresh" content="0; url=/service/typesmanagement.html" >';
  	  break;
      case 'delete':
	  	mysql_query("DELETE FROM `{$tbl_full_prefix}attributes` WHERE `ID`=".$atid);
	   	$rescheck = mysql_query("SELECT `ID`, `TYPE_ID`, `STATIC`, `MANUAL` FROM `{$tbl_full_prefix}type_attr_xref` WHERE `ATTR_ID`=".$atid);
		if(mysql_num_rows($rescheck)>0) {
		  while ($check = mysql_fetch_array($rescheck)){
			if (!$check['MANUAL']) {
				mysql_query("DELETE FROM `{$tbl_full_prefix}available_values` WHERE `TA_XREF_ID`=".$check['ID']);
			}
			if ($check['STATIC']) {
				mysql_query("ALTER TABLE `{$tbl_full_prefix}producttype_".$check['TYPE_ID']."_static` DROP `attr{$atid}`");
			}
			else {
				mysql_query("ALTER TABLE `{$tbl_full_prefix}producttype_".$check['TYPE_ID']."` DROP `attr{$atid}`");
			}
			
			mysql_query("DELETE FROM `{$tbl_full_prefix}type_attr_xref` WHERE `ID`=".$check['ID']." AND `ATTR_ID`=".$atid);
	
		  }	
		}
  	  break;      
    }
  break;
  case 'avval' :
  	switch ($action) {
      case 'view':
		if ($xrid) {
			$resid = mysql_query("SELECT * FROM `{$tbl_full_prefix}type_attr_xref` WHERE `ID`=".$xrid);
			$id = mysql_fetch_array($resid);
			$tid = $id['TYPE_ID'];
			$atid = $id['ATTR_ID'];
		}
			$output.="<script type='text/javascript'>
				function deletevalue (vid) {
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
						  window.location.reload();
					  }
				  }
				  xmlhttp.open('GET','/service/typesmanagement.html?table=avval&action=delete&vid='+vid,true);
				  xmlhttp.send();
					
				}
				
				function addemptyvalue (xrid) {
				var table = document.getElementById('values');
				var tbody = table.getElementsByTagName('tbody')[0];
				var tr = document.createElement('tr');
				tr.innerHTML = \"<td><form action='/service/typesmanagement.html' method='POST'><input type='text' id='value' name='value' value='' /><input type='hidden' name='table' value='avval' /><input type='hidden' name='action' value='new' /><input type='hidden' name='xrid' value='\"+xrid+\"' /><input type='submit' name='submit' value='Добавить' /></form></td><td><a href='javascript:window.location.reload()'>Отменить</a></td>\";
				tbody.appendChild(tr);
				document.getElementById('value').focus();
			}
			
			
			
			</script>";
			$resname1 = mysql_query("SELECT `NAME` FROM `{$tbl_full_prefix}goods_types` WHERE `ID`=".$tid);
			$name1 = mysql_fetch_array($resname1);
			$resname2 = mysql_query("SELECT `NAME` FROM `{$tbl_full_prefix}attributes` WHERE `ID`=".$atid);
			$name2 = mysql_fetch_array($resname2);
	  		$resxrid = mysql_query("SELECT `ID`, `DBTYPE` FROM `{$tbl_full_prefix}type_attr_xref` WHERE `TYPE_ID`={$tid} AND `ATTR_ID`={$atid}");
			$rxrid = mysql_fetch_array($resxrid);
	  		$xrid = $rxrid['ID'];
	  		$dir = '';
			if ($attrs['SORTDIR'] == 1) {
				$dir = ' ASC';
			}
			if ($attrs['SORTDIR'] == -1) {
				$dir = ' DESC';
			}
	  		if($rxrid['DBTYPE']=='DIGIT') {
	  			$orderby = '*1 '.$dir;
	  		}
	  		else{
	  			$orderby = $dir;
	  		}
	  		$output.="Тип - \"".$name1['NAME']."\"; Атрибут - \"".$name2['NAME']."\"<br />";
			$resdata = mysql_query("SELECT * FROM `{$tbl_full_prefix}available_values` WHERE `TA_XREF_ID` IN 
			(SELECT `ID` FROM `{$tbl_full_prefix}type_attr_xref` WHERE `TYPE_ID`=".$tid." AND `ATTR_ID`=".$atid.") 
			ORDER BY `VALUE`{$orderby}
			");
			$output.="<a href='javascript:addemptyvalue({$xrid})'>Добавить значение</a><br />
			<a href='/service/typesmanagement.html?table=type&mode=old&action=view&tid={$tid}'>Назад</a>
			<table border='0' width='200' id='values'><tbody>";
			if (mysql_num_rows($resdata)>0) {
			    $output.="<caption>Возможные значения атрибута</caption>";
			    while ($data = mysql_fetch_array($resdata)) {
					$output.="<tr><td>".$data['VALUE']."   <a href='javascript:deletevalue(".$data['ID'].")'>
					<img src='/assets/templates/main/img/deleteButton.png' title='Удалить' alt='Удалить'/>
					</a></td></tr>";
			    }
			}
			$output.="</tbody></table>";
			
  	  break;
	  case 'new':
			$value = mysql_real_escape_string($_REQUEST['value']);
			mysql_query("INSERT INTO `{$tbl_full_prefix}available_values` SET `TA_XREF_ID`={$xrid}, `VALUE`='{$value}'");
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
			<meta http-equiv="refresh" content="0; url=/service/typesmanagement.html?table=avval&action=view&xrid='.$xrid.'" >';
  	  break;
      case 'delete':
			mysql_query("DELETE FROM `{$tbl_full_prefix}available_values` WHERE `ID`=".$vid);
  	  break;
      
      
      
    }
  break;
  
  case 'xref' :
	switch ($action) {
      
	  case 'add':
	    $manual ='';
	    $static ='';
	    $type = '';
	    $css = '';
	    $index = '';
	    $typeq='';
	    $cssq='';
	    $multiselect = '';
	    $dir = '';
	    $file = '';
		$fbh = '';
		$fileq='';
		if (isset($_REQUEST['fileflag']) && $_REQUEST['fileflag']==1) {
		  $file = 1;
		  $fbh = $_REQUEST['fbehavior'];
		  $fileq= ", `FILE`=1, `FILE_BEHAVIOR`='{$fbh}'";
		}
	    if (isset($_REQUEST['css'])) {
	      $css = mysql_real_escape_string($_REQUEST['css']);
	      $cssq = ", `CSS`='{$css}'";
	    }
	    if (isset($_REQUEST['multiselect'])) {
		     $multiselect = ", `MULTISELECT`=".$_REQUEST['multiselect'];
		  }
	    if (isset($_REQUEST['manual'])) {
	       $manual = ", `MANUAL`=".$_REQUEST['manual'];
	    }
	    if (isset($_REQUEST['static'])) {
	       $static = ", `STATIC`=".$_REQUEST['static'];
	    }
	    if ($_REQUEST['type']=='TEXT') {
	      $typeq= ", `DBTYPE`='TEXT'";
	      $type = 'TEXT';
	    }
	    if ($_REQUEST['type']=='DIGIT') {
	      $type = 'VARCHAR(255)';
	      $typeq= ", `DBTYPE`='DIGIT'";
	    }
	    if ($_REQUEST['type']=='BOOL') {
	      $type = 'INT(1)';
	      $typeq= ", `DBTYPE`='BOOL'";
	    }
	    if (isset($_REQUEST['DIR'])) {
	       $dir = ", `SORTDIR`=".$_REQUEST['DIR'];
	    }
	    mysql_query("INSERT INTO `{$tbl_full_prefix}type_attr_xref` SET `TYPE_ID`={$tid}, `ATTR_ID`={$atid}{$manual}{$static}{$typeq}{$cssq}{$multiselect}{$dir}{$fileq}") 
	      or $modx->log(modX::LOG_LEVEL_ERROR, "ошибка привязки атрибута ".mysql_error());
	    if($static) {
			$resexist = mysql_query("SHOW COLUMNS FROM `{$tbl_full_prefix}producttype_{$tid}_static` LIKE 'attr{$atid}'");
			if(mysql_num_rows($resexist)==0){
			  mysql_query("ALTER TABLE `{$tbl_full_prefix}producttype_{$tid}_static` ADD COLUMN `attr{$atid}` {$type} NOT NULL DEFAULT  ''");
			  mysql_query("ALTER TABLE `{$tbl_full_prefix}producttype_{$tid}_static` ADD FULLTEXT (`attr{$atid}`)");
			}
		}
		else {
			$resexist = mysql_query("SHOW COLUMNS FROM `{$tbl_full_prefix}producttype_{$tid}` LIKE 'attr{$atid}'");
			if(mysql_num_rows($resexist)==0){
			  mysql_query("ALTER TABLE `{$tbl_full_prefix}producttype_{$tid}` ADD COLUMN `attr{$atid}` {$type} NOT NULL DEFAULT  ''");
			  mysql_query("ALTER TABLE `{$tbl_full_prefix}producttype_{$tid}` ADD FULLTEXT (`attr{$atid}`)");
			}
		}
	    if($manual) {
		  echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		  <meta http-equiv="refresh" content="0; url=/service/typesmanagement.html?table=type&mode=old&action=view&tid='.$tid.'" >';
	    }
	    else {
	      echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		  <meta http-equiv="refresh" content="0; url=/service/typesmanagement.html?table=avval&action=view&tid='.$tid.'&atid='.$atid.'" >';
	    }
  	  break;
      case 'delete':
		  $rescheck = mysql_query("SELECT `ID`, `MANUAL`, `STATIC` FROM `{$tbl_full_prefix}type_attr_xref` WHERE `TYPE_ID`={$tid} AND `ATTR_ID`=".$atid);
		  $check = mysql_fetch_array($rescheck);
		  if (!$check['MANUAL']) {
			mysql_query("DELETE FROM `{$tbl_full_prefix}available_values` WHERE `TA_XREF_ID`=".$check['ID']);
		  }
		  if ($check['STATIC']) {
			  $resexist = mysql_query("SHOW COLUMNS FROM `{$tbl_full_prefix}producttype_{$tid}_static` LIKE 'attr{$atid}'");
			  if(mysql_num_rows($resexist)){
				mysql_query("ALTER TABLE `{$tbl_full_prefix}producttype_{$tid}_static` DROP `attr{$atid}`");
			  }
		  }
		  else {
			  $resexist = mysql_query("SHOW COLUMNS FROM `{$tbl_full_prefix}producttype_{$tid}` LIKE 'attr{$atid}'");
			  if(mysql_num_rows($resexist)){
				mysql_query("ALTER TABLE `{$tbl_full_prefix}producttype_{$tid}` DROP `attr{$atid}`");
			  }
		  }
		  mysql_query("DELETE FROM `{$tbl_full_prefix}type_attr_xref` WHERE `TYPE_ID`={$tid} AND `ATTR_ID`={$atid}");
  	  break;
    }
  
  break;
  
  default:
  	$output.="<script type='text/javascript'>
	      function deletetype(tid) {
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
				      window.location.reload();
			      }
		      }
		      xmlhttp.open('GET','/service/typesmanagement.html?table=type&action=delete&tid='+tid,true);
		      xmlhttp.send();
	      }
	      function deleteattr(atid) {
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
				      window.location.reload();
			      }
		      }
		      xmlhttp.open('GET','/service/typesmanagement.html?table=attr&action=delete&atid='+atid,true);
		      xmlhttp.send();
	      }
	      
	</script>
	";
  $output.="<table width='800'  border=0><tr><td width='400'>";
	$restypes = mysql_query("SELECT * FROM `{$tbl_full_prefix}goods_types`");
	if (mysql_num_rows($restypes)>0){
	  $output.="Выбрать существующий тип:<table border='0'>";
	  while ($types = mysql_fetch_array($restypes)) {
	    $output.="<tr><td>".$types['ID']."</td><td>".$types['NAME']."</td>
      
      <td><a href='/service/typesmanagement.html?table=type&action=view&mode=old&tid=".$types['ID']."'>Изменить</a></td>
      <td><a href='javascript:deletetype(".$types['ID'].")'>Удалить</a></td>

      </tr>";
	    
	  }
	  $output.="</table><br />или 
      ";
	}
	$output.="<a href='/service/typesmanagement.html?table=type&action=view&mode=new'>Создать новый тип</a>";
	$output.="</td><td width='300'>";
	$resattr = mysql_query("SELECT * FROM `{$tbl_full_prefix}attributes`");
	if (mysql_num_rows($resattr)>0){
	  $output.="Выбрать существующий атрибут:<table border='0'>";
	  while ($attr = mysql_fetch_array($resattr)) {
	    $output.="<tr><td>".$attr['ID']."</td><td>".$attr['NAME']."</td><td>{$attr['ALIAS']}</td>
      
      <td><a href='/service/typesmanagement.html?table=attr&action=view&mode=old&atid=".$attr['ID']."'>Переименовать</a></td>
      <td><a href='javascript:deleteattr(".$attr['ID'].")'>Удалить</a></td>
      </tr>";
	    
	  }
	  $output.="</table><br />или 
      ";
	}
	$output.="<a href='/service/typesmanagement.html?table=attr&action=view&mode=new'>Создать новый атрибут</a>";
  break;
  
}
echo $output;
<?php
if(!isset($_SESSION['modx.user.contextTokens']['mgr'])) die('Access denied.');
session_start();
include('./core/config/config.inc.php');
mysql_connect ($database_server, $database_user, $database_password);
mysql_set_charset($database_connection_charset);
mysql_select_db ($dbase);
if (isset($_REQUEST['aid']) && is_numeric($_REQUEST['aid'])) {
  
	$aid = $_REQUEST['aid'];
  
} else {
	$aid = 0;
}
 $category = "<select name='cat[]' onChange='selectGroupValue(this.options[this.selectedIndex].value, \"category\")'><option disabled>Выберите категорию</option>";
  $query = "SELECT * FROM `{$table_prefix}site_content` WHERE `template`=3 AND `isfolder`=1";
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result)) {
		if ($aid == $row['id']) $selected = " selected"; else $selected="";
		$category.= "<option value='".$row['id']."' ".$selected.">".$row['pagetitle']."</option>";    
    }
  $category.="</select>";
  $warehouse = "<select name='wid[]' onChange='selectGroupValue(this.options[this.selectedIndex].value, \"warehouse\")'><option disabled>Выберите склад</option><option selected value='0'>На все</option>";
  $query = "SELECT * FROM `{$table_prefix}ms_modWarehouse`";
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result)) {
		if ($aid == $row['id']) $selected = " selected"; else $selected="";
		$warehouse.= "<option value='".$row['id']."' ".$selected.">".$row['name']."</option>";    
    }
  $warehouse.="</select>";
  $types = "<select name='type[]' onChange='selectGroupValue(this.options[this.selectedIndex].value, \"type\")'><option disabled>Выберите тип</option>";
  $query = "SELECT * FROM `{$tbl_full_prefix}goods_types`";
    $result = mysql_query($query);
    while($row = mysql_fetch_array($result)) {
		if ($aid == $row['ID']) $selected = " selected"; else $selected="";
		$types.= "<option value='".$row['ID']."' ".$selected.">".$row['NAME']."</option>";    
    }
  $types.="</select>";
echo '<link href="/assets/components/multiupload/swf2201/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/assets/components/multiupload/swf2201/swfupload.js"></script>
<script type="text/javascript" src="/assets/components/multiupload/swf2201/handlers.js"></script>
<script type="text/javascript">
		function selectGroupValue(what, where) {
			$("."+where+" option[selected]").removeAttr("selected");
			$("."+where+" option[value="+what+"]").attr("selected","selected");
			
		}

		var swfu;
		window.onload = function () {
			swfu = new SWFUpload({
				// Backend Settings
upload_url: "/service/upload.html",
				post_params: {"file": "1", 
				"PHPSESSID": "'.session_id().'", 
"aid": "'.$aid.'"},
				
				// File Upload Settings
				file_size_limit : "2 MB",	// 2MB
				file_types : "*.jpg;*.png;*.gif",
				file_types_description : "Images",
				file_upload_limit : "0",

				// Event Handler Settings - these functions as defined in Handlers.js
				//  The handlers are not part of SWFUpload but are part of my website and control how
				//  my website reacts to the SWFUpload events.
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,

				// Button Settings
button_image_url : "/assets/components/multiupload/swf2201/images/SmallSpyGlassWithTransperancy_17x18.png",
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 250,
				button_height: 18,
				button_text : \'<span class="button">Выберите изображения <span class="buttonSmall">(до 2МБ каждое)</span></span>\',
				button_text_style : \'.button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; } .buttonSmall { font-size: 10pt; }\',
				button_text_top_padding: 0,
				button_text_left_padding: 18,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				// Flash Settings
flash_url : "/assets/components/multiupload/swf2201/Flash/swfupload.swf",

				custom_settings : {
					upload_target : "divFileProgressContainer"
				},
				
				// Debug Settings
				debug: false
			});
		};
	</script>
<h2>Загрузка изображений</h2>
	<p>Внимание! Каждое изображение - отдельный товар! Не загружайте с помощью этой формы изображения к одному товару!</p>
		<form>
		<div style="display: inline; border: solid 1px #7FAAFF; background-color: #C5D9FF; padding: 2px;">
			<span id="spanButtonPlaceholder"></span>
		</div>
	</form>
		<div id="divFileProgressContainer" style="height: 75px;"></div>
<form method="POST" action="/service/upload.html">

<table border="0" id="info" style="visibility: hidden;"><tbody>
<tr><td colspan="2"><fieldset style="border:solid 1px #aaa;"><legend>Групповые операции</legend>
					<table border="0">
						<tr>
							<td>Категория: <br />'.$category.'</td>
							<td>Тип: <br />'.$types.'</td>
							<td>Склад: <br />'.$warehouse.'</td>
						</tr>
					</table>
		</fieldset>
</td></tr>
<tr><td><input type="hidden" name="info" value="1" />
<input type="hidden" name="aid" value="'.$aid.'" />
<input type="submit" name="submit" value="Сохранить" /></td></tr>
</tbody></table></form>

';
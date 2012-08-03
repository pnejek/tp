<?php
include('./core/config/config.inc.php');
mysql_connect ($database_server, $database_user, $database_password);
mysql_set_charset($database_connection_charset);
mysql_select_db ($dbase);
if(isset($_REQUEST['action'])) 
  	$action = $_REQUEST['action'];
else {
	$action = '';
}
switch ($action) {
	case 'goods_types': 
		$list[] = array(
			'GTYPE_ID' => '0',
			'NAME' => "Не задано (0)",
		);
		$query = "SELECT * FROM `{$tbl_full_prefix}goods_types` ORDER BY `ID` ASC";
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result)) {
		     $list[] = array(
			'GTYPE_ID' => $row['ID'],
			'NAME' => $row['NAME']." ({$row['ID']})",
		    );   
		}
	break;
}
echo json_encode($list);
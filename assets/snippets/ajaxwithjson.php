<?php
if(!isset($_SESSION['modx.user.contextTokens']['mgr'])) die('Доступ запрещен.');
include('./core/config/config.inc.php');
mysql_connect ($database_server, $database_user, $database_password);
mysql_set_charset($database_connection_charset);
mysql_select_db ($dbase);
if(isset($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
} 
else {
	$action = '';
}
switch ($action) {
	case 'changeorder' :
		if(isset($_REQUEST['tid']) && is_numeric($_REQUEST['tid']) )
  			$tid = $_REQUEST['tid'];
		if(isset($_REQUEST['atid']) && is_numeric($_REQUEST['atid']) )
 			$atid = $_REQUEST['atid'];
 		if(isset($_REQUEST['st']) && is_numeric($_REQUEST['st']) )
 			$st = $_REQUEST['st'];
 		$error = 0;
  	  	$out = '';
  	  	if(isset($_REQUEST['value']) && is_numeric($_REQUEST['value'])) {
  	  		$value = $_REQUEST['value'];
  	  	} else {
  	  		$error='Value is illegal.';
  	  	}
  	  	if($value == -1) {
  	  		$resthis = mysql_query("SELECT `SORTORDER` FROM `{$tbl_full_prefix}type_attr_xref` 
  	  		  WHERE `TYPE_ID`={$tid} AND `ATTR_ID`={$atid}") or $error = 'Cant get this sortorder.';
  	  		$thisobj = mysql_fetch_array($resthis);
  	  		$thisorder = $thisobj['SORTORDER'];
  	  		$resnext = mysql_query("SELECT `SORTORDER`, `ID` FROM `{$tbl_full_prefix}type_attr_xref` 
  	  		  WHERE `TYPE_ID`={$tid} AND `SORTORDER`>{$thisorder} AND `STATIC`={$st} ORDER BY `SORTORDER` ASC LIMIT 1") 
  	  		  or $error = 'Can\'t get next sortorder.';
  	  		$next = mysql_fetch_array($resnext);
  	  		$nextorder = $next['SORTORDER'];
  	  		mysql_query("UPDATE `{$tbl_full_prefix}type_attr_xref` SET `SORTORDER`={$nextorder} 
  	  		  WHERE `TYPE_ID`={$tid} AND `ATTR_ID`={$atid}") or $error = 'Cant update this sortorder.';
  	  		mysql_query("UPDATE `{$tbl_full_prefix}type_attr_xref` SET `SORTORDER`={$thisorder} 
  	  		  WHERE `ID`={$next['ID']}") or $error = 'Can\'t update next sortorder.'; 
  	  	}
  	  	if($value == 0) {
  	  		$resthis = mysql_query("SELECT `SORTORDER` FROM `{$tbl_full_prefix}type_attr_xref` 
  	  		  WHERE `TYPE_ID`={$tid} AND `ATTR_ID`={$atid}") or $error = 'Cant get this sortorder.';
  	  		$thisobj = mysql_fetch_array($resthis);
  	  		$thisorder = $thisobj['SORTORDER'];
  	  		$resprev = mysql_query("SELECT `SORTORDER`, `ID` FROM `{$tbl_full_prefix}type_attr_xref` 
  	  		  WHERE `TYPE_ID`={$tid} AND `SORTORDER`<{$thisorder} AND `STATIC`={$st} ORDER BY `SORTORDER` DESC LIMIT 1") 
  	  		  or $error = 'Can\'t get prev sortorder.';
  	  		$prev = mysql_fetch_array($resprev);
  	  		$prevorder = $prev['SORTORDER'];
  	  		mysql_query("UPDATE `{$tbl_full_prefix}type_attr_xref` SET `SORTORDER`={$prevorder} 
  	  		  WHERE `TYPE_ID`={$tid} AND `ATTR_ID`={$atid}") or $error = 'Cant update this sortorder.';
  	  		mysql_query("UPDATE `{$tbl_full_prefix}type_attr_xref` SET `SORTORDER`={$thisorder} 
  	  		  WHERE `ID`={$prev['ID']}") or $error = 'Can\'t update prev sortorder.'; 
  	  	}
  	  	if($value>0) {
  	  		mysql_query("UPDATE `{$tbl_full_prefix}type_attr_xref` SET `SORTORDER`={$value} 
  	  		  WHERE `TYPE_ID`={$tid} AND `ATTR_ID`={$atid}") or $error = 'Cant update this sortorder.';
  	  	}
  	  	if($error != 0) {
  	  		$out = array('result'=>'failure', 'error'=>$error);
  	  	} else {
  	  		$out = array('result'=>'success');
  	  	}
  	  	echo json_encode($out);
	break;
	
}
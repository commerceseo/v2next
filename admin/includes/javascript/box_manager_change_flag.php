<?php 

include_once('../configure.php');

$connect = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
mysql_select_db(DB_DATABASE);

if(!empty($_POST)) {
	$id = explode('_',$_POST['id']);
	
	if($id[0] == 'bs') {
		$check_query = mysql_query("SELECT status FROM boxes WHERE id = '".$id[1]."' ");
		$check = mysql_fetch_array($check_query);
		if($check['status'] == '0') {
			$db = mysql_query("UPDATE boxes SET status = '1' WHERE id = '".$id[1]."' ");
			echo '<img src="'.DIR_WS_IMAGES.'icon_status_green.gif" alt="" height="10" title="" />';
			echo ' <a class="box_flag" id="'.$_POST['id'].'" href="javascript:void(0)" onclick="javascript:changeFlag(this.id);"><img src="images/icon_status_red_light.gif" height="10" alt="" title="deaktivieren" /></a>';
		} else {
			$db = mysql_query("UPDATE boxes SET status = '0' WHERE id = '".$id[1]."' ");
			echo '<a class="box_flag" id="'.$_POST['id'].'" height="10" href="javascript:void(0)" onclick="javascript:changeFlag(this.id);"><img src="images/icon_status_green_light.gif" alt="" height="10" title="aktivieren" /></a> ';
			echo '<img src="images/icon_status_red.gif" alt="" title="deaktiviert" height="10" />';
		}
	} elseif($id[0] == 'bn') {
		$check_name_query = mysql_query("SELECT status,box_name FROM boxes_names WHERE id = '".$id[1]."' ");
		$check_name = mysql_fetch_array($check_name_query);
		if($check_name['status'] == '0') {
			$db = mysql_query("UPDATE boxes_names SET status = '1' WHERE box_name = '".$check_name['box_name']."' ");
			echo '<img src="'.DIR_WS_IMAGES.'icon_status_green.gif" alt="" height="10" title="" />';
			echo ' <a class="box_flag" id="'.$_POST['id'].'" href="javascript:void(0)" onclick="javascript:changeFlag(this.id);"><img src="images/icon_status_red_light.gif" height="10" alt="" title="deaktivieren" /></a>';
		} else {
			$db = mysql_query("UPDATE boxes_names SET status = '0' WHERE box_name = '".$check_name['box_name']."' ");
			echo '<a class="box_flag" id="'.$_POST['id'].'" height="10" href="javascript:void(0)" onclick="javascript:changeFlag(this.id);"><img src="images/icon_status_green_light.gif" alt="" height="10" title="aktivieren" /></a> ';
			echo '<img src="images/icon_status_red.gif" alt="" title="deaktiviert" height="10" />';
		}
	} elseif($id[0] == 'bm') {
		$check_query = mysql_query("SELECT mobile FROM boxes WHERE id = '".$id[1]."' ");
		$check = mysql_fetch_array($check_query);
		if($check['mobile'] == '0') {
			$db = mysql_query("UPDATE boxes SET mobile = '1' WHERE id = '".$id[1]."' ");
			echo '<img src="'.DIR_WS_IMAGES.'icon_status_green.gif" alt="" height="10" title="" />';
			echo ' <a class="box_flag" id="'.$_POST['id'].'" href="javascript:void(0)" onclick="javascript:changeFlag(this.id);"><img src="images/icon_status_red_light.gif" height="10" alt="" title="deaktivieren" /></a>';
		} else {
			$db = mysql_query("UPDATE boxes SET mobile = '0' WHERE id = '".$id[1]."' ");
			echo '<a class="box_flag" id="'.$_POST['id'].'" height="10" href="javascript:void(0)" onclick="javascript:changeFlag(this.id);"><img src="images/icon_status_green_light.gif" alt="" height="10" title="aktivieren" /></a> ';
			echo '<img src="images/icon_status_red.gif" alt="" title="deaktiviert" height="10" />';
		}
	}
}

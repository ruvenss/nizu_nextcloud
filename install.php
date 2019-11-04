<?php
defined('BASEPATH') or exit('No direct script access allowed');
include(getcwd()."/nizu/config.php");
include(getcwd()."/nizu/db.php");
include(getcwd()."/nizu/functions.php");
// Update Knowledge Base
// Check if NIZU Agenda Group Exist
$groupid=0;
$groupid = sqlSelect($mas, db_prefix()."knowledge_base_groups", "groupid", "group_slug='en-nizu-nextcloud'");
if ($groupid>0) {
    sqlUpdate($mas, db_prefix()."knowledge_base_groups", array("name","group_slug","description","active","color"),array("Nizu NextCloud [ en ]","en-nizu-nextcloud","Nizu NextCloud is a module to create and manage meetings and events with customers, suppliers, members, or partners.","1","#0057bd"),"groupid",$groupid);
} else {
    $groupid = sqlInsert($mas, db_prefix()."knowledge_base_groups", array("name","group_slug","description","active","color"),array("Nizu NextCloud [ en ]","en-nizu-nextcloud","Nizu NextCloud is a module to create and manage meetings and events with customers, suppliers, members, or partners.","1","#0057bd"));
}
// Check if Article Exist
$articleid = sqlSelect($mas, db_prefix()."knowledge_base", "articleid", "slug='en-nizu-nextcloud'");
$article_description=file_get_contents(getcwd()."/modules/nizu_nextcloud/knowledge_base/nizu-nextcloud.html");
if ($articleid>0) {
    
} else {
    $articleid = sqlInsert($mas, db_prefix()."knowledge_base", array("articlegroup","slug","subject","description","active","datecreated","staff_article"),array($groupid,"en-nizu-nextcloud","Nizu NextCloud [ en ]",$article_description,1,"2019-11-03 12:12:33",1));
}
// Check if nizu settings are set 
$nizu_settings_key = sqlSelect($mas, db_prefix()."nizu_settings", "value", "name='nizu_settings_key'");
if (strlen($nizu_settings_key)>0) {
	$nizu_nextcloud_dbprefix = sqlSelect($mas, db_prefix()."nizu_settings", "value", "name='nizu_nextcloud_dbprefix'");
	if (strlen($nizu_nextcloud_dbprefix)>0) {
		// This module has been updated already
	} else {
		sqlInsert($mas, db_prefix()."nizu_settings", array("name","value"),array("nizu_nextcloud_dbprefix","oc_"));
		sqlInsert($mas, db_prefix()."nizu_settings", array("name","value"),array("nizu_nextcloud_db",$nizudbname));
		sqlInsert($mas, db_prefix()."nizu_settings", array("name","value"),array("nizu_nextcloud_host",$nizudbhost));
		sqlInsert($mas, db_prefix()."nizu_settings", array("name","value"),array("nizu_nextcloud_user",$nizudbuser));
		sqlInsert($mas, db_prefix()."nizu_settings", array("name","value"),array("nizu_nextcloud_pswd",$nizudbpswd));
		sqlInsert($mas, db_prefix()."nizu_settings", array("name","value"),array("nizu_nextcloud_path","/home/"));
	}
}
// Update code in Nizu
$sourcecode1 = "hooks()->do_action('contact_created', ".'$contact_id);';
$sourcecode2 = "log_activity('Contact Password Changed [ContactID: ' . ".'$id'." . ']');";
$sourcecode3 = '$contact      = $this->get_contact($id);';
$sourcecode4 = '$data = hooks()->apply_filters('."'before_update_contact', ".'$data, $id);';
$sourcecode5 = "log_activity('New Customer Group Created [ID:' . ".'$insert_id'." . ', Name:' . ".'$data'."['name'] . ']');";
nizu_code_addcontent(getcwd()."/application/models/Clients_model.php",$sourcecode1,'$data'."['password_before_hash']".'=$password_before_hash;$data'."['id']=".'$contact_id;hooks()->do_action'."('contact_created_nizu', ".'$data);');
nizu_code_addcontent(getcwd()."/application/models/Clients_model.php",$sourcecode2,'$data'."['password_before_hash']".'=$newPassword;$data'."['id']=".'$id;hooks()->do_action'."('contact_update_password_nizu', ".'$data);');
//nizu_code_addcontent(getcwd()."/application/models/Clients_model.php",$sourcecode3,'$data'."['password_before_hash']".'=$data'."['password'];",false);
nizu_code_addcontent(getcwd()."/application/models/Clients_model.php",$sourcecode3,'$data'."['id']=".'$id;hooks()->do_action'."('contact_update_password_nizu', ".'$data);',false);
nizu_code_addcontent(getcwd()."/application/models/Client_groups_model.php",$sourcecode5,'$data'."['group_id']".'=$insert_id;hooks()->do_action'."('group_created_nizu', ".'$data);');

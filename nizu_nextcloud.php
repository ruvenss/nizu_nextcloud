<?php

/**
 * Ensures that the module init file can't be accessed directly, only within the application.
 */
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Nizu NextCloud
Description: Sync system between Nizu MRM CRM and NextCloud Platform.
Version: 2.4.0
Requires at least: 2.3.*
*/
define('NIZU_NEXTCLOUD_MODULE_NAME', 'nizu_nextcloud');
$CI = &get_instance();
hooks()->add_action('admin_init', 'nizu_nextcloud_init_menu_items');
hooks()->add_action('contact_created_nizu', 'nizu_nextcloud_adduser');
hooks()->add_action('contact_update_password_nizu', 'nizu_nextcloud_update_user_password');
function nizu_nextcloud_settings($mas){
    $sqlquery="SELECT `name`,`value` FROM ".db_prefix()."nizu_settings WHERE name LIKE 'nizu_nextcloud_%'";
    $result = $mas->query($sqlquery);
    if (!$result) {
        return(array());
    } else {
        $nizu_nextcloud_settings=array();
        while($row = $result->fetch_assoc()) {
            $field=$row['name'];
            $fieldval=$row['value'];
            $nizu_nextcloud_settings[$field]=$fieldval;
        }
        return ($nizu_nextcloud_settings);
    }
}
function nizu_nextcloud_update_user_password($nizudata) {
    $user_password=$nizudata['password'];
    $nizu_user_email=$nizudata['email'];
    include(getcwd()."/nizu/config.php");
    include(getcwd()."/nizu/db.php");
    include(getcwd()."/nizu/functions.php");
    $nizu_nextcloud_settings=nizu_nextcloud_settings($mas);
    $e="cd ".$nizu_nextcloud_settings['nizu_nextcloud_path'].";export OC_PASS=$user_password;".'php occ user:resetpassword --password-from-env '.$nizu_user_email;
    print_r($e);
    exec($e);
    //die();
}
function nizu_nextcloud_adduser($nizudata){
    $CI = &get_instance();
    $id=$nizudata['id'];
    $user_password=$nizudata['password_before_hash'];
    $firstname=$nizudata['firstname'];
    $lastname=$nizudata['lastname'];
    $displayname=$firstname." ".$lastname;
    // Check if NextCloud is installed and set
    include(getcwd()."/nizu/config.php");
    include(getcwd()."/nizu/db.php");
    include(getcwd()."/nizu/functions.php");
    $nizu_nextcloud_settings=nizu_nextcloud_settings($mas);
    if ($id>0) {
        $nizu_contact_email=sqlSelect($mas,db_prefix()."contacts","email","id=$id");
        if (sizeof($nizu_nextcloud_settings)) {
            $nizu_nextcloud_user_uid=sqlSelect($mas,$nizu_nextcloud_settings['nizu_nextcloud_dbprefix']."users","uid","uid_lower='$nizu_contact_email'");
            if(strlen($nizu_nextcloud_user_uid)>0) {
                // Update Password
            } else {
                // Adding user using occ terminal
                $e="cd ".$nizu_nextcloud_settings['nizu_nextcloud_path'].";export OC_PASS=$user_password;".'php occ user:add --password-from-env --display-name="'.$nizu_contact_email.'" --group="users" '.$nizu_contact_email;
                exec($e);
                // Update Display Name in NextCloud
                sqlUpdate($mas, $nizu_nextcloud_settings['nizu_nextcloud_dbprefix']."users", array("displayname"),array($displayname),"uid",$nizu_contact_email);
            }
        } else {
            print_r($nizu_nextcloud_settings);
        }
    }
}
/**
 * Init menu setup module menu items in setup in admin_init hook
 * @return null
 */

function nizu_nextcloud_init_menu_items(){
    /**
    * If the logged in user is administrator, add custom menu in Setup
    */
    if (is_admin()) {
        $CI = &get_instance();
        $CI->app_menu->add_setup_menu_item('nizu-nextcloud', [
            'slug'     => 'nizu-nextcloud',
            'name'     => "Nizu NextCloud",
            'href'     => admin_url('nizu_nextcloud'),
            'position' => 64,
            'icon'     => 'fa fa-hexagon', // Font awesome icon
        ]);
    }
}

/**
* Register activation module hook
*/
register_activation_hook(NIZU_NEXTCLOUD_MODULE_NAME, 'nizu_nextcloud_module_activation_hook');

function nizu_nextcloud_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}
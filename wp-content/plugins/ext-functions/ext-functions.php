<?php
/*
Plugin Name: Extend Functions
Plugin URI: http://www.3smovie.com/
Description: Extend The Wordpress Functions.
Version: 1.0.0
Author: Ison Zhang
Author URI: http://www.3smovie.com/
License: New BSD License
*/



add_action('admin_menu', 'extendFunctionsSetting');
function extendFunctionsSetting(){
	add_submenu_page("tools.php", '扩展功能设置', '扩展功能设置', 'manage_options', 'extend-functions-setting', 'extendFunctionsSettings');
}
function extendFunctionsSettings(){
	require_once "setting.php";
}

add_action('query_vars', 'extendFunctionsUrlVar');
function extendFunctionsUrlVar($public_query_vars){
	$public_query_vars[] = 'extend-functions-url';
	return $public_query_vars;
}

add_action("template_redirect", 'extendFunctionsUrl');
function extendFunctionsUrl(){
	global $wp;
	global $wp_query, $wp_rewrite;
	$reditect_page =  $wp_query->query_vars['extend-functions-url'];
	if ($reditect_page == "ext-function-setting-save"){
		include_once "setsave.php";
		die();
	}
}

//-----------------------------------------------------------------------------------------
include_once 'database.php';
include_once 'email/sMail.php';
require_once 'functions.php';

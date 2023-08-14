<?php
include_once 'class-zoho-api.php';
include_once 'class-zoho-books.php';

class ZohoLauncher {

    public static function activation(){
        define('PLUGIN_NAME', 'zoho-api-support');
    }

    public static function deactivation(){

    }

    public static function uninstall(){

    }

    public static function init(){

    }

    public static function admin_menu() {
        add_menu_page(
            'Zoho',
            'Zoho API Settings',
            'manage_options',
            'zoho-settings',
            'render_settings',
            "",
            10
        );

        function render_settings() {
            include_once WP_PLUGIN_DIR.'/'.PLUGIN_NAME.'/template/settings.php';
        }
    }

}
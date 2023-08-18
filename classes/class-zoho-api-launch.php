<?php

defined( 'ABSPATH' ) || exit;

require_once 'class-zoho-api.php';
require_once 'class-zoho-books.php';
require_once 'class-log-control.php';

class ZohoLauncher {

    public static function activation(){
        update_option('zoho_api_access_token', '');
        update_option('zoho_api_refresh_token', '');
        update_option('zoho_api_refresh_token_time', '');
        update_option('zoho_api_token_error', '0');
        LogControl::create_table();
        LogControl::insert(__FILE__, __LINE__, 'activado plugin: Zoho Api Support', "zohoapi,internal");
    }

    public static function deactivation(){
        LogControl::insert(__FILE__, __LINE__, 'desactivado plugin: Zoho Api Support', "zohoapi,internal");
    }

    public static function uninstall(){
        LogControl::drop_table();
    }

    public static function init(){
        add_action( 'admin_menu', [__CLASS__, 'admin_menu']);
        add_action( 'wp_ajax_update_settings', [__CLASS__, 'update_settings']);
        add_action( 'wp_ajax_generate_code', [__CLASS__, 'generate_code']);
        do_action( 'zoho_api');
        if( get_option('zoho_api_books', '0')=='1' ) {
            do_action('zoho_api_books');
        }
    }

    public static function generate_code() {
        if( isset($_POST['code']) ) {
            $code = $_POST['code'];
            $client_id = get_option('zoho_api_client_id', '');
            $client_secret = get_option('zoho_api_client_secret', '');
            $response = ZohoBooks::generate_code( $code, $client_id, $client_secret );
            if( $response['error'] ) {
                update_option('zoho_api_token_error', '1');
                LogControl::insert(__FILE__, __LINE__, "Error al crear Refresh Token","zohoapi,error");
                die($response["message"]);
            }
            $refresh_token = $response['data'];
            update_option( 'zoho_api_refresh_token', $refresh_token );
            $response = ZohoBooks::refresh_token( $refresh_token, $client_id, $client_secret );
            if( $response['error'] ) {
                update_option('zoho_api_token_error', '1');
                LogControl::insert(__FILE__, __LINE__, "Error al crear Access Token","zohoapi,error");
                die($response["message"]);
            }
            $access_token = $response['data'];
            update_option( 'zoho_api_access_token', $access_token );
            update_option( 'zoho_api_access_token_time', date('Y-m-d H:i:s') );
            update_option( 'zoho_api_token_error', '0');
            LogControl::insert(__FILE__, __LINE__, "Aprobado este codigo $code => generado refresh token $access_token","zohoapi,message");
            die(json_encode([
                "code" => $code,
                "token" => $access_token
            ]));
        }
    }

    public static function update_settings() {
        $updates = [];
        $message = "";
        if ( isset($_POST['zoho_api_books']) ) {
            $updates[] = 'zoho_api_books';
            update_option('zoho_api_books', $_POST['zoho_api_books']);
            LogControl::insert(__FILE__, __LINE__, $_POST['zoho_api_books']=="1" ? 'Enable Api Books' : 'Disable Api Books',"zohoapi,internal,settings");
            $message .= "Update Books <br/>";
        }
        if( isset($_POST['zoho_api_book_organization']) ) {
            $updates[] = 'zoho_api_book_organization';
            update_option('zoho_api_book_organization', $_POST['zoho_api_book_organization']);
            LogControl::insert(__FILE__, __LINE__, "Organization id change by ".$_POST['zoho_api_book_organization'],"zohoapi,internal,settings");
            $message .= "Update Organization ID <br/>";
        }
        if( isset($_POST['zoho_api_client_id']) ) {
            $updates[] = 'zoho_api_client_id';
            update_option('zoho_api_client_id', $_POST['zoho_api_client_id']);
            LogControl::insert(__FILE__, __LINE__, "Client id change by ".$_POST['zoho_api_client_id'],"zohoapi,internal,settings");
            $message .= "Update Client ID <br/>";
        }
        if( isset($_POST['zoho_api_client_secret']) ) {
            $updates[] = 'zoho_api_client_secret';
            update_option('zoho_api_client_secret', $_POST['zoho_api_client_secret']);
            LogControl::insert(__FILE__, __LINE__, "Client Secret change by ".$_POST['zoho_api_client_secret'],"zohoapi,internal,settings");
            $message .= "Update Client Secret <br/>";
        }
        die( json_encode([
            "updates" => $updates,
            "message" => $message
        ]) );
    }

    public static function admin_menu() {
        add_menu_page(
            null,
            'Zoho',
            'manage_options',
            'zoho-settings',
            null,
            null,
            10
        );

        add_submenu_page(
            'zoho-settings',
            'Settings',
            'Settings',
            'manage_options',
            'zoho-settings',
            'render_settings'
        );

        add_submenu_page(
            'zoho-settings',
            'Credentials',
            'Credentials',
            'manage_options',
            'credentials',
            'render_credentials'
        );

        add_submenu_page(
            'zoho-settings',
            'Logs',
            'Logs',
            'manage_options',
            'logs',
            'render_logs'
        );

        function render_settings() {
            include_once WP_PLUGIN_DIR.'/zoho-api-support/template/settings.php';
        }

        function render_credentials(){
            include_once WP_PLUGIN_DIR.'/zoho-api-support/template/credentials.php';
        }

        function render_logs(){
            include_once WP_PLUGIN_DIR.'/zoho-api-support/template/logs.php';
        }
    }

}
<?php

defined( 'ABSPATH' ) || exit;

if( !class_exists('ZohoApi') ) {

    class ZohoApi {

        public static function generate_code( $code, $client_id, $client_secret ) {
            $url = 'https://accounts.zoho.com/oauth/v2/token';
            $data = [
                "grant_type" => "authorization_code",
                "client_id" => $client_id,
                "client_secret" => $client_secret,
                "code" => $code
            ];
            if ( class_exists( 'LogControl' ) ) {
                LogControl::insert(__FILE__, __LINE__, "code for Generate Code is $code","zohoapi,internal");
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = json_decode( curl_exec($ch), true );
            curl_close($ch);

            $refresh_token = isset($response['refresh_token']) ? $response['refresh_token'] : null;

            if ( class_exists( 'LogControl' ) ) {
                if( $refresh_token ) LogControl::insert(__FILE__, __LINE__, "Refresh token generate $refresh_token","zohoapi,internal");
                else LogControl::insert(__FILE__, __LINE__, "Refresh token not generate", "zohoapi,internal,error");
            }
            return ($refresh_token!==null) ? [
                "error" => false,
                "data" => $refresh_token,
                "message" => "success"
            ] : [
                "error" => true,
                "data" => null,
                "message" => json_encode( $response )
            ];
        }

        public static function refresh_token( $refresh_token, $client_id, $client_secret ) {
            $url = 'https://accounts.zoho.com/oauth/v2/token';
            $data = [
                "grant_type" => "refresh_token",
                "refresh_token" => $refresh_token,
                "client_id" => $client_id,
                "client_secret" => $client_secret
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $response = json_decode( curl_exec($ch), true );
            curl_close($ch);

            $access_token = isset($response['access_token']) ? $response['access_token'] : null;
            return ($access_token!==null) ? [
                "error" => false,
                "data" => $access_token,
                "message" => "success"
            ] : [
                "error" => true,
                "data" => null,
                "message" => json_encode( $response )
            ];
        }

        public static function get_token() {
            $error = 0;
            $refresh_token = get_option('zoho_api_refresh_token', '');
            $access_token = get_option('zoho_api_access_token', '');
            $client_id = get_option('zoho_api_client_id', '');
            $client_secret = get_option('zoho_api_client_secret', '');
            if ( ! is_string( $access_token ) ) {
                $error = 0;
                $access_token = '';
                update_option('zoho_api_access_token', '');
                update_option('zoho_api_token_error', '1');
            } else {
                $present = date('Y-m-d H:i:s');
                $time_limit = date('Y-m-d H:i:s', strtotime( get_option('zoho_api_access_token_time', '').' + 1 minute' ));
                if( $present > $time_limit ) {
                    $response = self::refresh_token( $refresh_token, $client_id, $client_secret );
                    if( !$response['error'] ) {
                        $error = 0;
                        $access_token = $response['data'];
                        update_option('zoho_api_access_token', $response['data']);
                        update_option( 'zoho_api_access_token_time', date('Y-m-d H:i:s') );
                    } else {
                        $error = 1;
                        $access_token = '';
                        update_option('zoho_api_access_token', '');
                        update_option('zoho_api_token_error', '1');
                    }
                }
            }
            return [
                "error" => $error,
                "access_token" => $access_token
            ];

        }
    }

}


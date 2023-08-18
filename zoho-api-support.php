<?php
/**
 * Plugin Name: Zoho Api Support
 * Plugin URI: https://zenx5.pro
 * Description: ...
 * Version: 1.0.0
 * Author: Octavio Martinez
 * Author URI: https://zenx5.pro
 * Domain Path: /i18n/languages/
 * Requires at least: 5.9
 * Requires PHP: 7.2
 * @package WooCommerce
 */

require_once 'classes/class-zoho-api-launch.php';
$nameclass = 'ZohoLauncher';

register_activation_hook(__FILE__, [$nameclass, 'activation']);
register_deactivation_hook(__FILE__, [$nameclass, 'deactivation']);
register_uninstall_hook(__FILE__, [$nameclass, 'uninstall']);
add_action('init', [$nameclass, 'init']);
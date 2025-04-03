<?php
/**
 * Plugin Name: BizPress Key Dates
 * Description: Show a calendar of key tax and business dates on your site.
 * Plugin URI: https://bizinkonline.com
 * Author: Bizink
 * Author URI: https://bizinkonline.com
 * Version: 1.3.6
 * Text Domain: bizink-client-keydates
 * Domain Path: /languages
 */

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin Updater
require 'plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;
$myUpdateChecker = PucFactory::buildUpdateChecker('https://github.com/BizInk/bizpress-key-dates',__FILE__,'bizpress-key-dates');
$myUpdateChecker->setBranch('main');
$myUpdateChecker->setAuthentication('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');

if(is_plugin_active("bizpress-client/bizink-client.php")){
	require 'key-dates.php';
}
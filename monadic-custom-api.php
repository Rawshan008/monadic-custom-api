<?php
/*
 * Plugin Name:       Monadic Custom Api
 * Plugin URI:        #
 * Description:       This plugin For Wordpress Custom Api integration.
 * Version:           1.0.0
 * Requires at least: 6
 * Requires PHP:      7.4
 * Author:            Rawshan
 * Author URI:        https://rawshanali.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         
 * Domain Path:       /languages
 */

// Exit if accessed directly.

use MonadicCustomApi\Api;

if (!defined('ABSPATH')) {
  exit;
}

require_once 'vendor/autoload.php';

final class Monadic_Custom_Api
{

  /**
   * Plugin Version
   */
  const VERSION = '1.0.0';

  public function __construct()
  {
    add_action('admin_menu', [$this, 'pending_contact_bubble']);
    new Api();
  }

  public function pending_contact_bubble()
  {
    $pending_count = wp_count_posts('contact')->pending;
    global $menu;
    foreach ($menu as $key => $menu_item) {
      if ('edit.php?post_type=contact' == $menu_item[2]) {
        if ($pending_count > 0) {
          $menu[$key][0] .= " <span class='update-plugins count-$pending_count'><span class='plugin-count'>$pending_count</span></span>";
        }
        break;
      }
    }
  }



  /**
   * Initializes an singleton Instance
   */
  public static function init()
  {
    static $instance = false;

    if (!$instance) {
      $instance = new self();
    }

    return $instance;
  }


  public function define_constsnts()
  {
    define('MS_INFO_VERSION', self::VERSION);
    define('MS_INFO_FILE', __FILE__);
    define('MS_INFO_PATH', __DIR__);
    define('MS_INFO_URL', plugins_url('', MS_INFO_FILE));
    define('MS_INFO_ASSETS', MS_INFO_URL . '/assets');
  }
}

/**
 * Initialize then Main Plugin
 */
function monadic_custom_api()
{
  return Monadic_Custom_Api::init();
}

/**
 * kick-off the plugin
 */
monadic_custom_api();

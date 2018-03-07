<?php
/*
Plugin Name: Social Media Navigation
Description: Social Media Navigation
Version: 1.0.0
Author: Bond
Author uri: https://bond.fi
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

// Run installation function only once on activation.
register_activation_hook(__FILE__, ['WP_Social_Nav', 'on_activation']);
register_deactivation_hook(__FILE__, ['WP_Social_Nav', 'on_deactivation']);
add_action('plugins_loaded', ['WP_Social_Nav', 'init']);

class WP_Social_Nav {
  protected static $instance; // Holds the instance.
  protected static $version = '1.0.0'; // The current version of the plugin.
  protected static $min_wp_version = '4.7.5'; // Minimum required WordPress version.
  protected static $min_php_version = '7.0'; // Minimum required PHP version.
  protected static $class_dependencies = []; // Class dependencies of the plugin.
  protected static $required_php_extensions = []; // PHP extensions required by the plugin.
  protected static $text_domain = 'wp-social-nav'; // Text domain of the plugin.



  public function __construct(){
    /**
     * Register your hooks here. Remember to register only on admin side if
     * it's only admin plugin and so forth.
     */
    add_action( 'after_setup_theme', array( $this, 'register_social_nav' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
    add_filter( 'wp_get_nav_menu_items', array( $this, 'social_nav_classes' ), 10, 3 );
  }



  public static function init() {
    is_null( self::$instance ) AND self::$instance = new self;
    return self::$instance;
  }

  function social_nav_classes( $items, $menu, $args ) {
    $theme_locations = get_nav_menu_locations();
    $menu_id = null;

    if( array_key_exists( 'wp-social-nav', $theme_locations ) ) {
      $menu_id = $theme_locations['wp-social-nav'];
    }

    if( $menu->term_id === $menu_id ) {
      foreach( $items as &$item ) {
        $item->classes[] = 'wp-social-nav-item';
      }
    }

    return $items;
  }



  function register_social_nav() {
    register_nav_menu( 'wp-social-nav', 'Social Media Navigation' );
  }



  function register_plugin_styles() {

    wp_register_style( 'wp-social-nav-fa', plugins_url( 'wp-social-nav/css/fa-brands.min.css' ), array(), self::$version, 'all' );
    wp_register_style( 'wp-social-nav', plugins_url( 'wp-social-nav/css/wp-social-nav.css' ), array( 'wp-social-nav-fa' ), self::$version, 'all' );
    wp_enqueue_style( 'wp-social-nav-fa' );
    wp_enqueue_style( 'wp-social-nav' );
  }



  /**
   * Checks if plugin dependencies & requirements are met.
   */
  protected static function are_requirements_met() {
    // Check for WordPress version
    if ( version_compare( get_bloginfo('version'), self::$min_wp_version, '<' ) ) {
      return false;
    }

    // Check the PHP version
    if ( version_compare( PHP_VERSION, self::$min_php_version, '<' ) ) {
      return false;
    }

    // Check PHP loaded extensions
    foreach ( self::$required_php_extensions as $ext ) {
      if ( ! extension_loaded( $ext ) ) {
        return false;
      }
    }

    // Check for required classes
    foreach ( self::$class_dependencies as $class_name ) {
      if ( ! class_exists( $class_name ) ) {
        return false;
      }
    }

    return true;
  }



  /**
   * Checks if plugin dependencies & requirements are met. If they are it doesn't
   * do anything if they aren't it will die.
   */
  public static function ensure_requirements_are_met() {
    if (!self::are_requirements_met()) {
      deactivate_plugins(__FILE__);
      wp_die( "<p>Some of the plugin dependencies aren't met and the plugin can't be enabled. This plugin requires the followind dependencies:</p><ul><li>Minimum WP version: ".self::$min_wp_version."</li><li>Minimum PHP version: ".self::$min_php_version."</li><li>Classes / plugins: ".implode (", ", self::$class_dependencies)."</li><li>PHP extensions: ".implode (", ", self::$required_php_extensions)."</li></ul>" );
    }
  }



  /**
   * A function that's run once when the plugin is activated. We just create
   * a scheduled run for the press release update.
   */
   public static function on_activation() {
    // Security stuff.
    if ( ! current_user_can( 'activate_plugins' ) ) {
      return;
    }

    $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
    check_admin_referer( "activate-plugin_{$plugin}" );

    // Check requirements.
    self::ensure_requirements_are_met();

    // Your activation code below this line.
    flush_rewrite_rules();
  }



  /**
   * A function that's run once when the plugin is deactivated. We just delete
   * the scheduled run for the press release update.
   */
   public static function on_deactivation() {
    // Security stuff.
    if ( ! current_user_can( 'activate_plugins' ) ) {
      return;
    }

    $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
    check_admin_referer( "deactivate-plugin_{$plugin}" );

    // Your deactivation code below this line.
    flush_rewrite_rules();
  }

} // Class ends
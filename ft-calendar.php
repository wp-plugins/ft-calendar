<?php
/**
 * @package FT_Calendar
 * @version 1.0.2
 */
/*
Plugin Name: FullThrottle Calendar
Plugin URI: http://fullthrottlecalendar.com/ft-calendar
Description: A feature rich calendar plugin for WordPress.
Author: FullThrottle Development
Version: 1.0.2
Author URI: http://fullthrottledevelopment.com/
Primary Developer: Glenn Ansley (glenn@glennansley.com)
Primary Developer: Lew Ayotte (lew@lewayotte.com)
*/

#### CONSTANTS ####
	define( 'FT_CAL_VERSION', '1.0.2' );
	define( 'FT_CAL_DB_VERSION', '1.0' );
	
	// From http://codex.wordpress.org/Determining_Plugin_and_Content_Directories
	if ( ! function_exists( 'is_ssl' ) ) {
		
		function is_ssl() {
			
			if ( isset( $_SERVER['HTTPS'] ) ) {
				
				if ( 'on' == strtolower( $_SERVER['HTTPS'] ) || '1' == $_SERVER['HTTPS'] )
					return true;
								
			} elseif ( isset( $_SERVER['SERVER_PORT'] ) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
				
				return true;
			
			}
			
			return false;
			
		}
		
	}
	
	// Set wp_content URL
	if ( version_compare( get_bloginfo( 'version' ), '3.0', '<' ) && is_ssl() )
		$wp_content_url = str_replace( 'http://' , 'https://' , get_option( 'siteurl' ) );
	else
		$wp_content_url = get_option( 'siteurl' );
	
	$wp_content_url 	.= '/wp-content';
	$wp_content_dir 	 = ABSPATH . 'wp-content';
	$wp_plugin_url 		 = $wp_content_url . '/plugins';
	$wp_plugin_dir 		 = $wp_content_dir . '/plugins';
	
	/**
	 * URL to Plugin folder
	 *
	 * @since 0.3
	 */
	define( 'FT_CAL_URL', $wp_plugin_url . '/' .   basename( dirname(__FILE__) ) );
	
	/**
	 * Server path to plugin Dir
	 *
	 * @since 0.3
	 */
	define( 'FT_CAL_PATH', $wp_plugin_dir . '/' .  basename( dirname(__FILE__) ) );


#### INCLUDES ####

	include_once( FT_CAL_PATH . '/classes/ft-ps-client.php' );
	include_once( FT_CAL_PATH . '/classes/class-options.php' );
	include_once( FT_CAL_PATH . '/classes/class-admin.php' );
	include_once( FT_CAL_PATH . '/classes/class-calendars.php' );
	include_once( FT_CAL_PATH . '/classes/class-events.php' );
	include_once( FT_CAL_PATH . '/classes/class-widgets.php' );
	include_once( FT_CAL_PATH . '/classes/class-shortcodes.php' );

#### PROCEDURAL POWER FTW! ####
	// Init Admin
	
	/**
	 * Options object to hold settins.
	 *
	 * @since 0.3
	 */
	$ft_cal_options		= new FT_CAL_Options();
	
	/**
	 * Class for setting up and displaying admin options
	 *
	 * @since 0.3
	 */
	$ft_cal_admin 		= new FT_CAL_Admin();
			
	/**
	 * Object and methods used to manage / display calendar post types
	 * 
	 * @since 0.3
	 */
	$ft_cal_calendars 	= new FT_CAL_Calendars();
	
	/**
	 * Object and methods used to manage / display events post types
	 *
	 * @since 0.3
	 */
	$ft_cal_events		= new FT_CAL_Events();
	
	/**
	 * Object used to manage / display shortcodes
	 *
	 * @since 0.3
	 */
	$ft_cal_shortcodes	= new FT_CAL_ShortCodes();
	
	/**
	 * Premium Support Client for SimpleMap
	 * @since 0.3
	 */
	$config = array( 
		'server_url' => 'http://calendar-plugin.com', 
		'product_id' => 2, 
		'product-slug' => 'ft-calendar-premium', 
		'plugin_support_page_ids' => array( 'ft-calendar_page_ftcalendar-help' ), 
		'plugin_basename' => plugin_basename( FT_CAL_PATH . '/ft-calendar.php' ), 
		'plugin_slug' => 'ft-calendar',
		'learn_more_link' => 'http://calendar-plugin.com/premium-support/' 
	);
	if ( class_exists( 'FT_Premium_Support_Client' ) && ( ! isset( $ftcalendar_ps ) || ! is_object( $ftcalendar_ps ) ) )
		$ftcalendar_ps = new FT_Premium_Support_Client( $config );

	// Maybe update tables
	if ( false === get_option( 'ft_cal_db_version' ) 
		|| version_compare( get_option( 'ft_cal_db_version' ), FT_CAL_DB_VERSION, '<' ) )
		$ft_cal_options->install_ftcal_table();

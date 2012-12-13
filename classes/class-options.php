<?php
/**
 * @package FT_Calendar
 * @since 0.3
 */
if ( ! class_exists( 'FT_CAL_Options' ) ) {

	/**
	 * This class controls options
	 *
	 * @since 0.3
	 */
	class FT_CAL_Options {
		
		/**
		 * An array of core options
		 *
		 * @since 0.3
		 */
		var $calendar_options 		= array();
		
		/**
		 * An array of additional options added by other plugins or developers
		 *
		 * @since 0.3
		 */
		var $additional_options		= array();
		
		/**
		 * An array of post types that the calendar is allowed to be attached to
		 *
		 * @since 0.3
		 */
		var $post_types				= array();
		
		/**
		 * Boolean to show support link on calendar
		 *
		 * @since 0.3.2
		 */
		var $show_support			= true;
		
		/**
		 * Boolean to enable SMART ordering for queries
		 *
		 * @since 1.1.7
		 */
		var $smart_ordering			= true;
		
		/**
		 * Boolean to include Recurring End dates in SMART ordering for queries
		 *
		 * @since 1.1.7
		 */
		var $include_recurring_end	= false;
		
		/**
		 * Boolean to include event Schedule in the Post
		 *
		 * @since 1.1.8
		 */
		var $show_post_schedule	= false;
		
		/**
		 * String to tell where the schedule should be displayed within the post content
		 *
		 * @since 1.1.8
		 */
		var $before_after	= 'before';
		
		/**
		 * PHP Constructor
		 *
		 * @since 0.3
		 */
		function ft_cal_options() {
			
			$this->set_options();
		
		}
		
		/**
		 * Boolean to include event Schedule in the Post
		 *
		 * @since 1.1.11
		 */
		var $use_event_date_as_pubdate	= false;
						
		/**
		 * Set default options
		 *
		 * 1) Grab current options in DB
		 * 2) Grab default plugin options
		 * 3) Merge options, giving preference to those in DB
		 * 3) Update the DB
		 * 4) Update the class properties
		 *
		 * @since 0.3
		 */
		function set_options(){
			
			$current 	= $this->get_calendar_options();
			$defaults	= $this->get_default_options();

			// Merge existing options with default options, giving existing options preference.
			$options = $this->parse_option_args( $current, $defaults );
			
			// Update options and set properties
			update_option( 'ft_calendar_options', $options );
			
			$this->options 				= $options;
			$this->calendar_options 	= $options['calendar'];
			$this->additional_options	= $options['additional'];
		
		}
						
		/**
		 * Get Calendar Options
		 *
		 * 1) Grab current options in DB
		 * 2) Grab default plugin options
		 * 3) Merge options, giving preference to those in DB
		 * 3) Update the DB
		 * 4) Update the class properties
		 *
		 * @since 0.3.2
		 */
		function get_calendar_options(){
			
			return get_option( 'ft_calendar_options' );
		
		}

		/**
		 * Access to default options
		 *
		 * @since 0.3
		 */
		function get_default_options(){
			
			// The following is an array of all options organized by group, name, default value
			// Calendar Options
			$calendar_options = array(
				'who_manages_calendars'			=> array( 'administrator' ),
				'who_populates_calendars'		=> array( 'administrator', 'editor', 'author', 'contributor' ),
				'attach_events_to_post_types'	=> array( 'post' ),
				'calendar_label_singular'		=> __( 'Calendar' ),
				'calendar_label_plural'			=> __( 'Calendars' ),
				'show_support'					=> true,
				'smart_ordering'				=> true,
				'include_recurring_end'			=> false,
				'show_post_schedule'			=> false,
				'before_after'					=> 'before',
				'use_event_date_as_pubdate'		=> false
			);
			$calendar_options = apply_filters( 'ft_cal_calendar_default_options', $calendar_options );
						
			// Allow other plugins to add additional options
			$additional_options = apply_filters( 'ft_cal_additional_default_options', array() );
			
			// Options Array
			$options = array( 
				'calendar' 		=> $calendar_options, 
				'additional'	=> $additional_options
			);
			
			return apply_filters( 'ft_cal_default_options', $options );
		
		}
		
		/**
		 * Parses the args (compensates for multidimentional array)
		 *
		 * @since 0.3
		 */
		function parse_option_args( $new, $old ) {
			
			// parse dimentional options
			if ( isset( $new['calendar'] ) && isset( $old['calendar'] ) )
				$options['calendar'] 	= wp_parse_args( $new['calendar'], $old['calendar'] );
			elseif ( isset( $new['calendar'] ) && !isset( $old['calendar'] ) )
				$options['calendar']	= $new['calendar'];
			elseif ( !isset( $new['calendar'] ) && isset( $old['calendar'] ) )
				$options['calendar']	= $old['calendar'];
			
			if ( isset( $new['additional'] ) && isset( $old['additional'] ) )
				$options['additional'] 	= wp_parse_args( $new['additional'], $old['additional'] );
			elseif ( isset( $new['additional'] ) && !isset( $old['additional'] ) )
				$options['additional']	= $new['additional'];
			elseif ( !isset( $new['additional'] ) && isset( $old['additional'] ) )
				$options['additional']	= $old['additional'];
			
			return $options;
		
		}
		
		/**
		 * Installs the database table
		 *
		 * @uses dbDelta()
		 * @since 0.3
		 * @link http://codex.wordpress.org/Creating_Tables_with_Plugins#Creating_or_Updating_the_Table
		 */
		function install_ftcal_table() {
		
			global $wpdb;

			$ftcalendar_events = $wpdb->prefix . "ftcalendar_events";

			$sql = "CREATE TABLE " . $ftcalendar_events . " (
				id bigint(20) NOT NULL AUTO_INCREMENT,
				calendar_id bigint(20) NOT NULL,
				post_parent bigint(20) NOT NULL,
				start_datetime datetime NOT NULL,
				end_datetime datetime NOT NULL,
				all_day BOOL NOT NULL DEFAULT 0,
				repeating BOOL NOT NULL DEFAULT 0,
				r_start_datetime datetime NULL,
				r_end BOOL NOT NULL DEFAULT 0,
				r_end_datetime datetime NULL,
				r_type varchar(255),
				r_label varchar(255),
				r_every tinyint(2),
				r_on varchar(7),
				r_by tinyint(1),
				UNIQUE KEY id (id)
				);";
			
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta( $sql );
			
			update_option( 'ft_cal_db_version', FT_CAL_DB_VERSION );
				
		}
		
		/**
		 * Performs any necessary updates
		 *
		 * @since 1.1.7
		 */
		function do_ftcal_update() {
		
			$current_version = get_option( 'ft_cal_version' );
		
			if ( version_compare( $current_version, '1.1.7', '<' ) ) {
				$this->upgrade_to_1_1_7();
			}
			
			update_option( 'ft_cal_version', FT_CAL_VERSION );
				
		}
		
		/**
		 * Upgrade for anything below 1.1.7
		 *
		 * @since 1.1.7
		 *
		 * UPDATE all calendar data, so r_end_datetime is NULL of r_end is 0
		 *
		 */
		function upgrade_to_1_1_7() {
			
			global $wpdb;
			
			$wpdb->query( "UPDATE " . $wpdb->prefix . "ftcalendar_events SET r_end_datetime = NULL WHERE r_end = 0" );
				
		}
	}
}

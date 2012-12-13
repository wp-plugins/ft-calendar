<?php
/**
 * @package FT_Calendar
 *
 * This script modifies and displays the default feed templates for FT Calendar taxonomy feeds.
 *
 * @since 0.3
 * @TODO If someone sets a weekly event to occur every Tuesday, but has the start date 
 * set to a different day, this can cause iCal to display the start date as one event
 * and the recurring dates as other events. In my opinion this is a bug in the iCal
 * implementation, but it is possible that it could cause some unforseen issues.
 */
if ( ! class_exists( 'FT_CAL_Feeds' ) ) {

	/**
	 * This class controls options
	 *
	 * @since 0.3
	 */
	class FT_CAL_Feeds {
		
		/**
		 * PHP Constructor
		 *
		 * @since 1.1.6
		 */
		function ft_cal_feeds() {
			
			add_action( 'init', 		array( &$this, 'add_ical_feed' ), 1 );
			add_action( 'do_feed_rdf', 	array( &$this, 'do_feed_rdf' ), 1, 1 );
			add_action( 'do_feed_atom', array( &$this, 'do_feed_atom' ), 1, 1 );
			add_action( 'do_feed_rss', 	array( &$this, 'do_feed_rss' ), 1, 1 );
			add_action( 'do_feed_rss2', array( &$this, 'do_feed_rss2' ), 1, 1 );
			add_action( 'do_feed_ical', array( &$this, 'do_feed_ical' ), 1, 1 );
		
		}
		
		/**
		 * Load the RDF RSS 0.91 Feed template
		 *
		 * @since 1.1.6
		 */
		function do_feed_rdf() {
			
			if ( $calendar = get_query_var( 'ftcalendar' ) ) {
				
				load_template( FT_CAL_PATH . '/includes/feed-rdf.php' );
				
				remove_action( 'do_feed_rdf', 'do_feed_rdf', 10, 1 );
				
			}
			
		}
		
		/**
		 * Load the RSS 1.0 Feed Template
		 *
		 * @since 1.1.6
		 */
		function do_feed_rss() {
			
			if ( $calendar = get_query_var( 'ftcalendar' ) ) {
				
				load_template( FT_CAL_PATH . '/includes/feed-rss.php' );
				
				remove_action( 'do_feed_rss', 'do_feed_rss', 10, 1 );
				
			}
			
		}
		
		/**
		 * Load either the RSS2 comment feed or the RSS2 posts feed.
		 *
		 * @since 1.1.6
		 *
		 * @param bool $for_comments false for normal all ftcalendar feeds
		 */
		function do_feed_rss2( $for_comments ) {
			
			if ( $for_comments )
				return;
			
			if ( $calendar = get_query_var( 'ftcalendar' ) ) {
				
				load_template( FT_CAL_PATH . '/includes/feed-rss2.php' );
				
				remove_action( 'do_feed_rss2', 'do_feed_rss2', 10, 1 );
				
			}
			
		}
		
		/**
		 * Load either Atom comment feed or Atom posts feed.
		 *
		 * @since 2.1.0
		 *
		 * @param bool $for_comments false for normal all ftcalendar feeds
		 */
		function do_feed_atom( $for_comments ) {
			
			if ( $for_comments )
				return;
			
			if ( $calendar = get_query_var( 'ftcalendar' ) ) {
				
				load_template( FT_CAL_PATH . '/includes/feed-atom.php' );
				
				remove_action( 'do_feed_atom', 'do_feed_atom', 10, 1 );
				
			}
			
		}
		
		/**
		 * Load the ICAL Feed template
		 *
		 * @since 1.1.8
		 * @TODO Why does get_query_var not work for non-permalinked sites here? but work for all other feeds?
		 */
		function do_feed_ical() {
			
			global $wp_rewrite;
			
			if ( ( $wp_rewrite->using_permalinks() && $calendar = get_query_var( 'ftcalendar' ) )  ||
					( isset( $_GET['ftcalendar'] ) && $calendar = $_GET['ftcalendar'] ) ) {
				
				load_template( FT_CAL_PATH . '/includes/feed-ical.php' );

				remove_action( 'do_feed_ical', 'do_feed_ical', 10, 1 );
				
			}
			
		}
		
		/**
		 * Add ICAL Feed Type
		 *
		 * @since 1.1.8
		 */
		function add_ical_feed() {
		
			add_feed( 'ical', array( &$this, 'do_feed_ical' ) );
				
		}
		
		/**
		 * Get calendar data for type of feed being displayed
		 *
		 * @since 1.1.6
		 *
		 */
		function get_calendar_data() {
				
			global $ft_cal_calendars, $ft_cal_shortcodes;
			$title = " ";
			
			$defaults = array( 
				'type'			=> 'month',
				'calendars'		=> 'all',
				'span'			=> '+1 Month'
			);
		
			if ( isset( $_GET['type'] ) )
				$atts['type'] = $_GET['type'];
		
			if ( isset( $_GET['span'] ) )
				$atts['span'] = $_GET['span'];
		
			if ( $cal = get_query_var( 'ftcalendar' ) )
				$atts['calendars'] = $cal;
				
			if ( 'ical' == get_query_var( 'feed' ) )
				$atts['type'] = 'ical';
			
			// Merge defaults with passed atts
			// Extract (make each array element its own PHP var
			$args = shortcode_atts( $defaults, $atts );
			
			extract( $args );
			
			$cur_date = getdate( current_time( 'timestamp' ) );
			$date = $cur_date['year'] . "-" . $cur_date['mon'] . "-" . $cur_date['mday'];
			
			switch( $type ) {
			
				case 'daily' :
					$str_start_date = strtotime( date_i18n( 'Y-m-d',  strtotime( $date ) ) );
					$start_date		= date_i18n( 'Y-m-d', $str_start_date );
					$end_date		= $start_date;
					
					$cal_data_arr 	= $ft_cal_calendars->get_ftcal_data_ids( $start_date, $end_date, $calendars );
					$title 			.= __( '(today)', 'ftcalendar' );
					break;
				
				case 'weekly' :
					$str_date		= strtotime( $date );
					$wday 			= date_i18n( 'w', $str_date );
					$str_start_date = strtotime( "-" . $wday . " Day", $str_date );
					$start_date		= date_i18n( 'Y-m-d', $str_start_date );
					$str_end_date 	= strtotime( date_i18n( 'Y-m-d', strtotime( "+7 Days", $str_start_date ) ) ) - 1;
					$end_date		= date_i18n( 'Y-m-d', $str_end_date );
					
					$cal_data_arr 	= $ft_cal_calendars->get_ftcal_data_ids( $start_date, $end_date, $calendars );
					$title 			.= __( '(this week)', 'ftcalendar' );
					break;
				
				case 'upcoming' :
					$start_date	= date_i18n( 'Y-m-d' );
					$end_date 	= date_i18n( 'Y-m-d', strtotime( rawurldecode( $span ) ) );
					
					$cal_data_arr 	= $ft_cal_calendars->get_ftcal_data_ids( $start_date, $end_date, $calendars );
					$title 			.= __( '(upcoming)', 'ftcalendar' );
					break;
				
				case 'ical' :
					$start_date	= date_i18n( 'Y-m-d' );
					$end_date 	= date_i18n( 'Y-m-d', strtotime( "+1 Year" ) );
					
					$cal_data_arr 	= $ft_cal_calendars->get_ftcal_data_ids( $start_date, $end_date, $calendars );
					$title 			= __( 'ical', 'ftcalendar' );
					break;
					
				default : //monthly
					$str_date		= strtotime( date_i18n( 'Y-m-01', strtotime( $date ) ) );
					$start_date		= date_i18n( 'Y-m-d', $str_date );
					$str_last_day	= strtotime( "+1 Month", $str_date ) - 1;
					$end_date		= date_i18n( 'Y-m-d', $str_last_day  );
					
					$cal_data_arr 	= $ft_cal_calendars->get_ftcal_data_ids( $start_date, $end_date, $calendars );
					$title 			.= __( '(this month)', 'ftcalendar' );
					break;
					
			}
			
			return array( $cal_data_arr, $title );
			
		}
		
		/**
		 * Hack for "all" reserved word, this is how we show and tag "All Calendars" in the feed
		 *
		 * @since 1.1.6
		 *
		 */
		function wp_title_rss( $sep = '&#187;' ) {
			
			if ( 'all' == get_query_var( 'ftcalendar' ) ) {
				
				echo apply_filters( 'get_wp_title_rss', __( " $sep All Calendars" ) );
				
			} else {
				
				wp_title_rss( $sep ); 
				
			}
			
		}
		
		/**
		 * Added FT Calendar taxonomy term names to category listings
		 *
		 * @since 1.1.6
		 *
		 */
		function the_category_rss( $type = null, $calendar_id ) {
			if ( empty($type) )
				$type = get_default_feed();
			$calendar = get_term( $calendar_id, 'ftcalendar' );
			$output = '';
		
			$filter = 'rss';
			if ( 'atom' == $type )
				$filter = 'raw';
		
			if ( !empty( $calendar ) ) {
				
				$calendar_name = sanitize_term_field( 'name', $calendar->name, $calendar->term_id, 'ftcalendar', $filter );
			
				if ( 'rdf' == $type )
				
					$output = "\t\t<dc:subject><![CDATA[$calendar_name]]></dc:subject>\n";
					
				elseif ( 'atom' == $type )
				
					$output = sprintf( '<category scheme="%1$s" term="%2$s" />', esc_attr( apply_filters( 'get_bloginfo_rss', get_bloginfo( 'url' ) ) ), esc_attr( $calendar_name ) );
					
				else
				
					$output = "\t\t<category><![CDATA[" . @html_entity_decode( $calendar_name, ENT_COMPAT, get_option('blog_charset') ) . "]]></category>\n";
				
			}
		
			echo apply_filters('the_category_rss', $output, $type);
		}
		
		/**
		 * Return event details for feed descriptions
		 *
		 * @since 1.1.6
		 *
		 */
		function get_the_rss_event_details( $post_id = null ) {
			
			global $post, $ft_cal_events;
			
			if ( is_null( $post_id ) )
				$post_id = $post->ID;
			
			$dateformat = get_option('date_format');
			$timeformat = get_option('time_format');
		
			$output = '';
			
			if ( isset( $post_id ) && $ftcal_data = $ft_cal_events->get_ftcal_data( $post_id ) ) {
				
				foreach ( (array)$ftcal_data as $entry ) {
					
					$start_date = date_i18n( $dateformat, strtotime( $entry->start_datetime ));
					$end_date = date_i18n( $dateformat, strtotime( $entry->end_datetime ));
					
					if ( $entry->all_day ) {
					
						if ( $start_date == $end_date )
							$output .= $start_date;
						else
							$output .= $start_date . " " . __( "to" ) . " " . $end_date;
					
					} else {
					
						$start_time = date_i18n( $timeformat, strtotime( $entry->start_datetime ) );
						$end_time = date_i18n( $timeformat, strtotime( $entry->end_datetime ) );
						
						if ( $start_date == $end_date )
							$output .= $start_date . " - " . $start_time . " " . __( "to" ) . " " . $end_time;
						else
							$output .= $start_date . " - " . $start_time . " " . __( "to" ) . " " . $end_date . " " . $end_time;
					
					}
						
					if ( $entry->repeating )
						$output .= "&nbsp;( " . __ ( "Repeating" ) . " " . $entry->r_label . " )";
						
					$output .= "<br />\n";
					
				}
				
			}
				
			return $output . "<br />\n";
			
		}

	}
	
}
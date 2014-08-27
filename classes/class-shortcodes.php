<?php
/**
 * @package FT_Calendar
 * @since 0.3
 */
if ( ! class_exists( 'FT_CAL_ShortCodes' ) ) {
	
	/**
	 * This class defines and returns the shortcodes
	 *
	 * @since 0.3
	 */
	class FT_CAL_ShortCodes {
		
		/**
		 * Class Constructor
		 *
		 * @TODO Add FT prefix to shortcode
		 * @since 0.3
		 */
		function ft_cal_shortcodes() {
						
			add_action( 'wp_enqueue_scripts', array( $this, 'ftcalendar_shortcode_wp_enqueue_scripts' ) );
				
			add_action( 'wp_ajax_nopriv_large_calendar_change', array( &$this, 'do_ftcal_large_calendar_change' ) );
			add_action( 'wp_ajax_large_calendar_change', array( &$this, 'do_ftcal_large_calendar_change' ) );
			add_action( 'wp_ajax_nopriv_thumb_month_change', array( &$this, 'do_ftcal_thumb_month_change' ) );
			add_action( 'wp_ajax_thumb_month_change', array( &$this, 'do_ftcal_thumb_month_change' ) );
				
			add_shortcode( 'ftcalendar_list', array( &$this, 'do_ftcal_event_list' ) );
			add_shortcode( 'ftcalendar', array( &$this, 'do_ftcal_large_calendar' ) );
			add_shortcode( 'ftcalendar_thumb', array( &$this, 'do_ftcal_thumb_calendar' ) );
			add_shortcode( 'ftcalendar_post_schedule', array( &$this, 'do_ftcal_post_schedule' ) );
		
		}
		
		function ftcalendar_shortcode_wp_enqueue_scripts() {
		
			wp_enqueue_style( 'ft-cal-single-post-page-shorts', FT_CAL_URL . '/includes/css/single-post-page-shorts.css' );
			wp_enqueue_script( 'jquery-tooltip', FT_CAL_URL . '/includes/js/jquery.tools.min.js', array( 'jquery' ) );
			wp_enqueue_script( 'ft-cal-single-post-page-shorts-js', FT_CAL_URL . '/includes/js/single-post-page-shorts.js', array( 'jquery' ) );
			wp_localize_script( 'ft-cal-single-post-page-shorts-js', 'FTCajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			
		}
		
		/**
		 * Shortcode for Print List
		 * Defaults set in ft_cal_calendars::get_ftcal_data_ids
		 *
		 * @TODO See if we can use the walker class for the nested loops
		 * @since 0.3
		 */
		function do_ftcal_event_list( $atts ) {
			
			global $ft_cal_events, $ft_cal_calendars, $ft_cal_options, $wp_rewrite, $post;
			$timeformat = get_option( 'time_format' );
			$list = "";
				
			$defaults = array( 
				'type'					=> 'list',
				'date'					=> '',
				'span'					=> '+1 Month',
				'calendars'				=> 'all',
				'class'					=> '',
				'limit'					=> 0,
				'dateformat'			=> 'jS',
				'timeformat'			=> $timeformat,
				'monthformat'			=> 'F Y',
				'event_template'		=> '<a href="%URL%">%TITLE% (%TIME%)</a>',
				'date_template'			=> '%DATE%',
				'month_template'		=> '%MONTH%',
				'show_rss_feed'			=> 'on',
				'show_ical_feed'		=> 'on',
				'show_post_schedule'	=> 'off',
				'hide_duplicates'		=> 'off'
			);
			
			// Merge defaults with passed atts
			// Extract (make each array element its own PHP var
			extract( shortcode_atts( $defaults, $atts ) );
			
			// Set the CLASS to the current calendar name being set.
			if ( '%CALNAME%' == $class ) {
				
				$class = str_replace( ',', ' ', $calendars );
				
			}
			
			if ( apply_filters( 'ftc_start_at_midnight', false ) ) {
				
				$start_time = "00:00:00";
				
			} else {
				
				$start_time = "H:i:s";
				
			}
			
			if ( apply_filters( 'ftc_end_at_midnight', false ) ) {
				
				$end_time = "23:59:59";
				
			} else {
				
				$end_time = "H:i:s";
				
			}
			
			if ( isset( $_GET['date'] ) )
				$date = $_GET['date'];
			
			if ( empty( $date ) ) 
				$start_date	= date_i18n( 'Y-m-d ' . $start_time );
			else
				$start_date = date_i18n( 'Y-m-d ' . $start_time, strtotime( $date ) );
				
			
			if ( empty( $date ) ) 
				$end_date = date_i18n( 'Y-m-d ' . $end_time, strtotime( $span ) );
			else
				$end_date = date_i18n( 'Y-m-d ' . $end_time, strtotime( $span, strtotime( $date ) ) );
				
			$cal_data_arr = $ft_cal_calendars->get_ftcal_data_ids( $start_date, $end_date, $calendars );
			$cal_entries = $this->parse_calendar_data( $start_date, $end_date, $cal_data_arr, false, false );
				
			if ( ! empty( $cal_entries ) ) {
				
				$original_post = $post;
				
				$break = false;
				$count = 1;
				
				$list .= "<div id='ftcalendar-list-div' class='ftcalendar ftlistcalendar " . $class . " " . $type . "'>";
		
				if ( 'on' == $show_rss_feed || 'on' == $show_ical_feed ) {
					
					$list .= "<div id='ftcalendar-feeds' style='width: auto; float: right;'>";
					
					if ( 'all' === $calendars )
						$feed_title = __( 'All Calendars', 'ftcalendar' );
					else
						$feed_title = $calendars;
					
					$site_url = get_bloginfo( 'url' );
				
					if ( 'on' == $show_rss_feed ) {
							
						if ( $wp_rewrite->using_permalinks() ) {
							
							$list .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/?type=upcoming&span=' . rawurlencode( $span ) . '&limit=' . $limit . '" title="' . __( 'Upcoming Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
						
						} else {
							
							$list .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/?ftcalendar=' . $calendars . '&feed=' . get_default_feed() . '&type=upcoming&span=' . rawurlencode( $span ) . '&limit=' . $limit . '" title="' . __( 'Upcoming Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
							
						}
								
					}
					
					if ( 'on' == $show_ical_feed ) {
							
						if ( $wp_rewrite->using_permalinks() ) {
							
							$list .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/ical/" title="' . __( 'iCal Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
						
						} else {
							
							$list .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/?ftcalendar=' . $calendars . '&feed=ical" title="' . __('iCal Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
							
						}
								
					}
								
					$list .= "</div>";
				
				}
				
				//$list .= "<ul>";
				$last_month = 0;
				foreach ( (array)$cal_entries as $date => $times ) {
					
					$str_date = strtotime( $date );
					$cur_month = date_i18n( 'n', $str_date );
					
					if ( isset( $event_date ) 
							&& ( !isset( $event_date ) || $event_date != date_i18n( $dateformat, $str_date ) ) )
						$list .= "</ul>";
					
					if ( isset( $event_month ) 
						&& ( !isset( $event_month) || $event_month != date_i18n( $monthformat, $str_date ) ) )
						$list .= "</ul>";
					
					if ( !empty( $month_template ) 
							&& ( !isset( $event_month) || $event_month != date_i18n( $monthformat, $str_date ) ) ) {
						
						$event_month = date_i18n( $monthformat, $str_date );
						$list .= "<ul>";
						$list .= "<li>" . str_replace( '%MONTH%', $event_month, $month_template ) . "</li>";
					
					}
					
					if ( !empty( $date_template ) 
							&& ( !isset( $event_date ) || $event_date != date_i18n( $dateformat, $str_date ) ) ) {
						
						$event_date = date_i18n( $dateformat, $str_date );
						$list .= "<ul>";
						$list .= "<li>" . str_replace( '%DATE%', $event_date, $date_template ) . "</li>";
					
					}
					
					ksort( $times );
					
					foreach ( (array)$times as $time => $event_ids ) {
						
						$list .= "<ul>";
						
						foreach ( (array)$event_ids as $event_id ) {
							
							$data = array();
							
							$post = get_post( $cal_data_arr[$event_id]->post_parent );
							setup_postdata( $post );
								
							if ( $cal_data_arr[$event_id]->all_day )
								$data['TIME'] 	= __( 'All day', 'ftcalendar' );
							else
								$data['TIME'] 	= date_i18n( $timeformat, strtotime( $time ) );
							
							$data['MONTH'] 			= date_i18n( $monthformat, strtotime( $date ) );
							$data['DATE'] 			= date_i18n( $dateformat, strtotime( $date ) );		
							$data['LINK'] 			= get_permalink( $post->ID );
							$data['URL'] 			= get_permalink( $post->ID );
							$data['TITLE'] 			= get_the_title( $post->ID );
							$calendar_term			= get_term_by( 'id', (int)$cal_data_arr[$event_id]->calendar_id, 'ftcalendar' );

							if ( !empty( $calendar_term ) ) {
								
								$data['CALNAME']		= $calendar_term->name;
								$data['CALSLUG']		= $calendar_term->slug;
								
							}
							
							if ( !( $picture = apply_filters( 'feature_image', get_post_meta( $post->ID, 'feature_image', true ), $post->ID ) ) ) {
								if ( function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID ) ) {
									
									$feature_image = get_post_thumbnail_id( $post->ID );
									list( $picture, $width, $height ) = wp_get_attachment_image_src( $feature_image );
									
								} 
								
							}	
							
							if ( !empty( $picture ) )
								$data['FEATUREIMAGE']	= '<img src="' . $picture . '" class="ft_cal_feature_image" />';							
							else
								$data['FEATUREIMAGE'] = '';
								
							if ( 'on' == $show_post_schedule ) {
								
								add_filter( 'the_content', array( &$ft_cal_events, 'get_post_schedule' ) );
								add_filter( 'the_excerpt', array( &$ft_cal_events, 'get_post_schedule' ) );
								
								$data['CONTENT']		= apply_filters( 'the_content', strip_shortcodes( $post->post_content ) );
								$data['EXCERPT']		= apply_filters( 'get_the_excerpt', strip_shortcodes( $post->post_excerpt ) );
								
								if ( !$ft_cal_options->calendar_options['show_post_schedule'] ) {
									
									remove_filter( 'the_content', array( &$ft_cal_events, 'get_post_schedule' ) );
									remove_filter( 'the_excerpt', array( &$ft_cal_events, 'get_post_schedule' ) );
									
								}
								
							} else {
								
								remove_filter( 'the_content', array( &$ft_cal_events, 'get_post_schedule' ) );
								remove_filter( 'the_excerpt', array( &$ft_cal_events, 'get_post_schedule' ) );
								
								$data['CONTENT']		= apply_filters( 'the_content', strip_shortcodes( $post->post_content ) );
								$data['EXCERPT']		= apply_filters( 'get_the_excerpt', strip_shortcodes( $post->post_excerpt ) );
								
								if ( $ft_cal_options->calendar_options['show_post_schedule'] ) {
									
									add_filter( 'the_content', array( &$ft_cal_events, 'get_post_schedule' ) );
									add_filter( 'the_excerpt', array( &$ft_cal_events, 'get_post_schedule' ) );
									
								}
								
							}
							
							// get author details
							$author = get_userdata( $post->post_author );
							
							$data['AUTHOR'] 	= $author->display_name;
							
							$data = apply_filters( 'ftc_custom_replacement_tags', $data, $post, $cal_data_arr[$event_id] );
			
							$event_li = "<li>" . $this->ftc_str_replace( $event_template, $data ) . "</li>";
							
							if ( 'on' == $hide_duplicates && empty( $date_template ) && $last_month == $cur_month
									&& preg_match( '/' . preg_quote( $event_li, '/' ) . '/', $list ) ) {
							
								$list .= '';
								
							} else {
							
								$list .= $event_li;
								
							}
							
							clean_post_cache( $post->ID );
							
							if ( 0 != $limit && ++$count > $limit ) {
								
								$break = true;
								break;
								
							}
							
							clean_post_cache( $post->ID );
							
						}
						
						$list .= "</ul>";
						
						if ( $break ) break;
						
					}
					
					$post = $original_post;
					
					$last_month = $cur_month;
					
					if ( $break ) break;
				
				}
			
				if ( isset( $event_date ) )
					$list .= "</ul>";
				
				if ( isset( $event_month ) )
					$list .= "</ul>";
				
				//$list .= "</ul>";
				$list .= "</div>";
				
			}
			
			return $list;
			
		}
		
		/**
		 * Change the calendar via AJAX
		 *
		 * @TODO Clean POST params
		 * @since 0.3
		 */
		function do_ftcal_large_calendar_change() {
			
			if ( isset( $_POST ) ) {
					
				$atts = array( 
					'date'				=> $_POST['date'],
					'type'				=> $_POST['type'],
					'heading_label'		=> $_POST['heading_label'],
					'calendars'			=> $_POST['calendars'],
					'class'				=> $_POST['tableclass'],
					'width'				=> $_POST['width'],
					'height'			=> $_POST['height'],
					'legend'			=> $_POST['legend'],
					'types'				=> $_POST['types'],
					'dateformat'		=> $_POST['dateformat'],
					'timeformat'		=> $_POST['timeformat'],
					'show_rss_feed'		=> $_POST['show_rss_feed'],
					'show_ical_feed'	=> $_POST['show_rss_feed'],
					'hide_duplicates'	=> $_POST['hide_duplicates']
				);
				
				$table = $this->do_ftcal_large_calendar( $atts );
			
				die( $table );
				
			} else {
			
				die( __( 'ERROR: POST not set...', 'ftcalendar' ) );
			
			}
			
		}
		
		/**
		 * Display large calendar
		 *
		 * @since 0.3
		 */
		function do_ftcal_large_calendar( $atts ) {
			
			global $ft_cal_calendars, $ft_cal_options, $wp_rewrite;
			
			$dateformat = get_option('date_format');
			$timeformat = get_option('time_format');
			
			$defaults = array( 
				'type'				=> 'month',
				'date'				=> null,
				'heading_label'		=> 'partial',
				'calendars'			=> 'all',
				'class'				=> '',
				'width'				=> '',
				'height'			=> '',
				'legend'			=> 'on',
				'types'				=> 'on',
				'dateformat'		=> $dateformat,
				'timeformat'		=> $timeformat,
				'show_rss_feed'		=> 'on',
				'show_ical_feed'	=> 'on',
				'hide_duplicates'	=> 'off'
			);
		
			if ( isset( $_GET['cal'] ) )
				$atts['calendars'] = $_GET['cal'];
			
			// Merge defaults with passed atts
			// Extract (make each array element its own PHP var
			$args = shortcode_atts( $defaults, $atts );
			
			// Set the CLASS to the current calendar name being set.
			if ( '%CALNAME%' == $args['class'] ) {
				
				$args['class'] = str_replace( ',', ' ', $args['calendars'] );
				
			}
			
			if ( 'on' == $args['types'] ) {
				
				if ( isset( $_GET['type'] ) )
					$args['type'] = $_GET['type'];
				
			}
			
			switch ( $args['type'] ) {
				
				case 'day' :
					$calendar = $this->get_day_calendar( $args );
					break;
				
				case 'week' :
					$calendar = $this->get_week_calendar( $args );
					break;
				
				case 'month' :
				default :
					$calendar = $this->get_month_calendar( $args );
					break;
			
			}
			
			return $calendar;
		
		}
		
		/**
		 * Query DB for calendar items for single day
		 *
		 * @since 0.3
		 */
		function get_day_calendar( $args ) {
			
			global $ft_cal_calendars, $ft_cal_options, $wp_rewrite;
			
			$ftcal_meta 	= get_option( 'ftcalendar_meta' );
			$ftcal_options	= $ft_cal_options->get_calendar_options();
			$current_offset = get_option( 'gmt_offset' );
			$permalink 		= get_permalink();
			
			if ( !$wp_rewrite->using_permalinks() && !is_front_page() )
				$sep = "&";
			else
				$sep = "?";
			
			// Merge defaults with passed atts
			// Extract (make each array element its own PHP var
			extract( $args );
		
			$cur_date = getdate( current_time( 'timestamp' ) );
		
			if ( isset( $_GET['date'] ) )
				$date = $_GET['date'];
			else if ( !isset( $date ) )
				$date = $cur_date['year'] . "-" . $cur_date['mon'] . "-" . $cur_date['mday'];
		
			$str_start_date = strtotime( date_i18n( 'Y-m-d',  strtotime( $date ) ) );
			$start_date		= date_i18n( 'Y-m-d', $str_start_date );
			$str_end_date 	= $str_start_date;
			$str_end_time	= strtotime( date_i18n( 'Y-m-d 23:59:59',  $str_end_date ) );
			$end_date		= $start_date;
			$month 			= date_i18n( 'm', $str_start_date );
			$year 			= date_i18n( 'Y', $str_start_date );
			$cur_dow 		= date_i18n( 'w', $str_start_date );
			$style			= '';
			
			$cal_data_arr 	= $ft_cal_calendars->get_ftcal_data_ids( $start_date, $end_date, $calendars );
			$cal_entries 	= $this->parse_calendar_data( $start_date, $end_date, $cal_data_arr );
			
			if ( isset( $width ) && !empty( $width ) )
				$style .= 'width: ' . $width . 'px;';
			
			if ( isset( $height ) && !empty( $height ) )
				$style .= 'height: ' . $height . 'px;';
			
			$prev_date 	= date_i18n( 'Y-m-d',  strtotime( "-1 Day", strtotime( $date ) ) );
			$next_date 	= date_i18n( 'Y-m-d',  strtotime( "+1 Day", strtotime( $date ) ) );
			
			$table 	= "<div id='ftcalendar-div' class='ftcalendar ftlargecalendar " . $class . " " . $type . "' style='" . $style . "'>";
			
			$table  .= "<input type='hidden' id='largecalendar-type' value='" . $type . "' />";
			$table  .= "<input type='hidden' id='largecalendar-heading_label' value='" . $heading_label . "' />";
			$table  .= "<input type='hidden' id='largecalendar-calendars' value='" . $calendars . "' />";
			$table  .= "<input type='hidden' id='largecalendar-class' value='" . $class . "' />";
			$table  .= "<input type='hidden' id='largecalendar-width' value='" . $width . "' />";
			$table  .= "<input type='hidden' id='largecalendar-height' value='" . $height . "' />";
			$table  .= "<input type='hidden' id='largecalendar-legend' value='" . $legend . "' />";
			$table  .= "<input type='hidden' id='largecalendar-types' value='" . $types . "' />";
			$table  .= "<input type='hidden' id='largecalendar-dateformat' value='" . $dateformat . "' />";
			$table  .= "<input type='hidden' id='largecalendar-timeformat' value='" . $timeformat . "' />";
			$table  .= "<input type='hidden' id='largecalendar-show_rss_feed' value='" . $show_rss_feed . "' />";
			$table  .= "<input type='hidden' id='largecalendar-show_ical_feed' value='" . $show_ical_feed . "' />";
			$table  .= "<input type='hidden' id='largecalendar-hide_duplicates' value='" . $hide_duplicates . "' />";
			
			if ( 'on' == $show_rss_feed || 'on' == $show_ical_feed ) {
				
				$table .= "<div id='ftcalendar-feeds'>";
					
				if ( 'all' === $calendars )
					$feed_title = __( 'All Calendars', 'ftcalendar' );
				else
					$feed_title = $calendars;
				
				$site_url = get_bloginfo( 'url' );
				
				if ( 'on' == $show_rss_feed ) {
							
					if ( $wp_rewrite->using_permalinks() ) {
						
						$table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/?type=daily" title="' . __( 'Daily Feed for', 'ftcalendar') . ' ' . $feed_title . '"></a> ';
					
					} else {
						
						$table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/?ftcalendar=' . $calendars . '&feed=' . get_default_feed() . '&type=daily" title="' . __( 'Daily Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
						
					}
				
				}
			
				if ( 'on' == $show_ical_feed ) {
						
					if ( $wp_rewrite->using_permalinks() ) {
						
						$table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/ical/" title="' . __( 'iCal Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
					
					} else {
						
						$table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/?ftcalendar=' . $calendars . '&feed=ical" title="' . __( 'iCal Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
						
					}
							
				}
							
				$table .= "</div>";
			
			}
			
			$table  .= "<div id='ftcalendar-nav'>";
			$table  .= "<span id='ftcalendar-prev'><a class='large-prev' ref='" . $prev_date . "' href='" . $permalink . $sep . "type=day&date=" . $prev_date . "'>" . apply_filters( 'ftcalendar-prev-arrow', '&lArr;' ) . "</a></span>";
			$table	.= "&nbsp;";
			$table  .= "<span id='ftcalendar-next'><a class='large-next' ref='" . $next_date . "' href='" . $permalink .  $sep . "type=day&date=" . $next_date . "'>" . apply_filters( 'ftcalendar-next-arrow', '&rArr;' ) . "</a></span>";
			$table  .= "<span id='ftcalendar-current'>" .  date_i18n( $dateformat, $str_start_date ) . "</span>";
			
			if ( 'on' == $types ) {
				
				$table .= "<span id='ftcalendar-types'>";
				$table .= '<a href="' . $permalink . $sep . 'type=day">' . __( 'Day', 'ftcalendar' ) . '</a> ' .
							'<a href="' . $permalink . $sep . 'type=week">' . __( 'Week', 'ftcalendar' ) . '</a> ' .
							'<a href="' . $permalink . $sep . 'type=month">' . __( 'Month', 'ftcalendar' ) . '</a>';
				$table .= "</span>";
			
			}
			
			$table .= "</div>";
			$table .= "<table id='ftcalendar-table' class='ftcalendar " . $class . "' style='" . $style . "'>";
			
			// Set table headings
			$headings = $this->get_headings( $heading_label );
			if ( !empty( $headings ) ) {
			
				// Verify GMT Offset later HEREHEREHERE
				$table .= "<tr>";
				$table .= "<th id='tz'>GMT" . $current_offset . "</td>";
				$table .= "<th id='ftcalendar-heading'>" . $headings[$cur_dow] . " " .  date_i18n( $dateformat, $str_start_date ) . "</th>";
				$table .= "</tr>";
				
			}
			
			if ( isset( $cal_entries[$start_date] ) ) {
				
				ksort( $cal_entries[$start_date] );
				
				foreach ( (array)$cal_entries[$start_date] as $time => $events ) {
					
					$duplicate_event_array = array(); //reinitialize for ever day
					
					foreach ( (array)$events as $event_id ) {
						
						if ( $cal_data_arr[$event_id]->all_day )
							$label = __( 'All day', 'ftcalendar' );
						else
							$label = date_i18n( $timeformat, strtotime( $cal_data_arr[$event_id]->start_datetime ) ) . ' - '  . date_i18n( $timeformat, strtotime( $cal_data_arr[$event_id]->end_datetime ) );
							
						$event_title = get_the_title( $cal_data_arr[$event_id]->post_parent );
							
						if ( 'on' == $hide_duplicates && in_array( $label . $event_title, $duplicate_event_array ) )
							continue;
						
						$table .= "<tr>";
						$table .= '<td class="ftcalendar-times">' . $label . '</td>';
						
						$style = 'color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
						$table .= "<td class='ftcalendar-event'><a style='" . $style . "' href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'>" . $event_title . "</a></td>";
						$table .= "</tr>";
						
						$duplicate_event_array[] = $label . $event_title;
					
					}
				}
				
			} else {
				
				$table .= "<td class='ftcalendar-event' style='text-align: center;' colspan=2>" . __( 'No Events Found', 'ftcalendar' ) . "</td>";
				
			}
			
			$table .= "</table>";
			
			if ( 'on' == $legend )
				$table .= $this->get_legend( $type, $start_date );
			
			if ( $ftcal_options['calendar']['show_support'] )
				$table .= $this->show_support();
			
			$table .= "<div class='ftc-clearboth'></div>";
			$table .= "</div>";
			
			return $table;
			
		}
		
		function get_week_calendar( $args ) {
			
			global $ft_cal_calendars, $ft_cal_options, $wp_rewrite;
			
			$ftcal_meta 	= get_option( 'ftcalendar_meta' );
			$ftcal_options	= $ft_cal_options->get_calendar_options();
			$permalink = get_permalink();
			
			if ( !$wp_rewrite->using_permalinks() && !is_front_page() )
				$sep = "&";
			else
				$sep = "?";
			
			// Merge defaults with passed atts
			// Extract (make each array element its own PHP var
			extract( $args );

			$cur_date = getdate( current_time( 'timestamp' ) );
		
			if ( isset( $_GET['date'] ) )
				$date = $_GET['date'];
			else if ( !isset( $date ) )
				$date = $cur_date['year'] . "-" . $cur_date['mon'] . "-" . $cur_date['mday'];
		
			$str_date		= strtotime( $date );
			$wday 			= date_i18n( 'w', $str_date );
			$mday			= date_i18n( 'j', $str_date );
			$month 			= date_i18n( 'm', $str_date );
			$year 			= date_i18n( 'Y', $str_date );
			
			$cur_day		= date_i18n( 'j' );
			$cur_month		= date_i18n( 'n' );
			$working_month 	= date_i18n( 'm', $str_date );
			$cur_year		= date_i18n( 'Y' );
			
			$start_of_week	= get_option('start_of_week');
			$sow_diff = $wday >= $start_of_week ? $wday - $start_of_week : abs( 7 - $start_of_week + $wday );
			$str_start_date = strtotime( "-" . $sow_diff . " Day", $str_date );
			$start_date		= date_i18n( 'Y-m-d', $str_start_date );
			$first_day		= date_i18n( 'j', $str_start_date );
			
			$str_end_date 	= strtotime( date_i18n( 'Y-m-d', strtotime( "+7 Days", $str_start_date ) ) ) - 1;
			$end_date		= date_i18n( 'Y-m-d', $str_end_date );
			$last_day		= date_i18n( 'j', $str_end_date );
			
			$style			= '';
			
			$cal_data_arr 	= $ft_cal_calendars->get_ftcal_data_ids( $start_date, $end_date, $calendars );
			$cal_entries 	= $this->parse_calendar_data( $start_date, $end_date, $cal_data_arr );
			
			if ( isset( $width ) && !empty( $width ) )
				$style .= 'width: ' . $width . 'px;';
			
			if ( isset( $height ) && !empty( $height ) )
				$style .= 'height: ' . $height . 'px;';
		
			$prev_week = date_i18n( 'Y-m-d',  strtotime( "-7 Days", $str_start_date ) );
			$next_week = date_i18n( 'Y-m-d',  strtotime( "+7 Days", $str_start_date ) );
			
			$table = "<div id='ftcalendar-div' class='ftcalendar ftlargecalendar " . $class . " " . $type . "' style='" . $style . "'>";
			
			$table .= "<input type='hidden' id='largecalendar-type' value='" . $type . "' />";
			$table .= "<input type='hidden' id='largecalendar-heading_label' value='" . $heading_label . "' />";
			$table .= "<input type='hidden' id='largecalendar-calendars' value='" . $calendars . "' />";
			$table .= "<input type='hidden' id='largecalendar-class' value='" . $class . "' />";
			$table .= "<input type='hidden' id='largecalendar-width' value='" . $width . "' />";
			$table .= "<input type='hidden' id='largecalendar-height' value='" . $height . "' />";
			$table .= "<input type='hidden' id='largecalendar-legend' value='" . $legend . "' />";
			$table .= "<input type='hidden' id='largecalendar-types' value='" . $types . "' />";
			$table .= "<input type='hidden' id='largecalendar-dateformat' value='" . $dateformat . "' />";
			$table .= "<input type='hidden' id='largecalendar-timeformat' value='" . $timeformat . "' />";
			$table  .= "<input type='hidden' id='largecalendar-show_rss_feed' value='" . $show_rss_feed . "' />";
			$table  .= "<input type='hidden' id='largecalendar-show_ical_feed' value='" . $show_ical_feed . "' />";
			$table  .= "<input type='hidden' id='largecalendar-hide_duplicates' value='" . $hide_duplicates . "' />";
			
			if ( 'on' == $show_rss_feed || 'on' == $show_ical_feed ) {
				
				$table .= "<div id='ftcalendar-feeds'>";
					
				if ( 'all' === $calendars )
					$feed_title = __( 'All Calendars', 'ftcalendar' );
				else
					$feed_title = $calendars;
				
				$site_url = get_bloginfo( 'url' );
				
				if ( 'on' == $show_rss_feed ) {
						
					if ( $wp_rewrite->using_permalinks() ) {
						
						$table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/?type=weekly" title="' . __( 'Weekly Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
					
					} else {
						
						$table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/?ftcalendar=' . $calendars . '&feed=' . get_default_feed() . '&type=weekly" title="' . __( 'Weekly Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
						
					}
					
				}
			
				if ( 'on' == $show_ical_feed ) {
						
					if ( $wp_rewrite->using_permalinks() ) {
						
						$table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/ical/" title="' . __( 'iCal Feed for', 'ftcalendar') . ' ' . $feed_title . '"></a> ';
					
					} else {
						
						$table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/?ftcalendar=' . $calendars . '&feed=ical" title="' . __( 'iCal Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
						
					}
							
				}
							
				$table .= "</div>";
			
			}
			
			$table .= "<div id='ftcalendar-nav'>";
			$table .= "<span id='ftcalendar-prev'><a class='large-prev' ref='" . $prev_week . "' href='" . $permalink . $sep . "type=week&date=" . $prev_week . "'>" . apply_filters( 'ftcalendar-prev-arrow', '&lArr;' ) . "</a></span>";
			$table	.= "&nbsp;";
			$table .= "<span id='ftcalendar-next'><a class='large-next' ref='" . $next_week . "' href='" . $permalink . $sep . "type=week&date=" . $next_week . "'>" . apply_filters( 'ftcalendar-next-arrow', '&rArr;' ) . "</a></span>";
			$table .= "<span id='ftcalendar-current'>" .  date_i18n( $dateformat, $str_start_date ) . ' - ' . date_i18n( $dateformat, $str_end_date ) . "</span>";
			
			if ( 'on' == $types ) {
			
				$table .= "<span id='ftcalendar-types'>";
				$table .= '<a href="' . $permalink . $sep . 'type=day">' . __( 'Day', 'ftcalendar' ) . '</a> ' .
							'<a href="' . $permalink . $sep . 'type=week">' . __( 'Week', 'ftcalendar' ) . '</a> ' .
							'<a href="' . $permalink . $sep . 'type=month">' . __( 'Month', 'ftcalendar' ) . '</a>';
				$table .= "</span>";
			
			}
			
			$table .= "</div>";
			$table .= "<table id='ftcalendar-table' class='ftcalendar " . $class . "' style='" . $style . "'>";
			
			// Set table headings
			$headings = $this->get_headings( $heading_label, $start_of_week );
			if ( !empty( $headings ) ) {
			
				$table .= "<tr>";
				foreach ( (array) $headings as $heading ) {
				
					$table .= "<th id='ftcalendar-headings'>" . $heading . "</th>";
				
				}
				$table .= "</tr>";
				
			}
			
			$link = "";
			$link_end = "";
			
			$table .= "<tr>";
			
			for ( $i = 0; $i < 7; $i++ ) {
				
				$str_time = strtotime( "+" . $i . " Days", $str_start_date );
				$day = date_i18n( 'j', $str_time );
				$month = date_i18n( 'm', $str_time );
				$year = date_i18n( 'Y', $str_time );
				$fordate = date_i18n( 'Y-m-d', $str_time );
				
				if ( $day == $cur_day && $month == $cur_month && $year == $cur_year )
					$current_day_class = 'current_day';
				else
					$current_day_class = '';
				
				if ( $month != $working_month ) {
					$current_month_class = 'unmonth';
				} else {
					$current_month_class = '';
				}
				
				$table .= "<td class='" . $current_day_class . " " . $current_month_class . "'>";
				
				if ( 'on' == $types ) {
					
					$link = "<a href='" . $permalink . $sep . "type=day&date=" . $fordate . "'>";
					$link_end = "</a>";
				
				}
				
				$table .= "<div class='ftcalendar-event-date'>" . $link . $day . $link_end . "</div>";
				
				if ( isset( $cal_entries[$fordate] ) ) {
				
					ksort( $cal_entries[$fordate] );
					
					$table .= "<div class='ftcalendar-events-div'>";
					
					foreach ( (array)$cal_entries[$fordate] as $time => $event_ids ) {
						
						$duplicate_event_array = array(); //reinitialize for ever day						
						
						foreach ( (array)$event_ids as $event_id ) {
							
							$event_title = get_the_title( $cal_data_arr[$event_id]->post_parent );
								
							if ( 'on' == $hide_duplicates && in_array( $cal_data_arr[$event_id]->start_datetime . $event_title, $duplicate_event_array ) )
								continue;
							
							if ( $cal_data_arr[$event_id]->all_day ) {
								
								$style = 'background-color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . '; border-color: #' . $ftcal_meta['ftcal-border-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
								$table .= "<div style='" . $style . "' class='ftcalendar-event'><div style='" . $style . "' ><a href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'>" . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></div></div>";
							
							} else {
								
								$style = 'color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
								$table .= "<div class='ftcalendar-event'><div><a style='" . $style . "' href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'><span class='ftcalendar-event-time'>" . date_i18n( $timeformat, strtotime( $cal_data_arr[$event_id]->start_datetime ) ) . "</span> " . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></div></div>";
							
							}
								
							$duplicate_event_array[] = $cal_data_arr[$event_id]->start_datetime . $event_title;
						
						}
					
					}
					
					$table .= "</div>";
				
				} 
				
				$table .= "</td>";
			
			}
			
			$table .= "</tr>";
			$table .= "</table>";
			
			if ( 'on' == $legend )
				$table .= $this->get_legend( $type, $start_date );
			
			if ( $ftcal_options['calendar']['show_support'] )
				$table .= $this->show_support();
			
			$table .= "<div class='ftc-clearboth'></div>";
			$table .= "</div>";
			
			return $table;
		
		}
		
		/**
		 * Grab a month view of the calendar
		 *
		 * @since 0.3
		 */
		function get_month_calendar( $args ) {
			
			global $ft_cal_calendars, $ft_cal_options, $wp_rewrite;
			
			$ftcal_meta 	= get_option( 'ftcalendar_meta' );
			$ftcal_options	= $ft_cal_options->get_calendar_options();
			$dateformat		= get_option( 'date_format' );
			$timeformat		= get_option( 'time_format' );
			$permalink 		= get_permalink();
			
			if ( !$wp_rewrite->using_permalinks() && !is_front_page() )
				$sep = "&";
			else
				$sep = "?";
			
			// Merge defaults with passed atts
			// Extract (make each array element its own PHP var
			extract( $args );
		
			$cur_date = getdate( current_time( 'timestamp' ) );
		
			if ( isset( $_GET['date'] ) )
				$date = $_GET['date'];
			else if ( !isset( $date ) )
				$date = $cur_date['year'] . "-" . $cur_date['mon'] . "-" . $cur_date['mday'];
		
			$str_date		= strtotime( date_i18n( 'Y-m-01', strtotime( $date ) ) );
			$wday 			= date_i18n( 'w', $str_date );
			$mday			= date_i18n( 'j', $str_date );
			$cur_day		= date_i18n( 'j' );
			$cur_month 		= date_i18n( 'n' );
			$working_month 	= date_i18n( 'm', $str_date );
			$cur_year 		= date_i18n( 'Y' );
			
			$start_of_week	= get_option('start_of_week');
			$sow_diff = $wday >= $start_of_week ? $wday - $start_of_week : abs( 7 - $start_of_week + $wday );
			$str_start_date = strtotime( "-" . $sow_diff . " Day", $str_date );
			$start_date		= date_i18n( 'Y-m-d', $str_start_date );
			$first_day 		= getdate( $str_start_date );
			$cur_dow 		= $first_day['wday'];
			
			$str_last_day	= strtotime( "+1 Month", $str_date ) - 1;
			$last_wday		= date_i18n( 'w', $str_last_day );
			
			if ( $last_wday + 1 == $start_of_week ) {
				$diff = 0;
			} else if ( $last_wday > $start_of_week ) {
				$diff = abs( 6 - ( $last_wday - $start_of_week ) );
			} else {
				$diff = abs( ( $start_of_week + $last_wday ) - 6 );
			}
			
			$str_end_date 	= strtotime( "+" . $diff . " Days", $str_last_day ) - 1;
			$end_date		= date_i18n( 'Y-m-d', $str_end_date );
			$last_day		= date_i18n( 'j', $str_end_date );
			
			$style			= '';
			
			$str_num_weeks	= ( $str_end_date - $str_start_date ) / 86400;  // 24 days * 60 minutes * 60 seconds
			$num_weeks		= ceil( $str_num_weeks / 7 ); // Tells us how many actual weeks are touched in the calendar
			$row_height		= floor( 100 / $num_weeks );
			
			$cal_data_arr = $ft_cal_calendars->get_ftcal_data_ids( $start_date, $end_date, $calendars );
			$cal_entries = $this->parse_calendar_data( $start_date, $end_date, $cal_data_arr );
			
			if ( isset( $width ) && !empty( $width ) )
				$style .= 'width: ' . $width . 'px;';
			
			if ( isset( $height ) && !empty( $height ) )
				$style .= 'height: ' . $height . 'px;';
		
			$prev_month = date_i18n( 'Y-m-d',  strtotime( "-1 Month", $str_date ) );
			$next_month = date_i18n( 'Y-m-d',  strtotime( "+1 Month", $str_date ) );
			
			$table = "<div id='ftcalendar-div' class='ftcalendar ftlargecalendar " . $class . " " . $type . "' style='" . $style . "'>";
			
			$table .= "<input type='hidden' id='largecalendar-type' value='" . $type . "' />";
			$table .= "<input type='hidden' id='largecalendar-heading_label' value='" . $heading_label . "' />";
			$table .= "<input type='hidden' id='largecalendar-calendars' value='" . $calendars . "' />";
			$table .= "<input type='hidden' id='largecalendar-class' value='" . $class . "' />";
			$table .= "<input type='hidden' id='largecalendar-width' value='" . $width . "' />";
			$table .= "<input type='hidden' id='largecalendar-height' value='" . $height . "' />";
			$table .= "<input type='hidden' id='largecalendar-legend' value='" . $legend . "' />";
			$table .= "<input type='hidden' id='largecalendar-types' value='" . $types . "' />";
			$table .= "<input type='hidden' id='largecalendar-dateformat' value='" . $dateformat . "' />";
			$table .= "<input type='hidden' id='largecalendar-timeformat' value='" . $timeformat . "' />";
			$table  .= "<input type='hidden' id='largecalendar-show_rss_feed' value='" . $show_rss_feed . "' />";
			$table  .= "<input type='hidden' id='largecalendar-show_ical_feed' value='" . $show_ical_feed . "' />";
			$table  .= "<input type='hidden' id='largecalendar-hide_duplicates' value='" . $hide_duplicates . "' />";
			
			if ( 'on' == $show_rss_feed || 'on' == $show_ical_feed ) {
				
				$table .= "<div id='ftcalendar-feeds'>";
				
				if ( 'all' === $calendars )
					$feed_title = __( 'All Calendars', 'ftcalendar' );
				else
					$feed_title = $calendars;
				
				$site_url = get_bloginfo( 'url' );
				
				if ( 'on' == $show_rss_feed ) {
						
					if ( $wp_rewrite->using_permalinks() ) {
						
						$table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/?type=monthly" title="' . __('Monthly Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
					
					} else {
						
						$table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/?ftcalendar=' . $calendars . '&feed=' . get_default_feed() . '&type=monthly" title="' . __( 'Monthly Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
						
					}
							
				}
			
				if ( 'on' == $show_ical_feed ) {
						
					if ( $wp_rewrite->using_permalinks() ) {
						
						$table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/ical/" title="' . __( 'iCal Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
					
					} else {
						
						$table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/?ftcalendar=' . $calendars . '&feed=ical" title="' . __( 'iCal Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
						
					}
							
				}
							
				$table .= "</div>";
			
			}
			
			$table .= "<div id='ftcalendar-nav'>";
			$table .= "<span id='ftcalendar-prev'><a class='large-prev' ref='" . $prev_month . "' href='" . $permalink . $sep . "type=month&date=" . $prev_month . "'>" . apply_filters( 'ftcalendar-prev-arrow', '&lArr;' ) . "</a></span>";
			$table	.= "&nbsp;";
			$table .= "<span id='ftcalendar-next'><a class='large-next' ref='" . $next_month . "' href='" . $permalink . $sep . "type=month&date=" . $next_month . "'>" . apply_filters( 'ftcalendar-next-arrow', '&rArr;' ) . "</a></span>";
			$table .= "<span id='ftcalendar-current'>" .  date_i18n( 'F Y', $str_date ) . "</span>";
			
			if ( 'on' == $types ) {
				
				$table .= "<span id='ftcalendar-types'>";
				$table .= '<a href="' . $permalink . $sep . 'type=day">' . __( 'Day', 'ftcalendar' ) . '</a> ' .
							'<a href="' . $permalink . $sep . 'type=week">' . __( 'Week', 'ftcalendar' ) . '</a> ' .
							'<a href="' . $permalink . $sep . 'type=month">' . __( 'Month', 'ftcalendar' ) . '</a>';
				$table .= "</span>";
			
			}
			
			$table .= "</div>";
			$table .= "<table id='ftcalendar-table' class='ftcalendar " . $class . "' style='" . $style . "'>";
			
			// Set table headings
			$headings = $this->get_headings( $heading_label, $start_of_week );
			if ( !empty( $headings ) ) {
			
				$table .= "<tr>";
				
				foreach ( (array) $headings as $heading ) {
					$table .= "<th id='ftcalendar-headings'>" . $heading . "</th>";
				}	
				
				$table .= "</tr>";
			
			}
			
			$link = "";
			$link_end = "";
			
			for ( $i = 0; $i < $num_weeks * 7; $i++ ) {
				
				$str_time = strtotime( "+" . $i . " Days", $str_start_date );
				$day = date_i18n( 'j', $str_time );
				$month = date_i18n( 'm', $str_time );
				$year = date_i18n( 'Y', $str_time );
				$fordate = date_i18n( 'Y-m-d', $str_time );
				
				if ( $cur_dow % 7 == $start_of_week ) {
					$table .= "<tr style='height=" . $row_height . "%;'>";
				}
				
				if ( $day == $cur_day && $month == $cur_month && $year == $cur_year ) {
					$current_day_class = 'current_day';
				} else {
					$current_day_class = '';
				}
				
				$table .= "<td class='" . $current_day_class . "'>";
				
				if ( 'on' == $types ) {
					$link = "<a href='" . $permalink . $sep . "type=day&date=" . $fordate . "'>";
					$link_end = "</a>";
				}
				
				if ( $month != $working_month ) {
					$current_month_class = 'unmonth';
				} else {
					$current_month_class = '';
				}
				
				if ( 1 == $day ) {
					$mo_short_name = date_i18n( 'M', $str_time );
					$table .= "<div class='ftcalendar-event-date " . $current_month_class . "'>" . $link . $mo_short_name . " " . $day . $link_end . "</div>";
				} else {
					$table .= "<div class='ftcalendar-event-date " . $current_month_class . "'>" . $link . $day . $link_end . "</div>";
				}
				
				if ( isset( $cal_entries[$fordate] ) ) {
				
					ksort( $cal_entries[$fordate] );
					
					$table .= "<div class='ftcalendar-events-div'>";
					
					foreach ( (array)$cal_entries[$fordate] as $time => $event_ids ) {
						
						$duplicate_event_array = array(); //reinitialize for ever day	
						
						foreach ( (array)$event_ids as $event_id ) {
							
							$event_title = get_the_title( $cal_data_arr[$event_id]->post_parent );
								
							if ( 'on' == $hide_duplicates && in_array( $cal_data_arr[$event_id]->start_datetime . $event_title, $duplicate_event_array ) )
								continue;
							
							if ( $cal_data_arr[$event_id]->all_day ) {
								
								$style = 'background-color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . '; border-color: #' . $ftcal_meta['ftcal-border-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
								$table .= "<div style='" . $style . "' class='ftcalendar-event'><div style='" . $style . "' ><a href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'>" . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></div></div>";
								
							} else {
								
								$style = 'color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
								$table .= "<div class='ftcalendar-event'><div><a style='" . $style . "' href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'><span class='ftcalendar-event-time'>" . date_i18n( $timeformat, strtotime( $cal_data_arr[$event_id]->start_datetime ) ) . "</span> " . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></div></div>";
								
							}
								
							$duplicate_event_array[] = $cal_data_arr[$event_id]->start_datetime . $event_title;
							
						}
						
					}
					
					$table .= "</div>";
					
				} 
				
				$table .= "</td>";
			
				$cur_dow++;
				if ( $cur_dow % 7 == $start_of_week ) {
					
					$table .= "</tr>";
					
				}
				
			}
			
			$table .= "</table>";
			
			if ( 'on' == $legend )
				$table .= $this->get_legend( $type, $date );
			
			if ( $ftcal_options['calendar']['show_support'] )
				$table .= $this->show_support();
			
			$table .= "<div class='ftc-clearboth'></div>";
			$table .= "</div>";
			
			return $table;
			
		}
		
		/** 
		 * Change the thumb calendar
		 *
		 * @since 0.3
		 */
		function do_ftcal_thumb_month_change() {
			
			if ( isset( $_POST ) ){
				
				$atts = array( 
					'date'				=> $_POST['date'],
					'calendars'			=> $_POST['calendars'],
					'class'				=> $_POST['tableclass'],
					'width'				=> $_POST['width'],
					'height'			=> $_POST['height'],
					'dateformat'		=> $_POST['dateformat'],
					'timeformat'		=> $_POST['timeformat'],
					'show_rss_feed'		=> $_POST['show_rss_feed'],
					'show_ical_feed'	=> $_POST['show_rss_feed'],
					'hide_duplicates'	=> $_POST['hide_duplicates']
				);
					
				$table = $this->do_ftcal_thumb_calendar( $atts );
			
				die( $table );
			
			} else {
				
				die( "ERROR: POST data is not set..." );
			
			}
			
		}
		
		/**
		 * Print the thumbnail calendar
		 *
		 * @since 0.3
		 */
		function do_ftcal_thumb_calendar( $atts ) {
			
			global $ft_cal_calendars, $ft_cal_options, $wp_rewrite;
			
			$ftcal_meta 	= get_option( 'ftcalendar_meta' );
			$ftcal_options	= $ft_cal_options->get_calendar_options();
			$dateformat 	= get_option( 'date_format' );
			$timeformat 	= get_option( 'time_format' );
			
			$defaults = array( 
				'type'				=> 'thumb',
				'date'				=> null,
				'calendars'			=> 'all',
				'class'				=> '',
				'width'				=> '',
				'height'			=> '',
				'dateformat'		=> $dateformat,
				'timeformat'		=> $timeformat,
				'show_rss_feed'		=> 'on',
				'show_ical_feed'	=> 'on',
				'hide_duplicates'	=> 'off'
			);
		
			// Merge defaults with passed atts
			// Extract (make each array element its own PHP var
			extract( shortcode_atts( $defaults, $atts ) );
		
			$cur_date = getdate( current_time( 'timestamp' ) );
		
			if ( isset( $_GET['thumb_date'] ) )
				$date = $_GET['thumb_date'];
			else if ( !isset( $date ) )
				$date = $cur_date['year'] . "-" . $cur_date['mon'] . "-" . $cur_date['mday'];
		
			$str_date		= strtotime( date_i18n( 'Y-m-01', strtotime( $date ) ) );
			$wday 			= date_i18n( 'w', $str_date );
			$mday			= date_i18n( 'j', $str_date );
			$cur_day		= date_i18n( 'j' );
			$cur_month 		= date_i18n( 'n' );
			$working_month 	= date_i18n( 'm', $str_date );
			$cur_year 		= date_i18n( 'Y' );
			
			$start_of_week	= get_option('start_of_week');
			$sow_diff = $wday >= $start_of_week ? $wday - $start_of_week : abs( 7 - $start_of_week + $wday );
			$str_start_date = strtotime( "-" . $sow_diff . " Day", $str_date );
			$start_date		= date_i18n( 'Y-m-d', $str_start_date );
			$first_day 		= getdate( $str_start_date );
			$cur_dow 		= $first_day['wday'];
			
			$str_last_day	= strtotime( "+1 Month", $str_date ) - 1;
			$last_wday		= date_i18n( 'w', $str_last_day );
			
			if ( $last_wday + 1 == $start_of_week ) {
				$diff = 0;
			} else if ( $last_wday > $start_of_week ) {
				$diff = abs( 6 - ( $last_wday - $start_of_week ) );
			} else {
				$diff = abs( ( $start_of_week + $last_wday ) - 6 );
			}
			
			$str_end_date 	= strtotime( "+" . $diff . " Days", $str_last_day ) - 1;
			$end_date		= date_i18n( 'Y-m-d', $str_end_date );
			$last_day		= date_i18n( 'j', $str_end_date );
			
			$style			= '';
			
			$str_num_weeks	= ( $str_end_date - $str_start_date ) / 86400;  // 24 days * 60 minutes * 60 seconds
			$num_weeks		= ceil( $str_num_weeks / 7 ); // Tells us how many actual weeks are touched in the calendar
			$row_height		= floor( 100 / $num_weeks );
			
			$cal_data_arr = $ft_cal_calendars->get_ftcal_data_ids( $start_date, $end_date, $calendars );
			$cal_entries = $this->parse_calendar_data( $start_date, $end_date, $cal_data_arr );
			
			if ( isset( $width ) && !empty( $width ) )
				$style .= 'width: ' . $width . 'px;';
			
			if ( isset( $height ) && !empty( $height ) )
				$style .= 'height: ' . $height . 'px;';
		
			$prev_month = date_i18n( 'Y-m-d',  strtotime( "-1 Month", $str_date ) );
			$next_month = date_i18n( 'Y-m-d',  strtotime( "+1 Month", $str_date ) );
			
			$table = "<div id='ftcalendar-div' class='ftcalendar ftthumbcalendar" . $class . " " . $type . "' style='" . $style . "'>";
			
			$table .= "<input type='hidden' id='thumbcalendar-calendars' value='" . $calendars . "' />";
			$table .= "<input type='hidden' id='thumbcalendar-class' value='" . $class . "' />";
			$table .= "<input type='hidden' id='thumbcalendar-width' value='" . $width . "' />";
			$table .= "<input type='hidden' id='thumbcalendar-height' value='" . $height . "' />";
			$table .= "<input type='hidden' id='thumbcalendar-dateformat' value='" . $dateformat . "' />";
			$table .= "<input type='hidden' id='thumbcalendar-timeformat' value='" . $timeformat . "' />";
			$table  .= "<input type='hidden' id='thumbcalendar-show_rss_feed' value='" . $show_rss_feed . "' />";
			$table  .= "<input type='hidden' id='thumbcalendar-show_ical_feed' value='" . $show_ical_feed . "' />";
			$table  .= "<input type='hidden' id='thumbcalendar-hide_duplicates' value='" . $hide_duplicates . "' />";
			
			if ( 'on' == $show_rss_feed || 'on' == $show_ical_feed ) {
				
				$table .= "<div id='ftcalendar-feeds'>";
				
				if ( 'all' === $calendars )
					$feed_title = __( 'All Calendars', 'ftcalendar' );
				else
					$feed_title = $calendars;
					
				$site_url = get_bloginfo( 'url' );
				
				if ( 'on' == $show_rss_feed ) {
					
					if ( $wp_rewrite->using_permalinks() ) {
						
						$table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/?type=monthly" title="' . __( 'Monthly Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
					
					} else {
						
						$table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/?ftcalendar=' . $calendars . '&feed=' . get_default_feed() . '&type=monthly" title="' . __( 'Monthly Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
						
					}
							
				}
			
				if ( 'on' == $show_ical_feed ) {
						
					if ( $wp_rewrite->using_permalinks() ) {
						
						$table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/ical/" title="' . __( 'iCal Feed for', 'ftcalendar' ) . ' ' . $feed_title . '"></a> ';
					
					} else {
						
						$table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/?ftcalendar=' . $calendars . '&feed=ical" title="' . __( 'iCal Feed for', 'ftcalendar') . ' ' . $feed_title . '"></a> ';
						
					}
							
				}
							
				$table .= "</div>";
			
			}
			
			$table .= "<div id='ftcalendar-nav'>";
			$table .= "<span id='ftcalendar-prev'><a class='thumb-prev' ref='" . $prev_month . "' href='?thumb_date=" . $prev_month . "'>" . apply_filters( 'ftcalendar-thumb-prev-arrow', '&lArr;' ) . "</a></span>";
			$table	.= "&nbsp;";
			$table .= "<span id='ftcalendar-next'><a class='thumb-next' ref='" . $next_month . "' href='?thumb_date=" . $next_month . "'>" . apply_filters( 'ftcalendar-thumb-next-arrow', '&rArr;' ) . "</a></span>";
			$table .= "<span id='ftcalendar-current'>" .  date_i18n( 'F Y', $str_date ) . "</span>";
			
			$table .= "</div>";
			$table .= "<table id='ftcalendar-table' class='ftcalendar " . $class . "' style='" . $style . "'>";
			
			// Set table headings
			$headings = $this->get_headings( 'letter', $start_of_week );
			if ( !empty( $headings ) ) {
			
				$table .= "<tr>";
				foreach ( (array) $headings as $heading ) {
				
					$table .= "<th id='ftcalendar-headings'>" . $heading . "</th>";
				
				}
				$table .= "</tr>";
				
			}
			
			$link = "";
			$link_end = "";
			for ( $i = 0; $i < $num_weeks * 7; $i++ ) {
				
				$str_time = strtotime( "+" . $i . " Days", $str_start_date );
				$day = date_i18n( 'j', $str_time );
				$month = date_i18n( 'm', $str_time );
				$year = date_i18n( 'Y', $str_time );
				$fordate = date_i18n( 'Y-m-d', $str_time );
				
				if ( $cur_dow % 7 == $start_of_week )
					$table .= "<tr style='height=" . $row_height . "%;'>";
				
				if ( $day == $cur_day && $month == $cur_month && $year == $cur_year )
					$current_day_class = 'current_day';
				else
					$current_day_class = '';
				
				if ( $month != $working_month )
					$unmonth_class = 'unmonth';
				else
					$unmonth_class = '';
				
				$table .= "<td class='ftcalendar-event-date " . $current_day_class . " " . $unmonth_class . "'>";
				
				
				if ( isset( $cal_entries[$fordate] ) ) {
					
					$table .= "<span class='thumb-event " . $unmonth_class . "' ref='" . $fordate . "' >$day</span>";
					$table .= "<div id='" . $fordate . "' class='thumb-event-div'>";
					$table .= "<div class='thumb-event-header'>" . date_i18n( $dateformat, strtotime( $fordate ) ) . "<span class='thumb-event-close'>x</span></div>";
					$table .= "<div class='thumb-events'>";
					
					foreach ( (array)$cal_entries[$fordate] as $time => $event_ids ) {
						
						$duplicate_event_array = array(); //reinitialize for ever day	
						
						foreach ( (array)$event_ids as $event_id ) {
							
							$event_title = get_the_title( $cal_data_arr[$event_id]->post_parent );
								
							if ( 'on' == $hide_duplicates && in_array( $cal_data_arr[$event_id]->start_datetime . $event_title, $duplicate_event_array ) )
								continue;
							
							if ( $cal_data_arr[$event_id]->all_day ) {
								
								$style = 'background-color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . '; border-color: #' . $ftcal_meta['ftcal-border-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
								$table .= "<div style='" . $style . "' class='ftcalendar-event'><div style='" . $style . "' ><a href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'>" . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></div></div>";
								
							} else {
								
								$style = 'color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
								$table .= "<div class='ftcalendar-event'><div><a style='" . $style . "' href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'><span class='ftcalendar-event-time'>" . date_i18n( $timeformat, strtotime( $cal_data_arr[$event_id]->start_datetime ) ) . "</span> " . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></div></div>";
								
							}
								
							$duplicate_event_array[] = $cal_data_arr[$event_id]->start_datetime . $event_title;
							
						}
						
					}
					
					$table .= "</div>";
					$table .= "</div>";
					
				} else {
					
					$table .= $day;
					
				}
				
				$table .= "</td>";
			
				$cur_dow++;
				if ( $cur_dow % 7 == $start_of_week )
					$table .= "</tr>";
				
			}
			
			$table .= "</table>";
			
			$table .= "<div class='ftc-clearboth'></div>";
			$table .= "</div>";
			
			return $table;
		
		}
		
		/**
		 * Returns the legend for the calendar
		 *
		 * @since 0.3
		 */
		function get_legend( $type = 'month', $date = null ) {
			
			global $wp_rewrite;
			
			$ftcal_meta = get_option('ftcalendar_meta');
			$permalink	= get_permalink();
			
			if ( !$wp_rewrite->using_permalinks() && !is_front_page() )
				$sep = "&";
			else
				$sep = "?";
				
			$table = "<div id='ftcalendar-legend'>";
			
			if ( isset( $_GET['cal'] ) )
				$available_calendars = get_terms( 'ftcalendar', array( 'hide_empty' => false, 'slug' => $_GET['cal'] ) );
			else
				$available_calendars = get_terms( 'ftcalendar', array( 'hide_empty' => false ) );
			
			$table .= "<p>" . __( 'Legend', 'ftcalendar' ) . ":</p>";
			if ( !empty( $available_calendars ) ) {
			
				foreach ( (array)$available_calendars as $key => $calendar ) :
					$style = 'background-color: #' . $ftcal_meta['ftcal-bg-color-' . $calendar->term_id] . '; border-color: #' . $ftcal_meta['ftcal-border-color-' . $calendar->term_id] . ';';
					$table .= "<div style='" . $style . "' class='ftcalendar-event'><div style='" . $style . "' ><a href='" . $permalink . $sep . "type=" . $type . "&date=" . $date . "&cal=" . $calendar->slug . "'>" . $calendar->name . "</a></div></div>";
				endforeach;
				
				if ( isset( $_GET['cal'] ) )
					$table .= "<a href='" . $permalink . $sep . "type=" . $type . "&date=" . $date . "'>( Unhide Calendars )</a>";
			
			} else {
				
				$table .= __( 'No calendars to display.', 'ftcalendar' );
			
			}
			
			$table .= "</div>";
			
			return $table;
			
		}
		
		/**
		 * Returns the support linke for the calendar
		 *
		 * @since 0.3.2
		 */
		function show_support() {
			
			$table = "<div id='ftcalendar-show-support'>";
			$table .= '<a href="http://calendar-plugin.com/">WordPress Calendar Plugin</a> by Full Throttle Development';
			$table .= "</div>";
			
			return $table;
			
		}
		
		/**
		 *
		 * @TODO Add description
		 * @TODO There might be a better way to do non-repeating events...
		 * @since 0.3
		 */
		function parse_calendar_data( $start_date, $end_date, $cal_data_arr = array(), $start_at_midnight = true, $end_at_midnight = true ) {
			
			if ( function_exists( 'date_default_timezone_get' ) && function_exists( 'date_default_timezone_set' ) ) {
				
				$tz = date_default_timezone_get();
				date_default_timezone_set( 'UTC' );
				$set_timezone = true;
				
			}
			
			$cal_entries = false;
			
			if ( $start_at_midnight )
				$start_date .= " 00:00:00";	// add Midnight in start date
				
			if ( $end_at_midnight )
				$end_date .= " 23:59:59";	// add 1 second before the next day in end date
			
			$str_start_date = strtotime( $start_date );
			$str_start_date_day = floor( $str_start_date / 86400 ); // 24 days * 60 minutes * 60 seconds
			
			$str_end_date = strtotime( $end_date );
			$str_end_date_day = floor( $str_end_date / 86400 );
		
			// 86400 = 24 hours (1 Day) in seconds
			for ( $i = $str_start_date_day; $i <= $str_end_date_day; $i++ ) {
				
				$strdate = $i * 86400; // 24 days * 60 minutes * 60 seconds
			
				foreach ( (array)$cal_data_arr as $cal_data ) {
					
					if ( 1 == $cal_data->repeating ) {
						
						$str_rsdatetime = strtotime( $cal_data->r_start_datetime );
						$str_rsdate = floor( $str_rsdatetime / 86400 );
						$rsdate = date_i18n( 'Y-m-d', $str_rsdatetime );
						$rstime = date_i18n( 'Hi', $str_rsdatetime );
						
						if ( 1 == $cal_data->r_end ) {
							
							$str_redatetime = strtotime( $cal_data->r_end_datetime );
							$str_redate = floor( $str_redatetime / 86400 );
							$redate = date_i18n( 'Y-m-d', $str_redatetime );
							$retime = date_i18n( 'Hi', $str_redatetime );
								
						} else {
							
							$str_redatetime = strtotime( $cal_data->end_datetime );
							$str_redate = $str_end_date_day;
							$redate = date_i18n( 'Y-m-d', $str_end_date );
							$retime = date_i18n( 'Hi', $str_redatetime );
								
						}
						
						if ( $i == $str_start_date_day && $rstime < date_i18n( 'Hi', $str_start_date ) && $retime < date_i18n( 'Hi', $str_start_date ) ) {
						
							continue;
							
						}
		
						if ( $i >= $str_rsdate && $i <= $str_redate ) {
							
							switch ( $cal_data->r_type ) {
								
								case 'daily' :
									if ( 0 == ( $i - $str_rsdate ) % $cal_data->r_every ) {
										
										$cal_entries[date_i18n( 'Y-m-d', $strdate )][$rstime][] = $cal_data->id;
										
									}
									break;
								
								case 'weekdays' :
									if ( in_array( date_i18n( 'w', $strdate ), array( 1, 2, 3, 4, 5 ) ) ) {
										
										$cal_entries[date_i18n( 'Y-m-d', $strdate )][$rstime][] = $cal_data->id;
										
									}
									break;
								
								case 'mwf' :
									if ( in_array( date_i18n( 'w', $strdate ), array( 1, 3, 5 ) ) ) {
										
										$cal_entries[date_i18n( 'Y-m-d', $strdate )][$rstime][] = $cal_data->id;
										
									}
									break;
								
								case 'tt':
									if ( in_array( date_i18n( 'w', $strdate ), array( 2, 4 ) ) ) {
										
										$cal_entries[date_i18n( 'Y-m-d', $strdate )][$rstime][] = $cal_data->id;
										
									}
									break;
								
								case 'weekly' :
									$day = date_i18n( 'w', $str_rsdate * 86400 ); 	//Get numeric day
									$str_rsweek = $str_rsdate - $day;		//Set start week
									$dow = array();	//track days of week and numeric days that event falls on
									$days = array();
									
									$days_of_week = array( 0, 1, 2, 3, 4, 5, 6 );
									for ( $x = 0; $x < 7; $x++) {
										
										if ( 1 == substr( $cal_data->r_on, $x, 1 ) ) {
											
											$dow[] = $days_of_week[$x];
											$days[] = $x;
											
										}
										
									}
								
									if ( in_array( date_i18n( 'w', $strdate ), $dow ) 
											&& in_array( ( $i - $str_rsweek ) % ( $cal_data->r_every * 7 ), $days ) ) {
												
										$cal_entries[date_i18n( 'Y-m-d', $strdate )][$rstime][] = $cal_data->id;
										
									}
									break;
								
								case 'monthly' :
									$s_y = date_i18n( 'Y', $str_rsdate * 86400 );
									$s_m = date_i18n( 'n', $str_rsdate * 86400 );
									$c_y = date_i18n( 'Y', $strdate );
									$c_m = date_i18n( 'n', $strdate );
									
									$month_diff = ( $c_y - $s_y ) * 12 + ( $c_m - $s_m );
									
									if ( 0 == $month_diff % $cal_data->r_every ) {
										
										if ( 0 == $cal_data->r_by ) { // by day of month
										
											if ( date_i18n( 'd', $strdate ) == date_i18n( 'd', strtotime( $cal_data->r_start_datetime ) ) ) {
												
												$cal_entries[date_i18n( 'Y-m-d', $strdate )][$rstime][] = $cal_data->id;
												
											}
											
										} else { // by day of week
										
											if ( date_i18n( 'w', $strdate ) == date_i18n( 'w', strtotime( $cal_data->r_start_datetime ) ) ) {
												
												$dom = $this->get_nth_weekday_of_month( strtotime( $cal_data->r_start_datetime ) );
												$cdom = $this->get_nth_weekday_of_month( $strdate );
												
												if ( $dom == $cdom )
													$cal_entries[date_i18n( 'Y-m-d', $strdate )][$rstime][] = $cal_data->id;
													
											}
											
										}
										
									}
									break;
								
								case 'yearly' :
									$s_y = date_i18n( 'Y', $str_rsdate * 86400 );
									$c_y = date_i18n( 'Y', $strdate );
									
									$year_diff = $c_y - $s_y;
									
									if ( 0 == $year_diff % $cal_data->r_every ) {
										
										if ( date_i18n( 'd-m', $strdate ) == date_i18n( 'd-m', strtotime( $cal_data->r_start_datetime ) ) ) {
											
											$cal_entries[date_i18n( 'Y-m-d', $strdate )][$rstime][] = $cal_data->id;
											
										}
										
									}
									break;
									
							}
							
						}
						
					} else {
					
						$str_sdatetime = strtotime( $cal_data->start_datetime );
						$str_sdays = floor( $str_sdatetime / 86400 );
						$stime = date_i18n( 'Hi', $str_sdatetime );
						
						if ( $i == $str_start_date_day && $stime < date_i18n( 'Hi', strtotime( $start_date ) ) )
							continue;
						
						if ( $i >= $str_sdays && $i <= $str_sdays )
							$cal_entries[date_i18n( 'Y-m-d', $str_sdatetime )][$stime][] = $cal_data->id;
						
					}
					
				}
				
			}
			
			if ( $set_timezone ) {
				date_default_timezone_set( $tz );
			}
			
			return $cal_entries;
		
		}
		
		/**
		 * Internal function for string replacement.
		 */
		function ftc_str_replace( $template, $data ) {
			
			foreach ( (array)$data as $key => $value ) {
			
				$template = str_ireplace( "%$key%", $value, $template );
			
			}
			
			return $template;
		
		}
		
		/**
		 * Get calendar headings based on option
		 *
		 * @param string $heading_label The type of label we want for days
		 * @since 0.3
		 */
		function get_headings( $heading_label, $start_of_week = 0 ) {
		
			$headings = array();
			
			// Set table headings
			switch ( $heading_label ) {
			
				case 'letter' :
					$headings = array( __( 'S', 'ftcalendar' ), __( 'M', 'ftcalendar' ), __( 'T', 'ftcalendar' ), __( 'W', 'ftcalendar' ), __( 'T', 'ftcalendar' ), __( 'F', 'ftcalendar' ), __( 'S', 'ftcalendar' ) );
					break;
			
				case 'partial' :
					$headings = array( __( 'Sun', 'ftcalendar' ), __( 'Mon', 'ftcalendar' ), __( 'Tue', 'ftcalendar' ), __( 'Wed', 'ftcalendar' ), __( 'Thu', 'ftcalendar' ), __( 'Fri', 'ftcalendar' ), __( 'Sat', 'ftcalendar' ) );
					break;
			
				case 'full' :
				default :
					$headings = array( __( 'Sunday', 'ftcalendar' ), __( 'Monday', 'ftcalendar' ), __( 'Tuesday', 'ftcalendar' ), __( 'Wednesday', 'ftcalendar' ), __( 'Thursday', 'ftcalendar' ), __( 'Friday', 'ftcalendar' ), __( 'Saturday', 'ftcalendar' ) );
					break;
			
			}
			
			$start = array_splice( $headings, $start_of_week );
					
			return array_merge( $start, $headings );
		
		}
		
		/**
		 * Get nth weekday of the month (i.e. 4th wednesday of the month)
		 *
		 * @param string $strdate A string to time of the date you're looking at
		 * @since 0.3
		 */
		function get_nth_weekday_of_month( $strdate ) {
			
			$englishnumber = array(
								'1',
								'2',
								'3',
								'4',
								'5'
							);
			
			$date = date_i18n( 'j', $strdate );
			$day = date_i18n( 'w', $strdate ) + 1;
			$firstday = date_i18n( 'w', strtotime( date_i18n( 'Y-m-1', $strdate ) ) ) + 1;
			
			for ( $i = 0; $i <= 5; $i++ ) {
			
				if ( ( $day + ( 7 * $i ) ) >= $date ) {
				
					if ( $day < $firstday ) {
					
						$i--;
						
					}
					
					return $englishnumber[$i];
					
				}
				
			}
		
			return false;
			
		}
		
		/**
		 * A shortcode to display the post schedule
		 *
		 * @since 1.2.7
		 */
		function do_ftcal_post_schedule( $atts ) {
			
			global $ft_cal_events;
			
			return $ft_cal_events->get_post_schedule( '', $atts );
			
		}
	
	}
	
}
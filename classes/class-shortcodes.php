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
			
			if ( ! is_admin() ) {
			
				wp_enqueue_style( 'ft-cal-single-post-page-shorts', FT_CAL_URL . '/includes/css/single-post-page-shorts.css' );
				wp_enqueue_script( 'jquery-tooltip', FT_CAL_URL . '/includes/js/jquery.tools.min.js', array( 'jquery' ) );
				wp_enqueue_script( 'ft-cal-single-post-page-shorts-js', FT_CAL_URL . '/includes/js/single-post-page-shorts.js', array( 'jquery' ) );
				wp_localize_script( 'ft-cal-single-post-page-shorts-js', 'FTCajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			
			}
				
			add_action( 'wp_ajax_nopriv_large_calendar_change', array( &$this, 'do_ftcal_large_calendar_change' ) );
			add_action( 'wp_ajax_large_calendar_change', array( &$this, 'do_ftcal_large_calendar_change' ) );
			add_action( 'wp_ajax_nopriv_thumb_month_change', array( &$this, 'do_ftcal_thumb_month_change' ) );
			add_action( 'wp_ajax_thumb_month_change', array( &$this, 'do_ftcal_thumb_month_change' ) );
				
			add_shortcode( 'ftcalendar_list', array( &$this, 'do_ftcal_event_list' ) );
			add_shortcode( 'ftcalendar', array( &$this, 'do_ftcal_large_calendar' ) );
			add_shortcode( 'ftcalendar_thumb', array( &$this, 'do_ftcal_thumb_calendar' ) );
		
		}
		
		/**
		 * Shortcode for Print List
		 * Defaults set in ft_cal_calendars::get_ftcal_data_ids
		 *
		 * @TODO See if we can use the walker class for the nested loops
		 * @since 0.3
		 */
		function do_ftcal_event_list( $atts ) {
			
			global $ft_cal_events, $ft_cal_calendars, $ft_cal_options, $wp_rewrite;
			$timeformat = get_option( 'time_format' );
			$permalink 	= get_permalink();
			$list = "";
				
			$defaults = array( 
				'type'				=> 'list',
				'span'				=> '+1 Month',
				'calendars'			=> 'all',
				'limit'				=> 0,
				'dateformat'		=> 'jS',
				'timeformat'		=> $timeformat,
				'monthformat'		=> 'F Y',
				'event_template'	=> '<a href="%URL%">%TITLE% (%TIME%)</a>',
				'date_template'		=> '%DATE%',
				'month_template'	=> '%MONTH%'
			);
			
			// Merge defaults with passed atts
			// Extract (make each array element its own PHP var
			extract( shortcode_atts( $defaults, $atts ) );
			
			// Set the CLASS to the current calendar name being set.
			if ( '%CALNAME%' == $class ) {
				
				$class = str_replace( ',', ' ', $calendars );
				
			}
				
			$start_date	= date_i18n( 'Y-m-d' );
			$end_date 	= date_i18n( 'Y-m-d', strtotime( $span ) );
				
			$cal_data_arr = $ft_cal_calendars->get_ftcal_data_ids( $start_date, $end_date, $calendars );
			$cal_entries = $this->parse_calendar_data( $start_date, $end_date, $cal_data_arr );
				
			if ( ! empty( $cal_entries ) ) {
				
				$break = false;
				$count = 1;
				$list .= "<ul>";
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
							
							$post = &get_post( $cal_data_arr[$event_id]->post_parent );
								
							if ( $cal_data_arr[$event_id]->all_day )
								$data['TIME'] 	= __( 'all day' );
							else
								$data['TIME'] 	= date_i18n( $timeformat, strtotime( $time ) );
							
							$data['MONTH'] 			= date_i18n( $monthformat, strtotime( $date ) );
							$data['DATE'] 			= date_i18n( $dateformat, strtotime( $date ) );		
							$data['LINK'] 			= get_permalink( $post->ID );
							$data['URL'] 			= get_permalink( $post->ID );
							$data['TITLE'] 			= get_the_title( $post->ID );
							$calendar_term			= get_term_by( 'id', (int)$cal_data_arr[$event_id]->calendar_id, 'ftcalendar' );
							
							$data['CALNAME']		= $calendar_term->name;
							$data['CALSLUG']		= $calendar_term->slug;
							
							// get author details
							$author = get_userdata( $post->post_author );
							
							$data['AUTHOR'] 	= $author->display_name;
			
							$list .= "<li>" . $this->ftc_str_replace( $event_template, $data ) . "</li>";
							
							if ( 0 != $limit && ++$count > $limit ) {
								
								$break = true;
								break;
								
							}
							
						}
						
						$list .= "</ul>";
						
						if ( $break ) break;
						
					}
					
					$last_month = $cur_month;
					
					if ( $break ) break;
				
				}
			
				if ( isset( $event_date ) )
					$list .= "</ul>";
				
				if ( isset( $event_month ) )
					$list .= "</ul>";
				
				$list .= "</ul>";
				
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
					'date'			=> $_POST['date'],
					'type'			=> $_POST['type'],
					'heading_label'	=> $_POST['heading_label'],
					'calendars'		=> $_POST['calendars'],
					'class'			=> $_POST['tableclass'],
					'width'			=> $_POST['width'],
					'height'		=> $_POST['height'],
					'legend'		=> $_POST['legend'],
					'types'			=> $_POST['types'],
					'dateformat'	=> $_POST['dateformat'],
					'timeformat'	=> $_POST['timeformat']
				);
				
				$table = $this->do_ftcal_large_calendar( $atts );
			
				die( $table );
				
			} else {
			
				die( __( "ERROR: POST not set..." ) );
			
			}
			
		}
		
		/**
		 * Display large calendar
		 *
		 * @since 0.3
		 */
		function do_ftcal_large_calendar( $atts ) {
			
			global $ft_cal_calendars;
			
			$dateformat = get_option('date_format');
			$timeformat = get_option('time_format');
			
			$defaults = array( 
				'type'			=> 'month',
				'date'			=> null,
				'heading_label'	=> 'partial',
				'calendars'		=> 'all',
				'class'			=> '',
				'width'			=> '',
				'height'		=> '',
				'legend'		=> 'on',
				'types'			=> 'on',
				'dateformat'	=> $dateformat,
				'timeformat'	=> $timeformat
			);
		
			if ( isset( $_GET['cal'] ) )
				$atts['calendars'] = $_GET['cal'];
			
			// Merge defaults with passed atts
			// Extract (make each array element its own PHP var
			$args = shortcode_atts( $defaults, $atts );
			
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
			
			global $ft_cal_calendars, $ft_cal_options;
			
			$ftcal_meta 	= get_option( 'ftcalendar_meta' );
			$ftcal_options	= $ft_cal_options->get_calendar_options();
			$current_offset = get_option( 'gmt_offset' );
			$permalink 		= get_permalink();
			
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
			
			$table  .= "<div id='ftcalendar-nav'>";
			$table  .= "<span id='ftcalendar-prev'><a class='large-prev' ref='" . $prev_date . "' href='" . $permalink . "?type=day&date=" . $prev_date . "'>" . apply_filters( 'ftcalendar-prev-arrow', '&lArr;' ) . "</a></span>";
			$table	.= "&nbsp;";
			$table  .= "<span id='ftcalendar-next'><a class='large-next' ref='" . $next_date . "' href='" . $permalink . "?type=day&date=" . $next_date . "'>" . apply_filters( 'ftcalendar-next-arrow', '&rArr;' ) . "</a></span>";
			$table  .= "<span id='ftcalendar-current'>" .  date_i18n( $dateformat, $str_start_date ) . "</span>";
			
			if ( 'on' == $types ) {
				
				$table .= "<span id='ftcalendar-types'>";
				$table .= '<a href="' . $permalink . '?type=day">' . __('Day') . '</a> ' .
							'<a href="' . $permalink . '?type=week">' . __('Week') . '</a> ' .
							'<a href="' . $permalink . '?type=month">' . __('Month') . '</a>';
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
					
					foreach ( (array)$events as $event_id ) {
						
						if ( $cal_data_arr[$event_id]->all_day )
							$label = __( 'All Day' );
						else
							$label = date_i18n( $timeformat, strtotime( $cal_data_arr[$event_id]->start_datetime ) ) . ' - '  . date_i18n( $timeformat, strtotime( $cal_data_arr[$event_id]->end_datetime ) );
						
						$table .= "<tr>";
						$table .= '<td class="ftcalendar-times">' . $label . '</td>';
						
						$style = 'color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
						$table .= "<td class='ftcalendar-event'><a style='" . $style . "' href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'>" . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></td>";
						$table .= "</tr>";
					
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
			
			$table .= "<div class='ftc-clearboth'></div></div>";
			$table .= "</div>";
			
			return $table;
			
		}
		
		function get_week_calendar( $args ) {
			
			global $ft_cal_calendars, $ft_cal_options;
			
			$ftcal_meta 	= get_option( 'ftcalendar_meta' );
			$ftcal_options	= $ft_cal_options->get_calendar_options();
			$permalink = get_permalink();
			
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
			
			$str_start_date = strtotime( "-" . $wday . " Day", $str_date );
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
			
			$table .= "<div id='ftcalendar-nav'>";
			$table .= "<span id='ftcalendar-prev'><a class='large-prev' ref='" . $prev_week . "' href='" . $permalink . "?type=week&date=" . $prev_week . "'>" . apply_filters( 'ftcalendar-prev-arrow', '&lArr;' ) . "</a></span>";
			$table	.= "&nbsp;";
			$table .= "<span id='ftcalendar-next'><a class='large-next' ref='" . $next_week . "' href='" . $permalink . "?type=week&date=" . $next_week . "'>" . apply_filters( 'ftcalendar-next-arrow', '&rArr;' ) . "</a></span>";
			$table .= "<span id='ftcalendar-current'>" .  date_i18n( $dateformat, $str_start_date ) . ' - ' . date_i18n( $dateformat, $str_end_date ) . "</span>";
			
			if ( 'on' == $types ) {
			
				$table .= "<span id='ftcalendar-types'>";
				$table .= '<a href="' . $permalink . '?type=day">' . __('Day') . '</a> ' .
							'<a href="' . $permalink . '?type=week">' . __('Week') . '</a> ' .
							'<a href="' . $permalink . '?type=month">' . __('Month') . '</a>';
				$table .= "</span>";
			
			}
			
			$table .= "</div>";
			$table .= "<table id='ftcalendar-table' class='ftcalendar " . $class . "' style='" . $style . "'>";
			
			// Set table headings
			$headings = $this->get_headings( $heading_label );
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
					
					$link = "<a href='" . $permalink . "?type=day&date=" . $fordate . "'>";
					$link_end = "</a>";
				
				}
				
				$table .= "<div class='ftcalendar-event-date'>" . $link . $day . $link_end . "</div>";
				
				if ( isset( $cal_entries[$fordate] ) ) {
				
					ksort( $cal_entries[$fordate] );
					
					$table .= "<div class='ftcalendar-events-div'>";
					
					foreach ( (array)$cal_entries[$fordate] as $time => $event_ids ) {
						
						foreach ( (array)$event_ids as $event_id ) {
							
							if ( $cal_data_arr[$event_id]->all_day ) {
								
								$style = 'background-color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . '; border-color: #' . $ftcal_meta['ftcal-border-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
								$table .= "<div style='" . $style . "' class='ftcalendar-event'><div style='" . $style . "' ><a href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'>" . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></div></div>";
							
							} else {
								
								$style = 'color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
								$table .= "<div class='ftcalendar-event'><div><a style='" . $style . "' href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'><span class='ftcalendar-event-time'>" . date_i18n( $timeformat, strtotime( $cal_data_arr[$event_id]->start_datetime ) ) . "</span> " . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></div></div>";
							
							}
						
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
			
			$table .= "<div class='ftc-clearboth'></div></div>";
			$table .= "</div>";
			
			return $table;
		
		}
		
		/**
		 * Grab a month view of the calendar
		 *
		 * @since 0.3
		 */
		function get_month_calendar( $args ) {
			
			global $ft_cal_calendars, $ft_cal_options;
			
			$ftcal_meta 	= get_option( 'ftcalendar_meta' );
			$ftcal_options	= $ft_cal_options->get_calendar_options();
			$dateformat		= get_option( 'date_format' );
			$timeformat		= get_option( 'time_format' );
			$permalink 		= get_permalink();
			
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
			
			$str_start_date = strtotime( "-" . $wday . " Day", $str_date );
			$start_date		= date_i18n( 'Y-m-d', $str_start_date );
			$first_day 		= getdate( $str_start_date );
			$cur_dow 		= $first_day['wday'];
			
			$str_last_day	= strtotime( "+1 Month", $str_date ) - 1;
			$last_wday		= date_i18n( 'w', $str_last_day );
			$diff			= 6 - $last_wday; //wday is 0 based
			
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
			
			
			$table .= "<div id='ftcalendar-nav'>";
			$table .= "<span id='ftcalendar-prev'><a class='large-prev' ref='" . $prev_month . "' href='" . $permalink . "?type=month&date=" . $prev_month . "'>" . apply_filters( 'ftcalendar-prev-arrow', '&lArr;' ) . "</a></span>";
			$table	.= "&nbsp;";
			$table .= "<span id='ftcalendar-next'><a class='large-next' ref='" . $next_month . "' href='" . $permalink . "?type=month&date=" . $next_month . "'>" . apply_filters( 'ftcalendar-next-arrow', '&rArr;' ) . "</a></span>";
			$table .= "<span id='ftcalendar-current'>" .  date_i18n( 'F Y', $str_date ) . "</span>";
			
			if ( 'on' == $types ) {
				
				$table .= "<span id='ftcalendar-types'>";
				$table .= '<a href="' . $permalink . '?type=day">' . __('Day') . '</a> ' .
							'<a href="' . $permalink . '?type=week">' . __('Week') . '</a> ' .
							'<a href="' . $permalink . '?type=month">' . __('Month') . '</a>';
				$table .= "</span>";
			
			}
			
			$table .= "</div>";
			$table .= "<table id='ftcalendar-table' class='ftcalendar " . $class . "' style='" . $style . "'>";
			
			// Set table headings
			$headings = $this->get_headings( $heading_label );
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
				
				if ( $cur_dow % 7 == 0 ) {
					$table .= "<tr style='height=" . $row_height . "%;'>";
				}
				
				if ( $day == $cur_day && $month == $cur_month && $year == $cur_year ) {
					$current_day_class = 'current_day';
				} else {
					$current_day_class = '';
				}
				
				$table .= "<td class='" . $current_day_class . "'>";
				
				if ( 'on' == $types ) {
					$link = "<a href='" . $permalink . "?type=day&date=" . $fordate . "'>";
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
						
						foreach ( (array)$event_ids as $event_id ) {
							
							if ( $cal_data_arr[$event_id]->all_day ) {
								
								$style = 'background-color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . '; border-color: #' . $ftcal_meta['ftcal-border-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
								$table .= "<div style='" . $style . "' class='ftcalendar-event'><div style='" . $style . "' ><a href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'>" . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></div></div>";
								
							} else {
								
								$style = 'color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
								$table .= "<div class='ftcalendar-event'><div><a style='" . $style . "' href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'><span class='ftcalendar-event-time'>" . date_i18n( $timeformat, strtotime( $cal_data_arr[$event_id]->start_datetime ) ) . "</span> " . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></div></div>";
								
							}
							
						}
						
					}
					
					$table .= "</div>";
					
				} 
				
				$table .= "</td>";
			
				$cur_dow++;
				if ( $cur_dow % 7 == 0 ) {
					
					$table .= "</tr>";
					
				}
				
			}
			
			$table .= "</table>";
			
			if ( 'on' == $legend )
				$table .= $this->get_legend( $type, $date );
			
			if ( $ftcal_options['calendar']['show_support'] )
				$table .= $this->show_support();
			
			$table .= "<div class='ftc-clearboth'></div></div>";
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
					'date'			=> $_POST['date'],
					'calendars'		=> $_POST['calendars'],
					'class'			=> $_POST['tableclass'],
					'width'			=> $_POST['width'],
					'height'		=> $_POST['height'],
					'dateformat'	=> $_POST['dateformat'],
					'timeformat'	=> $_POST['timeformat']
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
			
			global $ft_cal_calendars, $ft_cal_options;
			
			$ftcal_meta 	= get_option( 'ftcalendar_meta' );
			$ftcal_options	= $ft_cal_options->get_calendar_options();
			$dateformat 	= get_option( 'date_format' );
			$timeformat 	= get_option( 'time_format' );
			$permalink 		= get_permalink();
			
			$defaults = array( 
				'type'			=> 'thumb',
				'date'			=> null,
				'calendars'		=> 'all',
				'class'			=> '',
				'width'			=> '',
				'height'		=> '',
				'dateformat'	=> $dateformat,
				'timeformat'	=> $timeformat
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
			
			$str_start_date = strtotime( "-" . $wday . " Day", $str_date );
			$start_date		= date_i18n( 'Y-m-d', $str_start_date );
			$first_day 		= getdate( $str_start_date );
			$cur_dow 		= $first_day['wday'];
			
			$str_last_day	= strtotime( "+1 Month", $str_date ) - 1;
			$last_wday		= date_i18n( 'w', $str_last_day );
			$diff			= 6 - $last_wday; //wday is 0 based
			
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
			
			$table .= "<div id='ftcalendar-nav'>";
			$table .= "<span id='ftcalendar-prev'><a class='thumb-prev' ref='" . $prev_month . "' href='" . $permalink . "?thumb_date=" . $prev_month . "'>" . apply_filters( 'ftcalendar-thumb-prev-arrow', '&lArr;' ) . "</a></span>";
			$table	.= "&nbsp;";
			$table .= "<span id='ftcalendar-next'><a class='thumb-next' ref='" . $next_month . "' href='" . $permalink . "?thumb_date=" . $next_month . "'>" . apply_filters( 'ftcalendar-thumb-next-arrow', '&rArr;' ) . "</a></span>";
			$table .= "<span id='ftcalendar-current'>" .  date_i18n( 'F Y', $str_date ) . "</span>";
			
			$table .= "</div>";
			$table .= "<table id='ftcalendar-table' class='ftcalendar " . $class . "' style='" . $style . "'>";
			
			// Set table headings
			$headings = $this->get_headings( 'letter' );
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
				
				if ( $cur_dow % 7 == 0 ) {
					$table .= "<tr style='height=" . $row_height . "%;'>";
				}
				
				if ( $day == $cur_day && $month == $cur_month && $year == $cur_year ) {
					$current_day_class = 'current_day';
				} else {
					$current_day_class = '';
				}
				
				if ( $month != $working_month ) {
					$unmonth_class = 'unmonth';
				} else {
					$unmonth_class = '';
				}
				
				$table .= "<td class='ftcalendar-event-date " . $current_day_class . " " . $unmonth_class . "'>";
				
				if ( isset( $cal_entries[$fordate] ) ) {
					$table .= "<span class='thumb-event " . $unmonth_class . "' ref='" . $fordate . "' >$day</span>";
					$table .= "<div id='" . $fordate . "' class='thumb-event-div'>";
					$table .= "<div class='thumb-event-header'>" . date_i18n( $dateformat, strtotime( $fordate ) ) . "<span class='thumb-event-close'>x</span></div>";
					$table .= "<div class='thumb-events'>";
					foreach ( (array)$cal_entries[$fordate] as $time => $event_ids ) {
						foreach ( (array)$event_ids as $event_id ) {
							if ( $cal_data_arr[$event_id]->all_day ) {
								$style = 'background-color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . '; border-color: #' . $ftcal_meta['ftcal-border-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
								$table .= "<div style='" . $style . "' class='ftcalendar-event'><div style='" . $style . "' ><a href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'>" . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></div></div>";
							} else {
								$style = 'color: #' . $ftcal_meta['ftcal-bg-color-' . $cal_data_arr[$event_id]->calendar_id] . ';';
								$table .= "<div class='ftcalendar-event'><div><a style='" . $style . "' href='" . get_permalink( $cal_data_arr[$event_id]->post_parent ) . "'><span class='ftcalendar-event-time'>" . date_i18n( $timeformat, strtotime( $cal_data_arr[$event_id]->start_datetime ) ) . "</span> " . get_the_title( $cal_data_arr[$event_id]->post_parent ) . "</a></div></div>";
							}
						}
					}
					$table .= "</div>";
					$table .= "</div>";
					
				} else {
					$table .= $day;
				}
				
				$table .= "</td>";
			
				$cur_dow++;
				if ( $cur_dow % 7 == 0 ) {
					$table .= "</tr>";
				}
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
			
			$ftcal_meta = get_option('ftcalendar_meta');
			$permalink	= get_permalink();
			$table = "<div id='ftcalendar-legend'>";
			
			if ( isset( $_GET['cal'] ) )
				$available_calendars = get_terms( 'ftcalendar', array( 'hide_empty' => false, 'slug' => $_GET['cal'] ) );
			else
				$available_calendars = get_terms( 'ftcalendar', array( 'hide_empty' => false ) );
			
			$table .= "<p>" . __( 'Legend' ) . ":</p>";
			if ( !empty( $available_calendars ) ) {
			
				foreach ( (array)$available_calendars as $key => $calendar ) :
					$style = 'background-color: #' . $ftcal_meta['ftcal-bg-color-' . $calendar->term_id] . '; border-color: #' . $ftcal_meta['ftcal-border-color-' . $calendar->term_id] . ';';
					$table .= "<div style='" . $style . "' class='ftcalendar-event'><div style='" . $style . "' ><a href='" . $permalink . "?type=" . $type . "&date=" . $date . "&cal=" . $calendar->slug . "'>" . $calendar->name . "</a></div></div>";
				endforeach;
				
				if ( isset( $_GET['cal'] ) )
					$table .= "<a href='" . $permalink . "?type=" . $type . "&date=" . $date . "'>( Unhide Calendars )</a>";
			
			} else {
				
				$table .= __( 'No calendars to display.' );
			
			}
			
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
		function parse_calendar_data( $start_date, $end_date, $cal_data_arr = array() ) {
			
			if ( function_exists( 'date_default_timezone_get' ) && function_exists( 'date_default_timezone_set' ) ) {
				$tz = date_default_timezone_get();
				date_default_timezone_set( 'UTC' );
				$set_timezone = true;
			}
			
			$cal_entries = false;
			
			$start_date .= " 00:00:00";	// add Midnight in start date
			$end_date .= " 23:59:59";	// add 1 second before the next day in end date
			
			$str_start_date = floor( strtotime( $start_date ) / 86400 ); // 24 days * 60 minutes * 60 seconds
			$str_end_date = floor( strtotime( $end_date ) / 86400 );
		
			// 86400 = 24 hours (1 Day) in seconds
			for ( $i = $str_start_date; $i <= $str_end_date; $i++ ) {
				
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
							
						} else {
							
							$str_redate = $str_end_date;
							$redate = date_i18n( 'Y-m-d', strtotime( $end_date ) );
							
						}
		
						if ( $i >= $str_rsdate && $i <= $str_redate ) {
							
							switch ( $cal_data->r_type ) {
								
								case 'daily' :
									if ( 0 == ( $i - $str_rsdate ) % $cal_data->r_every ) {
										
										$cal_entries[date_i18n( 'Y-m-d', $strdate )][$rstime][] = $cal_data->id;
										
									}
									break;
								
								case 'weekdays' :
									if ( in_array( date_i18n( 'D', $strdate ), array( 'Mon', 'Tue', 'Wed', 'Thu', 'Fri' ) ) ) {
										
										$cal_entries[date_i18n( 'Y-m-d', $strdate )][$rstime][] = $cal_data->id;
										
									}
									break;
								
								case 'mwf' :
									if ( in_array( date_i18n( 'D', $strdate ), array( 'Mon', 'Wed', 'Fri' ) ) ) {
										
										$cal_entries[date_i18n( 'Y-m-d', $strdate )][$rstime][] = $cal_data->id;
										
									}
									break;
								
								case 'tt':
									if ( in_array( date_i18n( 'D', $strdate ), array( 'Tue', 'Thu' ) ) ) {
										
										$cal_entries[date_i18n( 'Y-m-d', $strdate )][$rstime][] = $cal_data->id;
										
									}
									break;
								
								case 'weekly' :
									$day = date_i18n( 'w', $str_rsdate * 86400 ); 	//Get numeric day
									$str_rsweek = $str_rsdate - $day;		//Set start week
									$dow = array();	//track days of week and numeric days that event falls on
									$days = array();
									
									$days_of_week = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );
									for ( $x = 0; $x < 7; $x++) {
										
										if ( 1 == substr( $cal_data->r_on, $x, 1 ) ) {
											
											$dow[] = $days_of_week[$x];
											$days[] = $x;
											
										}
										
									}
								
									if ( in_array( date_i18n( 'D', $strdate ), $dow ) 
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
										
											if ( date_i18n( 'D', $strdate ) == date_i18n( 'D', strtotime( $cal_data->r_start_datetime ) ) ) {
												
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
		function get_headings( $heading_label ) {
		
			$headings = array();
			
			// Set table headings
			switch ( $heading_label ) {
			
				case 'letter' :
					$headings = array( __( 'S' ), __( 'M' ), __( 'T' ), __( 'W' ), __( 'T' ), __( 'F' ), __( 'S' ) );
					break;
			
				case 'partial' :
					$headings = array( __( 'Sun' ), __( 'Mon' ), __( 'Tue' ), __( 'Wed' ), __( 'Thu' ), __( 'Fri' ), __( 'Sat' ) );
					break;
			
				case 'full' :
				default :
					$headings = array( __( 'Sunday' ), __( 'Monday' ), __( 'Tuesday' ), __( 'Wednesday' ), __( 'Thursday' ), __( 'Friday' ), __( 'Saturday' ) );
					break;
			
			}
					
			return $headings;
		
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
	
	}
	
}
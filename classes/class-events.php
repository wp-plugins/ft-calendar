<?php
/**
 * @package FT_Calendar
 * @since 0.3
 */
if ( !class_exists( 'FT_CAL_Events' ) ) {
	
	/**
	 * This class controls events
	 *
	 * @since 0.3
	 */
	class FT_CAL_Events {
		
		/**
		 * PHP4 Constructor. Adds most of our actions
		 *
		 * @since 0.3
		 */
		function ft_cal_events() {
			
			add_action( 'admin_init', array( &$this, 'attach_event_boxes_to_post_types' ) );
			add_action( 'admin_print_styles-edit.php', array( &$this, 'enqueue_write_edit_post_css' ) );
			add_action( 'admin_print_scripts-edit.php', array( &$this, 'enqueue_write_edit_post_js' ) );
			add_action( 'admin_print_styles-post.php', array( &$this, 'enqueue_write_edit_post_css' ) );
			add_action( 'admin_print_scripts-post.php', array( &$this, 'enqueue_write_edit_post_js' ) );
			add_action( 'admin_print_styles-post-new.php', array( &$this, 'enqueue_write_edit_post_css' ) );
			add_action( 'admin_print_scripts-post-new.php', array( &$this, 'enqueue_write_edit_post_js' ) );
			add_action( 'wp_ajax_save_ftcal_data', array( &$this, 'save_ftcal_data_ajax' ) );
			add_action( 'wp_ajax_delete_ftcal_data', array( &$this, 'delete_ftcal_data_ajax' ) );
			add_action( 'deleted_post', array( $this, 'delete_post_ftcal_data' ) ); //keep an eye on this hook, it's duplicated in wp_delete_post... hook may change in future release of WP
		
		}
				
		/**
		 * Add custom meta boxes to post types if set to options allow events
		 *
		 * @since 0.3
		 */
		function attach_event_boxes_to_post_types() {
			
			global $current_user, $ft_cal_options;
			
			$event_creation_modes = (array) $ft_cal_options->calendar_options['attach_events_to_post_types'];
			$available_calendars = get_terms( 'ftcalendar', array( 'hide_empty' => false ) );
				
			foreach ( (array)$event_creation_modes as $mode ) {
				
				add_meta_box( 'ftcal_data_meta_box' . $mode, __('Calendar Data'), array( $this, 'add_ftcal_data_meta_box' ), $mode );
			
			}
		}
		
		/**
		 * Display form for adding new recurrence
		 *
		 * @since 0.3
		 */
		function add_ftcal_data_meta_box( $post ) {
		
			global $current_user, $ft_cal_calendars, $ft_cal_options, $wp_locale;
			
			// Grab options from DB
			/**
			 * WordPress date format
			 * @since 0.3
			 */
			$dateformat = get_option('date_format');
			
			/**
			 * WordPress time format
			 */
			$timeformat = get_option('time_format');

			/**
			 * What calendars does the current users have permission to post to?
			 *
			 * @since 0.3
			 */
			$available_calendars = get_terms( 'ftcalendar', array( 'hide_empty' => false ) );
			
			// Grab existing variables
			
			/**
			 * Start date for the event
			 *
			 * @since 0.3
			 */
			$date_start 		= date_i18n( 'm/d/Y' );
			
			/**
			 * End date for the event
			 *
			 * @since 0.3
			 */
			$date_end 			= date_i18n( 'm/d/Y' );
			
			/**
			 * Is this an all day event
			 * @since 0.3
			 */
			$all_day			= false;
			
			/**
			 * Will this event repeat
			 */
			$repeats			= false;
			
			/**
			 * Start time for the event
			 * @since 0.3
			 */
			$time_start 		= $this->round_to_15( date_i18n( 'U' ) );
			
			/**
			 * End time for the event
			 * @since 0.3
			 */
			$time_end 			= $this->round_to_15( date_i18n( 'U' ), '+1 hour' );
			
			/**
			 * What type of repeat will this event have
			 * @since 0.3
			 */
			$repeats_select 	= 'daily';
			
			/**
			 * What will the repeat freq be
			 * @since 0.3
			 */
			$repeats_every	 	= 1;
			
			/**
			 * If we're repeating weekly, what days?
			 * @since 0.3
			 */
			$repeats_on		 	= array( strtolower( date( 'D' ) ) );
			
			/**
			 * If we're repeating monthly, is it by day of month of by day of week?
			 * @since 0.3
			 */
			$repeats_by		 	= '0'; // 0 = day_of_month, 1 = day_of_week
			
			/** What is the start date of the repitition?
			 * @since 0.3
			 */
			$range_start		= $date_start;
			
			/** 
			 * If we're repeating, when do the repeats stop?
			 * @since 0.3
			 */
			$range_end_type		= '0'; // 0 = never, 1 = until
			
			/**
			 * If the repeats stop on a date, what date?
			 * @since 0.3
			 */
			$date_until			= '';
			?>
            
          
			<?php if ( !empty( $available_calendars ) ) : ?>
            
            	<div id="main_ft_cal_options">
            
                <?php if ( 1 < count( $available_calendars ) ) { ?>
				
                Select Calendar:
                <select name='ft_cal_calendars' id='calendar'>
                    <?php foreach ( (array)$available_calendars as $key => $calendar ) : ?>
                        <option value='<?php echo $calendar->term_id; ?>'><?php echo $calendar->name;?></option>
                    <?php endforeach; ?>
                </select>
				<br />
            
                <?php } else { ?>
                
                    <?php foreach ( (array)$available_calendars as $key => $calendar ) : ?>
                        <input type="hidden" id='calendar' name='ft_cal_calendars' value='<?php echo $calendar->term_id; ?>'>
                    <?php break; endforeach; ?>
                
                <?php } ?>
                <!-- Date start -->
                <input style="width: 85px;" type="text" value='<?php echo esc_attr( $date_start ); ?>' id='event_date_start' />
                <!-- Time Start -->
                <input style='width:70px;' type='text' id='event_time_start' value='<?php echo esc_attr( $time_start ); ?>' />
                    
                <?php _e( 'to', 'ftcalendar' ); ?> 
                
                <!-- Date end -->
                <input style="width: 85px;" type="text" value='<?php echo esc_attr( $date_end ); ?>' id='event_date_end' />
                <!-- Time End -->
                <input style='width:70px;' type="text" id='event_time_end' value="<?php echo esc_attr( $time_end ); ?>" />
                
                <!-- All Day? -->
                <input type='checkbox' name='ft_cal_event_all_day' id='ft_cal_event_all_day' <?php checked( $all_day ); ?> /> <?php _e( 'All day', 'ftcalendar' ); ?>
                <!-- All Day? -->
                <input type='checkbox' name='ft_cal_event_repeats' id='ft_cal_event_repeats' <?php checked( $repeats ); ?> /> <?php _e( 'Repeats...', 'ftcalendar' ); ?>
                <input style='float: right;' type='button' name='ft_cal_clear_event' id='ft_cal_clear_event' value="X" />
                <input style='float: right; margin-right: 5px;' type='button' name='ft_cal_save_event' id='ft_cal_save_event' value="+" />
                
                <input type='hidden' id='ft_cal_repeats_label_value' name='ft_cal_repeats_label_value' value='' />
                </div>
                 
                <div id='event_recurring_field_options'>
                    <table id='recurring_table'>
                        <tr>
                            <th style="text-align: right; width: 100px;"><strong><?php _e( 'Repeats', 'ftcalendar' ); ?>:</strong></th>
                            <td style="text-align: left; width: 400px;">
                            <select id='repeats_select' name='ft_cal_repeats' style='margin-bottom:5px;'>
                                <option value='daily' <?php selected( 'daily', $repeats_select ); ?> ><?php _e( 'Daily', 'ftcalendar' ); ?></option>
                                <option value='weekdays' <?php selected( 'weekdays', $repeats_select ); ?> ><?php _e( 'Every weekday', 'ftcalendar' ); ?></option>
                                <option value='mwf' <?php selected( 'mwf', $repeats_select ); ?> ><?php _e( 'Every Mon., Wed., and Fri.', 'ftcalendar' ); ?></option>
                                <option value='tt' <?php selected( 'tt', $repeats_select ); ?> ><?php _e( 'Every Tues., and Thurs.', 'ftcalendar' ); ?></option>
                                <option value='weekly' <?php selected( 'weekly', $repeats_select ); ?> ><?php _e( 'Weekly', 'ftcalendar' ); ?></option>
                                <option value='monthly' <?php selected( 'montly', $repeats_select ); ?> ><?php _e( 'Monthly', 'ftcalendar' ); ?></option>
                                <option value='yearly' <?php selected( 'yearly', $repeats_select ); ?> ><?php _e( 'Yearly', 'ftcalendar' ); ?></option>
                            </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <td colspan="2">
                            <div id='repeats_label' style='text-align: center;'>
                                <p id='repeats_daily_p' class='repeats_label_item' ><span id='repeats_daily_label'></span><span class='date_until'></span></p>
                                <p id='repeats_weekdays_p' class='repeats_label_item' ><span id='repeats_weekdays_single'><?php _e( 'Weekly on weekdays', 'ftcalendar' ); ?></span><span class='date_until'></span></p>
                                <p id='repeats_mwf_p' class='repeats_label_item' ><span id='repeats_mwf_single'><?php _e( 'Weekly on Monday, Wednesday, Friday', 'ftcalendar' ); ?></span><span class='date_until'></span></p>
                                <p id='repeats_tt_p' class='repeats_label_item' ><span id='repeats_tt_single'><?php _e( 'Weekly on Tuesday, Thursday', 'ftcalendar' ); ?></span><span class='date_until'></span></p>
                                <p id='repeats_weekly_p' class='repeats_label_item' ><span id='repeats_weekly_label'></span><span id='repeats_weekly_on'></span><span class='date_until'></span></p>
                                <p id='repeats_monthly_p' class='repeats_label_item' ><span id='repeats_monthly_label'></span><span class='date_until'></span></p>
                                <p id='repeats_yearly_p' class='repeats_label_item' ><span id='repeats_yearly_label'></span><span class='date_until'></span></p>
                            </div>
                            </td>
                        </tr>
    
                        <tr id='repeats_every'>
                            <th><strong><?php _e( 'Repeat Every', 'ftcalendar' ); ?>:</strong></th>
                            <td>
                            <div style='margin-bottom:5px;'>
                                <select name='ft_cal_repeats_every' id='repeats_every_select'>
                                    <?php for($i=1;$i<=30;$i++){ ?><option value='<?php echo $i; ?>' <?php checked( $i, $repeats_every ); ?> ><?php echo $i; ?></option><?php } ?>
                                </select> <span id='repeats_every_label'><?php _e( 'days', 'ftcalendar' ); ?></span>
                            </div>
                            </td>
                        </tr>
                    
                    <tr id='repeats_on' style='margin-bottom:5px;display:none;'>
                        <th><strong><?php _e( 'Repeat On:', 'ftcalendar' ); ?></strong></th>
                        <td>
                        <input type='checkbox' id='repeats_on_sun' class='repeats_on' name='ft_cal_repeats_on[]' value='sun' <?php if ( in_array( 'sun', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'S', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <input type='checkbox' id='repeats_on_mon' class='repeats_on' name='ft_cal_repeats_on[]' value='mon' <?php if ( in_array( 'mon', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'M', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <input type='checkbox' id='repeats_on_tue' class='repeats_on' name='ft_cal_repeats_on[]' value='tue' <?php if ( in_array( 'tue', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'T', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <input type='checkbox' id='repeats_on_wed' class='repeats_on' name='ft_cal_repeats_on[]' value='wed' <?php if ( in_array( 'wed', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'W', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <input type='checkbox' id='repeats_on_thu' class='repeats_on' name='ft_cal_repeats_on[]' value='thu' <?php if ( in_array( 'thu', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'T', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <input type='checkbox' id='repeats_on_fri' class='repeats_on' name='ft_cal_repeats_on[]' value='fri' <?php if ( in_array( 'fri', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'F', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <input type='checkbox' id='repeats_on_sat' class='repeats_on' name='ft_cal_repeats_on[]' value='sat' <?php if ( in_array( 'sat', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'S', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        </td>
                    </tr>
                    
                    <tr id='repeats_by' style='margion-bottom:5px;display:none;'>
                        <th><strong><?php _e( 'Repeat By', 'ftcalendar' ); ?>:</strong></th>
                        <td>
                        <input type='radio' name='ft_cal_repeats_by' class='repeats_by' id='repeats_by_dayofmonth' value='0' <?php checked( '0', $repeats_by ); ?> /> <?php _e( 'day of the month', 'ftcalendar' ); ?> &nbsp;&nbsp;
                        <input type='radio' name='ft_cal_repeats_by' class='repeats_by' id='repeats_by_dayofweek' value='1' <?php checked( '1', $repeats_by ); ?>/> <?php _e( 'day of the week', 'ftcalendar' ); ?> 
                        </td>
                    </tr>
                    
                    <tr id='starts_on'>
                        <th><strong><?php _e( 'Starts On', 'ftcalendar' ); ?>:</strong></th>
                        <td>
                            <input id='range_start' name='ft_cal_range_start' type='text' style='width:100px;' readonly='readonly' value="<?php echo esc_attr( $range_start ); ?>" /><br />
                        </td>
                    </tr>
                    
                    <tr id='ends_on'>
                        <th><strong><?php _e( 'Ends On:', 'ftcalendar' ); ?></strong></th>
                        <td>
                            <input id='range_end_type_never' class='range_end_type' name='ft_cal_range_end_type' type='radio' value='0' <?php checked( '0', $range_end_type ); ?> /> <?php _e( 'Never', 'ftcalendar' ); ?> 
                            <input id='range_end_type_until' name='ft_cal_range_end_type' class='range_end_type' type='radio' value='1' <?php checked( '1', $range_end_type ); ?> /> <?php _e( 'Until', 'ftcalendar' ); ?> 
                            <input style="width: 100px;" type="text" value='<?php echo esc_attr( $date_until ); ?>' id='range_end' />
                        </td>
                    </tr>
                    </table>
                    <?php wp_nonce_field( 'save_ftcal_data', 'save_ftcal_data_nonce' ); ?>
                </div>
                
                <div id="ftcal_existing">
                <?php 
				$get_post_id = isset( $_GET['post'] ) ? $_GET['post'] : null;
				echo $this->refresh_ftcal_existing_div( $get_post_id ); ?>
                </div>
                
                <?php /* PARTNERS
                <div id="ftsupport">
                    <div style="margin-left: auto; margin-right: auto; width: 468px;">
                    <a href="http://www.shareasale.com/r.cfm?b=255473&u=474529&m=28169&urllink=&afftrack=" target="_blank"><img src="http://www.shareasale.com/image/28169/468x60.png" alt="Genesis Framework for WordPress" border="0"></a>
                    </div>
                    <p>
                    <a href="admin.php?page=ftcalendar-help">remove partners</a>
                    </p>
                </div>
				<?php /**/ ?>
                
            <?php else :
                _e( 'You must <a href="edit-tags.php?taxonomy=ftcalendar">create a calendar</a> before you can add an event to it!', 'ftcalendar', 'ftcalendar' );
             endif;
		}
		
		/**
		 * Query the DB for the event data associated with this post
		 *
		 * @since 0.3
		 */
		function get_ftcal_data( $post_id ) {
			
			global $wpdb;
			
			$sql = $wpdb->prepare(
					"SELECT * FROM " . $wpdb->prefix . "ftcalendar_events" .
					" WHERE post_parent = %d" . 
					" ORDER BY start_datetime ASC", $post_id );
			
			return $wpdb->get_results( $sql );
		
		}
		
		/**
		 * Save the data via AJAX
		 *
		 * @TODO clean params
		 * @since 0.3
		 */
		function save_ftcal_data_ajax() {			
			
			check_ajax_referer( 'save_ftcal_data' );
			
			global $wpdb;
			
			$all_day = ( 'true' === $_POST['all_day'] ? 1 : 0 );
			$repeating = ( 'true' === $_POST['repeating'] ? 1 : 0 );
			
			$data = array(
				'calendar_id' 		=> $_POST['cal_ID'],
				'post_parent' 		=> $_POST['post_ID'],
				'start_datetime' 	=> date_i18n( "Y-m-d H:i:s", strtotime( $_POST['start_date'] . " " .  $_POST['start_time'] ) ),
				'end_datetime' 		=> date_i18n( "Y-m-d H:i:s", strtotime( $_POST['end_date'] . " " .  $_POST['end_time'] ) ),
				'all_day' 			=> $all_day,
				'repeating'			=> $repeating,
				'r_start_datetime' 	=> date_i18n( "Y-m-d H:i:s", strtotime( $_POST['r_start_date'] . " " .  $_POST['start_time'] ) ),
				'r_end'				=> $_POST['r_end'],
				'r_end_datetime' 	=> date_i18n( "Y-m-d H:i:s", strtotime( $_POST['r_end_date'] . " " .  $_POST['end_time'] ) ),
				'r_type' 			=> $_POST['r_type'],
				'r_label' 			=> $_POST['r_label'],
				'r_every' 			=> $_POST['r_every'],
				'r_on' 				=> $_POST['r_on'],
				'r_by' 				=> $_POST['r_by'] 
			);
			
			if ( $wpdb->insert( $wpdb->prefix . "ftcalendar_events", $data ) )
				die( $this->refresh_ftcal_existing_div( $_POST['post_ID'] ) );
			else
				die( "unsuccessful" );

		}
		
		/**
		 * Delete an event via AJAX
		 *
		 * @TODO Clean params
		 * @since 0.3
		 */
		function delete_ftcal_data_ajax() {
			
			check_ajax_referer( 'delete_ftcal_data' );
			
			global $wpdb;
			
			if ( isset( $_POST['event_ids'] ) && is_array( $_POST['event_ids'] ) ) {
				
				foreach ( (array)$_POST['event_ids'] as $event_id ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "ftcalendar_events" .
													" WHERE id = " . absint( $event_id['value'] ) . ";\n" ) );
				}
			
			}
		
			die( $this->refresh_ftcal_existing_div( absint( $_POST['post_ID'] ) ) );
		
		}
		
		/**
		 * Delete calendar data from events
		 *
		 * @param int post_id
		 * @since 0.3
		 */
		function delete_post_ftcal_data( $post_id = null ) {
			
			global $wpdb;
			
			if ( ! is_null( $post_id ) ) {
				
				$wpdb->query( $wpdb->prepare( "DELETE FROM " . $wpdb->prefix . "ftcalendar_events " .
												"WHERE post_parent = %d", $post_id ) );
			
			}
			
		}
		
		/**
		 * Resets add event form after adding an event
		 *
		 * @since 0.3
		 * @param int Post ID for current post
		 */
		function refresh_ftcal_existing_div( $post_id = null ) {
			
			$dateformat = get_option('date_format');
			$timeformat = get_option('time_format');
		
			$output = '';
			
			if ( isset( $post_id ) && $ftcal_data = $this->get_ftcal_data( $post_id ) ) {
				
				$output = "<table>";
				$output .= "<tr><th>Delete?</th><th>Event Details</th></tr>";
				
				foreach ( (array)$ftcal_data as $entry ) {
					
					$start_date = date_i18n( $dateformat, strtotime( $entry->start_datetime ));
					$end_date = date_i18n( $dateformat, strtotime( $entry->end_datetime ));
					
					$output .= "<tr>";
					$output .= '<td style="text-align: center; vertical-align: middle;"><input type="checkbox" class="delete_event" name="delete_event" value="' . $entry->id . '" /></td>';
					$output .= '<td>';
					
					if ( $entry->all_day ) {
					
						$output .= $start_date . " to " . $end_date;
					
					} else {
					
						$start_time = date_i18n( $timeformat, strtotime( $entry->start_datetime ) );
						$end_time = date_i18n( $timeformat, strtotime( $entry->end_datetime ) );
						
						$output .= $start_date . " " . $start_time . " to " . $end_date . " " . $end_time;
					
					}
						
					if ( $entry->repeating )
						$output .= "<br /><span style='margin-left: 20px;'>(Repeating " . $entry->r_label . ")</span>";
					
					$output .= "</td></tr>";
				}
				
				$output .="<tr><td><input type='button' name='ft_cal_delete_events' id='ft_cal_delete_events' value='x' /></td><td>&nbsp;</td></tr>";

				$output .= "</table>";
				$output .= wp_nonce_field( 'delete_ftcal_data', 'delete_ftcal_data_nonce', true, false );
			
			} else {
				
				$output = "<h2 style='text-align:center;'>There is currently no calendar data associated with this post.</h2>";
			
			}
			
			return $output;
			
		}
		
		/**
		 * Converts time from 24 to 12 and adds or subtracts increments given.
		 *
		 * @since 0.3
		 */
		function round_to_15( $time, $increment=false ){
			
			if ( $increment )
				$time = strtotime( $increment, $time );
						
			$roundedtime = ( round( $time / 900 ) * 900 );
			
			return date_i18n( 'g:i a', $roundedtime );
		
		}
		
		/**
		 * Enqueues CSS for the write-edit-post screen
		 *
		 * @since 0.3
		 */
		function enqueue_write_edit_post_css() {
			
			global $current_screen, $ft_cal_options;
			
			foreach ( (array) $ft_cal_options->calendar_options['attach_events_to_post_types'] as $post_type ) {
				
				if ( $post_type == $current_screen->post_type ) {
					
					wp_enqueue_style( 'write-edit-post', FT_CAL_URL . '/includes/css/write-edit-post.css' );
					wp_enqueue_style( 'jquery-ui', FT_CAL_URL . '/includes/css/ui-lightness/jquery-ui-1.8.6.custom.css' );
					wp_enqueue_style( 'timePicker', FT_CAL_URL . '/includes/css/timePicker.css' );
				
				}
				
			}
			
		}				

		/**
		 * Enqueues JS on the write-edit-post
		 *
		 * @since 0.3
		 */
		function enqueue_write_edit_post_js() {
			
			global $current_screen, $ft_cal_options;
			
			foreach ( (array) $ft_cal_options->calendar_options['attach_events_to_post_types'] as $post_type ) {
				
				if ( $post_type == $current_screen->post_type ) {
					
					wp_enqueue_script( 'write-edit-post', FT_CAL_URL . '/includes/js/write-edit-post.js' );
					wp_enqueue_script( 'ui-datepicker', FT_CAL_URL . '/includes/js/ui.datepicker.min.js' );
					wp_enqueue_script( 'jquery-timepicker', FT_CAL_URL . '/includes/js/jquery.timePicker.min.js' );
				
				}
				
			}
			
		}
		
	}
	
}
?>

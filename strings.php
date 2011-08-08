<?php
// Used for generating accurate POT files
?>

		<label for="ftcalendar-color"><?php _e( 'Calendar Label Color', 'ftcalendar' ); ?></label>
            <th valign="top" scope="row"><?php _e( 'Calendar Label Color', 'ftcalendar' ); ?></th>
                <?php _e( 'to', 'ftcalendar' ); ?> 
                <input type='checkbox' name='ft_cal_event_all_day' id='ft_cal_event_all_day' <?php checked( $all_day ); ?> /> <?php _e( 'All day', 'ftcalendar' ); ?>
                <input type='checkbox' name='ft_cal_event_repeats' id='ft_cal_event_repeats' <?php checked( $repeats ); ?> /> <?php _e( 'Repeats...', 'ftcalendar' ); ?>
                            <th style="text-align: right; width: 100px;"><strong><?php _e( 'Repeats', 'ftcalendar' ); ?>:</strong></th>
                                <option value='daily' <?php selected( 'daily', $repeats_select ); ?> ><?php _e( 'Daily', 'ftcalendar' ); ?></option>
                                <option value='weekdays' <?php selected( 'weekdays', $repeats_select ); ?> ><?php _e( 'Every weekday', 'ftcalendar' ); ?></option>
                                <option value='mwf' <?php selected( 'mwf', $repeats_select ); ?> ><?php _e( 'Every Mon., Wed., and Fri.', 'ftcalendar' ); ?></option>
                                <option value='tt' <?php selected( 'tt', $repeats_select ); ?> ><?php _e( 'Every Tues., and Thurs.', 'ftcalendar' ); ?></option>
                                <option value='weekly' <?php selected( 'weekly', $repeats_select ); ?> ><?php _e( 'Weekly', 'ftcalendar' ); ?></option>
                                <option value='monthly' <?php selected( 'montly', $repeats_select ); ?> ><?php _e( 'Monthly', 'ftcalendar' ); ?></option>
                                <option value='yearly' <?php selected( 'yearly', $repeats_select ); ?> ><?php _e( 'Yearly', 'ftcalendar' ); ?></option>
                                <p id='repeats_weekdays_p' class='repeats_label_item' ><span id='repeats_weekdays_single'><?php _e( 'Weekly on weekdays', 'ftcalendar' ); ?></span><span class='date_until'></span></p>
                                <p id='repeats_mwf_p' class='repeats_label_item' ><span id='repeats_mwf_single'><?php _e( 'Weekly on Monday, Wednesday, Friday', 'ftcalendar' ); ?></span><span class='date_until'></span></p>
                                <p id='repeats_tt_p' class='repeats_label_item' ><span id='repeats_tt_single'><?php _e( 'Weekly on Tuesday, Thursday', 'ftcalendar' ); ?></span><span class='date_until'></span></p>
                            <th><strong><?php _e( 'Repeat Every', 'ftcalendar' ); ?>:</strong></th>
                                </select> <span id='repeats_every_label'><?php _e( 'days', 'ftcalendar' ); ?></span>
                        <th><strong><?php _e( 'Repeat On:', 'ftcalendar' ); ?></strong></th>
                        <input type='checkbox' id='repeats_on_sun' class='repeats_on' name='ft_cal_repeats_on[]' value='sun' <?php if ( in_array( 'sun', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'S', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <input type='checkbox' id='repeats_on_mon' class='repeats_on' name='ft_cal_repeats_on[]' value='mon' <?php if ( in_array( 'mon', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'M', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <input type='checkbox' id='repeats_on_tue' class='repeats_on' name='ft_cal_repeats_on[]' value='tue' <?php if ( in_array( 'tue', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'T', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <input type='checkbox' id='repeats_on_wed' class='repeats_on' name='ft_cal_repeats_on[]' value='wed' <?php if ( in_array( 'wed', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'W', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <input type='checkbox' id='repeats_on_thu' class='repeats_on' name='ft_cal_repeats_on[]' value='thu' <?php if ( in_array( 'thu', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'T', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <input type='checkbox' id='repeats_on_fri' class='repeats_on' name='ft_cal_repeats_on[]' value='fri' <?php if ( in_array( 'fri', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'F', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <input type='checkbox' id='repeats_on_sat' class='repeats_on' name='ft_cal_repeats_on[]' value='sat' <?php if ( in_array( 'sat', $repeats_on ) ) { echo "checked='checked'"; } ?> /> <?php _e( 'S', 'ftcalendar' ); ?>&nbsp;&nbsp;
                        <th><strong><?php _e( 'Repeat By', 'ftcalendar' ); ?>:</strong></th>
                        <input type='radio' name='ft_cal_repeats_by' class='repeats_by' id='repeats_by_dayofmonth' value='0' <?php checked( '0', $repeats_by ); ?> /> <?php _e( 'day of the month', 'ftcalendar' ); ?> &nbsp;&nbsp;
                        <input type='radio' name='ft_cal_repeats_by' class='repeats_by' id='repeats_by_dayofweek' value='1' <?php checked( '1', $repeats_by ); ?>/> <?php _e( 'day of the week', 'ftcalendar' ); ?> 
                        <th><strong><?php _e( 'Starts On', 'ftcalendar' ); ?>:</strong></th>
                        <th><strong><?php _e( 'Ends On:', 'ftcalendar' ); ?></strong></th>
                            <input id='range_end_type_never' class='range_end_type' name='ft_cal_range_end_type' type='radio' value='0' <?php checked( '0', $range_end_type ); ?> /> <?php _e( 'Never', 'ftcalendar' ); ?> 
                            <input id='range_end_type_until' name='ft_cal_range_end_type' class='range_end_type' type='radio' value='1' <?php checked( '1', $range_end_type ); ?> /> <?php _e( 'Until', 'ftcalendar' ); ?> 
                _e( 'You must <a href="edit-tags.php?taxonomy=ftcalendar">create a calendar</a> before you can add an event to it!', 'ftcalendar', 'ftcalendar' );
				<h2><?php _e( 'FullThrottle Calendar General Settings', 'ftcalendar' ); ?> <a href="admin.php?page=ftcalendar-help" style="text-decoration: none;"><?php /** _e( '(Upgrade to Premium Support)', 'ftcalendar' ); /**/ ?></a></h2>
                                <h3 class="hndle"><span><?php _e( 'General Options', 'ftcalendar' ); ?></span></h3>
                                                <th scope="row"><?php _e( 'Enable Calendar items for these post types:', 'ftcalendar' ) ?></th>
                                                    <fieldset><legend class="screen-reader-text"><span><?php _e( 'Enable Calendar items for these post types:', 'ftcalendar' ) ?></span></legend>
                                                <th scope="row"><?php _e( 'Show Support Link:', 'ftcalendar' ) ?></th>
                                                <th scope="row"><?php _e( 'Enable SMART Post Ordering:', 'ftcalendar' ) ?></th>
                                                <th scope="row"><?php _e( 'Include Recurring End Date in SMART Ordering:', 'ftcalendar' ) ?></th>
                                                <th scope="row"><?php _e( 'Show Schedule within Post:', 'ftcalendar' ) ?></th>
                                                <th scope="row"><?php _e( 'Where to Display Schedule?', 'ftcalendar' ) ?></th>
                                                <th scope="row"><?php _e( 'Use Event Date as pubDate in feeds:', 'ftcalendar' ) ?></th>
                                                <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'ftcalendar' ) ?>" />
                                    <h3><?php _e( 'Partners', 'ftcalendar' ); ?></h3>
				<h2><?php _e( 'FullThrottle Calendar Import', 'ftcalendar' ); ?> <a href="admin.php?page=ftcalendar-help" style="text-decoration: none;"><?php /** _e( '(Upgrade to Premium Support)', 'ftcalendar' ); /**/ ?></a></h2>
                                <h3 class="hndle"><span><?php _e( 'FullThrottle Calendar', 'ftcalendar' ); ?></span></h3>
                                                <th scope="row"><?php _e( 'Select file to import', 'ftcalendar' ) ?></th>
                                                <th scope="row"><?php _e( 'Delete existing data?', 'ftcalendar' ) ?></th>
                                                <input type="submit" class="button-primary" name="import" value="<?php _e( 'Import FullThrottle Calendar Data', 'ftcalendar' ) ?>" />
                                <h3 class="hndle"><span><?php _e( 'Event Calendar 3', 'ftcalendar' ); ?></span></h3>
                                                <th scope="row"><?php _e( 'Import Event Calendar 3 data to this Calendar', 'ftcalendar' ) ?></th>
                                                <input type="submit" class="button-primary" name="import" value="<?php _e( 'Import EC3 Data', 'ftcalendar' ) ?>" />
                                                _e( 'You must <a href="edit-tags.php?taxonomy=ftcalendar">create a calendar</a> before you can import EC3 data.', 'ftcalendar' );
                                    <h3><?php _e( 'Partners', 'ftcalendar' ); ?></h3>
				<h2><?php _e( 'FullThrottle Calendar Export', 'ftcalendar' ); ?> <a href="admin.php?page=ftcalendar-help" style="text-decoration: none;"><?php /** _e( '(Upgrade to Premium Support)', 'ftcalendar' ); /**/ ?></a></h2>
									<h3 class="hndle"><span><?php _e( 'FullThrottle Calendar', 'ftcalendar' ); ?></span></h3>
													<th scope="row"><?php _e( 'Select calendar(s) to export', 'ftcalendar' ) ?></th>
                                                            <option value="0" selected="selected"><?php _e( 'All Calendars', 'ftcalendar' ); ?></option>
                        		_e( 'You must <a href="edit-tags.php?taxonomy=ftcalendar">create a calendar</a> before you can export data.', 'ftcalendar' );
                                    <h3><?php _e( 'Partners', 'ftcalendar' ); ?></h3>
				<h2><?php _e( 'FullThrottle Calendar Help', 'ftcalendar' ); ?></h2>
                                <h3 class="hndle"><span><?php _e( '[ftcalendar] - Calendar Shortcode', 'ftcalendar' ); ?></span></h3>
                                <h3 class="hndle"><span><?php _e( '[ftcalendar_thumb] - Thumbnail Calendar Shortcode', 'ftcalendar' ); ?></span></h3>
                                <h3 class="hndle"><span><?php _e( '[ftcalendar_list] - Event List Shortcode', 'ftcalendar' ); ?></span></h3>
                                <h3 class="hndle"><span><?php _e( 'Calendar of Events Widget', 'ftcalendar' ); ?></span></h3>
                                <h3 class="hndle"><span><?php _e( 'Upcoming Events List Widget', 'ftcalendar' ); ?></span></h3>
                                    <h3><?php _e( 'Premium Support and Features', 'ftcalendar' ); ?></h3>
                                                <?php _e( 'By signing up for FT Calendar premium support, you help to ensure future enhancements to this excellent project as well as the following benefits:', 'ftcalendar' ); ?>
                                                <li><?php _e( 'Around the clock access to our knowledge base and support forum from within your WordPress dashboard', 'ftcalendar' ); ?></li>
                                                <li><?php _e( 'Professional and timely response times to all your questions from the FT Calendar team', 'ftcalendar' ); ?></li>
                                                <li><?php _e( 'A 10% discount for any custom functionality you request from the FT Calendar developers', 'ftcalendar' ); ?></li>
                                                <li><?php _e( 'A 6-12 month advance access to new features integrated into the auto upgrade functionality of WordPress', 'ftcalendar' ); ?></li>
                                                <li><?php _e( 'Ads removed from Calendar Meta Boxes and Control Panels' ); ?></li>
                                                <li><a href='<?php echo get_ftps_paypal_button( $ftcalendar_ps ); ?>'><?php _e( 'Signup Now', 'ftcalendar' ); ?></a></li>
                                                <li><a target='_blank' href='<?php echo get_ftps_learn_more_link( $ftcalendar_ps ); ?>'><?php _e( 'Learn More', 'ftcalendar' ); ?></a></li>
                                            <p><a href='#' id='premium_help'><?php _e( 'Launch Premium Support widget', 'ftcalendar' ); ?></a> | <a target="blank" href="http://support.calendar-plugin.com?sso=<?php echo get_ftps_sso_key( $ftcalendar_ps ); ?>"><?php _e( 'Visit Premium Support web site', 'ftcalendar' );?></a></p>
                                    <h3><?php _e( 'Partners', 'ftcalendar' ); ?></h3>
	        	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'ftcalendar' ); ?></label>
	        	<label for="<?php echo $this->get_field_id('show_rss_feed'); ?>"><?php _e( 'Show XML Feed?', 'ftcalendar' ); ?></label>
	        	<label for="<?php echo $this->get_field_id('show_ical_feed'); ?>"><?php _e( 'Show iCal Feed?', 'ftcalendar' ); ?></label>
	        	<label><?php _e( 'Calendars:', 'ftcalendar' ); ?></label>
                <label for="<?php echo $this->get_field_id( $calendar->slug ); ?>"><?php _e( 'All Calendars', 'ftcalendar' ); ?></label>
				<label for="<?php echo $this->get_field_id( 'span' ); ?>"><?php _e( 'Time Span:', 'ftcalendar' ); ?></label>
					<option value="Day" <?php selected( $date_types, "Day" ); ?>><?php _e( 'Day(s)', 'ftcalendar' ); ?></option>
					<option value="Week" <?php selected( $date_types, "Week" ); ?>><?php _e( 'Week(s)', 'ftcalendar' ); ?></option>
					<option value="Month" <?php selected( $date_types, "Month" ); ?>><?php _e( 'Month(s)', 'ftcalendar' ); ?></option>
					<option value="Year" <?php selected( $date_types, "Year" ); ?>><?php _e( 'Year(s)', 'ftcalendar' ); ?></option>
	        	<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'ftcalendar' ); ?></label>
				<small><?php _e( '0 = Show all events in given time span', 'ftcalendar' ); ?></small>
	        	<label for="<?php echo $this->get_field_id( 'timeformat' ); ?>"><?php _e( 'Time Format:', 'ftcalendar' ); ?></label>
	            <small><?php _e( 'See <a href="http://php.net/date/" target="_blank">PHP\'s Format Parameters</a> for help.', 'ftcalendar' ); ?></small>        
	        	<label for="<?php echo $this->get_field_id('dateformat'); ?>"><?php _e( 'Date Format:', 'ftcalendar' ); ?></label>
	            <small><?php _e( 'See <a href="http://php.net/date/" target="_blank">PHP\'s Format Parameters</a> for help.', 'ftcalendar' ); ?></small>        
	        	<label for="<?php echo $this->get_field_id( 'date_template' ); ?>"><?php _e( 'Date Template:', 'ftcalendar' ); ?></label>
	        	<label for="<?php echo $this->get_field_id( 'monthformat '); ?>"><?php _e( 'Month Format:', 'ftcalendar' ); ?></label>
	            <small><?php _e( 'See <a href="http://php.net/date/" target="_blank">PHP\'s Format Parameters</a> for help.', 'ftcalendar' ); ?></small>
	        	<label for="<?php echo $this->get_field_id( 'month_template' ); ?>"><?php _e( 'Month Template:', 'ftcalendar' ); ?></label>
	        	<label for="<?php echo $this->get_field_id( 'event_template' ); ?>"><?php _e( 'Event Template:', 'ftcalendar' ); ?></label>
	        	<label for="<?php echo $this->get_field_id('hide_duplicates'); ?>"><?php _e( 'Hide Duplicates?', 'ftcalendar' ); ?></label>
	            <small><?php _e( 'Date Template must be blank.', 'ftcalendar' ); ?></small>
            _e( 'You have to create a calendar before you can use this widget.', 'ftcalendar' );
	        	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'ftcalendar' ); ?></label>
	        	<label for="<?php echo $this->get_field_id('show_rss_feed'); ?>"><?php _e( 'Show XML Feed?', 'ftcalendar' ); ?></label>
	        	<label for="<?php echo $this->get_field_id('show_ical_feed'); ?>"><?php _e( 'Show iCal Feed?', 'ftcalendar' ); ?></label>
	        	<label><?php _e( 'Calendars:', 'ftcalendar' ); ?></label>
                <label for="<?php echo $this->get_field_id( $calendar->slug ); ?>"><?php _e( 'All Calendars', 'ftcalendar' ); ?></label>
            _e( 'You have to create a calendar before you can use this widget.', 'ftcalendar' );

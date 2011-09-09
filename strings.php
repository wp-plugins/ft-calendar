<?php
// Used for generating accurate POT files
?>
    <label for="ftcalendar-color"><?php _e( 'Calendar Label Color', 'ftcalendar' ); ?></label>
    <th valign="top" scope="row"><?php _e( 'Calendar Label Color', 'ftcalendar' ); ?></th>
    return new WP_Error( 'reserved_term_all', printf( __( '"%s" is a reserved term in FT Calendar' ), $term ) );
    $feed_title = __( "All Calendars" );
    $list .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/?type=upcoming&span=' . rawurlencode( $span ) . '&limit=' . $limit . '" title="' . __('Upcoming Feed for ') . $feed_title . '"></a> ';
    $list .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/?calendars=' . $calendars . '&feed=' . get_default_feed() . '&type=upcoming&span=' . rawurlencode( $span ) . '&limit=' . $limit . '" title="' . __('Upcoming Feed for ') . $feed_title . '"></a> ';
    $list .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/ical/" title="' . __('iCal Feed for ') . $feed_title . '"></a> ';
    $list .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/?calendars=' . $calendars . '&feed=ical" title="' . __('iCal Feed for ') . $feed_title . '"></a> ';
    $data['TIME'] = __( 'all day' );
    die( __( "ERROR: POST not set..." ) );
    $feed_title = __( "All Calendars" );
    $table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/?type=daily" title="' . __('Daily Feed for ') . $feed_title . '"></a> ';
    $table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/?calendars=' . $calendars . '&feed=' . get_default_feed() . '&type=daily" title="' . __('Daily Feed for ') . $feed_title . '"></a> ';
    $table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/ical/" title="' . __('iCal Feed for ') . $feed_title . '"></a> ';
    $table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/?calendars=' . $calendars . '&feed=ical" title="' . __('iCal Feed for ') . $feed_title . '"></a> ';
    $table .= '<a href="' . $permalink . $sep . 'type=day">' . __('Day') . '</a> ' .
    '<a href="' . $permalink . $sep . 'type=week">' . __('Week') . '</a> ' .
    '<a href="' . $permalink . $sep . 'type=month">' . __('Month') . '</a>';
    $label = __( 'All Day' );
    $table .= "<td class='ftcalendar-event' style='text-align: center;' colspan=2>" . __( 'No Events Found', 'ftcalendar' ) . "</td>";
    $feed_title = __( "All Calendars" );
    $table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/?type=weekly" title="' . __('Weekly Feed for ') . $feed_title . '"></a> ';
    $table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/?calendars=' . $calendars . '&feed=' . get_default_feed() . '&type=weekly" title="' . __('Weekly Feed for ') . $feed_title . '"></a> ';
    $table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/ical/" title="' . __('iCal Feed for ') . $feed_title . '"></a> ';
    $table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/?calendars=' . $calendars . '&feed=ical" title="' . __('iCal Feed for ') . $feed_title . '"></a> ';
    $table .= '<a href="' . $permalink . $sep . 'type=day">' . __('Day') . '</a> ' .
    '<a href="' . $permalink . $sep . 'type=week">' . __('Week') . '</a> ' .
    '<a href="' . $permalink . $sep . 'type=month">' . __('Month') . '</a>';
    $feed_title = __( "All Calendars" );
    $table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/?type=monthly" title="' . __('Monthly Feed for ') . $feed_title . '"></a> ';
    $table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/?calendars=' . $calendars . '&feed=' . get_default_feed() . '&type=monthly" title="' . __('Monthly Feed for ') . $feed_title . '"></a> ';
    $table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/ical/" title="' . __('iCal Feed for ') . $feed_title . '"></a> ';
    $table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/?calendars=' . $calendars . '&feed=ical" title="' . __('iCal Feed for ') . $feed_title . '"></a> ';
    $table .= '<a href="' . $permalink . $sep . 'type=day">' . __('Day') . '</a> ' .
    '<a href="' . $permalink . $sep . 'type=week">' . __('Week') . '</a> ' .
    '<a href="' . $permalink . $sep . 'type=month">' . __('Month') . '</a>';
    $feed_title = __( "All Calendars" );
    $table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/?type=monthly" title="' . __('Monthly Feed for ') . $feed_title . '"></a> ';
    $table .=  '<a class="ftcal-rss-icon" href="' . $site_url . '/?calendars=' . $calendars . '&feed=' . get_default_feed() . '&type=monthly" title="' . __('Monthly Feed for ') . $feed_title . '"></a> ';
    $table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/calendars/' . $calendars . '/feed/ical/" title="' . __('iCal Feed for ') . $feed_title . '"></a> ';
    $table .=  '<a class="ftcal-ical-icon" href="' . $site_url . '/?calendars=' . $calendars . '&feed=ical" title="' . __('iCal Feed for ') . $feed_title . '"></a> ';
    $table .= "<p>" . __( 'Legend' ) . ":</p>";
    $table .= __( 'No calendars to display.' );
    $headings = array( __( 'S' ), __( 'M' ), __( 'T' ), __( 'W' ), __( 'T' ), __( 'F' ), __( 'S' ) );
    $headings = array( __( 'Sun' ), __( 'Mon' ), __( 'Tue' ), __( 'Wed' ), __( 'Thu' ), __( 'Fri' ), __( 'Sat' ) );
    $headings = array( __( 'Sunday' ), __( 'Monday' ), __( 'Tuesday' ), __( 'Wednesday' ), __( 'Thursday' ), __( 'Friday' ), __( 'Saturday' ) );
    add_meta_box( 'ftcal_data_meta_box' . $mode, __('Calendar Data'), array( $this, 'add_ftcal_data_meta_box' ), $mode );
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
    $output .= $start_date . " " . __( "to" ) . " " . $end_date;
    $output .= $start_date . " " . $start_time . " " . __( "to" ) . " " . $end_date . " " . $end_time;
    $output .= "<br /><span style='margin-left: 20px;'>(" . __( "Repeating" ) . " " . $entry->r_label . ")</span>";
    $output .= $start_date . " " . __( "to" ) . " " . $end_date;
    $output .= $start_date . " - " . $start_time . " " . __( "to" ) . " " . $end_time;
    $output .= $start_date . " - " . $start_time . " " . __( "to" ) . " " . $end_date . " " . $end_time;
    'until' => __( 'until' ),
    'Daily' => __( 'Daily' ),
    'day' => __( 'day' ),
    'Every' => __( 'Every' ),
    'days' => __( 'days' ),
    'Weekly' => __( 'Weekly' ),
    'week' => __( 'week' ),
    'weeks' => __( 'weeks' ),
    'everyday' => __( 'every day' ),
    'on' => __( 'on' ),
    'Monthly' => __( 'Monthly' ),
    'month' => __( 'month' ),
    'months' => __( 'months' ),
    'AnnuallyOn' => __( 'Annually On' ),
    'year' => __( 'year' ),
    'yearson' => __( 'years on' ),
    'January' => __( 'January' ),
    'February' => __( 'February' ),
    'March' => __( 'March' ),
    'April' => __( 'April' ),
    'May' => __( 'May' ),
    'June' => __( 'June' ),
    'July' => __( 'July' ),
    'August' => __( 'August' ),
    'September' => __( 'September' ),
    'October' => __( 'October' ),
    'November' => __( 'November' ),
    'December' => __( 'December' ),
    'Sunday' => __( 'Sunday' ),
    'Monday' => __( 'Monday' ),
    'Tuesday' => __( 'Tuesday' ),
    'Wednesday' => __( 'Wednesday' ),
    'Thursday' => __( 'Thursday' ),
    'Friday' => __( 'Friday' ),
    'Saturday' => __( 'Saturday' ),
    'first' => __( 'first' ),
    'second' => __( 'second' ),
    'third' => __( 'third' ),
    'fourth' => __( 'fourth' ),
    'fifth' => __( 'fifth' ),
    'onthe' => __( 'on the' ),
    'ofthemonth' => __( 'of the month' ),
    'unabletodeterminedate' => __( 'unable to determine date.' ),
    'errorsfound' => __( 'Errors found, please correct before attempting to save.' ),
    'erroradding' => __( 'Error Adding New Event, Please contact support@ftcalendar.com for assistance.' ),
    'areyousureaddevents' => __( 'Are you sure you want to delete these events?' )
    $title .= __( '(today)', 'ftcalendar' );
    $title .= __( '(this week)', 'ftcalendar' );
    $title .= __( '(upcoming)', 'ftcalendar' );
    $title = __( 'ical', 'ftcalendar' );
    $title .= __( '(this month)', 'ftcalendar' );
    echo apply_filters( 'get_wp_title_rss', __( " $sep All Calendars" ) );
    $output .= $start_date . " " . __( "to" ) . " " . $end_date;
    $output .= $start_date . " - " . $start_time . " " . __( "to" ) . " " . $end_time;
    $output .= $start_date . " - " . $start_time . " " . __( "to" ) . " " . $end_date . " " . $end_time;
    add_menu_page( __( 'FullThrottle Calendar General Settings', 'ftcalendar' ), __( 'FT Calendar', 'ftcalendar' ), 'install_plugins', 'ftcalendar-general', array( &$this, 'options_page' ) );
    add_submenu_page( 'ftcalendar-general', __( 'FullThrottle Calendar General Settings', 'ftcalendar' ), __( 'FT Calendar', 'ftcalendar' ), 'install_plugins', 'ftcalendar-general', array( &$this, 'options_page' ) );
    add_submenu_page( 'ftcalendar-general', __( 'Manage ', 'ftcalendar') . ucwords( $ft_cal_options->calendar_options['calendar_label_plural'] ), __( ucwords( $ft_cal_options->calendar_options['calendar_label_plural'] ), 'ftcalendar' ), 'install_plugins', 'edit-tags.php?taxonomy=ftcalendar' );
    add_submenu_page( 'ftcalendar-general', __( 'FullThrottle Calendar Import', 'ftcalendar' ), __( 'Import', 'ftcalendar' ), 'install_plugins', 'ftcalendar-import', array( &$this, 'import_page' ) );
    add_submenu_page( 'ftcalendar-general', __( 'FullThrottle Calendar Export', 'ftcalendar' ), __( 'Export', 'ftcalendar' ), 'install_plugins', 'ftcalendar-export', array( &$this, 'export_page' ) );
    add_submenu_page( 'ftcalendar-general', __( 'FullThrottle Calendar Help', 'ftcalendar' ), __( 'FT Calendar Help', 'ftcalendar' ), 'install_plugins', 'ftcalendar-help', array( &$this, 'help_page' ) );
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
    <?php submit_button( __('Export FullThrottle Calendar Data'), 'primary', 'ftc-export' ); ?>
    _e( 'You must <a href="edit-tags.php?taxonomy=ftcalendar">create a calendar</a> before you can export data.', 'ftcalendar' );
            <h3><?php _e( 'Partners', 'ftcalendar' ); ?></h3>
    <h2><?php _e( 'FullThrottle Calendar Help', 'ftcalendar' ); ?></h2>
        <h3 class="hndle"><span><?php _e( '[ftcalendar] - Calendar Shortcode', 'ftcalendar' ); ?></span></h3>
        <h3 class="hndle"><span><?php _e( '[ftcalendar_thumb] - Thumbnail Calendar Shortcode', 'ftcalendar' ); ?></span></h3>
        <h3 class="hndle"><span><?php _e( '[ftcalendar_list] - Event List Shortcode', 'ftcalendar' ); ?></span></h3>
        <h3 class="hndle"><span><?php _e( 'Calendar of Events Widget', 'ftcalendar' ); ?></span></h3>
        <h3 class="hndle"><span><?php _e( 'Upcoming Events List Widget', 'ftcalendar' ); ?></span></h3>
            <h3><?php _e( 'Premium Support and Features', 'ftcalendar' ); ?></h3>
                    <h4><?php printf( __( 'FT Calendar Premium Support Benefits', 'ftcalendar' ), esc_attr( get_option( 'siteurl' ) ) ); ?></h4>
                        <?php printf( __( 'FT Calendar offers a premium support package for the low cost of %s per year per domain.', 'ftcalendar' ), '$49.00 USD' ); ?>
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
    echo '<div class="updated"><p><strong>' . __( 'Settings Saved.', 'ftcalendar' ) . '</strong></p></div>';
    return __( 'FullThrottle Calendar data successfully imported' );
    return __( 'Unable to parse CSV data' );
    return __( 'Cannot locate uploaded file. Possible a permissions issue on your server.' );
    return __( 'Unable to instantiate CSV parser.' );
    return __( 'No file selected.' );
    return __( 'Unable to determine Calendar ID' );
    return __( 'Event Calendar 3 data successfully imported' );
    return __( 'No data found' );
    return __( 'Unable to locate Event Calendar 3 tables' );
    return __( 'Unable to determine Calendar ID(s)' );
    echo "<div class='update-nag'>" . sprintf( __( "Thanks for upgrading FT Calendar! Act now and get a $15.00 discount on our premium features and support. Use coupon code: '15off'?<br /><a href='%s' target='_blank'>Yes, I want the discount!</a> | <a href='%s'>No thanks</a>." ), $link, $no_thanks ) . "</div>";
    'calendar_label_singular'=> __( 'Calendar' ),
    'calendar_label_plural'=> __( 'Calendars' ),
    $widget_ops = array('classname' => 'ftc_event_list', 'description' => __( "Full Throttle Calendar's Upcoming Events List Widget", 'ftcalendar' ) );
    $this->WP_Widget('FT_CAL_Event_List', __('Upcoming Events List'), $widget_ops);
    $title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Upcoming Events' ) : $instance['title'], $instance, $this->id_base);
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'ftcalendar' ); ?></label>
    <label for="<?php echo $this->get_field_id('show_rss_feed'); ?>"><?php _e( 'Show XML Feed?', 'ftcalendar' ); ?></label>
    <label for="<?php echo $this->get_field_id('show_ical_feed'); ?>"><?php _e( 'Show iCal Feed?', 'ftcalendar' ); ?></label>
    <label><?php _e( 'Calendars:', 'ftcalendar' ); ?></label>
    <label for="<?php echo $this->get_field_id( $calendar->slug ); ?>"><?php _e( 'All Calendars', 'ftcalendar' ); ?></label>
    <label for="<?php echo $this->get_field_id('date'); ?>"><?php _e( 'Start Date:', 'ftcalendar' ); ?></label>
    <label for="<?php echo $this->get_field_id( 'span' ); ?>"><?php _e( 'Time Span:', 'ftcalendar' ); ?></label>
    <option value="Day" <?php selected( $date_types, "Day" ); ?>><?php _e( 'Day(s)', 'ftcalendar' ); ?></option>
    <option value="Week" <?php selected( $date_types, "Week" ); ?>><?php _e( 'Week(s)', 'ftcalendar' ); ?></option>
    <option value="Month" <?php selected( $date_types, "Month" ); ?>><?php _e( 'Month(s)', 'ftcalendar' ); ?></option>
    <option value="Year" <?php selected( $date_types, "Year" ); ?>><?php _e( 'Year(s)', 'ftcalendar' ); ?></option>
    <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'ftcalendar' ); ?></label>
    <small><?php _e( '0 = Show all events in given time span', 'ftcalendar' ); ?></small>
    <label for="<?php echo $this->get_field_id( 'timeformat' ); ?>"><?php _e( 'Time Format:', 'ftcalendar' ); ?></label>
    <small><?php _e( 'See <a href="http://php.net/date/" target="_blank">PHP\'s Format Parameters</a> for help.', 'ftcalendar' ); ?></small>
    <label for="<?php echo $this->get_field_id( 'dateformat' ); ?>"><?php _e( 'Date Format:', 'ftcalendar' ); ?></label>
    <small><?php _e( 'See <a href="http://php.net/date/" target="_blank">PHP\'s Format Parameters</a> for help.', 'ftcalendar' ); ?></small>
    <label for="<?php echo $this->get_field_id( 'date_template' ); ?>"><?php _e( 'Date Template:', 'ftcalendar' ); ?></label>
    <label for="<?php echo $this->get_field_id( 'monthformat '); ?>"><?php _e( 'Month Format:', 'ftcalendar' ); ?></label>
    <small><?php _e( 'See <a href="http://php.net/date/" target="_blank">PHP\'s Format Parameters</a> for help.', 'ftcalendar' ); ?></small>
    <label for="<?php echo $this->get_field_id( 'month_template' ); ?>"><?php _e( 'Month Template:', 'ftcalendar' ); ?></label>
    <label for="<?php echo $this->get_field_id( 'event_template' ); ?>"><?php _e( 'Event Template:', 'ftcalendar' ); ?></label>
    <label for="<?php echo $this->get_field_id( 'hide_duplicates' ); ?>"><?php _e( 'Hide Duplicates?', 'ftcalendar' ); ?></label>
    <small><?php _e( 'Date Template must be blank.', 'ftcalendar' ); ?></small>
    _e( 'You have to create a calendar before you can use this widget.', 'ftcalendar' );
    $widget_ops = array( 'classname' => 'ftc_thumb_calendar', 'description' => __( 'Full Throttle Calendar\'s Thumb Calendar Widget' ) );
    $this->WP_Widget( 'FT_CAL_Thumb_Calendar', __( 'Calendar of Events' ), $widget_ops );
    $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Calendar of Events' ) : $instance['title'], $instance, $this->id_base );
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'ftcalendar' ); ?></label>
    <label for="<?php echo $this->get_field_id('show_rss_feed'); ?>"><?php _e( 'Show XML Feed?', 'ftcalendar' ); ?></label>
    <label for="<?php echo $this->get_field_id('show_ical_feed'); ?>"><?php _e( 'Show iCal Feed?', 'ftcalendar' ); ?></label>
    <label><?php _e( 'Calendars:', 'ftcalendar' ); ?></label>
    <label for="<?php echo $this->get_field_id( $calendar->slug ); ?>"><?php _e( 'All Calendars', 'ftcalendar' ); ?></label>
    _e( 'You have to create a calendar before you can use this widget.', 'ftcalendar' );
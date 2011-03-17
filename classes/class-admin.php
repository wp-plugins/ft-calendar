<?php
/**
 * This file contains the admin class
 * @package FT_Calendar
 * @since 0.3
 */
if ( !class_exists( 'FT_CAL_Admin' ) ) {

	/**
	 * Admin class sets up admin pages, registers options, and manages menus
	 *
	 * @since 0.3
	 */
	class FT_CAL_Admin {
		
		/**
		 * Class constructor. Puts things in motion
		 *
		 * @uses is_admin()
		 * @uses add_action()
		 * @since 0.3
		 */
		function ft_cal_admin() {
			
			if ( is_admin() ) {

				add_action( 'admin_menu', array( $this, 'register_option_pages' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'ftcalendar_admin_js' ) );

			}
			
		}
		
		/**
		 * Add Options pages to menu
		 *
		 * @uses add_menu_page()
		 * @uses add_submenu_page()
		 * @since 0.3
		 */
		function register_option_pages() {
			
			global $ft_cal_options, $wp_version;

			// Adds top level page to menu
			add_menu_page( __( 'FullThrottle Calendar General Settings', 'ftcalendar' ), __( 'FT Calendar', 'ftcalendar' ), 'install_plugins', 'ftcalendar-general', array( &$this, 'options_page' ) );

			// // Adds submenu item pre 2.9
			if ( version_compare( $wp_version , '2.9' , '>=' ) )
				add_submenu_page( 'ftcalendar-general', __( 'FullThrottle Calendar General Settings', 'ftcalendar' ), __( 'FT Calendar', 'ftcalendar' ), 'install_plugins', 'ftcalendar-general', array( &$this, 'options_page' ) );
			
			// Adds submenu to item
				add_submenu_page( 'ftcalendar-general', __( 'Manage ' . ucwords( $ft_cal_options->calendar_options['calendar_label_plural'] ), 'ftcalendar' ) , __( ucwords( $ft_cal_options->calendar_options['calendar_label_plural'] ), 'ftcalendar' ), 'install_plugins', 'edit-tags.php?taxonomy=ftcalendar' );

			// Help Page
				add_submenu_page( 'ftcalendar-general', __( 'FullThrottle Calendar Help', 'ftcalendar' ), __( 'FT Calendar Help', 'ftcalendar' ), 'install_plugins', 'ftcalendar-help', array( &$this, 'help_page' ) );
		
		}
		
		/**
		 * Content for the calendar options page
		 *
		 * @uses wp_nonce_field()
		 * @uses get_post_types()
		 * @since 0.3
		 */
		function options_page() {
			
			global $ft_cal_options, $wp_roles, $wp_version;
			
			// Checks for updated form and updates DB if needed
			$options = $this->process_options();
			
			// Creates vars out of option array elements
			extract( $options['calendar'] );
			
			// If we have additional options, put them in vars as well
			if ( isset( $options['additional'] ) )
				extract( $options['additional'] );
				
			?>
			<div class="wrap">
				<div id="icon-options-general" class="icon32"><br /></div>
				<h2><?php _e( 'FullThrottle Calendar General Settings', 'ftcalendar' ); ?> <a href="admin.php?page=ftcalendar-help" style="text-decoration: none;"><?php _e( '(Upgrade to Premium Support)' ); ?></a></h2>
					
				<div id="dashboard-widgets-wrap" class="clear">
					
                    <div id='widgets-left' class='postbox-container' style="margin-right: 0; width: 68%;">
                    
                        <div id='available-widgets' class='widgets-holder-wrap ui-droppable'>
                        
                            <div class="sidebar-name">
                
                                <div title="Click to toggle" class="sidebar-name-arrow"><br></div>
                
                                <h3 class="hndle"><span><?php _e( 'General Options', 'ftcalendar' ); ?></span></h3>
                                
                            </div>
                            
                            <div class="widget-holder">
                            
                                <div class="inside" style="padding: 0 10px 10px 10px;">
                                    
                                    <div class="table">

                                        <form method="post" action="">
                                        
                                            <?php
                                            // nonce field for security
                                            if ( function_exists( 'wp_nonce_field' ) )
                                                wp_nonce_field( 'ft-cal-update_calendar-options' );
                                            ?>
                                            <input type='hidden' name='ft_cal_calendar_options_submitted' value='1' />
                                            <table class="form-table" style="clear: none; width: auto;">
                                            
                                            <!-- How are events added to calendars? -->
                                            <tr valign="top">
                                                <th scope="row"><?php _e('Enable Calendar items for these post types:') ?></th>
                                                <td> 
                                                    <?php
													if ( version_compare( $wp_version, '3.0', '>' ) ) {
													?>
                                                    <fieldset><legend class="screen-reader-text"><span><?php _e( 'Enable Calendar items for these post types:', 'ftcalendar' ) ?></span></legend>
                                                    <select style="height: 70px; width: 150px;" id="attach_events_to_post_types" name='attach_events_to_post_types[]' multiple="multiple" size="5">
													
                                                    <?php
														foreach ( (array)get_post_types( array( 'show_ui' => true ) ) as $type ) {
															if ( $posttype = get_post_type_object( $type ) ) :
															?>
															<option class="attach_events_to_post_type" value="<?php echo esc_attr( $type );?>" <?php selected( in_array( $type, $attach_events_to_post_types ) ); ?>><?php echo esc_attr( $posttype->labels->name );?></option>
															<?php
															endif;
														}
													?>
                                                    
                                                    </select>
                                                    </fieldset>
													
													<?php
													
													} else {
													
														echo "<p>To take full advantage of publishing calendar data to Pages and Custom Post Types, please upgrade to the latest version of WordPress.</p>";
														
													}
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row"><?php _e('Show Support Link:') ?></th>
                                                <td>
                                                    <input type="checkbox" name="show_support" <?php checked( $show_support ); ?>/>
                                                </td>
                                            </tr>
                                            </table>
                                    
                                            <!-- Submit Button -->	
                                            <p class="submit">
                                                <input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'ftcalendar' ) ?>" />
                                            </p>
                                    
                                        </form>
                                        
                                    </div>
                                    
                                </div> <!-- inside -->
                                
                            </div> <!-- postbox -->
                        
                        </div> <!-- meta-box-sortables -->

                    </div> <!-- postbox-container -->

                    <div class='widget-liquid-right' style='width: 30%;'>
                            
                        <div id='widgets-right' style='width: 100%;'>
                        
                        	<div class="widgets-holder-wrap">
                            
								<?php do_action( 'sm-help-side-sortables-top' ); ?>
                                
                                <!-- #### PREMIUM SUPPORT #### -->
                                
                                <div class="sidebar-name" style='color:#fff;text-shadow:0 1px 0 #000;background: #fff url( <?php echo FT_CAL_URL; ?>/includes/images/blue-grad.png ) top left repeat-x; cursor: default;'>
                                    
                                    <h3><?php _e( 'Partners', 'ftcalendar' ); ?></h3>
                                    
                                </div>
                                
                                <div class="widgets-sortables">
                                    
                                    <div class="inside" style='padding: 0pt 10px 10px;' >
                                    
                                        <table class="form-table">
                                        
                                            <tr>
                                            
                                                <td>
                                                
													<div style="margin-left: auto; margin-right: auto; text-align: center;">
                                                    	<a href="http://www.shareasale.com/r.cfm?b=241692&u=474529&m=28169&urllink=&afftrack=" target="_blank"><img src="http://www.shareasale.com/image/28169/120x240.jpg" /></a>
                                                    
                                                    </div>
                                                    
                                                    <br />
                                                    
                                                    <div style="margin-left: auto; margin-right: auto; text-align: center;">
                                                    	<a href="http://ithemes.com/member/go.php?r=14530&i=b16"><img src="http://ithemes.com/graphics/backupbuddy_sidebarad.png" border=0 alt="Backup WordPress Easily" width="200px" /></a>
                                                    </div>
                                                    
                                                    <p style="text-align: center;">
                                                    	<a href="admin.php?page=ftcalendar-help">remove partners</a>
                                                    </p>
                    
                                                </td>
                                                
                                            </tr>
                                            
                                        </table>
                                        
                                    </div> <!-- inside -->
                                    
                                </div> <!-- postbox -->
                                
                            </div>
                            
                        </div> <!-- meta-box-sortables -->
                        
                    </div> <!-- postbox-container -->

				</div><!-- dashboard-widgets-wrap -->
                
			</div>
			<?php
		}

		/**
		 * Content for the calendar help page
		 *
		 * @since 0.3
		 */
		function help_page() {
			$timeformat = get_option( 'time_format' );
			$dateformat = get_option( 'date_format' );
			
			$ftcalendars = get_terms( 'ftcalendar', 'order=ASC&orderby=name&hide_empty=0' );
			foreach ( $ftcalendars as $ftcalendar ) {
				$calendars[] = $ftcalendar->slug;
				$single_calendar = $ftcalendar->slug;
			}
			$calendar_string = join( ',', $calendars );
			
			?>
			<div class="wrap">
				<div id="icon-options-general" class="icon32"><br /></div>
				<h2><?php _e( 'FullThrottle Calendar Help', 'ftcalendar' ); ?></h2>
					
				<div id="dashboard-widgets-wrap" class="clear">
					
                    <div id='widgets-left' class='postbox-container' style="margin-right: 0; width: 56%;">
                    
                        <div id='available-widgets' class='widgets-holder-wrap ui-droppable'>
                        
                            <div class="sidebar-name">
                
                                <div title="Click to toggle" class="sidebar-name-arrow"><br></div>
                
                                <h3 class="hndle"><span><?php _e( '[ftcalendar] - Calendar Shortcode', 'ftcalendar' ); ?></span></h3>
                                
                            </div>
                            
                            <div class="widget-holder">
                            
                                <div class="inside" style="padding: 0 10px 10px 10px;">
                                    
                                    <div class="table">
                                    
                                        <table class="form-table">
                                    
                                            <tr>
                                            
                                                <td>
                                                
                                                    Full Page Calendar: <code style="font-size: 1.2em; background: #ffffe0;">[ftcalendar]</code>
                                                
                                                    <pre>
                                                    
Default Arguments:

heading_label='partial'
calendars='all'
class=''
width=''
height=''
legend='on'
types='on'
dateformat='<?php echo $dateformat; ?>'
timeformat='<?php echo $timeformat; ?>'

Accepted Arguments:

heading_label: letter | partial | full
calendars: all,<?php echo $calendar_string; ?> 
class: The class assigned to calendar's ftcalendar-div &lt;div&gt;
width: The integer width of calendar's ftcalendar-div &lt;div&gt;
height: The integer height of the calendar's ftcalendar-div &lt;div&gt;
legend: on | off (turns off ftcalendar-legend &lt;div&gt;)
types: on | off (turns off ftcalendar-types -- Day, Week, Month)
dateformat: Date format string from <a href="http://php.net/date/" target="_blank">PHP's date() parameters</a>
timeformat: Time format string from <a href="http://php.net/date/" target="_blank">PHP's date() parameters</a>

Examples:

[ftcalendar width='600' height='600']
[ftcalendar calendars='<?php echo $single_calendar; ?>' dateformat='F j' types='off' legend='off']
[ftcalendar calendars='<?php echo $calendar_string; ?>' heading_label='full' timeformat='g:i']

                                                    </pre>
                                                    
See <a href="http://php.net/date/" target="_blank">PHP's Format Parameters</a> for help with Date and Time format strings. 
                                                    
                                                </td>
                                                
                                            </tr>
                                            
                                        </table>
                                        
                                    </div>
                                    
                                    <div class="clear"></div>
                                    
                                </div> <!-- inside -->
                                
                            </div> <!-- postbox -->
                        
                        </div> <!-- meta-box-sortables -->
                    
                        <div id='available-widgets' class='widgets-holder-wrap ui-droppable closed'>
                        
                            <div class="sidebar-name">
                
                                <div title="Click to toggle" class="sidebar-name-arrow"><br></div>
                
                                <h3 class="hndle"><span><?php _e( '[ftcalendar_thumb] - Thumbnail Calendar Shortcode', 'ftcalendar' ); ?></span></h3>
                            </div>
                            
                            <div class="widget-holder">
                            
                                <div class="inside" style="padding: 0 10px 10px 10px;">
                                    
                                    <div class="table">
                                    
                                        <table class="form-table">
                                    
                                            <tr>
                                            
                                                <td>
                                                
                                                    Thumbnail Calendar: <code style="font-size: 1.2em; background: #ffffe0;">[ftcalendar_thumb]</code>
                                                
                                                    <pre>

Default Arguments:

calendars='all'
class=''
width=''
height=''
dateformat='<?php echo $dateformat; ?>'
timeformat='<?php echo $timeformat; ?>'

Accepted Arguments:

calendars: all,<?php echo $calendar_string; ?> 
class: The class assigned to calendar's ftcalendar-div &lt;div&gt;
width: The integer width of the calendar's ftcalendar-div &lt;div&gt;
height: The integer height of the calendar's ftcalendar-div &lt;div&gt;
dateformat: Date format string from <a href="http://php.net/date/" target="_blank">PHP's date() parameters</a>
timeformat: Time format string from <a href="http://php.net/date/" target="_blank">PHP's date() parameters</a>

Examples:

[ftcalendar_thumb width='200' height='200']
[ftcalendar_thumb calendars='<?php echo $single_calendar; ?>' dateformat='F j' width='400']
[ftcalendar_thumb calendars='<?php echo $calendar_string; ?>' timeformat='g:i']

                                                    </pre>
                                                    
See <a href="http://php.net/date/" target="_blank">PHP's Format Parameters</a> for help with Date and Time format strings. 
                                                    
                                                </td>
                                                
                                            </tr>
                                            
                                        </table>
                                        
                                    </div>
                                    
                                    <div class="clear"></div>
                                    
                                </div> <!-- inside -->
                                
                            </div> <!-- postbox -->
                        
                        </div> <!-- meta-box-sortables -->
                    
                        <div id='available-widgets' class='widgets-holder-wrap ui-droppable closed'>
                        
                            <div class="sidebar-name">
                
                                <div title="Click to toggle" class="sidebar-name-arrow"><br></div>
                
                                <h3 class="hndle"><span><?php _e( '[ftcalendar_list] - Event List Shortcode', 'ftcalendar' ); ?></span></h3>
                            </div>
                            
                            <div class="widget-holder">
                            
                                <div class="inside" style="padding: 0 10px 10px 10px;">
                                    
                                    <div class="table">
                                    
                                        <table class="form-table">
                                    
                                            <tr>
                                            
                                                <td>
                                                
                                                    Upcoming Events List: <code style="font-size: 1.2em; background: #ffffe0;">[ftcalendar_list]</code>
                                                
                                                    <pre>
                                                    
Default Arguments:

span='+1 Month'
calendars='all'
limit='0'
dateformat='jS'
timeformat='<?php echo $timeformat; ?>'
monthformat='F Y'
event_template='&lt;a href="%URL%"&gt;%TITLE% (%TIME%)&lt;/a&gt;'
date_template='%DATE%'
month_template='%MONTH%'

Accepted Arguments:

span: Time string of upcoming events, as a relative string.
calendars: all,<?php echo $calendar_string; ?> 
limit: Maximum number of items to display (0 = all)
dateformat: Date format string from <a href="http://php.net/date/" target="_blank">PHP's date() parameters</a>
timeformat: Time format string from <a href="http://php.net/date/" target="_blank">PHP's date() parameters</a>
monthformat: Time format string from <a href="http://php.net/date/" target="_blank">PHP's date() parameters</a>
event_template: HTML template for displaying the event details
date_template: HTML template for displaying the date
month_template: HTML template for displaying the month

Acceptable Template Replacement Tags:

%DATE% - Date (from dateformat)
%MONTH% - Month (from monthformat)
%URL% - Permalink to the event details
%TITLE% - Title of the event
%TIME% - Time (fromt he timeformat)
%AUTHOR% - Author of the event

Examples:

[ftcalendar_list span='+1 Year' limit='50']
[ftcalendar_list calendars='<?php echo $single_calendar; ?>' dateformat='d' date_template='' monthformat='F' event_template='%DATE%  -  &lt;a href="%LINK%"&gt;%TITLE%&lt;/a&gt;']
[ftcalendar_list calendars='<?php echo $calendar_string; ?>' timeformat='g:i']

                                                    </pre>
                                                    
See <a href="http://php.net/date/" target="_blank">PHP's Format Parameters</a> for help with Date, Time, and Month format strings. 

                                                </td>
                                                
                                            </tr>
                                            
                                        </table>
                                        
                                    </div>
                                    
                                </div> <!-- inside -->
                                
                            </div> <!-- postbox -->
                        
                        </div> <!-- meta-box-sortables -->
                    
                        <div id='available-widgets' class='widgets-holder-wrap ui-droppable closed'>
                        
                            <div class="sidebar-name">
                
                                <div title="Click to toggle" class="sidebar-name-arrow"><br></div>
                
                                <h3 class="hndle"><span><?php _e( 'Calendar of Events Widget', 'ftcalendar' ); ?></span></h3>
                            </div>
                            
                            <div class="widget-holder">
                            
                                <div class="inside" style="padding: 0 10px 10px 10px;">
                                    
                                    <div class="table">
                                    
                                        <table class="form-table">
                                    
                                            <tr>
                                            
                                                <td>
                                                
                                                    <p>This widget is available in <a href='widgets.php'>Widgets</a> section of your WordPress dashboard. It is basically the Thumbnail Calendar. You can give it a title and select a calendar to display.</p>

                                                </td>
                                                
                                            </tr>
                                            
                                        </table>
                                        
                                    </div>
                                                                        
                                </div> <!-- inside -->
                                
                            </div> <!-- postbox -->
                        
                        </div> <!-- meta-box-sortables -->
                    
                        <div id='available-widgets' class='widgets-holder-wrap ui-droppable closed'>
                        
                            <div class="sidebar-name">
                
                                <div title="Click to toggle" class="sidebar-name-arrow"><br></div>
                
                                <h3 class="hndle"><span><?php _e( 'Upcoming Events List Widget', 'ftcalendar' ); ?></span></h3>
                            </div>
                            
                            <div class="widget-holder">
                            
                                <div class="inside" style="padding: 0 10px 10px 10px;">
                                    
                                    <div class="table">
                                    
                                        <table class="form-table">
                                    
                                            <tr>
                                            
                                                <td>

                                                   
                                                   <p>This widget is available in <a href='widgets.php'>Widgets</a> section of your WordPress dashboard. It is basically the Event List shortcode. You are able to customize the same settings as the shortcode.</p>
                                                                                                        
													<p>See <a href="http://php.net/date/" target="_blank">PHP's Format Parameters</a> for help with Date, Time, and Month format strings.</p>

                                                </td>
                                                
                                            </tr>
                                            
                                        </table>
                                        
                                    </div>
                                    
                                </div> <!-- inside -->
                                
                            </div> <!-- postbox -->
                        
                        </div> <!-- meta-box-sortables -->

                    </div> <!-- postbox-container -->

                    <div class='widget-liquid-right' style='width: 42%;'>
                            
                        <div id='widgets-right' style='width: 100%;'>
                        
                        	<div class="widgets-holder-wrap">
                            
								<?php do_action( 'sm-help-side-sortables-top' ); ?>
                                
                                <!-- #### PREMIUM SUPPORT #### -->
                                
                                <div class="sidebar-name" style='color:#fff;text-shadow:0 1px 0 #000;background: #fff url( <?php echo FT_CAL_URL; ?>/includes/images/blue-grad.png ) top left repeat-x; cursor: default;'>
                                    
                                    <h3><?php _e( 'Premium Support and Features', 'ftcalendar' ); ?></h3>
                                    
                                </div>
                                
                                <div class="widgets-sortables">
                                    
                                    <div class="inside" style='padding: 0pt 10px 10px;' >
                                        
                                        <?php
                                        // Check for premium support status
                                        global $ftcalendar_ps;
    
                                        if ( ! url_has_ftps_for_item( $ftcalendar_ps ) ) : ?>
                                        
                                            <h4><?php printf( __( 'FT Calendar Premium Support Benefits', 'ftcalendar' ), esc_attr( get_option( 'siteurl' ) ) ); ?></h4>
                                            <p>
                                                <?php printf( __( 'FT Calendar offers a premium support package for the low cost of %s per year per domain.', 'ftcalendar' ), '$49.00 USD' ); ?>
                                            </p>
                                            <p>
                                                <?php _e( 'By signing up for FT Calendar premium support, you help to ensure future enhancements to this excellent project as well as the following benefits:', 'ftcalendar' ); ?>
                                            </p>
                                        
                                            <ul style='margin-left:25px;list-style-type:disc'>
                                                <li><?php _e( 'Around the clock access to our knowledge base and support forum from within your WordPress dashboard', 'ftcalendar' ); ?></li>
                                                <li><?php _e( 'Professional and timely response times to all your questions from the FT Calendar team', 'ftcalendar' ); ?></li>
                                                <li><?php _e( 'A 10% discount for any custom functionality you request from the FT Calendar developers', 'ftcalendar' ); ?></li>
                                                <li><?php _e( 'A 6-12 month advance access to new features integrated into the auto upgrade functionality of WordPress', 'ftcalendar' ); ?></li>
                                                <li><?php _e( 'Ads removed from Calendar Meta Boxes and Control Panels' ); ?></li>
                                            </ul>
                                            
                                            <ul style='margin-left:25px;list-style-type:none'>
                                                <li><a href='<?php echo get_ftps_paypal_button( $ftcalendar_ps ); ?>'><?php _e( 'Signup Now', 'ftcalendar' ); ?></a></li>
                                                <li><a target='_blank' href='<?php echo get_ftps_learn_more_link( $ftcalendar_ps ); ?>'><?php _e( 'Learn More', 'ftcalendar' ); ?></a></li>
                                            </ul>
                                        <?php else : ?>
    
                                            <p class='howto'><?php printf( "Your premium support for <code>%s</code> was purchased on <code>%s</code> by <code>%s</code> (%s). It will remain valid for this URL until <code>%s</code>.", get_ftps_site( $ftcalendar_ps ), date( "F d, Y", get_ftps_purchase_date( $ftcalendar_ps ) ), get_ftps_name( $ftcalendar_ps ), get_ftps_email( $ftcalendar_ps ), date( "F d, Y", get_ftps_exp_date( $ftcalendar_ps ) ) ); ?></p>
                                            <p><a href='#' id='premium_help'><?php _e( 'Launch Premium Support widget', 'ftcalendar' ); ?></a> | <a target="blank" href="http://support.calendar-plugin.com?sso=<?php echo get_ftps_sso_key( $ftcalendar_ps ); ?>"><?php _e( 'Visit Premium Support web site', 'ftcalendar' );?></a></p>
                                            <script type="text/javascript" charset="utf-8">
                                              Tender = {
                                                hideToggle: true,
                                                sso: "<?php echo get_ftps_sso_key( $ftcalendar_ps ); ?>",
                                                widgetToggles: [document.getElementById('premium_help')]
                                              }
                                            </script>
                                            <script src="https://ft-calendar.tenderapp.com/tender_widget.js" type="text/javascript"></script>
                                        
                                        <?php endif; ?>
                                        
                                    </div> <!-- inside -->
                                    
                                </div> <!-- postbox -->
                                
                            </div>
                            
                        	<div class="widgets-holder-wrap">
                            
								<?php do_action( 'sm-help-side-sortables-top' ); ?>
                                
                                <!-- #### PREMIUM SUPPORT #### -->
                                
                                <div class="sidebar-name" style='color:#fff;text-shadow:0 1px 0 #000;background: #fff url( <?php echo FT_CAL_URL; ?>/includes/images/blue-grad.png ) top left repeat-x; cursor: default;'>
                                
                                    <h3><?php _e( 'Partners', 'ftcalendar' ); ?></h3>
                                    
                                </div>
                                
                                <div class="widgets-sortables">
                                    
                                    <div class="inside" style='padding: 0pt 10px 10px;' >
                                    
                                        <table class="form-table">
                                        
                                            <tr>
                                            
                                                <td>
                                                
                                                    <a href="http://ithemes.com/member/go.php?r=14530&i=b1"><img src="http://ithemes.com/wp-content/uploads/2008/11/ithemes-ad1.jpg" border=0 alt="WordPress Themes" width=125 height=125></a>
                                                </td>
                                                
                                                <td>
                                                
                                                    <a href="http://www.shareasale.com/r.cfm?b=241692&u=474529&m=28169&urllink=&afftrack=" border=0 alt="Backup WordPress Easily" target="_blank"><img src="http://www.shareasale.com/image/28169/120x240.jpg" /></a>
                                            
                                                </td>
                                            
                                                <td>    
                                                                                    
                                                    <a href="http://ithemes.com/member/go.php?r=14530&i=b15"><img src="http://ithemes.com/graphics/backupbuddy-125.gif" border=0 alt="Backup WordPress Easily" width=125 height=125 target="_blank"></a>
                                                </td>
                                                
                                            </tr>
                                            
                                        </table>
                                        
                                    </div> <!-- inside -->
                                    
                                </div> <!-- postbox -->
                                
                            </div>
                            
                        </div> <!-- meta-box-sortables -->
                        
                    </div> <!-- postbox-container -->

				</div><!-- dashboard-widgets-wrap -->
                
			</div>	

			<?php
		}
		
		/**
		 * Enqueue styles and scripts for Admin Option pages
		 *
		 * @since 0.3.2
		 */
		function ftcalendar_admin_js( $hook_suffix ) {
			
			if ( 'ft-calendar_page_ftcalendar-help' == $hook_suffix ) {

				wp_enqueue_style( 'widgets' );
				wp_enqueue_script( 'admin-widgets' );
				
			} else if ( 'toplevel_page_ftcalendar-general' == $hook_suffix ) {

				wp_enqueue_style( 'widgets' );
				wp_enqueue_script( 'admin-widgets' );
				
			}
			
		}
		
		/**
		 * Process data when form is submitted
		 *
		 * @since 0.3
		 */
		function process_options() {
			
			global $ft_cal_options;
			
			// Grab existing options from database
			$existing['calendar'] = $ft_cal_options->calendar_options;
			$existing['additional'] = $ft_cal_options->additional_options;

			// If the form hasn't been submitted, return what we have in the DB
			if ( !isset( $_POST['ft_cal_calendar_options_submitted'] ) )
				return $existing;
				
			// Confirm user can update options
			//if ( !current_user_can( 'manage_ftcalendars' ) )
			//	return $existing;
			
			// Confirm nonce
			check_admin_referer( 'ft-cal-update_calendar-options' );

			### Validate fields
			
			// Attach events to post types?
			if ( isset( $_POST['attach_events_to_post_types'] ) && is_array( $_POST['attach_events_to_post_types'] ) ) {
			
				foreach ( (array)$_POST['attach_events_to_post_types'] as $key => $value ) {
					
					$submitted['calendar']['attach_events_to_post_types'][$key] = $value;
					
				}
				
			}
			
			if ( isset( $_POST['show_support'] ) && 'on' == $_POST['show_support'] ) {
			
				$submitted['calendar']['show_support'] = true;
				
			} else {
			
				echo "off";
				
				$submitted['calendar']['show_support'] = false;
				
			}
			
			$options = apply_filters( 'ft_cal_update_calendar_options', $_POST );

			$options = $ft_cal_options->parse_option_args( $submitted, $existing );
			
			update_option( 'ft_calendar_options', $options );
			
			return $options;
			
		}
		
	}
	
}

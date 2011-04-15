<?php
/**
 * @package FT_Calendar
 *
 * This script registers custom post types for calendar and events
 * It also registers custom taxonomies and defines meta callback functions
 * Finally, it should be noted that this script also modifies the way the post types
 * are displayed in the GUI depending on the Calendars Options
 *
 * @since 0.3
 */
 
if ( !class_exists( 'FT_Cal_Calendars' ) ) {
	
	/**
	 * Calendar Class. Handles post types, taxonomies, meta callbacks
	 *
	 * @since 0.3
	 */
	class FT_Cal_Calendars {
		
		/**
		 * PHP4 Constructor. Registers most of our actions for calendars
		 *
		 * @since 0.3
		 */
		function ft_cal_calendars() {
			
			add_action( 'init', array( &$this, 'register_calendar_taxonomy' ) );
			add_action( 'ftcalendar_add_form_fields', array( &$this, 'ftcalendar_taxonomy_add_form_fields' ) );
			add_action( 'ftcalendar_edit_form_fields', array( &$this, 'ftcalendar_taxonomy_edit_form_fields' ), 10, 2 );
			add_action( 'edited_ftcalendar', array( &$this, 'save_ftcal_meta_options'), 10, 2);
			add_action( 'created_ftcalendar', array( &$this, 'save_ftcal_meta_options'), 10, 2);
			add_filter( 'manage_ftcalendar_sortable_columns', array( &$this, 'ftcalendar_taxonomy_add_column' ) );
			add_action( 'admin_print_styles-edit-tags.php', array( &$this, 'enqueue_add_edit_taxonomy_css' ) );
			add_action( 'admin_print_scripts-edit-tags.php', array( &$this, 'enqueue_add_edit_taxonomy_js' ) );
			add_filter( 'pre_insert_term', array( &$this, 'ftcal_reserved_terms' ), 10, 2 );
		}
		
		/**
		 * Registers the Calendar Taxonomy
		 *
		 * @since 0.3
		 */
		function register_calendar_taxonomy() {
			
			global $ft_cal_options;
			
			$singular_label = $ft_cal_options->calendar_options['calendar_label_singular'];
			$plural_label 	= $ft_cal_options->calendar_options['calendar_label_plural'];
			
			// Custom Taxonomies for calendars
			$calendar_tax_args = array( 
				'label' => 'Calendars',
				'hierarchical' => false,
				'rewrite' => array( 'slug' => 'calendars', 'with_front' => false ),
				'show_tagcloud' => false,
				'show_ui' => false,
				/*
				'capabilities' => array(
					'manage_terms' => 'manage_ftcalendars',
					'edit_terms'   => 'manage_ftcalendars',
					'delete_terms' => 'manage_ftcalendars',
					'assign_terms' => 'edit_ftcalendars',
				),
				*/
				'labels' => array(
					'name' => ucfirst( $plural_label ),
					'singular_name' => ucfirst( $singular_label ),
					'search_items' => 'Search ' . ucfirst( $plural_label ),
					'popular_items' => 'Popular ' . ucfirst( $plural_label ),
					'all_items' => 'All ' . ucfirst( $plural_label ),
					'parent_item' => 'Parent ' . ucfirst( $singular_label ),
					'parent_item_colon' => 'Parent ' . ucfirst( $singular_label ) . ':',
					'edit_item' => 'Edit ' . ucfirst( $singular_label ),
					'update_item' => 'Update ' . ucfirst( $singular_label ),
					'add_new_item' => 'Add New ' . ucfirst( $singular_label ),
					'new_item_name' => 'New ' . ucfirst( $singular_label ) . ' Name',
					'separate_items_with_commas' => 'Separate ' . $plural_label . ' with commas',
					'add_or_remove_items' => 'Add or remove ' . $singular_label,
					'choose_from_most_used' => 'Choose from the most used ' . $plural_label
				)
			);
			
			// Loop through all the post types we want calendars available to and register.
			foreach ( (array) $ft_cal_options->calendar_options['attach_events_to_post_types'] as $post_type ) {
				register_taxonomy( 'ftcalendar', $post_type, $calendar_tax_args );
			}

		}
		
		/**
		 * Attaches the calendar color form fields to the Add Calendar Taxonomy page
		 *
		 * @since 0.3
		 */
		function ftcalendar_taxonomy_add_form_fields() {
			?>	
            <div class="form-field">
            	<label for="ftcalendar-color"><?php _e( 'Calendar Label Color', 'ftcalendar' ); ?></label>
                <?php echo $this->get_calendar_colors(); ?>
            </div>
			<?php
		}
		
		/**
		 * Attaches the calendar color fields to the Edit Calendar taxonomy page
		 *
		 * @since 0.3
		 */
		function ftcalendar_taxonomy_edit_form_fields( $tag, $taxonomy ) {
		    
		    $ftcal_meta = get_option( $taxonomy . "_meta" );
			
			?>		
			<tr class="form-field">
            <th valign="top" scope="row"><?php _e( 'Calendar Label Color', 'ftcalendar' ); ?></th>
            <td><?php echo $this->get_calendar_colors( $ftcal_meta['ftcal-bg-color-' . $tag->term_id ] ); ?></td>
			</tr>
			<?php
			
		}
		
		/**
		 * Display the color options for the current calendar
		 *
		 * @since 0.3
		 */
		function get_calendar_colors( $ftcal_color = null ) {
			
			$full_colors = $this->get_full_colors();

			$color = ( isset( $ftcal_color ) ) ? $ftcal_color : '668cd9';
			
			$style = "background-color: #" . $color . "; border-color: #" . $color . ";";

			$cc = '<div id="ftcalendar-color-picker">';
			$cc .= '<input type="hidden" value="' . $color .'" id="ftcal-color" name="ftcal-color" />';
			$cc .= "<div id='calendar-label-color' style='width: 175px; clear: both; " . $style . "'><div style='" . $style . "'>calendar</div></div>";
			$cc .= "<ul>";
			foreach ( (array)$full_colors as $bg_color => $border_color ) {
				$cc .= '<li class="calcolor-li"><a class="calcolor-square" style="background-color: #' . $bg_color .'; border: 1px solid #' . $border_color . ';">&nbsp;</a></li>';
			}
			$cc .= "</ul>";
			$cc .= '</div>';
			   
			return $cc;
		}
		
		/**
		 * Save calendar options when calendar is saved
		 *
		 * @since 0.3
		 */
		function save_ftcal_meta_options( $term_id, $taxonomy_id) {
			
			if ( ! $term_id ) return;
						
			$full_colors = $this->get_full_colors();
			
			$meta_options = get_option( 'ftcalendar_meta' );
			
			if ( isset( $_POST['ftcal-color'] ) ) {
				
				if ( preg_match( '/rgb\((\d+), (\d+), (\d+)\)/', $_POST['ftcal-color'], $matches ) )
					$hex = $this->rgbToHex( $matches[1], $matches[2], $matches[3] );
				else
					$hex = ltrim( $_POST['ftcal-color'], '#' );
			
				$meta_options['ftcal-bg-color-' . $term_id] = $hex;
				$meta_options['ftcal-border-color-' . $term_id] = $full_colors[$hex];
			
			}
			
			update_option( 'ftcalendar_meta', $meta_options );
		
		}
		
		/**
		 * Array of possible colors for calendars
		 *
		 * @since 0.3
		 */
		function get_full_colors() {
			return apply_filters( 'ftc_full_colors', array( 
									'd96666' => 'cc3333',
									'e67399' => 'dd4477',
									'b373b3' => '994499',
									'8c66d9' => '6633cc',
									'668cb3' => '336699',
									'668cd9' => '3366cc',
									'59bfb3' => '22aa99',
									
									'65ad89' => '329262',
									'4cb052' => '109618',
									'8cbf40' => '66aa00',
									'bfbf4d' => 'aaaa11',
									'e0c240' => 'd6ae00',
									'f2a640' => 'ee8800',
									'e6804d' => 'dd5511',
									
									'be9494' => 'a87070',
									'a992a9' => '8c6d8c',
									'8997a5' => '627487',
									'94a2be' => '7083a8',
									'85aaa5' => '5c8d87',
									'a7a77d' => '898951',
									'c4a883' => 'b08b59',
									
									'c7561e' => '9f3501',
									'b5515d' => '8a2d38',
									'c244ab' => '962181',
									'603f99' => '402175',
									'536ca6' => '30487e',
									'3640ad' => '182186',
									'3c995b' => '1f753c',
									
									'5ca632' => '3d8215',
									'7ec225' => '5a9a08',
									'a7b828' => '81910b',
									'cf9911' => '9d7000',
									'd47f1e' => 'aa5a00',
									'b56414' => '8d4500',
									'914d14' => '743500',
									
									'ab2671' => '870b50',
									'9643a5' => '70237f',
									'4585a3' => '25617d',
									'737373' => '515151',
									'41a587' => '227f63',
									'd1bc36' => 'a59114',
									'ad2d2d' => '871111' ) );
		}
		
		/**
		 * Converts an RGB color value to a Hex color value
		 *
		 * @since 0.3
		 */
		function rgbToHex( $r = 0, $g = 0, $b = 0 ) {
			
			//String padding bug found and the solution put forth by Pete Williams (http://snipplr.com/users/PeteW)
			$hex = str_pad( dechex($r), 2, "0", STR_PAD_LEFT );
			$hex .= str_pad( dechex($g), 2, "0", STR_PAD_LEFT );
			$hex .= str_pad( dechex($b), 2, "0", STR_PAD_LEFT );
			
			return $hex;
		
		}

		/**
		 * Prevent the calendar taxonomy box from displaying in the write / edit post sidebars
		 *
		 * @since 0.3
		 */
		function modify_calendar_tax_displays(){
			
			global $ft_cal_options;
			
			foreach ( (array) $ft_cal_options->calendar_options['attach_events_to_post_types'] as $post_type ) {
				
				// Remove calendar box from write / edit post views
				remove_meta_box( 'ftcalendardiv', $post_type, 'side' );
			
			}
				
		}
		
		/**
		 * Query database for post IDs matching the start date, end date, and calendar params
		 *
		 * @since 0.3
		 * @param string $start_date SQL format
		 * @param string $end_Date SQL format
		 * @returns obj
		 */
		function get_ftcal_data_ids( $start_date, $end_date, $calendar = 'all' ) {	
			global $wpdb;
			global $ft_cal_options;
			
			$start_date .= " 00:00:00";	// add Midnight in start date
			$end_date .= " 23:59:59";	// add 1 second before the next day in end date
			
			$sql = 	"SELECT ftc.* FROM " . $wpdb->prefix . "ftcalendar_events as ftc " .
					"JOIN " . $wpdb->posts . " as posts on posts.ID = ftc.post_parent " .
					"WHERE ( " .
					"( ( ftc.start_datetime >= '$start_date' || ftc.end_datetime >= '$start_date' ) " .
					"    && ( ftc.start_datetime <= '$end_date' || ftc.end_datetime <= '$end_date' ) ) " .
					"  || ( ftc.repeating = 1 " . 
					"       && ( ftc.r_start_datetime >= '$start_date' " .
					"            || ( ftc.r_end = 0 || ( ftc.r_end = 1 && ftc.r_end_datetime >= '$start_date' ) ) ) " .
					"       && ( ftc.r_start_datetime <= '$end_date' " .
					"            || ( ftc.r_end = 0 || ( ftc.r_end = 1 && ftc.r_end_datetime <= '$end_date' ) ) ) ) ) " .
					" && posts.post_status = 'publish' ";
					
			if ( 'all' != $calendar ) {
				$sql .= " && ( ";
				$calendars = preg_split("/\s?,\s?/", $calendar);
				
				$i = 0;
				$last = count($calendars) - 1;
				foreach ( (array)$calendars as $calendar ) {
					$term = get_term_by( 'slug', $calendar, 'ftcalendar' );
					if ( !empty( $term ) ) {
						$sql .= " calendar_id = " . $term->term_id . " ";
						
						if ( $last > $i ) {
							$sql .= " || ";
						}
					} else {
						$sql .= " 0 ";	
					}
					
					$i++;
				}
				$sql .= " ) ";
			}
					
			$sql .= " ORDER BY all_day DESC, start_datetime ASC";
			
			$results = $wpdb->get_results( $wpdb->prepare( $sql ), OBJECT_K );
			
			return $results;
		
		}
		
		/**
		 * Enqueues CSS for the write-edit-post screens
		 *
		 * @since 0.3
		 */
		function enqueue_add_edit_taxonomy_css() {
			
			global $current_screen;
			
			if ( 'ftcalendar' == $current_screen->taxonomy )
				wp_enqueue_style( 'add-edit-taxonomy', FT_CAL_URL . '/includes/css/add-edit-taxonomy.css' );
		
		}				

		/**
		 * Enqueues JS for the write-edit-post screens
		 *
		 * @since 0.3
		 */
		function enqueue_add_edit_taxonomy_js() {
			
			global $current_screen;
			
			if ( 'ftcalendar' == $current_screen->taxonomy )
				wp_enqueue_script( 'add-edit-taxonomy', FT_CAL_URL . '/includes/js/add-edit-taxonomy.js' );
		
		}				

		/**
		 * Check for reserved terms and return error if one is found
		 *
		 * @since 1.1.6
		 */
		function ftcal_reserved_terms( $term, $taxonomy ) {
			
			if ( 'ftcalendar' == $taxonomy ) {
				
				if ( 0 == strcasecmp( 'all', $term ) )		
					return new WP_Error( 'reserved_term_all', __( '"' . $term . '" is a reserved term in FT Calendar' ) );
			
			}
			
			return $term;
			
		}
		
	}
	
}
?>

<?php
/**
 * @package FT_Calendar
 * @since 0.3
 */
 
/**
 * This class registers and returns the Event List Widget
 *
 * @since 0.3
 */
class FT_CAL_Event_List extends WP_Widget {
	
	/**
	 * Set's widget name and description
	 *
	 * @since 0.3
	 */
	function FT_CAL_Event_List() {
		
		$widget_ops = array('classname' => 'ftc_event_list', 'description' => __( "Full Throttle Calendar's Upcoming Events List Widget", 'ftcalendar' ) );
		$this->WP_Widget('FT_CAL_Event_List', __('Upcoming Events List'), $widget_ops);
	
	}
	
	/**
	 * Displays the widget on the front end
	 *
	 * @since 0.3
	 */
	function widget( $args, $instance ) {
		global $ft_cal_shortcodes;
		extract( $args );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Upcoming Events' ) : $instance['title'], $instance, $this->id_base);
		
		if ( $term = get_term_by( 'name', $instance['calendars'], 'ftcalendar' ) ) {
			$instance['calendars'] = $term->slug;
		}
		
		$out = $ft_cal_shortcodes->do_ftcal_event_list( $instance );
		
		if ( ! empty( $out ) ) {
			
			echo $before_widget;
			
			if ( $title)
				echo $before_title . $title . $after_title;
			
			echo $out; 
			
			echo $after_widget;	
		
		}
	
	}

	/**
	 * Save's the widgets options on submit
	 *
	 * @since 0.3
	 */
	function update( $new_instance, $old_instance ) {
		
		$instance 					= $old_instance;
		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['span'] 			= "+" . $new_instance['number_of'] . " " . $new_instance['date_types'];
		$instance['number_of'] 		= $new_instance['number_of'];
		$instance['date_types'] 	= $new_instance['date_types'];
		$instance['calendars'] 		= strip_tags($new_instance['calendars']);
		$instance['limit'] 			= strip_tags($new_instance['limit']);
		$instance['dateformat'] 	= strip_tags($new_instance['dateformat']);
		$instance['timeformat'] 	= strip_tags($new_instance['timeformat']);
		$instance['monthformat'] 	= strip_tags($new_instance['monthformat']);
		$instance['event_template'] = $new_instance['event_template'];
		$instance['date_template'] 	= $new_instance['date_template'];
		$instance['month_template'] = $new_instance['month_template'];
	
		return $instance;
	
	}

	/**
	 * Displays the widget options in the dashboard
	 *
	 * @since 0.3
	 */
	function form( $instance ) {
		
		$timeformat 			= get_option('time_format');
		$available_calendars 	= get_terms( 'ftcalendar', array( 'hide_empty' => false ) );
			
		//Defaults
		$defaults = array( 
			'title'				=> '',
			'span'				=> '+1 Month',
			'number_of'			=> '1',
			'date_types'		=> 'Month',
			'calendars'			=> 'all',
			'limit'				=> 0,
			'timeformat'		=> $timeformat,
			'dateformat'		=> 'jS',
			'date_template'		=> '%DATE%',
			'monthformat'		=> 'F Y',
			'month_template'	=> '%MONTH%',
			'event_template'	=> '<a href="%URL%">%TITLE% (%TIME%)</a>'
		);
		
		extract( wp_parse_args( (array) $instance, $defaults ) );
	
		if ( ! empty( $available_calendars ) ) : 
			?>
			<p>
	        	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'ftcalendar' ); ?></label>
	            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( strip_tags( $title ) ); ?>" />
	        </p>
			<p>
	        	<label for="<?php echo $this->get_field_id('calendars'); ?>"><?php _e( 'Calendars:', 'ftcalendar' ); ?></label>
	            <br />
	            <input type="radio" value="all" name="<?php echo $this->get_field_name( 'calendars' ) ?>" id="calendars" <?php checked( $calendars, "all" ) ?> class="checkbox" /> <label for="<?php echo $this->get_field_id( 'calendars '); ?>"><?php _e( 'All Calendars', 'ftcalendar' ); ?></label>
				<?php foreach ( (array)$available_calendars as $key => $calendar ) : ?>
	                <br />
	                <input type="radio" value="<?php echo $calendar->slug ?>" name="<?php echo $this->get_field_name( 'calendars' ) ?>" id="calendars" <?php checked( $calendars, $calendar->slug ) ?> class="checkbox" /> <label for="<?php echo $this->get_field_id( 'calendars' ); ?>"><?php echo $calendar->name ?></label>
	            <?php endforeach; ?>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'span' ); ?>"><?php _e( 'Time Span:', 'ftcalendar' ); ?></label>
				+<select name="<?php echo $this->get_field_name( 'number_of' ); ?>" id="<?php echo $this->get_field_id( 'number_of' ); ?>">
	            <?php for ( $i = 1; $i <= 30; $i++ ) { ?>
					<option value="<?php echo $i; ?>" <?php selected( $number_of, $i ); ?>><?php echo $i; ?></option>
	            <?php } ?>
				</select>
				<select name="<?php echo $this->get_field_name( 'date_types' ); ?>" id="<?php echo $this->get_field_id( 'date_types' ); ?>">
					<option value="Day" <?php selected( $date_types, "Day" ); ?>><?php _e( 'Day(s)', 'ftcalendar' ); ?></option>
					<option value="Week" <?php selected( $date_types, "Week" ); ?>><?php _e( 'Week(s)', 'ftcalendar' ); ?></option>
					<option value="Month" <?php selected( $date_types, "Month" ); ?>><?php _e( 'Month(s)', 'ftcalendar' ); ?></option>
					<option value="Year" <?php selected( $date_types, "Year" ); ?>><?php _e( 'Year(s)', 'ftcalendar' ); ?></option>
				</select>
			</p>
			<p>
	        	<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'ftcalendar' ); ?></label>
	            <input type="text" value="<?php echo esc_attr( $limit ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" id="<?php echo esc_attr( $limit ); ?>" />
				<br />
				<small><?php _e( '0 = Show all events in given time span', 'ftcalendar' ); ?></small>
			</p>
	        <p>
	        	<label for="<?php echo $this->get_field_id( 'timeformat' ); ?>"><?php _e( 'Time Format:', 'ftcalendar' ); ?></label>
	            <input type="text" value="<?php echo esc_attr( strip_tags( $timeformat ) ); ?>" name="<?php echo $this->get_field_name( 'timeformat' ); ?>" id="<?php echo esc_attr( strip_tags( $timeformat ) ); ?>" class="widefat" />
				<br />
	            <small><?php _e( 'See <a href="http://php.net/date/" target="_blank">PHP\'s Format Parameters</a> for help.', 'ftcalendar' ); ?></small>        
			</p>
	        <p>
	        	<label for="<?php echo $this->get_field_id('dateformat'); ?>"><?php _e( 'Date Format:', 'ftcalendar' ); ?></label>
	            <input type="text" value="<?php echo esc_attr( strip_tags( $dateformat ) ); ?>" name="<?php echo $this->get_field_name( 'dateformat' ); ?>" id="<?php echo esc_attr( strip_tags( $dateformat ) ); ?>" class="widefat" />
				<br />
	            <small><?php _e( 'See <a href="http://php.net/date/" target="_blank">PHP\'s Format Parameters</a> for help.', 'ftcalendar' ); ?></small>        
			</p>
	        <p>
	        	<label for="<?php echo $this->get_field_id( 'date_template' ); ?>"><?php _e( 'Date Template:', 'ftcalendar' ); ?></label>
	            <input type="text" value="<?php echo esc_attr( $date_template ); ?>" name="<?php echo $this->get_field_name( 'date_template' ); ?>" id="<?php echo esc_attr( $date_template ); ?>" class="widefat" />   
			</p>
	        <p>
	        	<label for="<?php echo $this->get_field_id( 'monthformat '); ?>"><?php _e( 'Month Format:', 'ftcalendar' ); ?></label>
	            <input type="text" value="<?php echo esc_attr( strip_tags( $monthformat ) ); ?>" name="<?php echo $this->get_field_name( 'monthformat' ); ?>" id="<?php echo esc_attr( strip_tags( $monthformat ) ); ?>" class="widefat" />
				<br />
	            <small><?php _e( 'See <a href="http://php.net/date/" target="_blank">PHP\'s Format Parameters</a> for help.', 'ftcalendar' ); ?></small>        
			</p>
	        <p>
	        	<label for="<?php echo $this->get_field_id( 'month_template' ); ?>"><?php _e( 'Month Template:', 'ftcalendar' ); ?></label>
	            <input type="text" value="<?php echo esc_attr( $month_template ); ?>" name="<?php echo $this->get_field_name( 'month_template' ); ?>" id="<?php echo esc_attr( $month_template ); ?>" class="widefat" />   
			</p>
	        <p>
	        	<label for="<?php echo $this->get_field_id( 'event_template' ); ?>"><?php _e( 'Event Template:', 'ftcalendar' ); ?></label>
	            <input type="text" value="<?php echo esc_attr( $event_template ); ?>" name="<?php echo $this->get_field_name( 'event_template' ); ?>" id="<?php echo esc_attr( $event_template ); ?>" class="widefat" />   
			</p>
        	<?php 
        
        else : 
        
            _e( 'You have to create a calendar before you can use this widget.', 'ftcalendar' );
        
        endif;
	
	}

}

/**
 * This class gives you a Thumbnail calendar widget
 *
 * @since 0.3
 */
class FT_CAL_Thumb_Calendar extends WP_Widget {

	/**
	 * Set's widget name and description
	 *
	 * @since 0.3
	 */
	function FT_CAL_Thumb_Calendar() {
		$widget_ops = array( 'classname' => 'ftc_thumb_calendar', 'description' => __( 'Full Throttle Calendar\'s Thumb Calendar Widget' ) );
		$this->WP_Widget( 'FT_CAL_Thumb_Calendar', __( 'Calendar of Events' ), $widget_ops );
	}
	

	/**
	 * Displays the widget on the front end
	 *
	 * @since 0.3
	 */
	function widget( $args, $instance ) {
		
		global $ft_cal_shortcodes;
		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Calendar of Events' ) : $instance['title'], $instance, $this->id_base );
		
		if ( $term = get_term_by( 'name', $instance['calendars'], 'ftcalendar' ) ) {
			$instance['calendars'] = $term->slug;
		}
		
		$out = $ft_cal_shortcodes->do_ftcal_thumb_calendar( $instance );
		
		if ( !empty( $out ) ) {
			echo $before_widget;
			
			if ( $title)
				echo $before_title . $title . $after_title;
			
			echo $out; 
			
			echo $after_widget;	
		}
		
	}

	/**
	 * Save's the widgets options on submit
	 *
	 * @since 0.3
	 */
	function update( $new_instance, $old_instance ) {
		
		$instance 				= $old_instance;
		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['calendars'] 	= strip_tags($new_instance['calendars']);
	
		return $instance;
	
	}


	/**
	 * Displays the widget options in the dashboard
	 *
	 * @since 0.3
	 */
	function form( $instance ) {
		$timeformat 			= get_option( 'time_format' );
		$available_calendars 	= get_terms( 'ftcalendar', array( 'hide_empty' => false ) );
			
		//Defaults
		$defaults = array( 
			'title'		=> '',
			'calendars'	=> 'all'
		);
		
		extract( wp_parse_args( (array) $instance, $defaults ) );
	
		if ( !empty( $available_calendars ) ) : 
			?>
			<p>
	        	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'ftcalendar' ); ?></label>
	            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( strip_tags( $title ) ); ?>" />
	        </p>
			<p>
	        	<label for="<?php echo $this->get_field_id( 'calendars' ); ?>"><?php _e( 'Calendars:', 'ftcalendar' ); ?></label>
	            <br />
	            <input type="radio" value="all" name="<?php echo $this->get_field_name( 'calendars' ) ?>" id="calendars" <?php checked( $calendars, "all" ) ?> class="checkbox" /> <label for="<?php echo $this->get_field_id( 'calendars' ); ?>"><?php _e( 'All Calendars', 'ftcalendar' ); ?></label>
				<?php foreach ( (array)$available_calendars as $key => $calendar ) : ?>
	                <br />
	                <input type="radio" value="<?php echo $calendar->slug ?>" name="<?php echo $this->get_field_name( 'calendars' ) ?>" id="calendars" <?php checked( $calendars, $calendar->slug ) ?> class="checkbox" /> <label for="<?php echo $this->get_field_id( 'calendars' ); ?>"><?php echo $calendar->name ?></label>
	            <?php endforeach; ?>
			</p>
        	<?php 
        else :
        
            _e( 'You have to create a calendar before you can use this widget.', 'ftcalendar' );
		
		endif;
	
	}
	
}

/**
 * Register our widgets with WP
 *
 * @since 0.3
 */
function register_ftc_widgets() {
	
	register_widget( 'FT_CAL_Event_List' );
	register_widget( 'FT_CAL_Thumb_Calendar' );

}
add_action( 'widgets_init', 'register_ftc_widgets' );

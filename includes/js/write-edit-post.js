jQuery(document).ready(function($) {
	$( 'input#event_date_start' ).datepicker({
		beforeShow : function(input, inst) {
			// days since epoch, used for calculating difference from start date and end date
			cur_start_date = parseInt( $.datepicker.formatDate( '@', $( 'input#event_date_start' ).datepicker( 'getDate' ) ) / 86400000 );
		},
		onSelect : function(dateText, inst) {
			// if the event_date_end has a valid date, we want to update it whenever we change the start date
			if ( $( 'input#event_date_end' ).datepicker( 'getDate' ) ) {
				// days since epoch
				new_start_date = parseInt( $.datepicker.formatDate( '@', $(this).datepicker( 'getDate' ) ) / 86400000 );
				diff = new_start_date - cur_start_date;
				
				// If we move the start date, we want to move the end date that many days ahead...
				new_end_date = new Date( $( 'input#event_date_end' ).datepicker( 'getDate' ) );
				new_end_date.setDate( new_end_date.getDate() + diff );
				
				$( 'input#event_date_end' ).datepicker( 'setDate', new_end_date );
			} else {
				$( 'input#event_date_end' ).val( dateText );
			}
			
			//Want to change the Starts on Range whenever we change the First event date (just cause)
			$( 'input#range_start' ).val( dateText );
			set_repeating_displays( $( 'select#repeats_select' ).val() );
		}
	});
	
	$( 'input#event_date_end' ).datepicker({
		onSelect : function(dateText, inst) {
			new_end_date = new Date( $(this).datepicker( 'getDate' ) );
			cur_start_date = new Date( $( 'input#event_date_start' ).datepicker( 'getDate' ) );
			
			// If we set the end date to before the start date, let's reset the start date to the end date...
			if ( new_end_date < cur_start_date ) {
				$( 'input#event_date_start' ).datepicker( 'setDate', new_end_date );
			}
		}
	});
	
	$( 'input#range_end' ).datepicker({
		beforeShow : function(input, inst) {
			$( 'input#range_end_type_until' ).attr('checked', true);
		},
		onSelect : function(dateText, inst) {
			set_repeating_displays( $( 'select#repeats_select' ).val() );
		}
	});
	
	$( 'input#range_end_type_until' ).focusin( function() {
		$( 'input#range_end' ).focus();
	});
	
	$( 'input#range_end_type_never' ).focusin( function() {
		$( 'input#range_end' ).val( null );
	});
	
	//http://labs.perifer.se/timedatepicker/
	$( 'input#event_time_start, input#event_time_end' ).timePicker({ 
		show24Hours: false,
		step: 15 
	});
	
	// Validate.
	$( 'input#event_time_start, input#event_date_start, input#event_time_end, input#event_date_end' ).change(function() {
		if ( $( 'input#event_date_start' ).datepicker( 'getDate' ) >= $( 'input#event_date_end' ).datepicker( 'getDate' ) ) {
			if( $.timePicker( 'input#event_time_start' ).getTime() > $.timePicker( 'input#event_time_end' ).getTime() ) {
				$( 'input#event_time_end' ).addClass( "ft_cal_error" );
			} else {
				$( 'input#event_time_end' ).removeClass( "ft_cal_error" );
			}
			
			$( 'input#event_date_start' ).removeClass( "ft_cal_error" );
		} else if ( $( 'input#event_date_start' ).datepicker( 'getDate' ) > $( 'input#event_date_end' ).datepicker( 'getDate' ) ) {
			$( 'input#event_date_start' ).addClass( "ft_cal_error" );	
			$( 'input#event_time_end' ).addClass( "ft_cal_error" );	
		} else {
			$( 'input#event_date_start' ).removeClass( "ft_cal_error" );	
			$( 'input#event_time_end' ).removeClass( "ft_cal_error" );
		}
	});
	
	// Toggle time fields with allday changes
	$( 'input#ft_cal_event_all_day' ).change( function(){
		$( 'input#event_time_start, input#event_time_end' ).toggle();
	});
	
	// Toggle recurring field options
	$( 'input#ft_cal_event_repeats' ).change( function(){
		$( 'div#event_recurring_field_options' ).toggle();
		set_repeating_displays( $( 'select#repeats_select' ).val() );
	});
	
	$( 'select#repeats_select, select#repeats_every_select, .repeats_on, .repeats_by' ).change( function() {
		set_repeating_displays( $( 'select#repeats_select' ).val() );
	});
	
	// this function adjusts displays and label as needed based on repeat combinations
	function set_repeating_displays( repeats_select ) {
		// Always start by showing/hiding the following
		$( '#repeats_label, #repeats_every, #starts_on, #ends_on' ).show();
		$( '.repeats_label_item, #repeats_on, #repeats_weekly_on, #repeats_by' ).hide();
		
		if ( null == repeats_select )
			repeats_select = 'daily';
			
		repeats_every = $('select#repeats_every_select').val();
		date_until = $( 'input#range_end' ).val();
		
		if ( '' != date_until ) {
			$( '.date_until' ).text(', until ' + $.datepicker.formatDate( 'MM dd, yy', $( 'input#range_end' ).datepicker( 'getDate' ) ) ).show()
		} else {
			$( '.date_until' ).hide();
		}
		
		// Show / Display fields
		switch( repeats_select ) {
			case 'daily' :
				// Set repeats label
				if ( repeats_every == 1 ) {
					$( '#repeats_daily_label' ).text('Daily').show()
					$( '#repeats_every_label' ).text('day').show()
				} else {
					$( '#repeats_daily_label' ).text( 'Every ' + repeats_every + ' days' ).show();
					$( '#repeats_every_label' ).text('days').show()
				}
				$( '#repeats_daily_p, #repeats_every, #repeats_range' ).show();
				break;
			case 'weekdays' :
				$( '#repeats_weekdays_p, #repeats_range' ).show();
				$( '#repeats_every' ).hide();
				break;
			case 'mwf' :
				$( '#repeats_mwf_p, #repeats_range' ).show();
				$( '#repeats_every' ).hide();
				break;
			case 'tt' :
				$( '#repeats_tt_p, #repeats_range' ).show();
				$( '#repeats_every' ).hide();
				break;
			case 'weekly' :
				// Set repeats label
				if ( repeats_every == 1 ) {
					$( '#repeats_weekly_label' ).text('Weekly').show()
					$( '#repeats_every_label' ).text('week').show()
				} else {
					$( '#repeats_weekly_label' ).text( 'Every ' + repeats_every + ' weeks' ).show();
					$( '#repeats_every_label' ).text( 'weeks' ).show();
				}
				
				checked = new Array();
				$( '.repeats_on' ).each( function() {
					if ( true == $( this ).attr( 'checked' ) ) {
						var str = $( this ).val();
						str = str.charAt(0).toUpperCase() + str.slice(1);
						checked[ checked.length ] = str;
					}
				});
				
				if ( 7 <= checked.length ) {
					$( '#repeats_weekly_on' ).text( ' every day' );
				} else {
					$( '#repeats_weekly_on' ).text( ' on ' + checked.join( ', ' ) );
				}
				
				// Show fields
				$( '#repeats_weekly_p, #repeats_every, #repeats_on, #repeats_weekly_on, #repeats_range' ).show();
				break;
			case 'monthly' :
				// Set repeats label
				var start_date 	= new Date( $( '#event_date_start' ).val() );
				var repeats_by	= $( '.repeats_by:checked' ).val();
				var daynumber 	= start_date.getDate();

				if ( 1 == repeats_by ) // 0 = day of the month, 1 = day of the week
					var label_out = get_nth_weekday_of_month( start_date );
				else
					var label_out = 'on day ' + daynumber;
									
				if ( repeats_every == 1 ) {
					$( '#repeats_monthly_label' ).text('Monthly ' + label_out ).show()		
					$( '#repeats_every_label' ).text( 'month' ).show();
				} else {
					$( '#repeats_monthly_label' ).text( 'Every ' + repeats_every + ' months ' + label_out ).show();		
					$( '#repeats_every_label' ).text( 'months' ).show();
				}
				
				// Show fields
				$( '#repeats_monthly_p, #repeats_every, #repeats_by, #repeats_range' ).show();
				break;
			case 'yearly' :
				// Set repeats label
				var start_date  = $.datepicker.formatDate( 'MM dd', $( '#event_date_start' ).datepicker( 'getDate' ) );
			
				if ( repeats_every == 1 ) {
					$( '#repeats_yearly_label' ).text('Annually on ' + start_date ).show();
					$( '#repeats_every_label' ).text( 'year' ).show();
				} else {
					$( '#repeats_yearly_label' ).text( 'Every ' + repeats_every + ' years on ' + start_date ).show();
					$( '#repeats_every_label' ).text( 'years' ).show();
				}

				// Show fields
				$( '#repeats_label, #repeats_yearly_p, #repeats_every, #repeats_range' ).show();
				break;
			default :
				$( '#repeats_label, #repeats_daily_p, #repeats_every, #starts_on, #ends_on' ).show();
				$( '#repeats_weekdays_p, #repeats_mwf_p, #repeats_tt_p, #repeats_weekly_p, #repeats_monthly_p, #repeats_yearly_p, #repeats_range, #repeats_on, #repeats_by' ).hide();
		}
	}
	
	// Returns the string for the nth dayoftheweek of every month
	function get_nth_weekday_of_month( date ) {
		var englishdays = new Array();
			englishdays[1] = 'Sunday';
			englishdays[2] = 'Monday';
			englishdays[3] = 'Tuesday';
			englishdays[4] = 'Wednesday';
			englishdays[5] = 'Thursday';
			englishdays[6] = 'Friday';
			englishdays[7] = 'Saturday';
		
		var englishnumber = new Array();
			englishnumber[0] = 'first';
			englishnumber[1] = 'second';
			englishnumber[2] = 'third';
			englishnumber[3] = 'fourth';
			englishnumber[4] = 'fifth';

		var currentdate	= date.getDate(); 		// Returns the day of the month (from 1-31)
		var currentday	= date.getDay() + 1; 	// Returns the day of the week (from 0-6)
		var firstday	= new Date( date.getFullYear(), date.getMonth(), 1 ).getDay() + 1;
		
		for ( i = 0; i <= 5; i++ ) { // 6 possible weeks in a month (if it starts on Fri/Sat and is 30/31 days)
			if ( ( currentday + ( 7 * i ) ) >= currentdate ) {
				if ( currentday < firstday ) {
					i--; // If first day of week is greater than the current day of week, we need to substract a week
				}
				
				return ' on the ' + englishnumber[i] + ' ' + englishdays[currentday] + ' of the month';
			}
		}
		
		return ' unable to determine date.';
	}
	
	$( 'input#ft_cal_clear_event' ).click( function() {
		$( 'input#ft_cal_event_all_day' ).attr('checked', false);
		$( 'input#ft_cal_event_repeats' ).attr('checked', false);
		$( 'input#range_end_type_never' ).attr('checked', true);
		$( 'input#range_end' ).val( '' );
		$( 'input#event_time_start, input#event_time_end' ).show();
		$( 'div#event_recurring_field_options' ).hide();
	});
	
	$('input#ft_cal_save_event').click(function() {
		if ( $('.ft_cal_error').length ) {
			alert( 'Errors found, please correct before attempting to save.' );
			return false;
		}
												
		var repeats_on = "";
		$( '.repeats_on' ).each( function() {
			if ( true == $( this ).is(':checked') ) {
				repeats_on = repeats_on + "1";	
			} else {
				repeats_on = repeats_on + "0";	
			}
		});
		
		if( $('select#calendar option:selected').length ) {
				calendar_id = $('select#calendar option:selected').val();
		} else {
				calendar_id = $('input#calendar').val();
		}
		
		var data = {
			action: 		'save_ftcal_data',
			cal_ID:			calendar_id,
			post_ID:  		$('input#post_ID').val(),
			start_date:		$('input#event_date_start').val(),
			start_time:		$('input#event_time_start').val(),
			end_date:		$('input#event_date_end').val(),
			end_time:		$('input#event_time_end').val(),
			all_day:		$('input#ft_cal_event_all_day').is(':checked'),
			repeating:		$('input#ft_cal_event_repeats').is(':checked'),
			r_type:			$('select#repeats_select option:selected').val(),
			r_label:		$('div#repeats_label p:visible').text(),
			r_every:		$('select#repeats_every_select option:selected').val(),
			r_on:			repeats_on,
			r_by:			$('input.repeats_by:checked').val(),
			r_start_date:	$('input#range_start').val(),
			r_end:			$('input.range_end_type:checked').val(),
			r_end_date:		$('input#range_end').val(),
			_wpnonce: 		$('input#save_ftcal_data_nonce').val()
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			if ( "unsuccess" == response ){
				alert( "Error Adding New Event, Please contact support@ftcalendar.com for assistance." );
			} else {
				$( 'div#ftcal_existing' ).html( response );
			}
		});
		
		$( 'input#ft_cal_event_all_day' ).attr('checked', false);
		$( 'input#ft_cal_event_repeats' ).attr('checked', false);
		$( 'input#range_end_type_never' ).attr('checked', true);
		$( 'input#range_end' ).val( '' );
		$( 'input#event_time_start, input#event_time_end' ).show();
		$( 'div#event_recurring_field_options' ).hide();
	});
	
	$('input#ft_cal_delete_events').live('click', function() {
		if ( confirm("Are you sure you want to delete these events?") ) {
			var data = {
				action: 		'delete_ftcal_data',
				post_ID:  		$('input#post_ID').val(),
				event_ids:		$('input.delete_event:checked').serializeArray(),
				_wpnonce: 		$('input#delete_ftcal_data_nonce').val()
			};
			
			jQuery.post(ajaxurl, data, function(response) {
				$( 'div#ftcal_existing' ).html( response );
			});
			
			$( 'input.delete_event:checked' ).attr('checked', false);
		}
	});
});
jQuery(document).ready(function($) {
	$( '.thumb-event' ).live( 'click', function() {
		$( '.thumb-event-div' ).hide();
		$event = $( this ).attr('ref');
		$( '#' + $event ).show();
	});
	
	$( '.thumb-event-close' ).live( 'click', function() {
		$( '.thumb-event-div' ).hide();				  
	});
	
	$( 'a.thumb-next, a.thumb-prev' ).live( 'click', function(event) {
		event.preventDefault();
		
		var data = {
			action: 	'thumb_month_change',
			date:		$( this ).attr( 'ref' ),
			calendars: 	$( 'input#thumbcalendar-calendars' ).val(),
			tableclass: $( 'input#thumbcalendar-class' ).val(),
			width: 		$( 'input#thumbcalendar-width' ).val(),
			height: 	$( 'input#thumbcalendar-height' ).val(),
			dateformat: $( 'input#thumbcalendar-dateformat' ).val(),
			timeformat: $( 'input#thumbcalendar-timeformat' ).val()
		};
			
		jQuery.post(FTCajax.ajaxurl, data, function(response) {
			$( 'div.ftthumbcalendar' ).replaceWith( response );
		});
	});
	
	$( 'a.large-next, a.large-prev' ).live( 'click', function(event) {
		event.preventDefault();
		
		var data = {
			action: 		'large_calendar_change',
			date:			$( this ).attr( 'ref' ),
			type: 			$( 'input#largecalendar-type' ).val(),
			heading_label: 	$( 'input#largecalendar-heading_label' ).val(),
			calendars: 		$( 'input#largecalendar-calendars' ).val(),
			tableclass: 	$( 'input#largecalendar-class' ).val(),
			width: 			$( 'input#largecalendar-width' ).val(),
			height: 		$( 'input#largecalendar-height' ).val(),
			legend: 		$( 'input#largecalendar-legend' ).val(),
			types: 			$( 'input#largecalendar-types' ).val(),
			dateformat: 	$( 'input#largecalendar-dateformat' ).val(),
			timeformat: 	$( 'input#largecalendar-timeformat' ).val()
		};
			
		jQuery.post(FTCajax.ajaxurl, data, function(response) {
			$( 'div.ftlargecalendar' ).replaceWith( response );
		});
	});
});
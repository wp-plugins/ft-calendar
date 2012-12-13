jQuery(document).ready(function($) {
	$( '.thumb-event' ).live( 'click', function() {
		$event = $( this ).attr('ref');
		$( '#' + $event ).show();
	});
	
	$( '.thumb-event-close' ).live( 'click', function() {
		$( '.thumb-event-div' ).hide();				  
	});
	
	$( 'a.thumb-next, a.thumb-prev' ).live( 'click', function(event) {
		event.preventDefault();
		
		var data = {
			action: 		'thumb_month_change',
			date:			$( this ).attr( 'ref' ),
			calendars: 		$( 'input#thumbcalendar-calendars' ).val(),
			tableclass: 	$( 'input#thumbcalendar-class' ).val(),
			width: 			$( 'input#thumbcalendar-width' ).val(),
			height: 		$( 'input#thumbcalendar-height' ).val(),
			dateformat: 	$( 'input#thumbcalendar-dateformat' ).val(),
			timeformat: 	$( 'input#thumbcalendar-timeformat' ).val(),
			show_rss_feed: 	$( 'input#thumbcalendar-show_rss_feed' ).val(),
			show_ical_feed: $( 'input#thumbcalendar-show_ical_feed' ).val(),
			hide_duplicates: $( 'input#thumbcalendar-hide_duplicates' ).val()
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
			timeformat: 	$( 'input#largecalendar-timeformat' ).val(),
			show_rss_feed: 	$( 'input#largecalendar-show_rss_feed' ).val(),
			show_ical_feed: $( 'input#largecalendar-show_ical_feed' ).val(),
			hide_duplicates: $( 'input#largecalendar-hide_duplicates' ).val()
		};
			
		jQuery.post(FTCajax.ajaxurl, data, function(response) {
			$( 'div.ftlargecalendar' ).replaceWith( response );
		});
	});
	
	$( 'a.ftcal-rss-icon[title]:gt(1)').tooltip({
		tip: '.tooltip',
		position: "bottom right",
		offset: [-50, -80]
	});
	
	$( 'a.ftcal-ical-icon[title]:gt(1)').tooltip({
		tip: '.tooltip',
		position: "bottom right",
		offset: [-50, -80]
	})
});
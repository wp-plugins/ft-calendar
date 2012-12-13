jQuery(document).ready(function($) {
	// Toggle time fields with allday changes
	$( 'input#tag-name' ).live('change', function(){
		$( 'div#calendar-label-color div').text( $( this ).val() );
	});
	
	$( 'a.calcolor-square').click( function() {
		$( 'input#ftcal-color').val( $( this ).css( 'background-color' ) );
		$( 'div#calendar-label-color').css( 'background-color', $( this ).css( 'background-color' ) );
		$( 'div#calendar-label-color').css( 'border-color', $( this ).css( 'background-color' ) );
		$( 'div#calendar-label-color div').css( 'background-color', $( this ).css( 'background-color' ) );
		$( 'div#calendar-label-color div').css( 'border-color', $( this ).css( 'background-color' ) );
	});
});
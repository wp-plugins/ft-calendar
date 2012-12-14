jQuery(document).ready(function($) {
	$( 'input#smart_ordering' ).live('change', function(){
		$( 'tr#include_recurring_end' ).toggle( 'fast' );
	});
	$( 'input#show_post_schedule' ).live('change', function(){
		$( 'tr#post_sched_before_after' ).toggle( 'fast' );
	});
});
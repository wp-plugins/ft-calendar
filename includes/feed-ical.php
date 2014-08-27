<?php
/**
 * ICAL Feed Template for displaying ICAL calendar feed.
 *
 * @package FT_Calendar
 * @TODO deal with DAYLIGHT and STANDARD time calculation (cannot until we require PHP 5.2+)
 */

global $ft_cal_feeds, $ft_cal_shortcodes;

list( $cal_data_arr, $title ) = $ft_cal_feeds->get_calendar_data();

$current_offset = get_option('gmt_offset');
$tzstring = get_option('timezone_string');

if ( empty( $tzstring ) ) { // Create a UTC+- zone if no timezone string exists
	$check_zone_info = false;
	if ( 0 == $current_offset ) {
		$tzstring = 'UTC+0';
		date_default_timezone_set( "Etc/GMT+0" ); 	
	} elseif ($current_offset < 0) {
		$tzstring = 'UTC' . $current_offset;
		date_default_timezone_set( "Etc/GMT+" . abs( $current_offset ) );  // Backwards, we want to add, because we get GMT later
	} else {
		$tzstring = 'UTC+' . $current_offset;
		date_default_timezone_set( "Etc/GMT-" . abs( $current_offset ) );   // Backwards, we want to subtract, because we get GMT later
	}
} else {
	date_default_timezone_set( $tzstring ); 	
}

header( 'HTTP/1.1 200 OK', true );
header( 'Content-Type: text/calendar; charset=' . get_option('blog_charset'), true ); 
header( 'Content-Disposition: inline; filename=calendar.ics' );
header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
header( 'Last-Modified: ' . date_i18n('D, d M Y H:i:s', false, true) . ' GMT' );
header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
header( 'Pragma: no-cache' );
?>
BEGIN:VCALENDAR
PRODID:-//<?php bloginfo( 'name' ); ?>//<?php bloginfo( 'description' ); ?>//EN
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:PUBLISH
X-WR-CALNAME:<?php bloginfo_rss( 'name' ); $ft_cal_feeds->wp_title_rss( '»' ); echo "\n"; ?>
X-WR-TIMEZONE:<?php echo $tzstring . "\n"; ?>
X-ORIGINAL-URL:<?php bloginfo( 'url' ); ?><?php echo $_SERVER['REQUEST_URI'] . "\n"; ?>
<?php
if ( !empty( $cal_data_arr ) ) {
	
	foreach ( (array)$cal_data_arr as $event ) { 
			
		$post = get_post( $event->post_parent ); 
		setup_postdata( $post ); 
		$post_date_str = strtotime( $post->post_date_gmt );
		$post_modified_str = strtotime( $post->post_modified_gmt );
		?>
BEGIN:VEVENT
DTSTAMP:<?php echo date_i18n( 'Ymd\THis\Z', $post_date_str, true ) . "\n"; ?>
LAST-MODIFIED:<?php echo date_i18n( 'Ymd\THis\Z', $post_modified_str, true ) . "\n"; ?>
CREATED:<?php echo date_i18n( 'Ymd\THis\Z', $post_date_str, true ) . "\n"; ?>
<?php
if ( $event->all_day ) {
	$format = 'Ymd';
	$DTSTART = date_i18n( $format, strtotime( $event->start_datetime ), true );
	$DTEND = date_i18n( $format, strtotime( $event->end_datetime ) + 86400, true );
	
	//$datetype = "DATE";
	echo "DTSTART;VALUE=DATE:" . $DTSTART . "\n";
	echo "DTEND;VALUE=DATE:" . $DTEND . "\n";
} else {
	$format = 'Ymd\THis\Z';
	
	$DTSTART = date_i18n( $format, strtotime( $event->start_datetime ), true );
	$DTEND = date_i18n( $format, strtotime( $event->end_datetime ), true );
	
	//$datetype = "DATE-TIME";
	echo "DTSTART;VALUE=DATE-TIME:" . $DTSTART . "\n";
	echo "DTEND;VALUE=DATE-TIME:" . $DTEND . "\n";
}

if ( 1 == $event->repeating ) {
	$days_of_week = array( 'SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA' );
			
	switch( $event->r_type ) {
		case 'daily' :
			$freq = 'FREQ=DAILY;';
			$interval = 'INTERVAL=' . $event->r_every . ';';
			$byday = '';
			break;
		case 'weekdays' :
			$freq = 'FREQ=WEEKLY;';
			$interval = 'INTERVAL=1;';
			$byday = 'BYDAY=MO,TU,WE,TH,FR;';
			break;
		case 'mwf' :
			$freq = 'FREQ=WEEKLY;';
			$interval = 'INTERVAL=1;';
			$byday = 'BYDAY=MO,WE,FR;';
			break;
		case 'tt' :
			$freq = 'FREQ=WEEKLY;';
			$interval = 'INTERVAL=1;';
			$byday = 'BYDAY=TU,TH;';
			break;
		case 'weekly' :
			$freq = 'FREQ=WEEKLY;';
			$interval = 'INTERVAL=' . $event->r_every . ';';
			$dow = array();
			for ( $x = 0; $x < 7; $x++) {
				if ( 1 == substr( $event->r_on, $x, 1 ) ) {
					$dow[] = $days_of_week[$x];
				}
			}
			$byday = 'BYDAY=' . implode( ',', $dow ) . ';';
			break;
		case 'monthly' :
			$freq = 'FREQ=MONTHLY;';
			$interval = 'INTERVAL=' . $event->r_every . ';';
			if ( 1 == $event->r_by ) {
				$day = date_i18n( 'w', strtotime( $event->r_start_datetime ) );
				$byday = 'BYDAY=' . $ft_cal_shortcodes->get_nth_weekday_of_month( strtotime( $event->r_start_datetime ) ) . $days_of_week[$day] . ';';
			} else {
				$byday = '';
			}
			break;
		case 'yearly' :
			$interval = 'INTERVAL=' . $event->r_every . ';';
			$freq = 'FREQ=YEARLY;';
			$byday = '';
			break;
		default :
			$freq = '';
			$interval = '';
			$byday = '';
			break;	
	}
	$rend = ( 1 == $event->r_end ) ? 'UNTIL=' . date_i18n( $format, strtotime( $event->r_end_datetime ), true ) . ';' : '';
	echo rtrim( 'RRULE:' . $freq . $rend . $interval . $byday, ';' ) . "\n";
}
?>
SUMMARY:<?php echo get_the_title(); echo "\n"; ?>
URL;VALUE=URI:<?php the_permalink_rss(); echo "\n"; ?>
UID:<?php echo $DTSTART . $DTEND . "-" . $post->ID . "-"; the_permalink_rss(); echo "\n"; ?>
DESCRIPTION:<?php the_excerpt_rss(); echo "\n"; ?>
TRANSP:TRANSPARENT
CLASS:PUBLIC
STATUS:CONFIRMED
END:VEVENT
<?php
	}
	
} ?>
END:VCALENDAR
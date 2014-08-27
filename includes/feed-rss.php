<?php
/**
 * RSS 0.92 Feed Template for displaying RSS 0.92 Posts feed.
 *
 * @package WordPress
 */

global $ft_cal_feeds, $ft_cal_options, $ft_cal_events;

remove_filter( 'the_content', array( &$ft_cal_events, 'get_post_schedule' ) );
remove_filter( 'the_excerpt', array( &$ft_cal_events, 'get_post_schedule' ) );

list( $cal_data_arr, $title ) = $ft_cal_feeds->get_calendar_data();

header( 'HTTP/1.1 200 OK', true );
header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<rss version="0.92">
<channel>
	<title><?php bloginfo_rss('name'); $ft_cal_feeds->wp_title_rss(); echo $title; ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<docs>http://backend.userland.com/rss092</docs>
	<language><?php echo get_option('rss_language'); ?></language>
	<?php do_action('rss_head'); ?>
    <?php 
	if ( !empty( $cal_data_arr ) ) {
		
		$limit = isset( $_GET['limit'] ) ? $_GET['limit'] : 0;
		$break = false;
		$count = 1;
		$cal_data_arr = array_reverse( $cal_data_arr, true ); //XML Feeds by default show the oldest last, newest first
		$unique_events = array();
		
		foreach ( (array)$cal_data_arr as $event ) { 
		
			if ( in_array( $event->post_parent, $unique_events ) ) {
				
				continue;
					
			}
		
			$unique_events[] = $event->post_parent;
								
			$post = get_post( $event->post_parent ); 
			setup_postdata( $post ); ?>
            
            <item>
                <title><?php the_title_rss() ?></title>
                <?php $event_details = $ft_cal_feeds->get_the_rss_event_details( $event->post_parent ); ?>
                <description><![CDATA[<?php echo $event_details; the_excerpt_rss() ?>]]></description>
                <link><?php the_permalink_rss() ?></link>
                <?php do_action('rss_item'); ?>
            </item>
            
            <?php	
			if ( 0 != $limit && ++$count > $limit ) {
				
				$break = true;
				break;
				
			}
		
		}
        
	} ?>
</channel>
</rss>

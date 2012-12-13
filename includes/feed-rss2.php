<?php
/**
 * RSS2 Feed Template for displaying RSS2 calendar feed.
 *
 * @package FT_Calendar
 */

global $ft_cal_feeds, $ft_cal_options, $ft_cal_events;

remove_filter( 'the_content', array( &$ft_cal_events, 'get_post_schedule' ) );
remove_filter( 'the_excerpt', array( &$ft_cal_events, 'get_post_schedule' ) );

list( $cal_data_arr, $title ) = $ft_cal_feeds->get_calendar_data();

header( 'HTTP/1.1 200 OK', true );
header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action('rss2_ns'); ?>
>

<channel>
	<title><?php bloginfo_rss('name'); $ft_cal_feeds->wp_title_rss(); echo $title; ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language><?php echo get_option('rss_language'); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>
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
				<link><?php the_permalink_rss() ?></link>
				<comments><?php comments_link_feed(); ?></comments>
				<?php if ( $ft_cal_options->calendar_options['use_event_date_as_pubdate'] ) { ?>
				<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $event->start_datetime, false); ?></pubDate>
				<?php } else { ?>
				<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
				<?php } ?>
				<dc:creator><?php the_author() ?></dc:creator>
				<?php the_category_rss('rss2') ?>
				<?php $ft_cal_feeds->the_category_rss( 'rss2', $event->calendar_id ) ?>
				<?php $event_details = $ft_cal_feeds->get_the_rss_event_details( $event->post_parent ); ?>
				<guid isPermaLink="false"><?php the_guid(); ?></guid>
				<?php if (get_option('rss_use_excerpt')) : ?>
                    <description><![CDATA[<?php echo $event_details; the_excerpt_rss() ?>]]></description>
            	<?php else : ?>
                    <description><![CDATA[<?php echo $event_details; the_excerpt_rss() ?>]]></description>
					<?php if ( strlen( $post->post_content ) > 0 ) : ?>
                        <content:encoded><![CDATA[<?php echo $event_details; the_content_feed('rss2') ?>]]></content:encoded>
                    <?php else : ?>
                        <content:encoded><![CDATA[<?php echo $event_details; the_excerpt_rss() ?>]]></content:encoded>
                    <?php endif; ?>
            	<?php endif; ?>
                <wfw:commentRss><?php echo esc_url( get_post_comments_feed_link( null, 'rss2' ) ); ?></wfw:commentRss>
                <slash:comments><?php echo get_comments_number(); ?></slash:comments>
            <?php rss_enclosure(); ?>
			<?php do_action('rss2_item'); ?>
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

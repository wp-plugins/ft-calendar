<?php
/**
 * RSS 1 RDF Feed Template for displaying RSS 1 Posts feed.
 *
 * @package WordPress
 */

global $ft_cal_feeds, $ft_cal_options, $ft_cal_events;

remove_filter( 'the_content', array( &$ft_cal_events, 'get_post_schedule' ) );
remove_filter( 'the_excerpt', array( &$ft_cal_events, 'get_post_schedule' ) );

list( $cal_data_arr, $title ) = $ft_cal_feeds->get_calendar_data();

header( 'HTTP/1.1 200 OK', true );
header('Content-Type: ' . feed_content_type('rdf') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<rdf:RDF
	xmlns="http://purl.org/rss/1.0/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	<?php do_action('rdf_ns'); ?>
>
<channel rdf:about="<?php bloginfo_rss("url") ?>">
	<title><?php bloginfo_rss('name'); $ft_cal_feeds->wp_title_rss(); echo $title; ?></title>
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss('description') ?></description>
	<dc:date><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT'), false); ?></dc:date>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<sy:updateBase>2000-01-01T12:00+00:00</sy:updateBase>
	<?php do_action('rdf_header'); ?>
	<items>
		<rdf:Seq>
		<?php 
		$unique_events = array();
		foreach( (array)$cal_data_arr as $event ) { 
		
			if ( in_array( $event->post_parent, $unique_events ) ) {
				
				continue;
					
			}
		
			$unique_events[] = $event->post_parent;
			$post = get_post( $event->post_parent ); 
			setup_postdata( $post ); ?>
			<rdf:li rdf:resource="<?php the_permalink_rss() ?>"/>
		<?php } ?>
		</rdf:Seq>
	</items>
</channel>
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
            
            <item rdf:about="<?php the_permalink_rss() ?>">
                <title><?php the_title_rss() ?></title>
                <link><?php the_permalink_rss() ?></link>
                <?php if ( $ft_cal_options->calendar_options['use_event_date_as_pubdate'] ) { ?>
                <dc:date><?php echo mysql2date('Y-m-d\TH:i:s\Z', $event->start_datetime, false); ?></dc:date>
                <?php } else { ?>
                <dc:date><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_post_time('Y-m-d H:i:s', true), false); ?></dc:date>
                <?php } ?>
                <dc:creator><?php the_author() ?></dc:creator>
                <?php the_category_rss('rdf') ?>
                <?php $ft_cal_feeds->the_category_rss( 'rdf', $event->calendar_id ) ?>
                <?php $event_details = $ft_cal_feeds->get_the_rss_event_details( $event->post_parent ); ?>
            <?php if (get_option('rss_use_excerpt')) : ?>
                <description><?php echo $event_details; the_excerpt_rss() ?></description>
            <?php else : ?>
                <description><?php echo $event_details; the_excerpt_rss() ?></description>
                <content:encoded><![CDATA[<?php echo $event_details; the_content_feed('rdf') ?>]]></content:encoded>
            <?php endif; ?>
                <?php do_action('rdf_item'); ?>
            </item>
            
            <?php	
			if ( 0 != $limit && ++$count > $limit ) {
				
				$break = true;
				break;
				
			}
		
		}
        
	} ?>
</rdf:RDF>

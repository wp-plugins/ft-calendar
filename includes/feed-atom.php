<?php
/**
 * Atom Feed Template for displaying Atom Posts feed.
 *
 * @package WordPress
 */

global $ft_cal_feeds, $ft_cal_options, $ft_cal_events;

remove_filter( 'the_content', array( &$ft_cal_events, 'get_post_schedule' ) );
remove_filter( 'the_excerpt', array( &$ft_cal_events, 'get_post_schedule' ) );

list( $cal_data_arr, $title ) = $ft_cal_feeds->get_calendar_data();

header( 'HTTP/1.1 200 OK', true );
header('Content-Type: ' . feed_content_type('atom') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>
<feed
  xmlns="http://www.w3.org/2005/Atom"
  xmlns:thr="http://purl.org/syndication/thread/1.0"
  xml:lang="<?php echo get_option('rss_language'); ?>"
  xml:base="<?php bloginfo_rss('url') ?>/wp-atom.php"
  <?php do_action('atom_ns'); ?>
 >
	<title type="text"><?php bloginfo_rss('name'); $ft_cal_feeds->wp_title_rss(); echo $title; ?></title>
	<subtitle type="text"><?php bloginfo_rss("description") ?></subtitle>

	<updated><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_lastpostmodified('GMT'), false); ?></updated>

	<link rel="alternate" type="text/html" href="<?php bloginfo_rss('url') ?>" />
	<id><?php bloginfo('atom_url'); ?></id>
	<link rel="self" type="application/atom+xml" href="<?php self_link(); ?>" />

	<?php do_action('atom_head'); ?>
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
            
            <entry>
                <author>
                    <name><?php the_author() ?></name>
                    <?php $author_url = get_the_author_meta('url'); if ( !empty($author_url) ) : ?>
                    <uri><?php the_author_meta('url')?></uri>
                    <?php endif; ?>
                </author>
                <title type="<?php html_type_rss(); ?>"><![CDATA[<?php the_title_rss() ?>]]></title>
                <link rel="alternate" type="text/html" href="<?php the_permalink_rss() ?>" />
                <id><?php the_guid() ; ?></id>
                <updated><?php echo get_post_modified_time('Y-m-d\TH:i:s\Z', true); ?></updated>
                <?php if ( $ft_cal_options->calendar_options['use_event_date_as_pubdate'] ) { ?>
                <published><?php echo mysql2date('Y-m-d\TH:i:s\Z', $event->start_datetime, false); ?></published>
                <?php } else { ?>
                <published><?php echo mysql2date('Y-m-d\TH:i:s\Z', get_post_time('Y-m-d H:i:s', true), false); ?></published>
                <?php } ?>
                <?php the_category_rss('atom') ?>
                <?php $ft_cal_feeds->the_category_rss( 'atom', $event->calendar_id ) ?>
                <?php $event_details = $ft_cal_feeds->get_the_rss_event_details( $event->post_parent ); ?>
                <summary type="<?php html_type_rss(); ?>"><![CDATA[<?php echo $event_details; the_excerpt_rss(); ?>]]></summary>
        	<?php if ( !get_option('rss_use_excerpt') ) : ?>
                <content type="<?php html_type_rss(); ?>" xml:base="<?php the_permalink_rss() ?>"><![CDATA[<?php echo $event_details; the_content_feed('atom') ?>]]></content>
			<?php endif; ?>
            <?php atom_enclosure(); ?>
            <?php do_action('atom_entry'); ?>
                <link rel="replies" type="text/html" href="<?php the_permalink_rss() ?>#comments" thr:count="<?php echo get_comments_number()?>"/>
                <link rel="replies" type="application/atom+xml" href="<?php echo get_post_comments_feed_link(0,'atom') ?>" thr:count="<?php echo get_comments_number()?>"/>
                <thr:total><?php echo get_comments_number()?></thr:total>
            </entry>
            
            <?php
			if ( 0 != $limit && ++$count > $limit ) {
				
				$break = true;
				break;
				
			}
		
		}
        
	} ?>
</feed>

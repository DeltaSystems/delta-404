<?php
/**
 * @package Delta_404
 * @version 1.0
 */
/*
Plugin Name: Delta 404
Plugin URI: https://deltasystemsgroup.com
Description: A simple plugin to get 404 data out of the Redirection plugin.
Author: Delta Systems Group
Version: 1.0
*/

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );

function add_action_links ( $links ) {
	$additionalLinks = array(
	'<a href="/?delta404&token=' . delta404GetToken() . '">404 RSS Feed</a>',
	);
	return array_merge( $links, $additionalLinks );
}

add_action( 'init', 'delta404' );

function delta404() {
    if (isset($_GET['delta404'])) {

        if (isset($_GET['token']) && delta404GetToken() === $_GET['token']) {
            global $wpdb;

            $items  = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}redirection_404" );

            header( 'Content-type: text/xml; charset='.get_option( 'blog_charset' ), true );
            echo '<?xml version="1.0" encoding="'.get_option( 'blog_charset' ).'"?'.">\r\n";
            ?>
            <rss version="2.0"
                 xmlns:content="http://purl.org/rss/1.0/modules/content/"
                 xmlns:wfw="http://wellformedweb.org/CommentAPI/"
                 xmlns:dc="http://purl.org/dc/elements/1.1/">
                <items>
                    <?php foreach ( (array) $items as $item ) : ?>
                        <item>
                            <id><?php echo esc_html( $item->id ); ?></id>
                            <created><?php echo date( 'D, d M Y H:i:s +0000', $item->created ); ?></created>
                            <url><?php echo esc_html( $item->url ); ?></url>
                            <agent><?php echo esc_html( $item->agent ); ?></agent>
                            <referrer><?php echo esc_html( $item->referrer ); ?></referrer>
                            <ip><?php echo esc_html( $item->ip ); ?></ip>
                        </item>
                    <?php endforeach; ?>
                </items>
            </rss>
            <?php
            die;
        } else {
            header('HTTP/1.0 403 Forbidden');
            die;
        }

    }
}

function delta404GetToken() {
    $options = get_option( 'redirection_options' );
    
    return $options['token'];
}

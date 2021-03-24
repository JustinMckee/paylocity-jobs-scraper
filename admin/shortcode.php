<?php

$plugin_dir = WP_PLUGIN_DIR . '/paylocity-jobs-scraper';

if ( is_dir( $plugin_dir ) ) {
    // plugin directory found!
    add_shortcode( 'myshortcode', 'my_handle_shortcode' );

    function my_handle_shortcode() {
      wp_enqueue_script( 'paylocity_jobs', $plugin_dir.'/assets/dist/main.js' );
    }
}

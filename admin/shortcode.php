<?php
// Resources
add_action( 'init', 'pjs_register_resources' );
function pjs_register_resources(){
  wp_register_script( 'paylocity-jobs', plugin_dir_url( __DIR__ ) . 'dist/scripts/main.js', array(), filemtime( plugin_dir_path( __DIR__ ) . 'dist/scripts/main.js'), true );
}

add_action( 'init', 'pjs_add_custom_shortcode' );
function pjs_add_custom_shortcode() {
  // plugin directory found!
  add_shortcode( 'paylocityjobs', 'pjs_shortcode' );

  function pjs_shortcode() {
    wp_enqueue_script( 'paylocity-jobs' );
    return '<section class="pjs_container"></section>';
  }
}

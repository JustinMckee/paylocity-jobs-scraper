<?php
/*
Plugin Name: Paylocity Jobs Scraper
Plugin URI: https://github.com/JustinMckee/paylocity-jobs-scraper/
Description: Scrapes job data hourly from Paylocity and saves it as JSON in uploads directory.
Version: 1.0
Author: Justin McKee
Author URI: https://github.com/JustinMckee
License: MIT
*/

register_activation_hook( __FILE__, 'paylocity_recruiting_activation' );
function paylocity_recruiting_activation() {
    $uploads = wp_upload_dir();
    $args = array(
      'scrape_url'  => 'https://recruiting.paylocity.com/recruiting/jobs/All/{YOUR_ID}',
      'write_file'   => 'jobs.json',
      'write_dir'   => $uploads['basedir'] .'/'. 'paylocity',
     );

      if (! is_dir($args['write_dir'])) {
         wp_mkdir_p($args['write_dir']);
      }

      $data_file = $args['write_dir'].'/'.$args['write_file'];
      if ( !is_file($data_file ) ) {
        $fp = fopen($data_file, 'w');
        fwrite($fp, '// this file intentionally left blank until data is scraped');
        fclose($fp);
        //scrape_jobs_hourly( $args );
      }

      if (! wp_next_scheduled ( 'my_hourly_event', $args )) {
          //scrape_jobs_hourly();
          wp_schedule_event( time(), 'hourly', 'my_hourly_event' );
      }
}

add_action( 'my_hourly_event', 'scrape_jobs_hourly', 10 );
function scrape_jobs_hourly() {
    // do something every hour
    $uploads = wp_upload_dir();
    $args = array(
      'scrape_url'  => 'https://recruiting.paylocity.com/recruiting/jobs/All/{YOUR_ID}',
      'write_file'   => 'jobs.json',
      'write_dir'   => $uploads['basedir'] .'/'. 'paylocity',
     );
    $data_file = $args['write_dir'].'/'.$args['write_file'];

    $data = url_get_contents($args['scrape_url']);
    $doc = new DOMDocument();
    $doc->loadHTML($data);

    $xpath = new DOMXPath($doc);

    $script = $xpath->evaluate("string(//script[contains(text(), 'window.pageData')]/text())");
    if ( preg_match("/(?=\{((?:[^{}]++|\{(?1)\})++)\})/", $script, $matches) ) {
        $data_file = $args['write_dir'].'/'.$args['write_file'];
        $json = json_decode('{' . $matches[1] . '}');
        $fp = fopen($data_file, 'w');
        fwrite($fp, json_encode($json->Jobs));
        fclose($fp);
    }

}

register_deactivation_hook( __FILE__, 'paylocity_recruiting_deactivation' );
function paylocity_recruiting_deactivation() {
  $args = array(
    'scrape_url'  => 'https://recruiting.paylocity.com/recruiting/jobs/All/{YOUR_ID}',
    'write_file'   => 'jobs.json',
    'write_dir'   => $uploads['basedir'] .'/'. 'paylocity',
   );
    wp_clear_scheduled_hook( 'my_hourly_event' );

    // Removes directory recursively

    global $wp_filesystem;
    require_once ( ABSPATH . '/wp-admin/includes/file.php' );
    WP_Filesystem();
    $uploads = wp_upload_dir();
    $wp_filesystem->rmdir($uploads['basedir'] .'/'. 'paylocity', true);
}

function url_get_contents($url, $useragent='cURL', $headers=false, $follow_redirects=true, $debug=false) {

    // initialise the CURL library
    $ch = curl_init();

    // specify the URL to be retrieved
    curl_setopt($ch, CURLOPT_URL,$url);

    // we want to get the contents of the URL and store it in a variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

    // specify the useragent: this is a required courtesy to site owners
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

    // ignore SSL errors
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // return headers as requested
    if ($headers==true){
        curl_setopt($ch, CURLOPT_HEADER,1);
    }

    // only return headers
    if ($headers=='headers only') {
        curl_setopt($ch, CURLOPT_NOBODY ,1);
    }

    // follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
    if ($follow_redirects==true) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    }

    // if debugging, return an array with CURL's debug info and the URL contents
    if ($debug==true) {
        $result['contents']=curl_exec($ch);
        $result['info']=curl_getinfo($ch);
    }

    // otherwise just return the contents as a variable
    else $result=curl_exec($ch);

    // free resources
    curl_close($ch);

    // send back the data
    return $result;
}

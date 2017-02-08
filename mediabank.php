<?php

/**
 * @package Mediabank
 */

/*
Plugin Name: Mediabank
Description: This plugin can be used to bootstrap the Picturae Mediabank inside a Wordpress website.
Version: 1.1
Author: Picturae
Author URI: https://picturae.com
License: GPLv2 or later
Text Domain: mediabank
*/

/**
 * Initialize the plugin.
 */
add_filter( 'request', 'alter_the_query' );
function alter_the_query( $request ) {
    $dummy_query = new WP_Query();  // the query isn't run if we don't pass any query vars
    $dummy_query->parse_query( $request );    

    $mediabank_pars = [ 
      'mode',
      'view'  ,
      'q',
      'rows',
      'page',
      'fq',
      'sort',
      'switch',
      'media',
      'record',
      'focus',
      'oldView',
      ];

      foreach($mediabank_pars as $par){
          unset($request[$par]);
      }
    
      return $request;
}

/**
 * Load the CMB2 library for metaboxes
 **/
if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

/**
 * Import the mediabank metabox
 **/
require_once(dirname(__FILE__) . '/inc/mediabankmetabox.php');

/**
 * Import the mediabank admin page
 **/
require_once(dirname(__FILE__) . '/inc/mediabankadmin.php');

/**
 * Import the mediabank admin page
 **/
require_once(dirname(__FILE__) . '/inc/mediabank.php');




// Setup the mediabank metabox
MediabankMetabox::setup();
MediabankAdmin::setup();

add_action( 'init', 'template_hooks', 1, 0 );
function template_hooks(){

    /* load page theme */
    add_filter( 'template', function(){

      // Setup the mediabank properties plugin
      Mediabank::setup();

    });

}

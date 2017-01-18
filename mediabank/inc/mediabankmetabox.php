<?php
/**
 * This is the Lets Build IT! 2016 template
 * This theme makes use of a module setup to include custom functionality.
 *
 * @package WordPress
 * @subpackage Picturae Mediabank
 * @since Picturae Mediabank 1.0
 */


// Page Mediabank metabox
class MediabankMetabox {

    static $name = 'page';

    /**
     * Setup the Mediabank metabox
     */
  	public static function setup() {
        add_action( 'cmb2_admin_init', [__CLASS__, 'create_meta_boxes']);
  	}


    /**
     * Create the metabox wit the CMB2 library
     */
    public static function create_meta_boxes()
    {

	        $cmb = new_cmb2_box( array(
	            'id'            => 'metabox_'.self::$name,
	            'title'         => __( 'Mediabank', 'cmb2' ),
	            'object_types'  => array(self::$name, ), // Post type
	            // 'show_on_cb' => 'yourprefix_show_if_front_page', // function should return a bool value
	             #'context'    => 'normal',
	            // 'priority'   => 'high',
	            // 'show_names' => true, // Show field names on the left
	            // 'cmb_styles' => false, // false to disable the CMB stylesheet
	            // 'closed'     => true, // true to keep the metabox closed by default
	        ));

          $cmb->add_field( array(
          	'name' => __( 'Display Mediabank', 'cmb2' ),
          	'desc' => __( '', 'cmb2' ),
          	'id'   => 'display_mediabank',
          	'type' => 'checkbox',
          ));

    }

}

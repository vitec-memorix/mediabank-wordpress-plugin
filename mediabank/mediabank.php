<?php
/**
 * @package Mediabank
 */
/*
Plugin Name: Mediabank
Description: This plugin can be used to bootstrap the Picturae Mediabank inside a Wordpress website.
Version: 1.0
Author: Picturae
Author URI: https://picturae.com
License: GPLv2 or later
Text Domain: mediabank
*/


/**
 * Initialize the plugin.
 */


$mediabank_settings = array();
add_action('init', 'mediabank_init');
add_filter('plugin_action_links', 'plugin_action_links');

function mediabank_init()
{
    add_shortcode('mediabank', 'insert_mediabank');
    wp_deregister_script('jquery');
    wp_register_script('jquery', '//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js');
    wp_enqueue_script('jquery');

    global $mediabank_settings;
    $mediabank_settings = array(
        'api_url' => 'https://webservices.picturae.com/mediabank/',
        'api_key' => false,
        'entities' => 0,
        'endless_scroll' => '',
        'sorting' => '',
        'watermark_url' => '',
        'search_help_url' => '',
        'gallery_modes' => array(
            'geo' => array(
                'label' => __('Map', 'mediabank'),
                'default' => 1),
            'horizontal' => array(
                'label' => __('Horizontal', 'mediabank'),
                'default' => 1),
            'gallery' => array(
                'label' => __('Gallery', 'mediabank'),
                'default' => 1),
            'vertical' => array(
                'label' => __('Vertical', 'mediabank'),
                'default' => 1),
            'list' => array(
                'label' => __('List', 'mediabank'),
                'default' => 1),
            'table' => array(
                'label' => __('Table', 'mediabank'),
                'default' => 1)
        ),
        'detail_modes' => array(
            'metadata' => array(
                'label' => __('Metadata', 'mediabank'),
                'default' => 1),
            'assets' => array(
                'label' => __('Assets', 'mediabank'),
                'default' => 1),
            'comments' => array(
                'label' => __('Comments', 'mediabank'),
                'default' => 1),
            'linkeddata' => array(
                'label' => __('Associations', 'mediabank'),
                'default' => 1),
            'facebook' => array(
                'label' => __('Facebook', 'mediabank'),
                'default' => 1),
            'twitter' => array(
                'label' => __('Twitter', 'mediabank'),
                'default' => 1),
            'pinterest' => array(
                'label' => __('Pinterest', 'mediabank'),
                'default' => 1),
            'webshop' => array(
                'label' => __('Webshop', 'mediabank'),
                'default' => 1),
            'download' => array(
                'label' => __('Download', 'mediabank'),
                'default' => 1),
            'permalink' => array(
                'label' => __('Permalink', 'mediabank'),
                'default' => 1),
        ),
        'topviewer_buttons' => array(
            'zoomIn' => array(
                'label' => __('zoomIn', 'mediabank'),
                'default' => 1),
            'zoomOut' => array(
                'label' => __('zoomOut', 'mediabank'),
                'default' => 1),
            'rotatePlus90' => array(
                'label' => __('rotatePlus90', 'mediabank'),
                'default' => 1),
            'fullscreen' => array(
                'label' => __('fullscreen', 'mediabank'),
                'default' => 1),
            'paginationLeft' => array(
                'label' => __('paginationLeft', 'mediabank'),
                'default' => 1),
            'paginationRight' => array(
                'label' => __('paginationRight', 'mediabank'),
                'default' => 1),
            'zoomingSlider' => array(
                'label' => __('zoomingSlider', 'mediabank'),
                'default' => 1)
        ));


    if (!get_option('mediabank_settings_are_initialized', false)) {
        mediabank_initialize_settings();
    }
}


/**
 * Add a "settings" link where plugins are listed.
 */

function plugin_action_links($links)
{
    $links[] = '<a href="options-general.php?page=mediabank_options">' . esc_html__('Settings', 'mediabank') . '</a>';
    return $links;
}

/**
 * This script is executed when the shortcode "[mediabank]" is found in a textfield.
 */

function insert_mediabank()
{
    global $mediabank_settings;
    $apiUrl = get_option('mediabank_api_url');
    $apiKey = get_option('mediabank_api_key');
    $entities = get_option('mediabank_entries');

    if (empty($apiUrl) || empty($apiKey)) {
        ?>
        <div class="update-nag notice">
        <p><?php _e('The Mediabank plugin is missing required configuration settings. Please check the adminpanel.', 'mediabank'); ?></p>
        </div><?php
        return;
    }

    $includeCss = [
        $apiUrl . '2.0/dist/css/vendors.css',
        $apiUrl . '2.0/dist/css/mediabank.css'
    ];
    $includeJs = [
        '//images.memorix.nl/topviewer/1.0/src/topviewer.compressed.js?v=1.0',
        $apiUrl . '2.0/dist/js/mediabank-deps.min.js',
        $apiUrl . '2.0/dist/js/mediabank-partials.min.js',
        $apiUrl . '2.0/dist/js/mediabank.min.js',
    ];

    foreach ($includeCss as $i => $path) {
        wp_register_style('css' . $i, $path);
        wp_enqueue_style('css' . $i);
    }
    foreach ($includeJs as $i => $path) {
        wp_register_script('js' . $i, $path, array('jquery'));
        wp_enqueue_script('js' . $i, array('jquery'));
    }

    $js_gallery_modes = array();
    foreach ($mediabank_settings['gallery_modes'] as $id => $value) {
        if (get_option('mediabank_gallery_modes_' . $id)) {
            $js_gallery_modes[] = "{id:'$id'}";
        }
    }
    $js_detail_modes = array();
    foreach ($mediabank_settings['detail_modes'] as $id => $value) {
        if (get_option('mediabank_detail_modes_' . $id)) {
            $js_detail_modes[] = "'$id'";
        }
    }

    $js_topviewer_buttons = array();
    foreach ($mediabank_settings['topviewer_buttons'] as $id => $value) {
        if (get_option('mediabank_topviewer_buttons_' . $id)) {
            $js_topviewer_buttons[] = "'$id'";
        }
    }


    ?>
    <pic-mediabank
        data-api-key="<?php echo $apiKey; ?>"
        data-api-url="<?php echo $apiUrl; ?>"
        data-entities="<?php echo $entities; ?>"
    />
    <script type="text/javascript">
        $(document).ready(function () {
            angular.module('Mediabank.Boot')
                .run(
                    function ($window, MediabankConfig) {
                        MediabankConfig.whenInitialized().then(
                            function () {
                                MediabankConfig.setOption('gallery.pagination.endless',<?php echo get_option('mediabank_endless_scroll') ? 'true' : 'false'; ?>);
                                <?php if(get_option('mediabank_search_help_url')){?>
                                MediabankConfig.setOption('search.help', true);
                                MediabankConfig.setOption('search.helpUrl', '<?php echo get_option('mediabank_search_help_url'); ?>');
                                <?php } ?>
                                MediabankConfig.setOption('gallery.pagination.sort', <?php echo get_option('mediabank_sorting') ? 'true' : 'false'; ?>);
                                MediabankConfig.setOption('gallery.modes', [<?php echo implode(",", $js_gallery_modes); ?>]);
                                MediabankConfig.setOption('detail.modes', _.filter(MediabankConfig.getOption('detail.modes'), function (o) {
                                    return _.includes([<?php echo implode(",", $js_detail_modes); ?>], o.id)
                                }));
                                MediabankConfig.setOption('detail.topviewer.buttons', [<?php echo implode(",", $js_topviewer_buttons); ?>]);
                                <?php
                                if(get_option('mediabank_watermark_url')){?>
                                MediabankConfig.setOption('detail.topviewer.watermark',
                                    {
                                        'addWatermarkSrc': '<?php echo get_option('mediabank_watermark_url'); ?>',
                                        'watermarkPosition': 'center center'
                                    }
                                );<?php } ?>
                            }
                        );
                    }
                );
            angular.bootstrap(document, ['Mediabank.Boot']);
        });
    </script>
    <?php
}


/**
 * Add an admin submenu link under Settings
 */
function mediabank_add_options_submenu_page()
{
    add_submenu_page(
        'options-general.php',          // admin page slug
        __('Mediabank', 'mediabank'), // page title
        __('Mediabank', 'mediabank'), // menu title
        'manage_options',               // capability required to see the page
        'mediabank_options',                // admin page slug, e.g. options-general.php?page=wporg_options
        'mediabank_options_page'            // callback function to display the options page
    );
}

add_action('admin_menu', 'mediabank_add_options_submenu_page');

/**
 * Register the settings
 */


function mediabank_register_settings()
{

    global $mediabank_settings;
    foreach ($mediabank_settings as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $id => $array) {
                register_setting('mediabank_options', 'mediabank_' . $key . '_' . $id);
            }
        } else {
            register_setting('mediabank_options', 'mediabank_' . $key);
        }
    }
}

/*
 * Set the default settings once after plug-in activation
 *
 * */

function mediabank_initialize_settings()
{
    global $mediabank_settings;
    foreach ($mediabank_settings as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $id => $array) {
                if (isset($array['default'])) {
                    update_option('mediabank_' . $key . '_' . $id, $array['default']);
                }
            }
        } else if ($value !== '') {
            update_option('mediabank_' . $key, $value);
        }
    }
    update_option('mediabank_settings_are_initialized', true);
}

add_action('admin_init', 'mediabank_register_settings');

/**
 * Build the options page
 */
function mediabank_options_page()
{
    global $mediabank_settings;
    if (!isset($_REQUEST['settings-updated']))
        $_REQUEST['settings-updated'] = false; ?>
    <div class="wrap">
        <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
        <form method="post" action="options.php">
            <?php settings_fields('mediabank_options'); ?>
            <h2 class="title"><?php _e('API settings', 'mediabank') ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Api url', 'mediabank'); ?></th>
                    <td>
                        <input class="regular-text" type="text" name="mediabank_api_url"
                               id="mediabank_api_url"
                               value="<?php echo get_option('mediabank_api_url', 'https://webservices.picturae.com/mediabank/') ?>"><br/>

                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Api key', 'mediabank'); ?></th>
                    <td><input class="regular-text" type="text" name="mediabank_api_key" id="mediabank_api_key"
                               value="<?php echo get_option('mediabank_api_key') ?>"><br/></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Entities', 'mediabank'); ?></th>
                    <td><textarea class="large-text" name="mediabank_entities"
                                  id="mediabank_entities"><?php echo get_option('mediabank_entities') ?></textarea><br/>
                        <label class="description"
                               for="mediabank_entities"><?php _e('Separate multiple entities with a comma.', 'mediabank'); ?></label>
                    </td>
                </tr>
            </table>
            <h2 class="title"><?php _e('Display options', 'mediabank') ?></h2>
            <table class="form-table">

                <tr valign="top">
                    <th scope="row"><?php _e('Gallery modes', 'mediabank'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Gallery modes', 'mediabank'); ?></span>
                            </legend>
                            <?php foreach ($mediabank_settings['gallery_modes'] as $id => $arr) { ?>

                                <label for="mediabank_gallery_modes_<?php echo $id; ?>"><input
                                        name="mediabank_gallery_modes_<?php echo $id; ?>"
                                        id="mediabank_gallery_modes_<?php echo $id; ?>"
                                        value="1" <?php checked(get_option('mediabank_gallery_modes_' . $id)); ?>
                                        type="checkbox"><?php _e($arr['label'], 'mediabank'); ?></label><br>

                                <?php
                            }
                            ?>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Detail modes', 'mediabank'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e('Detail modes', 'mediabank'); ?></span>
                            </legend>
                            <?php foreach ($mediabank_settings['detail_modes'] as $id => $arr) { ?>
                                <label for="mediabank_detail_modes_<?php echo $id; ?>"><input
                                        name="mediabank_detail_modes_<?php echo $id; ?>"
                                        id="mediabank_detail_modes_<?php echo $id; ?>"
                                        value="1" <?php checked(get_option('mediabank_detail_modes_' . $id)); ?>
                                        type="checkbox"><?php _e($arr['label'], 'mediabank'); ?></label><br>
                                <?php
                            }
                            ?>
                        </fieldset>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Endless scrolling', 'mediabank'); ?></th>
                    <td>
                        <label for="mediabank_endless_scroll"><input
                                name="mediabank_endless_scroll"
                                id="mediabank_endless_scroll"
                                value="1" <?php checked(get_option('mediabank_endless_scroll')); ?>
                                type="checkbox"><?php _e('Enable endless scrolling', 'mediabank'); ?></label>

                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Sorting', 'mediabank'); ?></th>
                    <td>
                        <label for="mediabank_sorting"><input
                                name="mediabank_sorting"
                                id="mediabank_sorting"
                                value="1" <?php checked(get_option('mediabank_sorting')); ?>
                                type="checkbox"><?php _e('Enable sorting', 'mediabank'); ?></label>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Watermark url', 'mediabank'); ?></th>
                    <td><input class="regular-text" type="text" name="mediabank_watermark_url"
                               id="mediabank_watermark_url" value="<?php echo get_option('mediabank_watermark_url') ?>"><br/>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Search help url', 'mediabank'); ?></th>
                    <td><input class="regular-text" type="text" name="mediabank_search_help_url"
                               id="mediabank_search_help_url"
                               value="<?php echo get_option('mediabank_search_help_url') ?>"><br/></td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Topviewer buttons', 'mediabank'); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e('Topviewer buttons', 'mediabank'); ?></span>
                            </legend>
                            <?php foreach ($mediabank_settings['topviewer_buttons'] as $id => $arr) { ?>

                                <label for="mediabank_topviewer_buttons_<?php echo $id; ?>"><input
                                        name="mediabank_topviewer_buttons_<?php echo $id; ?>"
                                        id="mediabank_topviewer_buttons_<?php echo $id; ?>"
                                        value="1" <?php checked(get_option('mediabank_topviewer_buttons_' . $id)); ?>
                                        type="checkbox"><?php _e($arr['label'], 'mediabank'); ?></label><br>

                                <?php
                            }
                            ?>
                        </fieldset>
                    </td>
                </tr>


            </table>
            <p class="submit"><input name="submit" id="submit" class="button button-primary"
                                     value="<?php _e('Save changes', 'mediabank'); ?>" type="submit"></p>
        </form>
    </div>

<?php }

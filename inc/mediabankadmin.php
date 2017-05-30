<?php

class MediabankAdmin
{
    static $mediabank_settings;

    /**
     * Setup the mediabank plugin
     */
    public static function setup()
    {
        self::$mediabank_settings = array(
            'api_url' => 'https://webservices.picturae.com/mediabank/',
            'api_key' => false,
            'entities' => 0,
            'endless_scroll' => '',
            'sorting' => '',
            //@TODO This does not work so is temporary disabled.
//            'watermark_url' => '',
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
            //@TODO This does not work so is temporary disabled.
//            'topviewer_buttons' => array(
//                'zoomIn' => array(
//                    'label' => __('zoomIn', 'mediabank'),
//                    'default' => 1),
//                'zoomOut' => array(
//                    'label' => __('zoomOut', 'mediabank'),
//                    'default' => 1),
//                'rotatePlus90' => array(
//                    'label' => __('rotatePlus90', 'mediabank'),
//                    'default' => 1),
//                'fullscreen' => array(
//                    'label' => __('fullscreen', 'mediabank'),
//                    'default' => 1),
//                'paginationLeft' => array(
//                    'label' => __('paginationLeft', 'mediabank'),
//                    'default' => 1),
//                'paginationRight' => array(
//                    'label' => __('paginationRight', 'mediabank'),
//                    'default' => 1),
//                'zoomingSlider' => array(
//                    'label' => __('zoomingSlider', 'mediabank'),
//                    'default' => 1)
//        )
        );

        add_action('admin_init', [__CLASS__, 'mediabank_init']);
        add_filter('plugin_action_links', [__CLASS__, 'plugin_action_links'], 10, 5);
        add_action('admin_menu', [__CLASS__, 'mediabank_add_options_submenu_page']);
        add_action('admin_init', [__CLASS__, 'mediabank_register_settings']);
        add_action('init', [__CLASS__, 'wpse26388_rewrites_init']);
    }

    /**
     * Add rewrite rule for mediabank angular compatibility
     */
    public static function wpse26388_rewrites_init()
    {

        $page = explode('/', $_SERVER['REQUEST_URI']);

        global $wp, $wp_rewrite;
        add_rewrite_rule(
                '^' . $page[1] . '/(.*)', 'index.php?pagename=' . $page[1], //.$base_name,
                'top');
        $wp_rewrite->flush_rules(false);
    }

    /**
     * Initialize the mediabank wordpress plugin
     */
    public static function mediabank_init()
    {

        wp_deregister_script('jquery');
        wp_register_script('jquery', '//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js');
        wp_enqueue_script('jquery');

        if (!get_option('mediabank_settings_are_initialized', false)) {
            self::mediabank_initialize_settings();
        }
    }
 

    /**
    * Add a "settings" link where plugins are listed.
    */
    public static function plugin_action_links($actions, $plugin_file)
    {
    
            if ('mediabank-wordpress-plugin/mediabank.php' == $plugin_file) {

                $settings = array('settings' => '<a href="options-general.php?page=mediabank_options">' . __('Settings', 'General') . '</a>');
                $actions = array_merge($settings, $actions);

                // TODO: create suppor page
                // $site_link = array('support' => '<a href="http://thetechterminus.com" target="_blank">Support</a>');
                // $actions = array_merge($site_link, $actions);
                    
            }
                
            return $actions;

    } 
 

    /**
     * Add an admin submenu link under Settings
     */
    public static function mediabank_add_options_submenu_page()
    {
        add_submenu_page(
                'options-general.php', // admin page slug
                __('Mediabank', 'mediabank'), // page title
                __('Mediabank', 'mediabank'), // menu title
                'manage_options', // capability required to see the page
                'mediabank_options', // admin page slug, e.g. options-general.php?page=wporg_options
                [__CLASS__, 'mediabank_options_page']         // callback function to display the options page
        );
    }

    /**
     * Register the settings
     */
    public static function mediabank_register_settings()
    {

        foreach (self::$mediabank_settings as $key => $value) {
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
     */
    public static function mediabank_initialize_settings()
    {

        foreach (self::$mediabank_settings as $key => $value) {
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

    /**
     * Build the options page
     */
    public static function mediabank_options_page()
    {
        if (!isset($_REQUEST['settings-updated']))
            $_REQUEST['settings-updated'] = false;
        ?>
        <div class="wrap">
            <img src="<?= plugins_url('/img/picturae-logo.png', __DIR__) ?>">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
        <?php settings_fields('mediabank_options'); ?>
                <h2 class="title"><?php _e('API settings', 'mediabank') ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('Api url', 'mediabank'); ?></th>
                        <td>
                            <input class="regular-text" type="text" name="mediabank_api_url"
                                   id="mediabank_api_url"
                                   value="<?php echo get_option('mediabank_api_url', 'https://webservices.picturae.com/mediabank/') ?>">
                            <br/>
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
                                <?php foreach (self::$mediabank_settings['gallery_modes'] as $id => $arr) { ?>
                                    <label for="mediabank_gallery_modes_<?php echo $id; ?>"><input
                                            name="mediabank_gallery_modes_<?php echo $id; ?>"
                                            id="mediabank_gallery_modes_<?php echo $id; ?>"
                                            value="1" <?php checked(get_option('mediabank_gallery_modes_' . $id)); ?>
                                            type="checkbox"><?php _e($arr['label'], 'mediabank'); ?></label><br>
                                <?php } ?>
                            </fieldset>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Detail modes', 'mediabank'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e('Detail modes', 'mediabank'); ?></span>
                                </legend>
                                <?php foreach (self::$mediabank_settings['detail_modes'] as $id => $arr) { ?>
                                    <label for="mediabank_detail_modes_<?php echo $id; ?>"><input
                                            name="mediabank_detail_modes_<?php echo $id; ?>"
                                            id="mediabank_detail_modes_<?php echo $id; ?>"
                                            value="1" <?php checked(get_option('mediabank_detail_modes_' . $id)); ?>
                                            type="checkbox"><?php _e($arr['label'], 'mediabank'); ?></label><br>
                                <?php } ?>
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

<?php
//@TODO This does not work so is temporary disabled.
<<<TODO
<tr valign="top">
    <th scope="row"><?php _e('Watermark url', 'mediabank'); ?></th>
    <td>
        <input class="regular-text" type="text" name="mediabank_watermark_url"
               id="mediabank_watermark_url" value="<?php echo get_option('mediabank_watermark_url') ?>">
        <br/>
    </td>
</tr>
TODO;
?>
                    
                    <tr valign="top">
                        <th scope="row"><?php _e('Search help url', 'mediabank'); ?></th>
                        <td><input class="regular-text" type="text" name="mediabank_search_help_url"
                                   id="mediabank_search_help_url"
                                   value="<?php echo get_option('mediabank_search_help_url') ?>"><br/></td>
                    </tr>
                    
<?php
//@TODO This does not work so is temporary disabled.
<<<TODO
                    <tr valign="top">
                        <th scope="row"><?php _e('Topviewer buttons', 'mediabank'); ?></th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Topviewer buttons', 'mediabank'); ?></span>
                                </legend>
                                <?php foreach (self::\$mediabank_settings['topviewer_buttons'] as \$id => \$arr) { ?>
                                    <label for="mediabank_topviewer_buttons_<?php echo \$id; ?>"><input
                                            name="mediabank_topviewer_buttons_<?php echo \$id; ?>"
                                            id="mediabank_topviewer_buttons_<?php echo \$id; ?>"
                                            value="1" <?php checked(get_option('mediabank_topviewer_buttons_' . \$id)); ?>
                                            type="checkbox"><?php _e(\$arr['label'], 'mediabank'); ?></label><br>
                                <?php } ?>
                            </fieldset>
                        </td>
                    </tr>
TODO;
?>

                </table>
                <p class="submit">
                    <input name="submit" id="submit" class="button button-primary"
                        value="<?php _e('Save changes', 'mediabank'); ?>" type="submit">
                </p>
            </form>
        </div>

    <?php
    }
}

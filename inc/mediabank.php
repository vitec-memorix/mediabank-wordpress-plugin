<?php

class Mediabank
{
    /**
     * Setup the mediabank plugin
     */
    public static function setup()
    {
        // Alway set rewrite rules for all pages with mediabank
        // @TODO Fetching the pages can be done with meta query for field "display_mediabank", but seems buggy
        foreach (self::get_pages_by_shortcode('[mediabank]') as $page) {
            self::add_mediabank_rewrite($page);
        }
        
        // Insert mediabank where needed
        add_shortcode('mediabank', [__CLASS__, 'insert_mediabank']);

        if (get_post_meta(get_the_ID(), 'display_mediabank', true)) {
            add_filter('the_content', [__CLASS__, 'add_filter_the_content']);
        }
    }

    /**
     * Add the mediabank to the content loop
     */
    public static function add_filter_the_content($content)
    {
        $beforecontent = do_shortcode('[mediabank]'); //self::show();
        $fullcontent = $beforecontent . $content;
        return $fullcontent;
    }
    
    /**
     * This script is executed when the shortcode "[mediabank]" is found in a textfield.
     */
    public static function insert_mediabank()
    {
        // global $mediabank_settings;
        $apiUrl = rtrim(get_option('mediabank_api_url'), '/') . '/';
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
        foreach (MediabankAdmin::$mediabank_settings['gallery_modes'] as $id => $value) {
            if (get_option('mediabank_gallery_modes_' . $id)) {
                $js_gallery_modes[] = "$id";
            }
        }
        $js_detail_modes = array();

        foreach (MediabankAdmin::$mediabank_settings['detail_modes'] as $id => $value) {
            if (get_option('mediabank_detail_modes_' . $id)) {
                $js_detail_modes[] = "$id";
            }
        }

        //@TODO This does not work so is temporary disabled.
//        $js_topviewer_buttons = array();
//        foreach (MediabankAdmin::$mediabank_settings['topviewer_buttons'] as $id => $value) {
//            if (get_option('mediabank_topviewer_buttons_' . $id)) {
//                $js_topviewer_buttons[] = "$id";
//            }
//        }
        ?>
        <pic-mediabank
            data-api-key="<?php echo $apiKey; ?>"
            data-api-url="<?php echo $apiUrl; ?>"
            data-entities="<?php echo $entities; ?>"
            />

        <?php
        $options = [
            'mediabank_endless_scroll' => get_option('mediabank_endless_scroll') ? 'true' : 'false',
            'mediabank_search_help_url' => get_option('mediabank_search_help_url'),
            'mediabank_sorting' => get_option('mediabank_sorting') ? 'true' : 'false',
            'js_gallery_modes' => $js_gallery_modes, // implode(",", $js_gallery_modes),
            'js_detail_modes' => $js_detail_modes, // implode(",", $js_detail_modes),
            'js_topviewer_buttons' => $js_topviewer_buttons, // implode(",", $js_topviewer_buttons),
            //@TODO This does not work so is temporary disabled.
//            'mediabank_watermark_url' => get_option('mediabank_watermark_url'),
        ];

        wp_enqueue_script('mbscr', plugins_url('/js/script.js', __DIR__), 'jquery');
        wp_localize_script('mbscr', 'options', $options);
    }
    
    public static function add_mediabank_rewrite($page)
    {
        $url = get_permalink($page->ID);
        
        $shortUrl = str_replace(home_url(), '', $url);
        $shortUrl = trim($shortUrl, '/');
        
        add_rewrite_rule(
            $shortUrl . '/.*',
            'index.php?pagename=' . $page->post_name,
            'top'
        );
    }

    public static function get_pages_by_shortcode($shortcode)
    {
        global $wpdb;

        $sql = 'SELECT *
		FROM ' . $wpdb->posts . '
		WHERE
			post_type = "page"
			AND post_status="publish"
			AND post_content LIKE "%' . $shortcode . '%"';

        return $wpdb->get_results($sql);
    }
}

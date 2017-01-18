$(document).ready(function () {

  angular.module('Mediabank.Boot')
        .run(
            function ($window, MediabankConfig) {
                MediabankConfig.whenInitialized().then(
                    function () {
                        console.log('test');
                        MediabankConfig.setOption('gallery.pagination.endless', options.mediabank_endless_scroll);
                        if(get_option('mediabank_search_help_url')){
                          MediabankConfig.setOption('search.help', true);
                          MediabankConfig.setOption('search.helpUrl', options.mediabank_search_help_url);
                        }
                        MediabankConfig.setOption('gallery.pagination.sort', options.mediabank_sorting);
                        MediabankConfig.setOption('gallery.modes', options.js_gallery_modes);
                        MediabankConfig.setOption('detail.modes', _.filter(MediabankConfig.getOption('detail.modes'), function (o) {
                            return _.includes(options.js_detail_modes, o.id);
                        }));
                        MediabankConfig.setOption('detail.topviewer.buttons', options.js_topviewer_buttons);

                        if(get_option('mediabank_watermark_url')){
                        MediabankConfig.setOption('detail.topviewer.watermark',
                            {
                                'addWatermarkSrc': options.mediabank_watermark_url,
                                'watermarkPosition': 'center center'
                            }
                        );}
                    }
                );
            }
        );
    angular.bootstrap(document, ['Mediabank.Boot']);

});

/** 
  * @desc this is the main mediabank js client
  * all the options are configured in the settimngs panel of the plugin.
  * @author Rick Overman r.overman@pictura.com
  * @required Mediabank API key
  */

$(document).ready(function () {

    angular.module('Mediabank.Boot')
        .run(
            function ($window, MediabankConfig) {
                MediabankConfig.whenInitialized().then(
                    function () {

                        // set endless scroll and disable pagination
                        MediabankConfig.setOption('gallery.pagination.endless', options.mediabank_endless_scroll=='true'?true:false);
                        MediabankConfig.setOption('gallery.pagination.top', options.mediabank_endless_scroll=='true'?false:true)
                        MediabankConfig.setOption('gallery.pagination.bottom', options.mediabank_endless_scroll=='true'?false:true)

                        // gallerymode
                        var gallerymodes = [];
                        $.each(options.js_gallery_modes, function(index,mode){
                            gallerymodes.push({id:mode});
                        });

                        // set gallery modes
                        MediabankConfig.setOption('gallery.modes', gallerymodes);

                        // set gallerymodes default options
                        MediabankConfig.setOption('gallery.default', 'horizontal');

                        // set detail modes
                        var detail_modes = [];
                        $.each(MediabankConfig.getOption('detail.modes'), function(i, mode){
                            if(options.js_detail_modes.indexOf(mode.id)>=0){
                                detail_modes.push(mode);
                            }
                        });
                        MediabankConfig.setOption('detail.modes', detail_modes);

                        // set the sorting
                        MediabankConfig.setOption('gallery.pagination.sort', options.mediabank_sorting=='true'?true:false);

                        // display a watermark
                        if(options.mediabank_watermark_url){
                        MediabankConfig.setOption('detail.topviewer.watermark',
                            {
                                'addWatermarkSrc': options.mediabank_watermark_url,
                                'watermarkPosition': 'center center'
                            }
                        )}

                        // set the help button with defined help url
                        if(options.mediabank_search_help_url){
                          MediabankConfig.setOption('search.help', true);
                          MediabankConfig.setOption('search.helpUrl', options.mediabank_search_help_url);
                        }

                        // set top viewer buttons
                        console.log(options.js_topviewer_buttons);
                        MediabankConfig.setOption('detail.topviewer.buttons', options.js_topviewer_buttons);

                    }
                );
            }
        );
    angular.bootstrap(document, ['Mediabank.Boot']);

});

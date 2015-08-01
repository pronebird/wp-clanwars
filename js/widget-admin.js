jQuery(function ($) {

    $('#wpbody').on('change', '.wp-clanwars-widget-settings .widget-setting-hide-older-than select', function () {
        var $container = $(this).closest('.wp-clanwars-widget-settings');
        var $durationWrap = $container.find('.widget-setting-custom-hide-duration');
        var isCustomDuration = $(this).val() === 'custom';

        ($durationWrap[ isCustomDuration ? 'slideDown' : 'slideUp' ])();
    });
});
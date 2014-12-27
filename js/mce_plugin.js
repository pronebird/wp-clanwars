(function($){
    var media = wp.media, shortcode_string = 'wp-clanwars';
    wp.mce = wp.mce || {};
    wp.mce.wp_clanwars = {
        shortcode_data: {},
        View: {
            template: media.template( 'wp-clanwars' ),
            postID: $('#post_ID').val(),
            initialize: function( options ) {
                this.shortcode = options.shortcode;
                wp.mce.wp_clanwars.shortcode_data = this.shortcode;
                console.log(this.shortcode);
            },
            getHtml: function() {
                var options = { shortcode: this.shortcode.string() };
                return this.template(options);
            }
        }
    };
    wp.mce.views.register( shortcode_string, wp.mce.wp_clanwars );
}(jQuery));
//
// Admin page: screenshots management
// (c) 2015 Andrej Mihajlov
//

(function ($, window) {

    function ScreenshotManager() {
        ScreenshotManager.prototype._init.apply(this, arguments);
    }

    ScreenshotManager.prototype = {

        _init: function () {
            var self = this;

            this._media = [];

            this._container = $('#screenshots-container');

            $('#add-screenshots-button').on('click', function (e) {
                var image = wp.media({
                    title: wpCWL10n.addScreenshots,
                    multiple: true
                })
                .open()
                .on('select', function() {
                    var selection = image.state().get('selection');
                    var models = selection.where({ type: 'image' });
                    
                    $.each(models, function (i, model) {
                        var atts = model.attributes;

                        self.add(atts.id, atts.url);
                    });
                });
            });

            this._container.sortable({
                items: '> .sshot',
                placeholder: 'sortable-placeholder'
            });
        },

        add: function (id, url) {
            var el = $('<div class="sshot"><input type="hidden" name="screenshots[]" /><img /></div>');

            el.attr('data-id', id)
                .attr('id', 'sshot-' + id)
                .find('input')
                    .val(id)
                .end()
                .find('img')
                    .attr('src', url);

            this._container.append(el);

            this._media.push(id);
        }

    };

    $(document).ready(function () {
        window.wpScreenshotManager = new ScreenshotManager();
    });

})(jQuery, window);

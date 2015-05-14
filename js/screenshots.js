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
                placeholder: 'sortable-placeholder',
                revert: true
            });

            this._container.on('click', '.sshot a.remove', this._onRemove);
        },

        _onRemove: function (e) {
            if(confirm(wpCWL10n.confirmDeleteScreenshot)) {
                $(this).parent().remove();
            }

            e.preventDefault();
            e.stopPropagation();
        },

        add: function (id, url) {
            var el = $('<div class="sshot"><input type="hidden" name="screenshots[]" /> \
                            <a href="#" class="remove"><span class="dashicons dashicons-no"></span></a> \
                            <img /> \
                        </div>');

            el.attr('data-id', id)
                .attr('id', 'sshot-' + id)
                .find('input')
                    .val(id)
                .end()
                .find('img')
                    .attr('src', url);

            this._container.append(el);
        }

    };

    $(document).ready(function () {
        window.wpScreenshotManager = new ScreenshotManager();
    });

})(jQuery, window);

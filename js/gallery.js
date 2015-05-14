//
// Admin page: gallery management
// (c) 2015 Andrej Mihajlov
//

(function ($, window) {

    function GalleryManager() {
        GalleryManager.prototype._init.apply(this, arguments);
    }

    GalleryManager.prototype = {

        _init: function () {
            var self = this;

            this._container = $('#gallery-container');

            $('#add-gallery-button').on('click', function (e) {
                var image = wp.media({
                    title: wpCWL10n.addGallery,
                    multiple: true
                })
                .open()
                .on('select', function() {
                    var selection = image.state().get('selection');
                    var models = selection.where({ type: 'image' });
                    
                    $.each(models, function (i, model) {
                        var atts = model.attributes;
                        var sizes = atts.sizes;
                        var url = (sizes && sizes.thumbnail && sizes.thumbnail.url) || atts.url;

                        self.add(atts.id, url);
                    });
                });
            });

            this._container.sortable({
                items: '> .gallery-item',
                placeholder: 'sortable-placeholder',
                revert: true
            });

            this._container.on('click', '.gallery-item a.remove', this._onRemove);
        },

        _onRemove: function (e) {
            if(confirm(wpCWL10n.confirmDeleteScreenshot)) {
                $(this).parent().remove();
            }

            e.preventDefault();
            e.stopPropagation();
        },

        add: function (id, url) {
            var el = $('<div class="gallery-item"><input type="hidden" name="gallery[ids][]" /> \
                            <a href="#" class="remove"><span class="dashicons dashicons-no"></span></a> \
                            <div class="centered"><img /></div> \
                        </div>');

            el.attr('data-id', id)
                .attr('id', 'gallery-item-' + id)
                .find('input')
                    .val(id)
                .end()
                .find('img')
                    .attr('src', url);

            this._container.append(el);
        }

    };

    $(document).ready(function () {
        window.wpGalleryManager = new GalleryManager();
    });

})(jQuery, window);

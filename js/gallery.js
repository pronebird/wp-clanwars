/*
    WP-Clanwars
    (c) 2011 Andrej Mihajlov

    This file is part of WP-Clanwars.

    WP-Clanwars is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WP-Clanwars is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with WP-Clanwars.  If not, see <http://www.gnu.org/licenses/>.
*/

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

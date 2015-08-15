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

    /**
     * Match map constructor.
     * @param {Number} id
     * @param {Number} game_id
     * @param {Number} map_id
     */
    function MatchMap(id, game_id, map_id) {
        MatchMap.prototype._init.apply(this, arguments);
    }

    MatchMap.prototype = {
        /**
         * Initializer.
         * @private
         * @param {Number} id
         * @param {Number} game_id
         * @param {Number} map_id
         */
        _init: function (id, game_id, map_id) {
            var self = this;

            this._id = id;
            this._mapID = typeof(map_id) !== 'undefined' ? parseInt(map_id, 10) : 0;
            this._gameID = game_id;
            this._rounds = {};
            this._roundID = 0;
            this._mapsContainer = $('#mapsite');
            this._mapElement = $('<div class="map">\n\
                            <div class="title">\n\
                                <span></span> <a href="#" title="' + wpCWL10n.excludeMap + '" class="remove remove-matchmap"><span class="dashicons dashicons-trash"></span></a>\n\
                                <br class="clear"/>\n\
                            </div>\n\
                            <div class="leftcol">\n\
                                <img src="' + wpCWL10n.plugin_url + '/images/no-map.jpg" class="screenshot" style="width: 150px;" />\n\
                                <select name="scores[' + this._id + '][map_id]" class="select2 map-select" data-width="style" disabled="disabled"></select>\n\
                            </div>\n\
                            <div class="add-round">\n\
                                <button class="button button-secondary"><span class="dashicons dashicons-plus"></span> ' + wpCWL10n.addRound + '</button>\n\
                            </div>\n\
                            <br class="clear"/>\n\
                        </div>');
            
            this._mapElement
                .on('click', '.remove-matchmap', function (evt) {
                    self.remove();
                    evt.preventDefault();
                })
                .on('click', '.add-round button', function (evt) {
                    self.addRound();
                    evt.preventDefault();
                    $(this).blur();
                });

            this._mapElement.find('.select2').select2();
            
            this._mapsContainer.append(this._mapElement);

            this._getMapList();
        },

        /**
         * Request map list from backend.
         * @private
         */
        _getMapList: function () {
            var postData = {
                action: 'get_maps',
                game_id: this._gameID
            };
            $.post(ajaxurl, postData, this._createOptions.bind(this), 'json');
        },

        /**
         * Fill select element with options.
         * @param {Object} json
         */
        _createOptions: function (json) {
            var self = this;
            var select = this._mapElement.find('.map-select');

            $.each(json, function (i, map) {
                select.append($('<option></option>')
                    .attr('rel', map.screenshot_url)
                    .val(map.id)
                    .text(map.title));
            });

            select.change(function () {
                var option = $(this).find('option:selected');
                var src = option.attr('rel');

                if(!src.length) {
                    src = wpCWL10n.plugin_url + '/images/no-map.jpg';
                }

                self._mapElement
                    .find('.screenshot')
                        .attr('src', src)
                    .end()
                    .find('.title span')
                        .first()
                        .text(option.text())
                    .end();

                self._mapID = option.val();
            });

            select.removeAttr('disabled');

            if(self._mapID > 0) {
                select.find('option[value=' + self._mapID + ']')
                    .attr('selected', 'selected').trigger('change');
            } 
            else {
                select.find('option:first').trigger('change');
            }
        },

        // public

        /**
         * Remove from DOM.
         */
        remove: function () {
            this._mapElement.remove();
        },
        
        /**
         * Remove a round.
         * @param {String} id
         */
        removeRound: function (id) {
            if(!this._rounds.hasOwnProperty(id)) {
                return;
            }
            this._rounds[id].remove();
            delete this._rounds[id];
        },

        /**
         * Add a round.
         * @param {Number} score1
         * @param {Number} score2
         * @param {String} round_id
         */
        addRound: function (score1, score2, round_id) {
            var self = this;

            if(typeof(round_id) == 'undefined') {
                round_id = 0;
            }

            var x = ++this._roundID;
            var n = $('<div class="round">\n\
                            <input type="text" name="scores[' + this._id + '][team1][]" class="small-text" value="0" />\n\
                            <input type="text" name="scores[' + this._id + '][team2][]" class="small-text" value="0" />\n\
                            <input type="hidden" name="scores[' + this._id + '][round_id][]" value="' + round_id + '" />\n\
                            <a href="#" title="' + wpCWL10n.removeRound + '" class="remove"><span class="dashicons dashicons-trash"></span></a>\n\
                        </div>');
            var i = n.find('input');

            i.eq(0).val(score1);
            i.eq(1).val(score2);

            n.insertBefore(this._mapElement.find('.add-round'));
            
            this._rounds[x] = n;

            n.find('.remove').click(function (e) {
               self.removeRound(x);
               return false;
            });

            return x;
        }
    };

    /**
     * Match manager constructor.
     */
    function MatchManager() {
        MatchManager.prototype._init.apply(this, arguments);
    }

    MatchManager.prototype = {
        /**
         * Initializer.
         * @private
         */
        _init: function () {
            var self = this;

            this._last_id = userSettings.time * -1;
            this._matchSite = $('#matchsite');
            this._maps = {};

            $(document).ready(function () {
                $('#game_id').change(function () {
                    self.removeAll();
                });

                $('#wp-cw-addmap button').click(function (evt) {
                    var map = self.addMap();

                    // add two rounds by default
                    map.addRound(0, 0);
                    map.addRound(0, 0);

                    evt.preventDefault();
                    $(this).blur();
                });

                $('#matchsite [name=team2]').change(function () {
                    $('#new_team_title').val('');
                });
            });
        },

        /**
         * Add a map.
         * @param {String} field_id
         * @param {map_id} map_id
         */
        addMap: function (field_id, map_id) {
            if(typeof(field_id) === 'undefined') {
                field_id = --this._last_id;
            }

            var game_id = $('#game_id').val();
            var map = new MatchMap(field_id, game_id, map_id);
            
            this._maps[field_id] = map;

            return map;
        },

        /**
         * Remove a map.
         * @param {String} id
         */
        remove: function (id) {
            if(this._maps.hasOwnProperty(id)) {
                this._maps[id].remove();
                delete this._maps[id];
            }
        },

        /**
         * Remove all maps.
         */
        removeAll: function () {
            $.each(this._maps, function (k, map) {
                map.remove();
            });
            this._maps = {};
        }
    };

    window.wpMatchManager = new MatchManager();

})(jQuery, window);

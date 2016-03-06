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

jQuery(function ( $ ) {

var timer;
var $gps = $('#wp-clanwars-cloud-items');

function toggle($button, active) {
    var newTitle = $button.attr('data-text-toggle');
    var oldTitle = $button.text();

    $button.attr('data-text-toggle', oldTitle);
    $button.toggleClass('active', active).text(newTitle);
}

function startTimer($button) {
    cancelTimer();

    timer = setTimeout( function () {
        toggle($button, false);
    }, 5000 );
}

function cancelTimer() {
    clearTimeout(timer);
}

function resetAll() {
    $gps.find('.wp-clanwars-install-button.active')
        .each(function () {
            toggle( $(this), false);
        });
}

$gps.on('click', '.wp-clanwars-install-button:not(:disabled)', 
    function (e) {
        var $this = $(this);

        if( !$this.hasClass('active') ) {
            resetAll();
            toggle($this, true);
            startTimer($this);

            e.preventDefault();
            e.stopPropagation();
        }
        else {
            cancelTimer();
        }
    });

/*
Voting
 */

function StarRatingWidget($ratingDiv) {
    this.$ratingDiv = $ratingDiv;

    this._addEventHandlers();
}

StarRatingWidget.destroy = function () {
    this.$ratingDiv.off('.star-rating-widget');
}

StarRatingWidget.prototype._addEventHandlers = function () {
    this.$ratingDiv
        .on('mouseenter.star-rating-widget', this._mouseenter.bind(this))
        .on('mousemove.star-rating-widget', this._mousemove.bind(this))
        .on('mouseleave.star-rating-widget', this._mouseleave.bind(this))
        .on('click.star-rating-widget', this._mouseclick.bind(this));
};

StarRatingWidget.prototype._save = function () {
    var children = this.$ratingDiv.children();

    $.each(children, function (i, item) {
        var $item = $(item);

        $item.attr('data-original-class', $item.attr('class'));
    });
};

StarRatingWidget.prototype._restore = function () {
    var children = this.$ratingDiv.children();
    
    $.each(children, function (i, item) {
        var $item = $(item);
        
        $item.attr('class', $item.attr('data-original-class'));
        $item.removeAttr('data-original-class');
    });
};

StarRatingWidget.prototype._vote = function (rating) {
    console.log('vote %d', rating);
};

StarRatingWidget.prototype._mouseenter = function() {
    this._save();
};

StarRatingWidget.prototype._mouseleave = function () {
    this._restore();
};

StarRatingWidget.prototype._mousemove = function (e) {
    var $star = $(e.target);
    var index = $star.index();
    var children = this.$ratingDiv.children();

    $.each(children, function (i, item) {
        var full = 'star-full';
        var empty = 'star-empty';
        var cl = i <= index ? full : empty;

        $(item).attr('class', 'star ' + cl);
    });
};

StarRatingWidget.prototype._mouseclick = function (e) {
    var $star = $(e.target);
    var index = $star.index();


};

$gps.find('.wp-clanwars-cloud-item.wp-clanwars-cloud-item-voting-allowed .wp-clanwars-cloud-item-column-rating .star-rating').each(function (i, el) {
    new StarRatingWidget( $(el) );
});


});

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

function StarRatingWidget($itemElement) {
    this.$itemElement = $itemElement;
    this.$ratingDiv = $itemElement.find('.star-rating');
    this.remoteID = $itemElement.attr('data-remote-id');

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
    this.$ratingDiv.children().each(function (i, item) {
        var $item = $(item);

        $item.attr('data-original-class', $item.attr('class'));
    });
};

StarRatingWidget.prototype._restore = function () {    
    this.$ratingDiv.children().each(function (i, item) {
        var $item = $(item);
        
        $item.attr('class', $item.attr('data-original-class'));
        $item.removeAttr('data-original-class');
    });
};

StarRatingWidget.prototype._vote = function (rating) {
    var settings = window.wpClanwarsGameBrowserSettings || {};
    var postData = {
        _ajax_nonce: settings.vote_nonce,
        action: settings.vote_action,
        rating: rating,
        remote_id: this.remoteID
    };

    if(this._xhr) {
        this._xhr.abort();
    }

    this.$itemElement.addClass('wp-clanwars-cloud-item-updating-vote');

    this._xhr = $.post(ajaxurl, postData, this._handleVote.bind(this), 'json');
};

StarRatingWidget.prototype._handleVote = function (response) {
    this.$itemElement.removeClass('wp-clanwars-cloud-item-updating-vote');

    if(typeof(response) !== 'object' || response === null) {
        return;
    }

    var newRating = response.rating;
    var newCount = response.count;
    var newCountText = '(' + newCount + ')';

    var isHovered = this.$ratingDiv.is(':hover');
    var updateAttribute = isHovered ? 'data-original-class' : 'class';
    
    this.$itemElement.find('.num-ratings').text(newCountText);

    this.$ratingDiv.children().each(function (i, item) {
        var $item = $(item);
        var index = i + 1;
        var star_class = 'empty';

        if( newRating >= index ) {
            star_class = 'full';
        }
        else if( Math.ceil(newRating) >= index ) {
            star_class = 'half';
        }

        star_class = 'star star-' + star_class;

        $item.attr(updateAttribute, star_class);
    });
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

    this.$ratingDiv.children().each(function (i, item) {
        var full = 'star-full';
        var empty = 'star-empty';
        var cl = i <= index ? full : empty;

        $(item).attr('class', 'star ' + cl);
    });
};

StarRatingWidget.prototype._mouseclick = function (e) {
    var $star = $(e.target);
    var index = $star.index();

    var rating = index + 1;

    this._vote(rating);
};

$gps.find('.wp-clanwars-cloud-item.wp-clanwars-cloud-item-votes-enabled').each(function (i, el) {
    new StarRatingWidget($(el));
});


});

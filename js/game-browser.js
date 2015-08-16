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

});

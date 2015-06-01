jQuery(function ( $ ) {

var timer;
var $gps = $('#wp-clanwars-gamepacks');

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

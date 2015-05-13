//
// Match browser helper script
// (c) 2015 Andrej Mihajlov
//

jQuery(document).ready(function ($) {
    $('.wp-clanwars-list .maplist a').each(function () {
        var href = $(this).attr('href');
        var title = $(this).attr('title');
        var p = href.indexOf('#');
        var split = [ 150, 150 ];

        if(p !== -1) {
            split = href.substr(++p).split('x', 2);
        }

        var w = split[0];
        var h = split[1];

        $(this).attr('title', '<div style="position: relative;"><img src="' + href + '" style="width: ' + w + 'px; height: ' + h + 'px;" /><div class="map-title">' + title + '</div></div>')
            .tipsy({html: true})
            .on('click', function () { return false; });
    });
});
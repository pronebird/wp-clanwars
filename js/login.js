jQuery(document).ready(function ($) {

    var settings = window.wpClanwarsLoginSettings || {};

    $('#steam-login').on('click', function () {
        window.open(settings.steam_login_url, 'wp-clanwars-auth', 'width=800,height=600');
    });

    $('#facebook-login').on('click', function () {
        window.open(settings.facebook_login_url, 'wp-clanwars-auth', 'width=520,height=320');
    });

});
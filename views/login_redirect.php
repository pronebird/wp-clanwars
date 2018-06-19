<html>
    <body>
        <form id="form" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" method="post">
            <input id="token" type="hidden" name="token" value="" />
        </form>
        <script type="text/javascript">
        (function () {
            var token, match = /accessToken=(.*)$/.exec(location.href);
            if(match) {
                token = match[1];
                if(!token.length) {
                    window.close();
                    return;
                }
                document.getElementById('token').value = token;
                document.getElementById('form').submit();
            }
        })();
        </script>
    </body>
</html>
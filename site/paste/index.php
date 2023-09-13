<!DOCTYPE html>
<html>
<head>
    <script src="/assets/vendor/jquery/jquery_3.2.0.min.js"></script>
    <title>Code paste</title>
    <link rel="icon" href="/assets/img/favicon.png" type="image/x-icon"/>
    <link rel="stylesheet" href="/assets/css/paste-add.css">
    
    <script>
        function save() {
            var name = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            for (var i = 0; i < 15; i++) {
                name += possible.charAt(Math.floor(Math.random() * possible.length));
            }
            $("#file").val(name);
            $.ajax({
                url: '?process=paste&method=save',
                type: 'post',
                data: $('#form').serialize(),
                success: function () {
                    window.open("?process=paste&method=view&f=" + name, "_self");
                }
            })
        }

        $(window).bind('keydown', function (event) {
            if (event.ctrlKey || event.metaKey) {
                switch (String.fromCharCode(event.which).toLowerCase()) {
                    case 's':
                        event.preventDefault();
                        save();
                    break;
                }
            }
        });
    </script>
</head>
<body>
    <a class="icon" onclick="save();">
        <img src="/assets/img/pasteSave.svg">
    </a>
    <form action="?process=paste&method=save" method="post" id="form">
        <input type="hidden" name="file" value="" id="file">
        <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="text" name="data" maxlength="30000"></textarea>
    </form>
</body>
</html>
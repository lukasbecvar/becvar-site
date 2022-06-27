<!DOCTYPE html>
<html>
<head>
    <script src="assets/js/jquery_3.2.0.min.js"></script>
    <title>Code paste</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon"/>
    
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

    <style>
        ::selection {
            background: #8c434f;
        }

        ::-moz-selection {
            background: #8c434f;
        }

        body {
            margin: 0;
        }

        textarea {
            padding: 10px;
            resize: none;
            border: none !important;
            outline: none !important;
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0px;
            font-size: 16px !important;
            left: 0px;
            background-color: #282c34;
            color: #abb2bf;
            font-family: monospace !important;
        }

        .icon {
            position: relative;
            float: right;
            margin-right: 10px;
            top: 10px;
            z-index: 9999;
            cursor: pointer;
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-user-drag: none;
            user-drag: none;
        }

        .icon img {
            width: 30px;
            height: 30px;
            pointer-events: none;
        }
    </style>
</head>
<body>

<a class="icon" onclick="save();">
    <img src="assets/img/pasteSave.svg">
</a>
<form action="?process=paste&method=save" method="post" id="form">
    <input type="hidden" name="file" value="" id="file">
    <textarea autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" id="text" name="data"></textarea>
</form>
</body>
</html>
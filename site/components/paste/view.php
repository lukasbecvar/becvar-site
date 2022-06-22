<?php //Code paste viewer

    //Get paste spec
    $spec = $mysqlUtils->escapeString($_GET["f"]);

    //Get paste content
    $pasteContent = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM pastes WHERE spec='$spec'"));

    if (empty($pasteContent["content"])) {
        if ($pageConfig->getValueByName("dev_mode") == true) {
            die("[DEV-MODE]:Error: paste content is empty");
        } else {
            $urlUtils->jsRedirect("ErrorHandlerer.php?code=404");
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Code paste</title>
    <link rel="icon" href="assets/img/favicon.png" type="image/x-icon"/>    
    <script type="text/javascript" src="assets/js/highlight.min.js"></script>
    <script src="assets/js/highlight.min.jshighlightjs-line-numbers.min.js"></script>
    <script>
        hljs.initHighlightingOnLoad();
        hljs.initLineNumbersOnLoad();
    </script>
    <link rel="stylesheet" href="assets/css/atom-one-dark.min.css">

    <script>
        $("img").on("dragstart", function(e) {
            e.preventDefault();
        });
    </script>

    <?php //Select title by file name
        echo "<title>Viewing".$_GET["f"]."</title>";
    ?>

    <style>
        * {
            background-color: #282c34;
        }

        /*Custom scroolbar design (Not working on mobile divices)*/
        ::-webkit-scrollbar { /*Own page scroolbar design*/
            width: 12px;
        } 

        ::-webkit-scrollbar-track { /*Own scroolbar bacground*/
            background: rgb(20,20,20);
            border: 1px solid #343a40;
        }
        
        ::-webkit-scrollbar-thumb { /*Own scrollbar button*/
            background: #343a40;
            -webkit-transition: all 0.2s ease;
            transition: all 0.2s ease;
        } 

        ::-webkit-scrollbar-thumb:hover { /*Own scrollbar button hover*/
            background: #4f555a;
        }
        /*End of Custom scrollBar*/

        ::selection {
            background: #8c434f;
        }

        ::-moz-selection {
            background: #8c434f;
        }

        /* for block of numbers */
        td.hljs-ln-numbers {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            text-align: center;
            vertical-align: top;
            padding-right: 5px;
            color: lightgray;
            /* your custom style here */
        }

        /* for block of code */
        td.hljs-ln-code {
            padding-left: 10px;
        }

        html {
            margin: 0px;
        }

        code {
            position: relative;
            height: 100%;
            left: 0px;
            top: 0px;
            margin: 0px;
            font-size: 16px;
            width: 99%;
        }

        pre {
            top: 0px;
            left: 0px;
            padding: 0px;
            margin: 0px;
        }

        .icon {
            position: relative;
            float: right;
            margin-right: 5px;
            top: 5px;
            z-index: 9999;
            user-drag: none;
            user-select: none;
            -moz-user-select: none;
            -webkit-user-drag: none;
            -webkit-user-select: none;
            -ms-user-select: none;
            cursor: pointer;
        }

        .icon img {
            width: 35px;
            height: 35px;
        }
    </style>
</head>
<body>
    <pre>
        <?php //Print content from db to site
            if (!empty($pasteContent["content"])) {
                echo "<code>".$pasteContent["content"]."</code>";
            }
        ?>     
    </pre>
</body>
</html>
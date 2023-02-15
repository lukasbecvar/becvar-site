<?php // code paste viewer

    // get paste spec
    $spec = $siteController->getQueryString("f");

    // get paste content
    $pasteContent = mysqli_fetch_assoc(mysqli_query($mysqlUtils->mysqlConnect($pageConfig->getValueByName('basedb')), "SELECT * FROM pastes WHERE spec='$spec'"));

    // check if content to view is not empty
    if (empty($pasteContent["content"])) {
        if ($siteController->isSiteDevMode()) {
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
    <link rel="icon" href="/assets/img/favicon.png" type="image/x-icon"/>    
    <script type="text/javascript" src="/assets/js/highlight.min.js"></script>
    <script src="/assets/js/highlight.min.jshighlightjs-line-numbers.min.js"></script>
    <script>
        hljs.initHighlightingOnLoad();
        hljs.initLineNumbersOnLoad();
    </script>
    <link rel="stylesheet" href="/assets/css/atom-one-dark.min.css">
    <link rel="stylesheet" href="/assets/css/pasteView.css">

    <script>
        $("img").on("dragstart", function(e) {
            e.preventDefault();
        });
    </script>

    <?php // select title by file name
        echo "<title>Viewing".$siteController->getQueryString("f")."</title>";
    ?>
</head>
<body>
    <pre>
        <?php // print content from db to site
            if (!empty($pasteContent["content"])) {
                echo "<code>".$pasteContent["content"]."</code>";
            }
        ?>     
    </pre>
</body>
</html>
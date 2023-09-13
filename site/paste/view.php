<?php // code paste viewer

    // get paste spec
    $spec = $site_manager->get_query_string("f");

    // get paste content
    $paste_content = $mysql->fetch_value("SELECT content FROM pastes WHERE spec='$spec'", "content");

    // check if content to view is not empty
    if (empty($paste_content)) {

        // handle error
        $site_manager->handle_error("[DEV-MODE]:Error: paste content is empty", 520);
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
    <link rel="stylesheet" href="/assets/css/atom-one-dark.css">
    <link rel="stylesheet" href="/assets/css/paste-view.css">

    <script>
        $("img").on("dragstart", function(e) {
            e.preventDefault();
        });
    </script>

    <?php // select title by file name
        echo "<title>Viewing".$site_manager->get_query_string("f")."</title>";
    ?>
</head>
<body>
    <pre>
        <?php // print content from db to site
            if (!empty($paste_content)) {
                echo "<code>".$paste_content."</code>";
            }
        ?>     
    </pre>
</body>
</html>
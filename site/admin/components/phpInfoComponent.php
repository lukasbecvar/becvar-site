<div class="admin-panel">
<style>
    .wrapper {
        color: white;
        overflow: auto;
    }
</style>
<?php // PHP info component form admin site

    function phpinfo_array() {
        ob_start();
        phpinfo();
        $info_arr = array();
        $info_lines = explode("\n", strip_tags(ob_get_clean(), "<tr><td><h2>"));
        $cat = "General";
        foreach($info_lines as $line) {
            preg_match("~<h2>(.*)</h2>~", $line, $title) ? $cat = $title[1] : null;
            if(preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) {
                $info_arr[$cat][$val[1]] = $val[2];
            } elseif(preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) {
                $info_arr[$cat][$val[1]] = array("local" => $val[2], "master" => $val[3]);
            }
        }
        return $info_arr;
    }

    function myprint_r($my_array) {
        if (is_array($my_array)) {
            echo "<table border=1 cellspacing=0 cellpadding=3 width=100%>";
            echo '<tr><td colspan=2><strong><font color=white>ARRAY</font></strong></td></tr>';
            foreach ($my_array as $k => $v) {
                echo '<tr><td valign="top" style="width:45px; color: white; padding: 5px;">';
                echo '<strong>' . $k . "</strong></td> <td>";
                myprint_r($v);
                echo "</td></tr>";
            }
            echo "</table>";
            return;
        }
        echo $my_array;
    }

    // print info
    echo '<div class="php-info table table-dark">';
    myprint_r(phpinfo_array());
    echo '</div>';
?>
</div>
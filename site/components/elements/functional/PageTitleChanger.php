<script>
    let title = '<?php echo $pageConfig->getValueByName('appName'); ?>';
    let i = 0;

    setInterval(() => {
        document.title = title.substring(0, i + 1);
        if (i == 0) {
            direction = 1;
        } else if (i == title.length) {
            direction = -1;
        }
            i += direction;
    }, 300);
</script>
<div class='card text-white mb-3 msg-box'>
    <div class=card-body>
        <h5 class=left-center class=card-title><?= $row["name"]." (".$row["email"].")" ?>
            <span class='text-success phone-none'>[<?= $row["time"] ?>]</span>, 
            <span class='text-warning phone-none'>[<?= $row["remote_addr"] ?>]</span>
            <a class='delete-link' href='<?= "?admin=inbox&delete=".$row["id"] ?>'>X</a>
        </h5>
        <p class=left-center class=card-text><?= $row["message"] ?></p>
    </div>
</div>
<div class="row mt-2">
    <div class="col-md-6 d-flex align-items-stretch">
        <div class="info-box">
            <i class="bx bx-share-alt"></i>
            <h3>Social sites</h3>
            <div class="social-links">
                <a href="<?php echo $pageConfig->getValuebyName("instagram"); ?>" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="<?php echo $pageConfig->getValuebyName("discord"); ?>" class="discord"><i class="bi bi-discord"></i></a>
                <a href="<?php echo $pageConfig->getValuebyName("telegram"); ?>" class="telegram"><i class="bi bi-telegram"></i></a>
                <a href="<?php echo $pageConfig->getValuebyName("twitter"); ?>" class="twitter"><i class="bi bi-twitter"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mt-4 mt-md-0 d-flex align-items-stretch">
        <div class="info-box">
            <i class="bx bx-envelope"></i>
             <h3>Email Me</h3>
            <p><?php echo $pageConfig->getValuebyName("email"); ?></p>
        </div>
    </div>
</div>
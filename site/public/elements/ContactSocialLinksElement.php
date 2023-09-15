<div class="row mt-2">
    <div class="col-md-6 d-flex align-items-stretch">
        <div class="info-box">
            <i class="bx bx-share-alt"></i>
            <h3>Social sites</h3>
            <div class="social-links">
                <a href="<?= $config->get_value("instagram") ?>" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="<?= $config->get_value("telegram") ?>" class="telegram"><i class="bi bi-telegram"></i></a>
                <a href="<?= $config->get_value("twitter") ?>" class="twitter"><i class="bi bi-twitter"></i></a>
                <a href="<?= $config->get_value("linkedin") ?>" class="linkedin"><i class="bi bi-linkedin"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mt-4 mt-md-0 d-flex align-items-stretch">
        <div class="info-box">
            <i class="bx bx-envelope"></i>
             <h3>Email Me</h3>
            <p><?= $config->get_value("email") ?></p>
        </div>
    </div>
</div>
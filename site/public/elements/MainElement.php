<header id="header">
    <div class="container">
        <h1><a href="/admin">Lukáš Bečvář</a></h1>
        <h2>I'm a <span>full stack developer</span></h2>
        <nav id="navbar" class="navbar">
            <ul>
                <li><a class="nav-link active" href="#header">Home</a></li>
                <li><a class="nav-link" href="#about">About</a></li>
                <li><a class="nav-link" href="#projects">Projects</a></li>
                <li><a class="nav-link" href="#contact">Contact</a></li>
                <li><a class="nav-link" href="#uploader">Uploader</a></li>
                <li><a class="nav-link" href="/?process=paste">Paste</a></li>
            </ul>
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav>
        <div class="social-links">
            <a href="<?= $config->get_value("instagram") ?>" target="_blank" class="instagram"><i class="bi bi-instagram"></i></a>
            <a href="<?= $config->get_value("twitter") ?>" target="_blank" class="twitter"><i class="bi bi-twitter"></i></a>
            <a href="<?= $config->get_value("github") ?>" target="_blank" class="github"><i class="bi bi-github"></i></a>
            <a href="<?= $config->get_value("telegram") ?>" target="_blank" class="telegram"><i class="bi bi-telegram"></i></a>
            <a href="<?= $config->get_value("linkedin") ?>" target="_blank" class="linkedin"><i class="bi bi-linkedin"></i></a>
        </div>
    </div>
</header>
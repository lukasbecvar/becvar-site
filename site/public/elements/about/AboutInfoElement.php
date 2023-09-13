<div class="row">
    <div class="col-lg-8 pt-4 pt-lg-0 content" data-aos="fade-left">
        <h3>JAVA, PHP & WEB Developer</h3>
            
        <p class="fst-italic">
            I am a full stack web & server solutions developer.
        </p>
        
        <div class="row">
           
        <div class="col-lg-6">
                <ul>
                    <li><i class="bi bi-chevron-right"></i> <strong>Birthday:</strong> <span>28 May 1999</span></li>
                    <li><i class="bi bi-chevron-right"></i> <strong>Website:</strong> <span><?php echo $config->get_value("app-name"); ?></span></li>
                    <li><i class="bi bi-chevron-right"></i> <strong>Email:</strong> <span><?php echo $config->get_value("email"); ?></span></li>
                </ul>
            </div>
            
            <div class="col-lg-6">
                <ul>
                    <li><i class="bi bi-chevron-right"></i> <strong>Age:</strong> <span><?php echo $site_manager->get_age("05/28/1999"); ?></span></li>
                    <li><i class="bi bi-chevron-right"></i> <strong>Freelance:</strong> <span>Available</span></li>
                </ul>
            </div>
        
        </div>
        
        <p>
            All my public projects available <?php echo '<a href="'.$config->get_value("github").'">here</a>'; ?>
        </p>

        <p>
            My first programming language was Pascal I started learning it in 2012.<br>
            My main programming languages are "currently" Java and PHP.
        </p>
    </div>
</div>
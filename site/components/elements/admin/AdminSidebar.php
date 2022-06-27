<div class="sidebar">
    <div class="profile">
        <img src="data:image/jpeg;base64,<?php echo $adminController->getUserAvatar(); ?>" alt="profile_picture">
        <h3><?php echo $adminController->getCurrentUsername(); ?></h3>
        <p><?php echo $adminController->getCurrentRole(); ?></p>
    </div>

    <ul>
        <li>
            <a class="sMenuButton" target="_blank" href="/">
                <span class="icon"><i class="fas fa-globe"></i></span>
                <span class="item">Main site</span>
            </a>
        </li>
        
        <li>
            <a class="sMenuButton" target="_blank" href="admin/adminer/adminer.php">
				<span class="icon"><i class="fas fa-user"></i></span>
				<span class="item">Adminer</span>
			</a>
        </li>

        <li>
            <a class="sMenuButton" href="?page=admin&process=dashboard">
				<span class="icon"><i class="fas fa-desktop"></i></span>
				<span class="item">Dashboard</span>
			</a>
		</li>
		
        <li>
		    <a class="sMenuButton" href="?page=admin&process=dbBrowser">
				<span class="icon"><i class="fas fa-database"></i></span>
				<span class="item">Database</span>
			</a>
		</li>
	
        <li>
			<a class="sMenuButton" href="?page=admin&process=logReader&limit=<?php echo $pageConfig->getValueByName("rowInTableLimit"); ?>&startby=0">
			    <span class="icon"><i class="fas fa-file"></i></span>
			    <span class="item">Log reader</span>
	    	</a>
		</li>
					
        <li>
			<a class="sMenuButton" href="?page=admin&process=mediaBrowser&limit=<?php echo $pageConfig->getValueByName("imagesInBrowserLimit"); ?>&startby=0">
	    		<span class="icon"><i class="fas fa-image"></i></span>
				<span class="item">Media browser</span>
			</a>
        </li>
					
        <?php //Add cloud link if nextcloud installed on server
			if ($servicesController->isServiceInstalled("nextcloud")) {
				echo '
					<li>
						<a class="sMenuButton" target="_blank" href="https://becvold.xyz/nextcloud/index.php/apps/files/?dir=/&fileid=6">
							<span class="icon"><i class="fas fa-cloud"></i></span>
							<span class="item">Cloud storage</span>
						</a>	
					</li>
				';
			}
		?>
					
        <li>
			<a class="sMenuButton" href="?page=admin&process=inbox">
				<span class="icon"><i class="fas fa-envelope"></i></span>
				<span class="item">Inbox</span>
			</a>
		</li>
					
        <li>
			<a class="sMenuButton" href="?page=admin&process=todos">
				<span class="icon"><i class="fas fa-tasks"></i></span>
				<span class="item">Todo Manager</span>
			</a>
		</li>
				
        <li>
			<a class="sMenuButton" href="?page=admin&process=phpInfo">
				<span class="icon"><i class="fab fa-php"></i></span>
				<span class="item">PHP information</span>
			</a>
		</li>
					
        <li>
			<a class="sMenuButton" href="?page=admin&process=pageSettings">
				<span class="icon"><i class="fas fa-cogs"></i></span>
				<span class="item">Page settings</span>
			</a>
		</li>

		<li>
			<a class="sMenuButton" href="?page=admin&process=accountSettings">
	    		<span class="icon"><i class="fas fa-user-cog"></i></span>
				<span class="item">Account settings</span>
			</a>
		</li>
	</ul>
</div>
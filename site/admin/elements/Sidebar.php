<div class="sidebar">
    <div class="profile">
        <img src="data:image/jpeg;base64,<?= $user_manager->get_avatar() ?>" alt="profile_picture">
        <h3><?= $user_manager->get_username() ?></h3>
        <p><?= $user_manager->get_role() ?></p>
    </div>

    <ul>
        <li>
            <a class="s-menu-button" target="_blank" href="/">
                <span class="icon"><i class="fas fa-globe"></i></span>
                <span class="item">Main site</span>
            </a>
        </li>
        
        <li>
            <a class="s-menu-button" target="_blank" href="admin/adminer/adminer.php">
				<span class="icon"><i class="fas fa-user"></i></span>
				<span class="item">Adminer</span>
			</a>
        </li>

        <li>
            <a class="s-menu-button" href="?admin=dashboard">
				<span class="icon"><i class="fas fa-desktop"></i></span>
				<span class="item">Dashboard</span>
			</a>
		</li>
		
        <li>
		    <a class="s-menu-button" href="?admin=dbBrowser">
				<span class="icon"><i class="fas fa-database"></i></span>
				<span class="item">Database</span>
			</a>
		</li>
	
        <li>
			<a class="s-menu-button" href="?admin=logReader&limit=<?php echo $config->get_value("row-in-table-limit"); ?>&startby=0">
			    <span class="icon"><i class="fas fa-file"></i></span>
			    <span class="item">Log reader</span>
	    	</a>
		</li>

		<li>
			<a class="s-menu-button" href="?admin=visitors&limit=<?php echo $config->get_value("row-in-table-limit"); ?>&startby=0">
			    <span class="icon"><i class="fas fa-user"></i></span>
			    <span class="item">Visitors manager</span>
	    	</a>
		</li>

        <li>
			<a class="s-menu-button" href="?admin=mediaBrowser&limit=<?php echo $config->get_value("images-in-browser-limit"); ?>&startby=0">
	    		<span class="icon"><i class="fas fa-image"></i></span>
				<span class="item">Media browser</span>
			</a>
        </li>
					
        <li>
			<a class="s-menu-button" href="?admin=inbox">
				<span class="icon"><i class="fas fa-envelope"></i></span>
				<span class="item">Inbox</span>
			</a>
		</li>
					
        <li>
			<a class="s-menu-button" href="?admin=todos">
				<span class="icon"><i class="fas fa-tasks"></i></span>
				<span class="item">Todo Manager</span>
			</a>
		</li>
				
        <li>
			<a class="s-menu-button" href="?admin=phpInfo">
				<span class="icon"><i class="fab fa-php"></i></span>
				<span class="item">PHP information</span>
			</a>
		</li>
					
        <li>
			<a class="s-menu-button" href="?admin=diagnostics">
				<span class="icon"><i class="fa fa-check"></i></span>
				<span class="item">Diagnostics</span>
			</a>
		</li>

        <li>
			<a class="s-menu-button" href="?admin=pageSettings">
				<span class="icon"><i class="fas fa-cogs"></i></span>
				<span class="item">Page settings</span>
			</a>
		</li>

		<li>
			<a class="s-menu-button" href="?admin=accountSettings">
	    		<span class="icon"><i class="fas fa-user-cog"></i></span>
				<span class="item">Account settings</span>
			</a>
		</li>
	</ul>
</div>
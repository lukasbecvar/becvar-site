<header>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<a class="navbar-brand" href="?page=admin"><?php echo $pageConfig->getValueByName('appName'); ?></a>
	 	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar1">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbar1">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item">
					<a class="nav-link" href="?page=home">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="?page=about">About</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="?page=generator">Generator / Crypter</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="?page=contact">Contact</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="?page=imageUploader">Uploader</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="?process=paste">Paste</a>
				</li>
			</ul>
		</div>
	</nav>
</header>
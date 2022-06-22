<header>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<a class="navbar-brand" href="index.php?page=admin"><?php echo $pageConfig->getValueByName('appName'); ?></a>
	 	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar1">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbar1">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item">
					<a class="nav-link" href="index.php?page=home">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="index.php?page=about">About</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="index.php?page=generator">Generator / Crypter</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="index.php?page=contact">Contact</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="index.php?page=imageUploader">Uploader</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="index.php?process=paste">Paste</a>
				</li>
			</ul>
		</div>
	</nav>
</header>
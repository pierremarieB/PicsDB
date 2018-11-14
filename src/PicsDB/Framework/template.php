<!DOCTYPE html>
<html lang="fr" xmlns:og="http://ogp.me/ns#">
<head>
	<title><?php echo $parts["title"] ?></title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" href="skin/style.css" />

	<meta name="twitter:card" content="<?php echo $parts['twitterCard']; ?>"/>
	<meta name="twitter:title" content="<?php echo $parts['title']; ?>"/>
	<meta name="twitter:description" content="<?php echo $parts['twitterDescription']; ?>"/>
	<meta name="twitter:image" content="<?php echo $parts['twitterImage']; ?>"/>

	<meta property="og:title" content="<?php echo $parts['title']; ?>"/>
	<meta property="og:type" content="article"/>
	<meta property="og:url" content="<?php echo $parts['url']; ?>"/>
	<meta property="og:image" content="<?php echo $parts['image']; ?>"/>
</head>
<body>
	<header>
		<nav class="menu">
			<ul>
				<li id="sitetitle">
					<h2>PicsDB</h2>
				</li>
				<?php
                    foreach ($menu as $text => $link) 
                    {
                        echo "<li><a href=\"$link\">$text</a></li>";
                    }
                ?>
			</ul>
			<ul id='auth'>
				<?php
                    foreach ($authMenu as $text => $link) 
                    {
                        echo "<li><a href=\"$link\">$text</a></li>";
                    }
                ?>
			</ul>
		</nav>
	</header>
	<main>
		<h1 id='title'><?php echo $parts["title"]; ?></h1>
		<h2 id='feedback'><?php echo $parts["feedback"]; ?></h2>
		<div id='content'>
			<?php echo $parts["content"]; ?>
		</div>
	</main>
</body>
</html>


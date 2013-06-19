<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title><?php echo $title_for_layout;?></title>

<meta name="title" content="">
<meta name="description" content="">
<meta name="keywords" content="">
<?php if(isset($og_title)) { ?><meta property="og:title" content="<?php echo $og_title;?>" /><?php } ?>
<?php if(isset($og_description)) { ?><meta property="og:description" content="<?php echo $og_description;?>" /><?php } ?>
<?php if(isset($og_url)) { ?><meta property="og:url" content="<?php echo $og_url;?>" /><?php } ?>
<?php if(isset($og_site_name)) { ?><meta property="og:site_name" content="<?php echo $og_site_name;?>" /><?php } ?>
<?php if(isset($og_image)) { ?><meta property="og:image" content="<?php echo $og_image;?>" /><?php } ?>

<?php echo $styles_for_layout;?>
<?php echo $scripts_for_layout;?>
</head>
<body>		
	<div id="content">
		<?php echo $content_for_layout;?>
	</div>
</body>
</html>
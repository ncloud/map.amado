<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<title><?php echo $title_for_layout;?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta name="title" content="">
	<meta name="description" content="">
	<meta name="keywords" content="">
	
<?php if(isset($og_title)) { ?><meta property="og:title" content="<?php echo $og_title;?>" /><?php } ?>
<?php if(isset($og_description)) { ?><meta property="og:description" content="<?php echo $og_description;?>" /><?php } ?>
<?php if(isset($og_url)) { ?><meta property="og:url" content="<?php echo $og_url;?>" /><?php } ?>
<?php if(isset($og_map_name)) { ?><meta property="og:map_name" content="<?php echo $og_map_name;?>" /><?php } ?>
<?php if(isset($og_image)) { ?><meta property="og:image" content="<?php echo $og_image;?>" /><?php } ?>
    
	<link href="<?php echo site_url("/cornerstone/bootstrap/css/bootstrap.css");?>" rel="stylesheet" />

<?php echo $styles_for_layout;?>
<?php echo $scripts_for_layout;?>
</head>
<body>
     <?php echo $content_for_layout;?>
</body>
</html>
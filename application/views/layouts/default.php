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
<?php if(isset($og_site_name)) { ?><meta property="og:site_name" content="<?php echo $og_site_name;?>" /><?php } ?>
<?php if(isset($og_image)) { ?><meta property="og:image" content="<?php echo $og_image;?>" /><?php } ?>

    <link rel="stylesheet" href="<?php echo site_url('/css/reset.css');?>" />
    <link rel="stylesheet" href="<?php echo site_url('/css/layout.css');?>" />
    
    
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap.css");?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap-modal.css");?>" rel="stylesheet" />
    <link type="text/css" href="<?php echo site_url('/bootstrap/css/bootstrap-notify.css');?>" rel="stylesheet">

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-modalmanager.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-modal.js");?>"></script>
    <script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-notify.js");?>"></script>

	<script type="text/javascript" src="<?php echo site_url("/js/basic.js");?>"></script>

	<script type="text/javascript">
		var service = {
			url: "<?php echo site_url('/');?>"
		};
	</script>
	        
<?php echo $styles_for_layout;?>
<?php echo $scripts_for_layout;?>
</head>
<body>    
	<div class='notifications top-center'></div>
    
    <div id="content">
        <?php echo $content_for_layout;?>
    </div>
</body>
</html>
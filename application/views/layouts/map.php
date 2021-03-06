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
    
	<link href="<?php echo site_url("/bootstrap/css/bootstrap.css");?>" rel="stylesheet" />
	<link href="<?php echo site_url("/bootstrap/css/bootstrap-modal.css");?>" rel="stylesheet" />
    <link href="<?php echo site_url('/bootstrap/css/bootstrap-notify.css');?>" rel="stylesheet">

	<link rel="stylesheet" href="<?php echo site_url("/css/bootstrap-custom.css");?>" />    

    <link rel="stylesheet" href="<?php echo site_url('/css/map.css');?>" />
    
	<link rel="stylesheet" href="<?php echo site_url("/css/bootstrap-custom-responsive.css");?>" />    
	<link rel="stylesheet" href="<?php echo site_url('/css/map-responsive.css');?>" />

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-modalmanager.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-modal.js");?>"></script>
    <script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-notify.js");?>"></script>

	<script type="text/javascript" src="<?php echo site_url("/js/lib/basic.js");?>"></script>

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
	<div class='notifications bottom-left'></div>
    
    <div id="content">
        <?php echo $content_for_layout;?>
    </div>
</body>
</html>
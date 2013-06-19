<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<link type='text/css' href='<?php echo site_url('/bootstrap/css/bootstrap.css');?>' rel='stylesheet' />
	<link type='text/css' href='<?php echo site_url('/bootstrap/css/bootstrap-responsive.css');?>' rel='stylesheet' />
	<link type='text/css' rel='stylesheet' href='<?php echo site_url('/css/admin.css');?>' />    
	
	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
	<script type='text/javascript' src='<?php echo site_url('/bootstrap/js/bootstrap.js');?>'></script>
</head>
<body>
    <div id="content">
        <?php echo $content_for_layout;?>
    </div>
</body>
</html>
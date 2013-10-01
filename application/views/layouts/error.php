<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<link type='text/css' rel='stylesheet' href='<?php echo site_url('/css/manage.css');?>' />    
	<link type='text/css' href='<?php echo site_url('/bootstrap/css/bootstrap.css');?>' rel='stylesheet' />
	<link type='text/css' href='<?php echo site_url('/bootstrap/css/bootstrap-responsive.css');?>' rel='stylesheet' />
	
	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
	<script type='text/javascript' src='<?php echo site_url('/bootstrap/js/bootstrap.js');?>'></script>
</head>
<body class="error">    

	<div class="navbar navbar-inverse navbar-fixed-top">
	  <div class="navbar-inner">
	    <div class="container">
	      <a class="brand" href="<?php echo site_url("/manage/");?>">
	        아마도.지도
	      </a>

	      <ul class="nav pull-right">
	      <?php if($current_user->id) { ?>
	        <li><a href="<?php echo site_url('/logout');?>">로그아웃</a></li>
	      <?php } else { ?>
	        <li><a href="<?php echo site_url('/login');?>">로그인</a></li>
	      <?php } ?>
	      </ul>
	    </div>
	  </div>
	</div>

  <div id="content" class="container">
        <?php echo $content_for_layout;?>
    </div>
</body>
</html>
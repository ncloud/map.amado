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
    
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap.css");?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap-responsive.css");?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap-modal.css");?>" rel="stylesheet" />
    <link type="text/css" href="<?php echo site_url('/bootstrap/css/bootstrap-notify.css');?>" rel="stylesheet" />
    
    <link rel="stylesheet" href="<?php echo site_url("/css/bootstrap-custom.css");?>" />    
    <link rel="stylesheet" href="<?php echo site_url("/css/style.css");?>" />    

	<link rel="stylesheet" href="<?php echo site_url('/css/welcome.css');?>" />

    <link rel="stylesheet" href="<?php echo site_url("/css/bootstrap-custom-responsive.css");?>" />    
	<link rel="stylesheet" href="<?php echo site_url('/css/style-responsive.css');?>" />

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-modalmanager.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-modal.js");?>"></script>
    <script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-notify.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/js/plugin/jquery.placeholder.js");?>"></script>

	<script type="text/javascript" src="<?php echo site_url("/js/basic.js");?>"></script>

	<script type="text/javascript">
		var service = {
			url: "<?php echo site_url('/');?>"
		};

		$(function() {
			$(".tip").tooltip({container:'body',placement:'bottom'});
			$("input, textarea").placeholder();
		})
	</script>
	        
<?php echo $styles_for_layout;?>
<?php echo $scripts_for_layout;?>
</head>
<body>        
	<div id="wrap">
	    <div id="fb-root"></div> 
	    <script type="text/javascript" src="http://connect.facebook.net/ko_KR/all.js"></script>
	    <script type="text/javascript"> 
	        FB.init({
	             appId  : '<?php echo $this->config->item('facebook_appid');?>',
	             channelUrl : '<?php echo site_url('/files/channel.php');?>', // Channel File
	             status : true, // check login status
	             cookie : true // enable cookies to allow the server to access the session
	         });
	    </script>

		<div class="navbar navbar-inverse navbar-fixed-top">
		  <div class="navbar-inner">
		    <div class="container">
		    	<div class="logo-wrap">
		      		<a class="brand" href="<?php echo site_url("/");?>">아마도.지도</a>
		      	</div>
		     
		    <!--  <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	          </button> -->

		      
			<ul class="nav nav-pills pull-right">
				<?php if($current_user->id) { ?>
					<li>
					  <div class="btn-group">
					  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $current_user->name;?> <span class="caret"></span></a>
					  <ul class="dropdown-menu user_menu pull-right">
					    <li><a href="<?php echo site_url('/edit');?>">정보수정</a></li>
					    <li class="divider"></li>
					    <li><a href="<?php echo site_url('/logout');?>">로그아웃</a></li>
					  </ul>
					</div>
				  </li>
				  <li class="point">
				  	<a href="<?php echo site_url('/manage/add');?>" class="tip" title="지도 만들기">+</a>
				  </li>
				<?php } else { ?>
				  <li><a href="<?php echo site_url('/login');?>">로그인</a></li>
				  <li class="point"><a href="<?php echo site_url('/join');?>">회원가입</a></li>
				<?php } ?>
			</ul>
		    </div>
		  </div>
		</div>

	    <div id="content" class="main-content">
	    	<div class="main-content-data">
	        	<?php echo $content_for_layout;?>
	        </div>
	    </div>
	</div>
</body>
</html>
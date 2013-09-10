<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap.css");?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap-responsive.css");?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap-custom.css");?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap-modal.css");?>" rel="stylesheet" />
    <link type="text/css" href="<?php echo site_url('/bootstrap/css/bootstrap-notify.css');?>" rel="stylesheet">

	<link type="text/css" rel="stylesheet" href="<?php echo site_url("/css/manage.css");?>" />    
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-modalmanager.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-modal.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-notify.js");?>"></script>	
	<script type="text/javascript" src="<?php echo site_url("/js/basic.js");?>"></script>	

	<script type="text/javascript">
		var service = {url: "<?php echo site_url('/');?>"};
	</script>
</head>
<body>    
	<div class='notifications top-center'></div>

	<div class="navbar navbar-inverse navbar-fixed-top">
	  <div class="navbar-inner">
	    <div class="container">
	      <a class="brand" href="<?php echo site_url("/manage/");?>">
	        아마도.지도
	      </a>
	      <?php if($site->id) { ?>
	      <a class="brand" href="<?php echo site_url($site->permalink."/manage/");?>"><small><?php echo $site->name;?></small></a>
		  <?php } ?>
	      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>

      <?php 
      	if($site->id) {
	      	$menu = isset($menu) ? $menu : '';
			$menu_title = '';
			
			switch($menu) {
				case 'place_approved':
				case 'course_approved': $menu_title = '인증'; break;
				case 'place_pending': 
				case 'course_pending': $menu_title = '대기'; break;
				case 'place_rejected':
				case 'course_rejected': $menu_title = '거부'; break;
				case 'place_all':
				case 'course_all': default: $menu_title = '전체'; break;
			} 
	      ?>
	      <div class="nav-collapse">
		      <ul class="nav">
		        <li<?php echo $menu == "all" ? ' class="active"' : "";?>>
		          <a href="<?php echo site_url($site->permalink."/manage/place");?>">장소</a>
		        </li>
		        <li<?php echo $menu == "course" ? ' class="active"' : "";?>>
		        	<a href="<?php echo site_url($site->permalink."/manage/course");?>">코스</a>
		        </li>
		        <li class="<?php echo in_array($menu, array('place_approved','place_pending','place_rejected','course_approved','course_pending','course_rejected')) ? 'active ' : '';?>dropdown">
		        	<a href="<?php echo site_url($site->permalink."/manage/list/place/all");?>" class="dropdown-toggle" data-toggle="dropdown">상태별 <b class="caret"></b></a>
		        	<ul class="dropdown-menu">
		                <li<?php echo $menu == "place_approved" ? ' class="active"' : "";?>>
			              <a href="<?php echo site_url($site->permalink."/manage/list/place/approved");?>">인증 장소 <span class="badge pull-right"><?php echo $total_place_approved;?></span></a>
			            </li>
			            <li<?php echo $menu == "place_pending" ? ' class="active"' : "";?>>
			              <a href="<?php echo site_url($site->permalink."/manage/list/place/pending");?>">대기 장소 <span class="badge pull-right"><?php echo $total_place_pending;?></span></a>
			            </li>
			            <li<?php echo $menu == "place_rejected" ? ' class="active"' : "";?>>
			              <a href="<?php echo site_url($site->permalink."/manage/list/place/rejected");?>">거부 장소 <span class="badge pull-right"><?php echo $total_place_rejected;?></span></a>
			            </li>
			            <li class="divider"></li>
		                <li<?php echo $menu == "course_approved" ? ' class="active"' : "";?>>
			              <a href="<?php echo site_url($site->permalink."/manage/list/course/approved");?>">인증 코스 <span class="badge pull-right"><?php echo $total_course_approved;?></span></a>
			            </li>
			            <li<?php echo $menu == "course_pending" ? ' class="active"' : "";?>>
			              <a href="<?php echo site_url($site->permalink."/manage/list/course/pending");?>">대기 코스 <span class="badge pull-right"><?php echo $total_course_pending;?></span></a>
			            </li>
			            <li<?php echo $menu == "course_rejected" ? ' class="active"' : "";?>>
			              <a href="<?php echo site_url($site->permalink."/manage/list/course/rejected");?>">거부 코스 <span class="badge pull-right"><?php echo $total_course_rejected;?></span></a>
			            </li>
		            </ul>
		        </li>
		        <li class="divider-vertical"></li>
		        <li class="<?php echo $menu == 'type' ? ' active' : '';?> dropdown">
		        	<a href="#" class="dropdown-toggle" data-toggle="dropdown">설정 <b class="caret"></b></a>

		        	<ul class="dropdown-menu">
		        		<li><a href="<?php echo site_url($site->permalink.'/manage/basic');?>">기본</a>
		        		<li><a href="<?php echo site_url($site->permalink.'/manage/type');?>">분류</a>
		        	</ul>
		        </li>
		      </ul>
		      <ul class="nav pull-right">
		        <li><a href="<?php echo site_url('/'.$site->permalink);?>">사이트 보기</a></li>
		        <li><a href="<?php echo site_url('/logout');?>">로그아웃</a></li>
		      </ul>
	      </div>
	<?php
		}
	?>

	    </div>
	  </div>
	</div>

    <div id="content" class="container">
        <?php echo $content_for_layout;?>
    </div>

    <footer class="footer"></footer>
</body>
</html>
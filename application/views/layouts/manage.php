<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap.css");?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap-custom.css");?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap-modal.css");?>" rel="stylesheet" />
    <link type="text/css" href="<?php echo site_url('/bootstrap/css/bootstrap-notify.css');?>" rel="stylesheet">

	<link type="text/css" rel="stylesheet" href="<?php echo site_url("/css/bootstrap-custom.css");?>" />    

	<link type="text/css" rel="stylesheet" href="<?php echo site_url("/css/style.css");?>" />    
	<link type="text/css" rel="stylesheet" href="<?php echo site_url("/css/manage.css");?>" />    
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-modalmanager.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-modal.js");?>"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap-notify.js");?>"></script>	
	
	<script type="text/javascript" src="<?php echo site_url("/js/lib/basic.js");?>"></script>	

	<script type="text/javascript">
		var service = {url: "<?php echo site_url('/');?>"};
	</script>
</head>
<body> 
	<div id="wrap">
		<div class='notifications top-center'></div>

		<div class="navbar navbar-inverse navbar-fixed-top">
		  <div class="navbar-inner">
		    <div class="container">
		      <div class="logo-wrap">
			      <a class="brand" href="<?php echo site_url("/manage");?>">아마도.지도</a>
			      <?php if($map->id) { ?>
			      <a class="sub-brand" href="<?php echo site_url($map->permalink."/manage/");?>"><?php echo $map->name;?></a>
				  <?php } ?>
			  </div>

		      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	            <span class="icon-bar"></span>
	          </button>

      <?php 
      	if($map->id) {
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
		        <li<?php echo in_array($menu, array("all","cours","place_all")) ? ' class="active"' : "";?>>
		          <a href="<?php echo site_url($map->permalink."/manage/place");?>">장소</a>
		        </li>
		        <li<?php echo in_array($menu,array("course","course_all")) ? ' class="active"' : "";?>>
		        	<a href="<?php echo site_url($map->permalink."/manage/course");?>">코스</a>
		        </li>		        
				<li class="<?php echo in_array($menu, array('place_approved','place_pending','place_rejected','course_approved','course_pending','course_rejected')) ? 'active ' : '';?>dropdown">
		        	<a href="<?php echo site_url($map->permalink."/manage/list/place/all");?>" class="dropdown-toggle" data-toggle="dropdown">상태별 <b class="caret"></b></a>
		        	<ul class="dropdown-menu">
		                <li<?php echo $menu == "place_approved" ? ' class="active"' : "";?>>
			              <a href="<?php echo site_url($map->permalink."/manage/list/place/approved");?>">인증 장소 <span class="badge pull-right"><?php echo $total_place_approved;?></span></a>
			            </li>
			            <li<?php echo $menu == "place_pending" ? ' class="active"' : "";?>>
			              <a href="<?php echo site_url($map->permalink."/manage/list/place/pending");?>">대기 장소 <span class="badge pull-right"><?php echo $total_place_pending;?></span></a>
			            </li>
			            <li<?php echo $menu == "place_rejected" ? ' class="active"' : "";?>>
			              <a href="<?php echo site_url($map->permalink."/manage/list/place/rejected");?>">거부 장소 <span class="badge pull-right"><?php echo $total_place_rejected;?></span></a>
			            </li>
			            <li class="divider"></li>
		                <li<?php echo $menu == "course_approved" ? ' class="active"' : "";?>>
			              <a href="<?php echo site_url($map->permalink."/manage/list/course/approved");?>">인증 코스 <span class="badge pull-right"><?php echo $total_course_approved;?></span></a>
			            </li>
			            <li<?php echo $menu == "course_pending" ? ' class="active"' : "";?>>
			              <a href="<?php echo site_url($map->permalink."/manage/list/course/pending");?>">대기 코스 <span class="badge pull-right"><?php echo $total_course_pending;?></span></a>
			            </li>
			            <li<?php echo $menu == "course_rejected" ? ' class="active"' : "";?>>
			              <a href="<?php echo site_url($map->permalink."/manage/list/course/rejected");?>">거부 코스 <span class="badge pull-right"><?php echo $total_course_rejected;?></span></a>
			            </li>
		            </ul>
		        </li>
		        <?php
		        	if(in_array($current_user->role, array('admin','super-admin'))) { 
		        ?>
		        <li class="<?php echo in_array($menu, array('basic','user','type')) ? ' active' : '';?> dropdown">
		        	<a href="#" class="dropdown-toggle" data-toggle="dropdown">설정 <b class="caret"></b></a>

		        	<ul class="dropdown-menu">
		        		<li<?php echo $menu == "basic" ? ' class="active"' : "";?>><a href="<?php echo site_url($map->permalink.'/manage/basic');?>">기본</a>
		        		<li<?php echo $menu == "user" ? ' class="active"' : "";?>><a href="<?php echo site_url($map->permalink.'/manage/user');?>">사용자</a>
		        		<li<?php echo $menu == "type" ? ' class="active"' : "";?>><a href="<?php echo site_url($map->permalink.'/manage/type');?>">분류</a>
		        		<li class="divider"></li>
		        		<li<?php echo $menu == "import" ? ' class="active"' : "";?>><a href="<?php echo site_url($map->permalink.'/manage/import');?>">가져오기</a></li>
		        		<!--<li<?php echo $menu == "export" ? ' class="active"' : "";?>><a href="<?php echo site_url($map->permalink.'/manage/export');?>">내보내기</a></li>-->
		        		<li<?php echo $menu == "delete" ? ' class="active"' : "";?>><a href="<?php echo site_url($map->permalink.'/manage/delete');?>">삭제</a></li>
		        	</ul>
		        </li>
		        <?php
		        	}
		        ?>
		      </ul>
		      <ul class="nav pull-right">
		        <li><a href="<?php echo site_url('/'.$map->permalink);?>">지도 보기</a></li>
		        <li><a href="<?php echo site_url('/logout');?>">로그아웃</a></li>
		      </ul>
	      </div>
	<?php
		} else {
	?>
      <ul class="nav pull-right">
      	<li><a href="<?php echo site_url('/');?>">홈</a></li>
        <li><a href="<?php echo site_url('/logout');?>">로그아웃</a></li>
      </ul>
	<?php		
		}
	?>

	    </div>
	  </div>
	</div>


    <div id="content" class="main-content">
    	<div class="main-content-data">
			<div class="content-padding">
    	    	<?php echo $content_for_layout;?>
    	    </div>
    	</div>
    </div>
</div>
</body>
</html>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="utf-8">
	<link type="text/css" rel="stylesheet" href="<?php echo site_url("/css/manage.css");?>" />    
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap.css");?>" rel="stylesheet" />
	<link type="text/css" href="<?php echo site_url("/bootstrap/css/bootstrap-responsive.css");?>" rel="stylesheet" />
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo site_url("/bootstrap/js/bootstrap.js");?>"></script>
</head>
<body>    
	
	<div class="navbar navbar-inverse navbar-fixed-top">
	  <div class="navbar-inner">
	    <div class="container">
	      <a class="brand" href="<?php echo site_url("/manage/");?>">
	        아마도.지도
	      </a>
	      <a class="brand" href="<?php echo site_url($site->permalink."/manage/");?>"><small><?php echo $site->name;?></small></a>
	      <?php 
	      	$menu = isset($menu) ? $menu : '';
			$menu_title = '';
			
			switch($menu) {
				case 'approved': $menu_title = '인증'; break;
				case 'pending': $menu_title = '대기'; break;
				case 'rejected': $menu_title = '거부'; break;
				case 'all': default: $menu_title = '전체'; break;
			} 
	      ?>
	      <ul class="nav">
	        <li<?php echo $menu == "all" ? ' class="active"' : "";?>>
	          <a href="<?php echo site_url($site->permalink."/manage/list/all");?>">장소</a>
	        </li>
	        <li>
	        	<a href="#">코스</a>
	        </li>
	        <li class="<?php echo in_array($menu, array('approved','pending','rejected')) ? 'active ' : '';?>dropdown">
	        	<a href="<?php echo site_url($site->permalink."/manage/list/all");?>" class="dropdown-toggle" data-toggle="dropdown">상태별 <b class="caret"></b></a>
	        	<ul class="dropdown-menu">
	                <li<?php echo $menu == "approved" ? ' class="active"' : "";?>>
		              <a href="<?php echo site_url($site->permalink."/manage/list/approved");?>">인증 장소 <span class="badge pull-right"><?php echo $total_approved;?></span></a>
		            </li>
		            <li<?php echo $menu == "pending" ? ' class="active"' : "";?>>
		              <a href="<?php echo site_url($site->permalink."/manage/list/pending");?>">대기 장소 <span class="badge pull-right"><?php echo $total_pending;?></span></a>
		            </li>
		            <li<?php echo $menu == "rejected" ? ' class="active"' : "";?>>
		              <a href="<?php echo site_url($site->permalink."/manage/list/rejected");?>">거부 장소 <span class="badge pull-right"><?php echo $total_rejected;?></span></a>
		            </li>
	            </ul>
	        </li>
	      </ul>
	      <ul class="nav pull-right">
	        <li><a href="<?php echo site_url('/logout');?>">로그아웃</a></li>
	      </ul>
	    </div>
	  </div>
	</div>

    <div id="content" class="container">
        <?php echo $content_for_layout;?>
    </div>
</body>
</html>
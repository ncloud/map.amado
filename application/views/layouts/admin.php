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
	
	<div class='navbar navbar-fixed-top'>
      <div class='navbar-inner'>
        <div class='container'>
          <a class='brand' href='<?php echo site_url('/admin/');?>'>
            아마도.지도
          </a>
          <ul class='nav'>
            <li>
              <a href='<?php echo site_url('/admin/list/all');?>'>전체</a>
            </li>
            <li>
              <a href='<?php echo site_url('/admin/list/approved');?>'>
                인증
                <span class='badge badge-info'><?php echo $total_approved;?></span>
              </a>
            </li>
            <li>
              <a href='<?php echo site_url('/admin/list/pending');?>'>
                대기
                <span class='badge badge-info'><?php echo $total_pending;?></span>
              </a>
            </li>
            <li>
              <a href='<?php echo site_url('/admin/list/rejected');?>''>
                거부
                <span class='badge badge-info'><?php echo $total_rejected;?></span>
              </a>
            </li>
          </ul>
          <form class='navbar-search pull-left' action='index.php' method='get'>
            <input type='text' name='search' class='search-query' placeholder='검색...' autocomplete='off' value='<?php echo isset($search) ? $search : '';?>'>
          </form>
          <ul class='nav pull-right'>
            <li><a href='login.php?task=logout'>로그아웃</a></li>
          </ul>
        </div>
      </div>
    </div>

  <div id='content'>
        <?php echo $content_for_layout;?>
    </div>
</body>
</html>
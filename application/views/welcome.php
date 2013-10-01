<style type="text/css">
	@import url(<?php echo site_url('/css/welcome.css');?>); 
</style>

<div class="container_welcome">
	<div class="masthead">
		<ul class="nav nav-pills pull-right">
		<?php if($current_user->id) { ?>
		  <li><a href="<?php echo site_url('/logout');?>">로그아웃</a></li>
		<?php } else { ?>
		  <li class="active"><a href="<?php echo site_url('/join');?>">회원가입</a></li>
		  <li><a href="<?php echo site_url('/login');?>">로그인</a></li>
		<?php } ?>
		</ul>
		<h3 class="muted">아마도.지도</h3>
	</div>

	<hr />

	<?php if($current_user->id) { ?>
	<div class="jumbotron">
        <a class="btn btn-success" href="<?php echo site_url('/manage/add');?>">지도 만들기</a>
    </div>

    <hr />
	<?php } ?>

	<div class="maps">
		<?php $cut_maps = array_chunk($maps, 2); ?>
		<?php foreach($cut_maps as $cut_map) { ?>
		<div class="row-fluid">
			<?php foreach($cut_map as $map) { ?>
			<div class="map span6">
				<h4><a href="<?php echo site_url($map->permalink);?>"><?php echo $map->name;?></a></h4>
			<?php if(!empty($map->description)) { ?>
				<p><?php echo $map->description;?></p>
			<?php } ?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
</div>
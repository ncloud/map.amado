	
	<hr />

	<?php if($current_user->id) { ?>
	<div class="jumbotron">
        <a class="btn btn-success" href="<?php echo site_url('/manage/add');?>">지도 만들기</a>
    </div>

    <hr />
	<?php } ?>

	<div class="maps">
		<?php if($current_user->id) { ?>
		<div class="page-header">
  			<h4>내 지도</h4>
		</div>
		
		<?php
			if(count($my_maps)) {
		?>
			<?php $cut_maps = array_chunk($my_maps, 2); ?>
			<?php foreach($cut_maps as $cut_map) { ?>
			<div class="all_maps row-fluid">
				<?php foreach($cut_map as $map) { ?>
				<div class="map span6">
					<h4><a href="<?php echo site_url($map->permalink);?>"><?php echo $map->name;?></a> <small><?php echo $map->permalink;?></small></h4>
				<?php if(!empty($map->description)) { ?>
					<p><?php echo $map->description;?></p>
				<?php } ?>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		<?php
			} else {
		?>
			<div class="all_maps row-fluid">
				<div class="empty">직접 만든 지도가 없습니다.</div>
			</div>
		<?php
			}
		?>
		<?php } ?>

		<div class="page-header">
  			<h4>전체 지도</h4>
		</div>
		<?php $cut_maps = array_chunk($maps, 2); ?>
		<?php foreach($cut_maps as $cut_map) { ?>
		<div class="all_maps row-fluid">
			<?php foreach($cut_map as $map) { ?>
			<div class="map span6">
				<h4><a href="<?php echo site_url($map->permalink);?>"><?php echo $map->name;?></a> <small><?php echo $map->permalink;?></small></h4>
			<?php if(!empty($map->description)) { ?>
				<p><?php echo $map->description;?></p>
			<?php } ?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>

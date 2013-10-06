	
	<div class="content-padding">
		<?php if($current_user->id) { ?>
		
		<?php } ?>

		<div class="maps">
			<?php if($current_user->id) { ?>
			<div class="page-header">
	  			<h4>내 지도</h4>
			</div>
			
			<?php
				if(count($my_maps)) {
			?>
				<?php $cut_maps = array_chunk($my_maps, 3); ?>
				<?php foreach($cut_maps as $cut_map) { ?>
				<div class="my_maps row-fluid">
					<?php foreach($cut_map as $map) { ?>
					<div class="map span4">
						<?php if(!empty($map->preview_map_url)) { ?>
						<img class="background" src="<?php echo $map->preview_map_url;?>" alt="" />
						<?php } ?>
						<a class="link" href="<?php echo site_url($map->permalink);?>"></a>
						<div class="info">
							<h5><a href="<?php echo site_url($map->permalink);?>"><?php echo $map->name;?></a> <small><?php echo $map->permalink;?></small></h5>
						</div>

						<div class="manage">
							<a href="<?php echo site_url($map->permalink.'/manage');?>" class="btn btn-small">관리</a>
						</div>
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
			<?php $cut_maps = array_chunk($maps, 3); ?>
			<?php foreach($cut_maps as $cut_map) { ?>
			<div class="all_maps row-fluid">
				<?php foreach($cut_map as $map) { ?>
				<div class="map span4">
					<?php if(!empty($map->preview_map_url)) { ?>
					<img class="background" src="<?php echo $map->preview_map_url;?>" alt="" />
					<?php } ?>
					<a class="link" href="<?php echo site_url($map->permalink);?>"></a>
					<div class="info">
						<h5><a href="<?php echo site_url($map->permalink);?>"><?php echo $map->name;?></a> <small><?php echo $map->permalink;?></small></h5>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
</div>
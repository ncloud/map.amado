  <div class="page-header">
  	<h4>
  	<?php
  		switch($status) {
			case 'all': echo '전체 장소'; break;
			case 'approved': echo '인증 장소'; break;
			case 'pending': echo '대기 장소'; break;
			case 'rejected': echo '거부 장소'; break;
		}
	?>
		<small>(<?php echo $paging->total_count;?>)</small>
  	
	  	<div class="pull-right">

      <div class="btn-group">
        <a class="btn" href="<?php echo site_url($map->permalink.'/manage/add/image');?>">사진 추가</a>
        <a class="btn btn-primary" href="<?php echo site_url($map->permalink.'/manage/add/place');?>">장소 추가</a>
      </div>
	  	</div>
  	</h4>
  </div>
  
  <table class="table table-striped">
  	<thead>
  		<th>#</th>
  		<th>인증</th>
      <th></th>
      <th>분류</th>
  		<th>정보</th>
  		<th></th>
  	</thead>
  	<tbody>
    <?php
    	if($places) {
      foreach($places as $place) {
	?>		  
          <tr>
          	<td class="num">
          		<?php echo $place->id;?>
          	</td>
          	<td class="approved">
            	<?php
            		switch($place->status) {
					case 'approved': echo '<span class="label label-success">인증됨</span>'; break;
					case 'pending': echo '<span class="label">대기중</span>'; break;
					case 'rejected': echo '<span class="label label-important">거부됨</span>'; break;
            		}
				      ?>
			</td>  
      <td class="type">
            <?php
              if($place->attached == 'image') {
            ?>
              <i class="icon-picture"></i>
            <?php
              } else {
            ?>
              <i class="icon-map-marker"></i>
            <?php
              }
            ?>
      </td>
      <td class="category">
        <?php 
            $type_name = $place->type_name;
            if(empty($type_name)) $type_name = '분류없음';
            
            if($current_user->role == 'super-admin' || $current_user->role == 'admin') {  ?>
            <div class="dropdown">
              <a class="dropdown-toggle" id="drop4" role="button" data-toggle="dropdown" href="#"><?php echo $type_name;?> <b class="caret"></b></a>
              <ul id="menu1" class="dropdown-menu">
              <?php
                foreach($place_types as $place_type) {
              ?>            
              <li<?php echo $place->type_id == $place_type->id ? ' class="disabled"' : '';?>><a role="menuitem" tabindex="-1" href="<?php echo site_url($map->permalink.'/manage/type/change/'.$place->id.'/'.$place_type->id);?>"><?php echo $place_type->name;?></a></li>
              <?php
                }

                if(count($place_types)) {
              ?>
                <li class="divider"></li>
              <?php
                }
              ?>
                <li><a role="menuitem" tabindex="-1" href="<?php echo site_url($map->permalink.'/manage/type/change/'.$place->id.'/none');?>">분류없음</a></li>
              </ul>
            </div>

        <?php } else { 
                echo '<span>' . $type_name . '</span>';
              }
        ?>
      </td>
      <td class='info'>
          <?php echo $place->title;?>
          <?php
          	if(!empty($place->uri)) {
          ?>
          <a href='<?php echo $place->uri;?>' target='_blank'>
          	<span class='url'>
          		<?php echo $place->uri;?>
          	</span>
        	</a>
        	<?php
		}
	?>
      </td>
      <td class='buttons'>
      <?php if(in_array($current_user->role, array('admin', 'super-admin'))) { ?>
      	<div class="btn-group">
          <a class="btn btn-<?php echo $place->status == 'approved' ? 'success' : 'danger';?> btn-small dropdown-toggle" data-toggle="dropdown" href="#">인증 처리 <span class="caret"></span></a>				 
      	  <ul class="dropdown-menu">
      	     <li<?php echo $place->status == 'approved' ? ' class="disabled"' : '';?>><a tabindex="-1" href="<?php echo site_url($map->permalink.'/manage/'.($place->attached=='image'?'image':'place').'/change/status/'.$place->id.'/approved');?>">인증하기</a></li>
      	     <li<?php echo $place->status == 'rejected' ? ' class="disabled"' : '';?>><a tabindex="-1" href="<?php echo site_url($map->permalink.'/manage/'.($place->attached=='image'?'image':'place').'/change/status/'.$place->id.'/rejected');?>">거부하기</a></li>
      	     <li class="divider"></li>
      	     <li<?php echo $place->status == 'pending' ? ' class="disabled"' : '';?>><a tabindex="-1" href="<?php echo site_url($map->permalink.'/manage/'.($place->attached=='image'?'image':'place').'/change/status/'.$place->id.'/pending');?>">대기하기</a></li>
      	  </ul>
      	</div>
      <?php } ?>
      	<div class="btn-group">
        <?php if($current_user->id == $place->user_id || in_array($current_user->role, array('admin', 'super-admin'))) { ?>
      		<a class='btn btn-small' href="<?php echo site_url($map->permalink.'/manage/'.($place->attached=='image'?'image':'place').'/edit/'.$place->id);?>">편집</a>
        <?php } ?>
      	</div>
      </td>
    </tr>
  <?php
      }
		}
		else {
	?>
		<tr><td colspan="5"><div class="text-center">장소가 비어있습니다.</div></td></tr>
	<?php
		}
    ?>
    </tbody>
  </table>
  
  <?php if($paging->max > 1) { ?>
  <div class="pagination pagination-centered">
    <ul>
        <li<?php echo $paging->page == 1 ? ' class="disabled"' : '';?>>
          <a href="<?php echo $paging->page == 1 ? '#' : site_url($map->permalink.'/manage/list/place/'.$status.'/'.($paging->page-1));?>">&larr;</a>
        </li>
      <?php
      	for($page = $paging->start; $page <= $paging->end ; $page++) {
      ?>
        <li<?php echo $page == $paging->page ? ' class="active"' : '';?>><a href="<?php echo site_url($map->permalink.'/manage/list/place/'.$status.'/'.$page);?>"><?php print_r($page);?></a></li>
      <?php } ?>
        <li<?php echo $paging->page >= $paging->max ? ' class="diabled"' : '';?>>
          <a href="<?php echo $paging->page < $paging->max ? '#' : site_url($map->permalink.'/manage/list/place/'.$status.'/'.($paging->page+1));?>">&rarr;</a>
        </li>
    </ul>
  </div>
  <?php } ?>
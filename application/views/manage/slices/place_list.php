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
        <a class="btn" href="<?php echo site_url($site->permalink.'/manage/add');?>">장소 추가</a>
        <a class="btn" href="<?php echo site_url($site->permalink.'/manage/add/image');?>">사진 추가</a>
      </div>
	  	</div>
  	</h4>
  </div>
  
  <table class="table table-striped">
  	<thead>
  		<th>#</th>
  		<th>인증</th>
      <th></th>
  		<th>정보</th>
  		<th></th>
  	</thead>
  	<tbody>
    <?php
    	if(count($places)) {
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
      	<div class="btn-group">
          <a class="btn btn-<?php echo $place->status == 'approved' ? 'success' : 'danger';?> btn-small dropdown-toggle" data-toggle="dropdown" href="#">인증 처리 <span class="caret"></span></a>				 
	  <ul class="dropdown-menu">
	     <li<?php echo $place->status == 'approved' ? ' class="disabled"' : '';?>><a tabindex="-1" href="<?php echo site_url($site->permalink.'/manage/change/status/'.$place->id.'/approved');?>">인증하기</a></li>
	     <li<?php echo $place->status == 'rejected' ? ' class="disabled"' : '';?>><a tabindex="-1" href="<?php echo site_url($site->permalink.'/manage/change/status/'.$place->id.'/rejected');?>">거부하기</a></li>
	     <li class="divider"></li>
	     <li<?php echo $place->status == 'pending' ? ' class="disabled"' : '';?>><a tabindex="-1" href="<?php echo site_url($site->permalink.'/manage/change/status/'.$place->id.'/pending');?>">대기하기</a></li>
	  </ul>
	</div>
	<div class="btn-group">
		<a class='btn btn-small' href="<?php echo site_url($site->permalink.'/manage/edit/'.$place->id);?>">편집</a>
	</div>
      </td>
    </tr>
  <?php
      }
		}
		else {
	?>
		<tr><td colspan="5"><div class="text-center">목록의 내용이 비어있습니다.</div></td></tr>
	<?php
		}
    ?>
    </tbody>
  </table>
  
  <?php if($paging->max > 1) { ?>
  <div class="pagination pagination-centered">
    <ul>
        <li<?echo $paging->page == 1 ? ' class="disabled"' : '';?>>
          <a href="<?php echo $paging->page == 1 ? '#' : site_url($site->permalink.'/manage/list/'.$status.'/'.($paging->page-1));?>">&larr;</a>
        </li>
      <?php
      	for($page = $paging->start; $page <= $paging->end ; $page++) {
      ?>
        <li<?php echo $page == $paging->page ? ' class="active"' : '';?>><a href="<?php echo site_url($site->permalink.'/manage/list/'.$status.'/'.$page);?>"><?php print_r($page);?></a></li>
      <?php } ?>
        <li<?echo $paging->page >= $paging->max ? ' class="diabled"' : '';?>>
          <a href="<?php echo $paging->page < $paging->max ? '#' : site_url($site->permalink.'/manage/list/'.$status.'/'.($paging->page+1));?>">&rarr;</a>
        </li>
    </ul>
  </div>
  <?php } ?>
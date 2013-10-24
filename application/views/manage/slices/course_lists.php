  <div class="page-header">
    <h4>
    <?php
      switch($status) {
      case 'all': echo '전체 코스'; break;
      case 'approved': echo '인증 코스'; break;
      case 'pending': echo '대기 코스'; break;
      case 'rejected': echo '거부 코스'; break;
    }
  ?> <small>(<?php echo $paging->total_count;?>)</small>
    
      <div class="pull-right">

      <div class="btn-group">
        <a class="btn btn-primary" href="<?php echo site_url($map->permalink.'/manage/add/course');?>">코스 추가</a>
      </div>
      </div>
    </h4>
  </div>
  
  <table class="table table-striped">
    <thead>
      <th>#</th>
      <th>인증</th>
      <th>정보</th>
      <th></th>
    </thead>
    <tbody>
  <?php
      if($courses) {
      foreach($courses as $course) {
  ?>      
          <tr>
            <td class="num">
              <?php echo $course->id;?>
            </td>
            <td class="approved">
              <?php
                switch($course->status) {
          case 'approved': echo '<span class="label label-success">인증됨</span>'; break;
          case 'pending': echo '<span class="label">대기중</span>'; break;
          case 'rejected': echo '<span class="label label-important">거부됨</span>'; break;
                }
        ?>
      </td>  
      <td class='info'>
          <?php echo $course->title;?>
          <?php
            if(!empty($course->uri)) {
          ?>
          <a href='<?php echo $course->uri;?>' target='_blank'>
            <span class='url'>
              <?php echo $course->uri;?>
            </span>
          </a>
          <?php
    }
  ?>
      </td>
      <td class='buttons'>
        <div class="btn-group">
          <a class="btn btn-<?php echo $course->status == 'approved' ? 'success' : 'danger';?> btn-small dropdown-toggle" data-toggle="dropdown" href="#">인증 처리 <span class="caret"></span></a>         
    <ul class="dropdown-menu">
       <li<?php echo $course->status == 'approved' ? ' class="disabled"' : '';?>><a tabindex="-1" href="<?php echo site_url($map->permalink.'/manage/course/change/status/'.$course->id.'/approved');?>">인증하기</a></li>
       <li<?php echo $course->status == 'rejected' ? ' class="disabled"' : '';?>><a tabindex="-1" href="<?php echo site_url($map->permalink.'/manage/course/change/status/'.$course->id.'/rejected');?>">거부하기</a></li>
       <li class="divider"></li>
       <li<?php echo $course->status == 'pending' ? ' class="disabled"' : '';?>><a tabindex="-1" href="<?php echo site_url($map->permalink.'/manage/course/change/status/'.$course->id.'/pending');?>">대기하기</a></li>
    </ul>
  </div>
  <div class="btn-group">
    <a class='btn btn-small' href="<?php echo site_url($map->permalink.'/manage/course/edit/'.$course->id);?>">편집</a>
  </div>
      </td>
    </tr>
    <?php
      }
    }
    else {
  ?>
    <tr><td colspan="4"><div class="text-center">코스가 비어있습니다.</div></td></tr>
  <?php
    }
    ?>
    </tbody>
  </table>
  
  <?php if($paging->max > 1) { ?>
  <div class="pagination pagination-centered">
    <ul>
        <li<?php echo $paging->page == 1 ? ' class="disabled"' : '';?>>
          <a href="<?php echo $paging->page == 1 ? '#' : site_url($map->permalink.'/manage/list/course/'.$status.'/'.($paging->page-1));?>">이전</a>
        </li>
      <?php
        for($page = $paging->start; $page <= $paging->end ; $page++) {
      ?>
        <li<?php echo $page == $paging->page ? ' class="active"' : '';?>><a href="<?php echo site_url($map->permalink.'/manage/list/course/'.$status.'/'.$page);?>"><?php print_r($page);?></a></li>
      <?php } ?>
        <li<?php echo $paging->page >= $paging->max ? ' class="diabled"' : '';?>>
          <a href="<?php echo $paging->page < $paging->max ? '#' : site_url($map->permalink.'/manage/list/course/'.$status.'/'.($paging->page+1));?>">다음</a>
        </li>
    </ul>
  </div>
  <?php } ?>
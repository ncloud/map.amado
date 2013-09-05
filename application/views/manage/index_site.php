  <div class="page-header">
  	<h4> 
  		전체 사이트
		<small>(<?php echo $paging->total_count;?>)</small>
  	
	  	<div class="pull-right">
	      <div class="btn-group">
	        <a class="btn" href="<?php echo site_url('/manage/add');?>">사이트 추가</a>
	      </div>
	  	</div>
  	</h4>
  </div>
  
  <table class="table table-striped">
  	<thead>
  		<th>#</th>
	    <th>제목</th>
	    <th class="author">제작</th>
  	</thead>
  	<tbody>
    <?php
    	if($sites) {
      foreach($sites as $site) {
	?>		  
          <tr>
          	<td class="num">
          		<?php echo $site->id;?>
          	</td>
      		<td>
      			<a href="<?php echo site_url($site->permalink.'/manage');?>"><?php echo $site->name;?></a>
	      	</td>
	      	<td>
	      		<?php echo $site->user_name;?>
	      	</td>
	    </tr>
  <?php
      }
		}
		else {
	?>
		<tr><td colspan="3"><div class="text-center">사이트가 비어있습니다.</div></td></tr>
	<?php
		}
    ?>
    </tbody>
  </table>
  
  <?php if($paging->max > 1) { ?>
  <div class="pagination pagination-centered">
    <ul>
        <li<?php echo $paging->page == 1 ? ' class="disabled"' : '';?>>
          <a href="<?php echo $paging->page == 1 ? '#' : site_url($site->permalink.'/manage/'.($paging->page-1));?>">&larr;</a>
        </li>
      <?php
      	for($page = $paging->start; $page <= $paging->end ; $page++) {
      ?>
        <li<?php echo $page == $paging->page ? ' class="active"' : '';?>><a href="<?php echo site_url($site->permalink.'/manage/'.$page);?>"><?php print_r($page);?></a></li>
      <?php } ?>
        <li<?php echo $paging->page >= $paging->max ? ' class="diabled"' : '';?>>
          <a href="<?php echo $paging->page < $paging->max ? '#' : site_url($site->permalink.'/manage/'.($paging->page+1));?>">&rarr;</a>
        </li>
    </ul>
  </div>
  <?php } ?>
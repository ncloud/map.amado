  <div class="page-header">
  	<h4>전체 목록</h4>
  </div>
  
  <table class="table table-striped">
  	<thead>
  		<th>#</th>
  		<th>정보</th>
  		<th></th>
  	</thead>
  	<tbody>
    <?php
      foreach($places as $place) {
        $place->uri = str_replace(array('http://', 'https://', ''), '', $place->uri);
	?>		  
          <tr>
          	<td class="num">
          		<?php echo $place->id;?>
          	</td>
            <td class='info'>
              <a href='http://<?php echo $place->uri;?>' target='_blank'>
                <?php echo $place->title;?>
                <span class='url'>
                	<?php echo $place->uri;?>
                </span>
              </a>
            </td>
            <td class='buttons'>
              <a class='btn btn-small' href=<?php echo site_url('/admin/edit/'.$place->id);?>)>편집</a>
            </td>
          </tr>
    <?php
      }
    ?>
    </tbody>
  </table>
  
  <?php if($paging->max > 1) { ?>
    <ul class="paging">
      <?php if($paging->page > 1) { ?>
        <li class="previous">
          <a href="#">&larr; 이전</a>
        </li>
      <?php } ?>
      <?php if($paging->page < $paging->max) { ?>
        <li class="next">
          <a href="#">다음 &rarr;</a>
        </li>
      <?php } ?>
    </ul>
  <?php } ?>
  <?php if($paging->max > 1) { ?>
    <ul class="pager">
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
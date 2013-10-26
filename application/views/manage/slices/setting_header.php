        <div class="content-sidebar">
          <div class="sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">설정</li>              
              <li<?php echo $menu == 'basic' ? ' class="active"' : '';?>><a href="<?php echo site_url($map->permalink.'/manage/basic');?>">기본</a></li>
              <li<?php echo $menu == 'user' ? ' class="active"' : '';?>><a href="<?php echo site_url($map->permalink.'/manage/user');?>">사용자</a></li>
              <li<?php echo $menu == 'type' ? ' class="active"' : '';?>><a href="<?php echo site_url($map->permalink.'/manage/type');?>">분류</a></li>
              <li class="divider"></li>
              <li class="nav-header">관리</li>              
              <li<?php echo $menu == 'import' ? ' class="active"' : '';?>><a href="<?php echo site_url($map->permalink.'/manage/import');?>">가져오기</a></li>
              <!--<li<?php echo $menu == 'export' ? ' class="active"' : '';?>><a href="<?php echo site_url($map->permalink.'/manage/export');?>">내보내기</a></li>-->
              <li<?php echo $menu == 'delete' ? ' class="active"' : '';?>><a href="<?php echo site_url($map->permalink.'/manage/delete');?>">삭제</a></li>
            </ul>
          </div><!--/.well -->
        </div>
        <div class="content-main">
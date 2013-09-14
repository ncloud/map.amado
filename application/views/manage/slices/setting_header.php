<div class="container-fluid">
      <div class="row-fluid">
        <div class="span2">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">설정</li>              
              <li<?php echo $menu == 'basic' ? ' class="active"' : '';?>><a href="<?php echo site_url($site->permalink.'/manage/basic');?>">기본</a></li>
              <li<?php echo $menu == 'user' ? ' class="active"' : '';?>><a href="<?php echo site_url($site->permalink.'/manage/user');?>">사용자</a></li>
              <li<?php echo $menu == 'type' ? ' class="active"' : '';?>><a href="<?php echo site_url($site->permalink.'/manage/type');?>">분류</a></li>
            </ul>
          </div><!--/.well -->
        </div>
        <div class="span10">
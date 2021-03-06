<?php
  echo $this->view('/manage/slices/setting_header');
  
  $errors = array();
?>
<form id="addform" action="<?php echo site_url($map->permalink.'/manage/basic');?>" class="form-horizontal" method="post">

  <div class="page-header">
    <?php if(isset($message) && !empty($message)) { ?>
    <div class="alert alert-<?php echo $message->type;?>">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <?php
        if(is_array($message->content)) {
          $errors = $message->content;
          echo '에러가 발생했습니다';
        } else {
          echo $message->content;
        }
      ?>
    </div>
    <?php
      }
    ?>
     <h3>기본 설정</h3>
  </div> <!-- header -->

  <fieldset>
        <div class="control-group<?php echo isset($errors['privacy']) ? ' error' : '';?>">
          <label class="control-label" for="map_privacy">공개</label>
          <div class="controls">
            <div class='radio-group'>
              <label class="radio inline">
                <input type="radio" name="privacy" value="public"<?php echo $map_data->privacy == 'public' ? ' checked="checked"' : '';?>> 공개
              </label>
              <label class="radio inline">
                <input type="radio" name="privacy" value="private"<?php echo $map_data->privacy == 'private' ? ' checked="checked"' : '';?>> 비공개
              </label>
            </div>
            <span class="help-block">
              비공개로 설정하시면 일반 방문객은 지도를 볼 수 없으며 로그인한 사용자의 권한에 따라 볼 수 있습니다.
            </span>
          </div>
        </div>  

        <div class="control-group<?php echo isset($errors['is_viewed_home']) ? ' error' : '';?>">
          <label class="control-label" for="is_viewed_home">홈 등록</label>
          <div class="controls">
            <div class='radio-group'>
              <label class="radio inline">
                <input type="radio" name="is_viewed_home" value="yes"<?php echo $map_data->is_viewed_home == 'yes' ? ' checked="checked"' : '';?>> 등록
              </label>
              <label class="radio inline">
                <input type="radio" name="is_viewed_home" value="no"<?php echo $map_data->is_viewed_home == 'no' ? ' checked="checked"' : '';?>> 등록안함
              </label>
            </div>
            <span class="help-block">
              홈에 등록하시면 "아마도.지도" 홈에 등록되어 더 많은 방문객들이 접근할 수 있습니다.
            </span>
          </div>
        </div>  

        <hr />


        <div class="control-group<?php echo isset($errors['default_menu']) ? ' error' : '';?>">
          <label class="control-label" for="default_menu">기본 보기</label>
          <div class="controls">
            <div class='radio-group'>
              <label class="radio inline">
                <input type="radio" name="default_menu" value="course"<?php echo $map_data->default_menu == 'course' ? ' checked="checked"' : '';?>> 코스
              </label>
              <label class="radio inline">
                <input type="radio" name="default_menu" value="type"<?php echo $map_data->default_menu == 'type' ? ' checked="checked"' : '';?>> 분류
              </label>
            </div>
            <span class="help-block">
              지도에 처음 접근시 보이는 기본 메뉴를 정합니다. 코스가 없을때는 분류가 기본으로 보여집니다.
            </span>
          </div>
        </div>

        <div class="control-group<?php echo isset($errors['add_role']) ? ' error' : '';?>">
          <label class="control-label" for="map_add_role">추가 권한</label>
          <div class="controls">
            <select id="map_add_role" name="add_role">
              <option value="guest"<?php echo $map_data->add_role=='guest' ? ' selected="selected"' : '';?>>Guest</option>
              <option value="member"<?php echo $map_data->add_role=='member' ? ' selected="selected"' : '';?>>Member</option>
              <option value="workman"<?php echo $map_data->add_role=='workman' ? ' selected="selected"' : '';?>>Workman</option>
              <option value="admin"<?php echo $map_data->add_role=='admin' ? ' selected="selected"' : '';?>>Admin</option>
            </select>
            <span class="help-block">
              장소를 추가할 수 있는 사용자의 권한을 지정합니다.
            </span>
          </div>
        </div>  

        <div class="control-group<?php echo isset($errors['name']) ? ' error' : '';?>">
          <label class="control-label" for="map_name">지도명</label>
          <div class="controls">
            <input type="text" id="map_name" name="name" value="<?php echo $map_data->name;?>" />
          </div>
        </div>

        <div class="control-group<?php echo isset($errors['description']) ? ' error' : '';?>">
          <label class="control-label" for="map_description">설명</label>
          <div class="controls">
            <textarea id="map_description" class="span6" name="description"><?php echo $map_data->description;?></textarea>
          </div>
        </div>

        <div class="control-group<?php echo isset($errors['permalink']) ? ' error' : '';?>">
          <label class="control-label" for="map_permalink">주소</label>
          <div class="controls">
            <input type="text" id="map_permalink" name="permalink" value="<?php echo $map_data->permalink;?>" />
            <span class="help-block">
              주소는 "<?php echo site_url('/');?>주소"와 같이 지도에 직접 접속할 수 있는 방법을 제공합니다.
            </span>
          </div>
        </div>
  </fieldset>

  <div class="form-actions">
      <button type="submit" class="btn btn-primary">변경사항 저장</button>  
  </div>
</form>

<?php
  echo $this->view('/manage/slices/setting_footer');
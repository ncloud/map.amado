<?php
  echo $this->view('/manage/slices/setting_header');
  $errors = array();
?>

<form id="importform" enctype="multipart/form-data" action="<?php echo site_url($map->permalink.'/manage/import');?>" class="form-horizontal" method="post">

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
     <h3>가져오기</h3>
  </div> <!-- header -->

  <fieldset>
	    <div class="control-group<?php echo isset($errors['status']) ? ' error' : '';?>">
          <label class="control-label" for="status">기본 상태</label>
          <div class="controls">
            <select id="status" name="status">
              <option value="pending"<?php echo $import_data->status=='pending' ? ' selected="selected"' : '';?>>대기</option>
              <option value="approved"<?php echo $import_data->status=='approved' ? ' selected="selected"' : '';?>>인증</option>
              <option value="rejected"<?php echo $import_data->status=='rejected' ? ' selected="selected"' : '';?>>거부</option>
            </select>

            <p class="help-block">
            	가져오는 장소들의 기본 상태를 지정합니다.
            </p>
          </div>
        </div>

        <div class="control-group<?php echo isset($errors['url']) ? ' error' : '';?>">
          <label class="control-label" for="url">URL</label>
          <div class="controls">
            <input type="text" id="url" name="url" class="span5" value="<?php echo $import_data->url;?>" />

            <p class="help-block">
            	가져오기에서 지원하는 파일 포맷은 KML입니다. KML 파일이 존재하는 전체 URL을 입력해주세요.<br />
            	현재는 KML파일의 Placemark 만 지원합니다.
            </p>
          </div>
        </div>

  </fieldset>

  <div class="form-actions">
      <button type="submit" class="btn btn-primary">가져오기</button>  
  </div>

</form>

<?php
  echo $this->view('/manage/slices/setting_footer');
?>
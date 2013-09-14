

<form id="addform" method="post" class="form-horizontal">
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
	<?php } ?>
  	<h3>사이트 추가</h3>
  </div>

  <div class="control-group<?php echo isset($errors['name']) ? ' error' : '';?>">
    <label class="control-label" for="site_name">사이트명</label>
    <div class="controls">
      <input type="text" id="site_name" name="name" value="<?php echo $site_data->name;?>" />
    </div>
  </div>

  <div class="control-group<?php echo isset($errors['permalink']) ? ' error' : '';?>">
    <label class="control-label" for="site_permalink">주소</label>
    <div class="controls">
      <input type="text" id="site_permalink" name="permalink" value="<?php echo $site_data->permalink;?>" />
      <span class="help-block">
      	주소는 "<?php echo site_url('/');?>주소"와 같이 지도에 직접 접속할 수 있는 방법을 제공합니다.
      </span>
    </div>
  </div>
  <!--
  	// TODO: 분류템플릿 기능
  <div class="control-group">
    <label class="control-label" for="site_type_template">분류 템플릿</label>
    <div class="controls">
		<select id="site_type_template" name="type_template">
		  <option value="none">사용안함</option>
		</select>
    </div>
  </div>
  -->

  <div class="form-actions">
	  <button type="submit" class="btn btn-primary">만들기</button>
	  <button type="button" class="btn">취소</button>
  </div>
</form>
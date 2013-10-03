<?php
	$errors = array();
?>

<form id="editform" class="form-horizontal" method="post">
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

  </div>

  <fieldset>
    <legend>정보수정</legend>
    <div class="control-group<?php echo isset($errors['name']) ? ' error' : '';?>">
      <label class="control-label" for="name">이름</label>
      <div class="controls">
		    <input id="name" type="text" class="span3" name="name" value="<?php echo $user_data->name;?>" />
      </div>
    </div>
    <div class="control-group<?php echo isset($errors['email']) ? ' error' : '';?>">
      <label class="control-label" for="email">이메일</label>
      <div class="controls">
        <input id="email" type="text" class="span3" name="email" value="<?php echo $user_data->email;?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">비밀번호</label>
      <div class="controls">
        <a href="#" class="btn">비밀번호 변경</a>
      </div>
    </div>
  </fieldset>

  <div class="form-actions">
      <button type="submit" class="btn btn-primary">변경사항 저장</button>
      <a href="<?php echo site_url('/');?>" class="btn">취소</a>
  </div>
</form>

<script type="text/javascript">
</script>

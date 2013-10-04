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
        <a href="#myModal" role="button" class="btn" data-toggle="modal">비밀번호 변경</a>
      </div>
    </div>
  </fieldset>

  <div class="form-actions">
      <button type="submit" class="btn btn-primary">변경사항 저장</button>
      <a href="<?php echo site_url('/');?>" class="btn">취소</a>
  </div>
</form>

<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <form method="POST" action="<?php echo site_url('/ajax/change_password');?>" class="form-horizontal" onsubmit="onChangePassword(this); return false;">

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">비밀번호 변경</h3>
  </div>
  <div class="modal-body" style="position:relative;">
      <fieldset>
        <div class="control-group">
          <label class="control-label" for="old_password">현재 비밀번호</label>
          <div class="controls">
            <input id="old_password" type="password" class="span3" name="old_password" value="" />
          </div>
        </div>
        <hr />
        <div class="control-group">
          <label class="control-label" for="new_password">새로운 비밀번호</label>
          <div class="controls">
            <input id="new_password" type="password" class="span3" name="new_password" value="" />
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="new_pasword_re">새로운 비밀번호 확인</label>
          <div class="controls">
            <input id="new_pasword_re" type="password" class="span3" name="new_pasword_re" value="" />
            <p class="help-block">
              잘못된 입력을 맞기 위해 새로운 비밀번호를 한번 더 입력해주세요.
            </p>
          </div>
        </div>
      </fieldset>
    </form>
  </div>
  <div class="modal-footer">    
    <button class="btn btn-primary" onclick="doChangePassword(); return false;">변경하기</button>
  </div>
  </form>
</div>

<script type="text/javascript">
  function doChangePassword() {
    $("#myModal").find('form').submit();
  }

  function onChangePassword(form) {
    if(form.old_password.value == '') {
      form.old_password.focus();
      return false;
    }

    if(form.new_password.value == '') {
      form.new_password.focus();
      return false;
    }

    if(form.new_password_re.value == '') {
      form.new_pasword_re.focus();
      return false;
    }

    if(form.new_password.value != form.new_pasword_re.value) {
      alert('새로운 비밀번호와 새로운 비밀번호 확인을 같게 입력해주세요.');

      return false;
    }

    return true;
  }
</script>

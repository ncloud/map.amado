<?php
	if($message->type == 'error') {
?>

  <div class="page-header">
  	<h4>에러가 발생했습니다.</h4>
  </div>
  <div class="alert alert-error">
	<?php echo $message->message;?>
  </div>

<?php
	} else {
?>
  <div class="page-header">
  	<h4>환영합니다</h4>
  </div>

  <h5>아마도 지도 [<?php echo $site->name;?>]에 초대되셨습니다.</h5>

  <?php
  	if(empty($current_user->id)) {
  ?>
  	회원로그인이 필요합니다. <a href="<?php echo site_url('/login');?>?redirect_uri=<?php echo urlencode(current_url());?>">로그인</a> 또는 <a href="<?php echo site_url('/join');?>?redirect_uri=<?php echo urlencode(current_url());?>">회원가입</a> 후 계속 진행해주세요.
  <?php
  	} else {
  ?>
	
  <p>
    초대를 수락하시면 지도 [<?php echo $site->name;?>]에서 "<?php echo $role->role_name;?>"권한으로 아래와 같은 권한 내용을 가집니다. <br />
  </p>
  <p>	
	<?php echo $role->role_description;?>
  </p>

  <div class="form-actions">
  	<a href="#" class="btn btn-primary">초대 수락</a>
  </div>

  <?php
  	}
  }
  ?>
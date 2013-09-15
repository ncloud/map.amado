<?php
  echo $this->view('/manage/slices/setting_header');
  
  $errors = array();
?>
<form id="addform" action="<?php echo site_url($site->permalink.'/manage/basic');?>" class="form-horizontal" method="post">

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
    <h3>사용자 설정</h3>
  </div> <!-- header -->

  <fieldset>
  	<table class="table">
  		<thead>
  			<th>이름</th>
  			<th>권한</th>
  			<th>설명</th>
  			<th>상태</th>
  			<th></th>
  		</thead>
  		<tbody>
  	<?php
  		foreach($users as $user) {
  	?>
  		<tr>
  			<td><?php echo $user->name;?></td>
  			<td><?php echo $user->role_name;?></td>
  			<td><?php echo $user->role_description;?></td>
  			<td>
  				<?php
  					switch($user->role_invite_status) {
  						case 'send_email':
  							echo '초대 이메일 발송';
  						break;
              case 'invited':
                echo '인증 완료';
              break;
  					}
  				?>
  			</td>
  			<td>
  				<?php if($site->user_id != $user->id) { ?>
  				
          <?php
            switch($user->role_invite_status) {
              case 'send_email':
                echo '<a href="' . site_url($site->permalink.'/manage/invite_cancel/'.$user->role_invite_code) . '" class="btn btn-small">초대취소</a>';
              break;
              case 'invited':
                echo '<a href="' . site_url($site->permalink.'/manage/invite_cancel/'.$site->id.'/'.$user->id) . '" class="btn btn-small">권한취소</a>';
              break;
            }
          ?>

  				<?php } ?>
  			</td>
  		</tr>
  	<?php
  		}
  	?>
  		</tbody>
  	</table>

	<div>
        <a class="btn" href="#" onclick="User.inviteWindow(); return false;">초대하기</a>
    </div>
   
  </fieldset>
</form>

<div class="modal fade" id="inviteModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editForm" class="form-horizontal" action="<?php echo site_url($site->permalink.'/manage/user/invite');?>" method="post" onsubmit="User.doInvite(this); return false;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">초대하기</h4>
        </div>
        <div class="modal-body">
          <div class="control-group">
            <label class="control-label" for="invite_email">이메일</label>
            <div class="controls">
              <input type="text" id="invite_email" name="email" />
            </div>
          </div>
          <div class="control-group">
            <label class="control-label" for="invite_privacy">권한</label>
            <div class="controls">
				<select id="invite_privacy" name="privacy" class="span4">
				<?php
					foreach($roles as $role) {
						if($role->name == 'super-admin') continue;
				?>
					<option value="<?php echo $role->name;?>"><?php echo ucfirst($role->name);?> : <?php echo $role->description;?></option>
				<?php
					}
				?>
				</select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
          <input type="submit" class="btn btn-primary" value="초대" />
        </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php 
	$privacies = array();
	foreach($roles as $role) {
		if($role->name == 'super-admin') continue;
		$privacies[] = '"' . $role->name . '"';
	}
?>

  <script type="text/javascript">
  	var User = function() {
  		var self = this;

  		self.doInvite = function(form) {
  			var $email = $(form.email);
  			if($email.val() == '') {
  				$email.focus();
  				return false;
  			} else if(!isEmail($email.val())) {
  				$email.focus();
  				return false;
  			}

  			var email = $email.val();
  			var privacy = 'member';
  			if($.inArray(form.privacy.value, [<?php echo implode(',',$privacies);?>]) >= 0) {
  				privacy = form.privacy.value;
  			}

  			$.ajax({
  				url:'<?php echo site_url('/ajax/add_role/'.$site->id);?>/' , 
  				data: 'email=' + encodeURIComponent(email) + '&privacy=' + encodeURIComponent(privacy),
  				type: 'POST',
  				dataType: 'json',
  				success: function(data) {
            $("#inviteModal").modal('hide');

            reload();
	  			}, error: function(data) {
	  			}
	  		});

  			return false;
  		}

  		self.inviteWindow = function() {
  			$("#inviteModal").modal();
  		}
  	}

  	var User = new User();
  </script>

<?php
  echo $this->view('/manage/slices/setting_footer');
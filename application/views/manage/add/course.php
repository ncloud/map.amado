<?php
	$edit_mode = isset($edit_mode) ? $edit_mode : false;
	$modal_mode = isset($modal_mode) ? $modal_mode : false;
	
	$errors = array();
?>

<form id="addform" action="<?php echo $edit_mode ? site_url($site->permalink.'/manage/course/edit/'.$course->id) :  site_url($site->permalink.'/manage/add/course');?>" class="form-horizontal<?php echo $modal_mode ? ' modal-form' : '';?>" method="post">
  <div class="<?php echo $modal_mode ? 'modal' : 'page';?>-header">  
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
  <?php
  	if($modal_mode) {
  ?>
  <button type="button" class="close" data-dismiss="modal">×</button>
  <?php		
  	} 
  ?>
  <?php
  	if($edit_mode) {
  ?>
  	<h3>코스 편집</h3>
  <?php		
  	} else {
  ?>
  	<h3>코스 추가</h3>
  <?php
	}
  ?>
  </div>
  <?php if($modal_mode) { ?>
  <div class="modal-body"> 
  <?php } ?>
  <fieldset>
    <div class="control-group<?php echo isset($errors['title']) ? ' error' : '';?>">
      <label class="control-label" for="course_title">이름 *</label>
      <div class="controls">
        <input type="text" id="course_title" class="span4" name="title" value="<?php echo isset($course) ? $course->title : ''?>" />
      </div>
    </div>
    <div class="control-group<?php echo isset($errors['permalink']) ? ' error' : '';?>">
      <label class="control-label" for="course_permalink">고유값</label>
      <div class="controls">
        <input type="text" id="course_permalink" class="span4" name="permalink" value="<?php echo isset($course) ? $course->permalink : ''?>" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="course_description">설명</label>
      <div class="controls">
        <textarea id="course_description" class="span4" name="description"><?php echo isset($course) ? $course->description : ''?></textarea>
        <p class="help-block">
          최대 150자 내외로 코스에 대한 설명을 입력해주세요.
        </p>
      </div>
    </div>    
  <?php
    if(($edit_mode && $course->status == 'pending' && in_array($current_user->role,array('admin','super-admin'))) ||
        (!$edit_mode && in_array($current_user->role,array('admin','super-admin')))) {
  ?>
    <div class="control-group">
      <label class="control-label" for="course_approved">바로인증</label>
      <div class="controls">
          <label class="checkbox">
            <input id="course_approved" type="checkbox" name="approved" /> 지금 인증하기
          </label>
          <p class="help-block">
          관리자는 인증절차 없이 바로 코스를 입력할 수 있습니다.
        </p>
      </div>
    </div>
  <?php
    }
  ?>
  </fieldset>
  <?php if($modal_mode) { ?>
  </div>
  <?php } ?>
  <div class="<?php echo $modal_mode ? 'modal-footer' : 'form-actions';?>">
  <?php
  	if($edit_mode) {
  ?>
      <button type="submit" class="btn btn-primary">변경사항 저장</button>
  <?php
	} else {
  ?>
      <button type="submit" class="btn btn-primary">추가</button>
  <?php
	}
  ?>
  
  <?php
  	if($modal_mode) {
  ?>
      <a href="#" class="btn" data-dismiss="modal">취소</a>
  <?php		
  	} else {
  ?>
      <a href="<?php echo site_url($site->permalink.'/manage/course');?>" class="btn">취소</a>
  <?php
	}
   if($edit_mode) {
  ?>
      <a href="<?php echo site_url($site->permalink.'/manage/course/delete/'.$course->id);?>" class="btn btn-danger pull-right" onclick="return confirm('삭제하시면 다시 복구하실 수 없습니다. 삭제하시겠습니까?');">삭제하기</a>
  <?php    
    }
  ?>
  </div>
</form>

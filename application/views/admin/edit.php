
<form id="admin" class="form-horizontal" method="post">

  <div class="page-header">  
  	 <?php if($message) { ?>
	  <div class="alert alert-<?php echo $message->type;?>">
	  	<button type="button" class="close" data-dismiss="alert">&times;</button>
	  	<?php echo $message->content;?>
	  </div>
	  <?php } ?>
  
  	<h3>장소 편집</h3>
  </div>
  <fieldset>
    <div class="control-group">
      <label class="control-label" for="">제목</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="title" value="<?php echo $place->title?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">종류</label>
      <div class="controls">
        <select class="input input-xlarge" name="type">
          <?php
          	foreach($place_types as $type) {
          ?>
          	<option value="<?php echo $type->key;?>"<?php if($place->type == $type->key) {?> selected="selected"<?php } ?>><?php echo $type->name;?></option>
          <?php
          	}
		  ?>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">주소</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="address" value="<?php echo $place->address?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">URL</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="uri" value="<?php echo $place->uri?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">설명</label>
      <div class="controls">
        <textarea class="input input-xlarge" name="description"><?php echo $place->description?></textarea>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">등록자 이름</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="owner_name" value="<?php echo $place->owner_name?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">등록자 Email</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="owner_email" value="<?php echo $place->owner_email?>" id="">
      </div>
    </div>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">변경사항 저장</button>
      <a href="<?php echo site_url('/admin');?>" class="btn" style="float: right;">취소</a>
    </div>
  </fieldset>
</form>

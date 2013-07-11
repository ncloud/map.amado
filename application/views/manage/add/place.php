<?php
	$edit_mode = isset($edit_mode) ? $edit_mode : false;
	$errors = array();
?>

<form id="manage" class="form-horizontal" method="post">

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
  <?php
  	if($edit_mode) {
  ?>
  	<h3>장소 편집</h3>
  <?php		
  	} else {
  ?>
  	<h3>장소 추가</h3>
  <?php
	}
  ?>
  </div>
  <fieldset>
    <div class="control-group<?php echo isset($errors['owner_name']) ? ' error' : '';?>">
      <label class="control-label" for="">등록자 이름 *</label>
      <div class="controls">
        <input id="owner_name" type="text" class="span3" name="owner_name" value="<?php echo $place->owner_name?>" id="">
      </div>
    </div>
    <div class="control-group<?php echo isset($errors['owner_email']) ? ' error' : '';?>">
      <label class="control-label" for="">등록자 이메일 *</label>
      <div class="controls">
        <input id="owner_email" type="text" class="span3" name="owner_email" value="<?php echo $place->owner_email?>" id="">
      </div>
    </div>
    <div class="control-group<?php echo isset($errors['type_id']) ? ' error' : '';?>">
      <label class="control-label" for="">종류 *</label>
      <div class="controls">
        <select id="type_id" class="span3" name="type_id">
        	<option value="">종류를 선택해주세요</option>
          <?php
          	foreach($place_types as $type) {
          ?>
          	<option value="<?php echo $type->id;?>"<?php if($place->type_id == $type->id) {?> selected="selected"<?php } ?>><?php echo $type->name;?></option>
          <?php
          	}
		  ?>
        </select>
      </div>
    </div>
    <div class="control-group<?php echo isset($errors['title']) ? ' error' : '';?>">
      <label class="control-label" for="">제목 *</label>
      <div class="controls">
        <input type="text" id="title" class="span4" name="title" value="<?php echo $place->title?>" id="">
      </div>
    </div>
    <div class="control-group<?php echo isset($errors['address']) ? ' error' : '';?>">
      <label class="control-label" for="">주소 *</label>
      <div class="controls">
        <input type="text" id="address" class="span4" name="address" value="<?php echo $place->address?>" id="">
        <span class="help-inline"><a href="#myModal" role="button" class="btn" data-toggle="modal">좌표 입력하기</a></span>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">URL</label>
      <div class="controls">
        <input type="text" id="url" class="span4" name="uri" value="<?php echo $place->uri?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">설명</label>
      <div class="controls">
        <textarea id="description" class="span4" name="description"><?php echo $place->description?></textarea>
      </div>
    </div>
    <div class="form-actions">
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
      <a href="<?php echo site_url($site->id.'/manage');?>" class="btn">취소</a>
    </div>
  </fieldset>
</form>


<!--modal-->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">좌표 입력하기</h3>
  </div>
  <div class="modal-body" style="position:relative;">
    <div id="map" style="width:100%; height:300px;"></div>
    
    <div style="width:1px; height:100%; background:rgba(0,0,0,0.1); left:50%; top:0px; position:absolute;"></div>
    <div style="width:100%; height:1px; background:rgba(0,0,0,0.1); left:1px; top:50%; position:absolute;"></div>
    
    <div style="width:3px; height:19px; background:rgba(255,100,0,0.6); left:50%; top:50%; margin-top:-9px; margin-left:-1px; position:absolute;"></div>
    <div style="width:19px; height:3px; background:rgba(255,100,0,0.6); left:50%; top:50%; margin-left:-9px; margin-top:-1px; position:absolute;"></div>
  </div>
  <div class="modal-footer">
  	<div class="text-left pull-left">
  		<div class="input-append">
  			<form onsubmit="searchFromAddress(this); return false;">
		  		<input class="span3" id="address_for_search" type="text" placeholder="주소" value="<?php echo $place->address_is_position == 'no' ? $place->address : '';?>" />
		  		<input type="submit" class="btn" type="button" value="주소 검색" />
		  	</form>
		</div>
  	</div>
  	
    <button class="btn btn-primary" onclick="selectMapPosition();">좌표 지정하기</button>
  </div>
</div>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>
<script type="text/javascript" src="<?php echo site_url('/js/plugin/gmap.js');?>"></script>
<script type="text/javascript">
	var gmap = null;
	$("#myModal").on('shown', function() {
		gmap = new GMaps({
		  div: '#map',  
		  zoom: 18,
		  <?php if(isset($place->lat)) { ?> lat: <?php echo $place->lat;?>, <?php } ?>
		  <?php if(isset($place->lng)) { ?> lng: <?php echo $place->lng;?>, <?php } ?>
		});
	})
	
	function searchFromAddress(form) {
		GMaps.geocode({
		  address: $('#address_for_search').val(),
		  callback: function(results, status) {
		    if (status == 'OK') {
		      var latlng = results[0].geometry.location;
		      gmap.setCenter(latlng.lat(), latlng.lng());
		    }
		  }
		});
	}
	
	function selectMapPosition()
	{
		var center = gmap.getCenter();

		$("#address").val(center.jb + ', ' + center.kb);
		$("#myModal").modal('hide');
	}
</script>

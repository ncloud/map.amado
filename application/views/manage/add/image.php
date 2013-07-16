<?php
  $edit_mode = isset($edit_mode) ? $edit_mode : false;
  $modal_mode = isset($modal_mode) ? $modal_mode : false;
  
  $errors = array();
?>

<form id="addform_image" enctype="multipart/form-data" method="post" action="<?php echo site_url($site->permalink.'/manage/add/image');?>" class="form-horizontal<?php echo $modal_mode ? ' modal-form' : '';?>">
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
      <h3>사진 편집</h3>
    <?php   
      } else {
    ?>
      <h3>사진 추가</h3>
    <?php
    }
    ?>
  </div>  
  <?php if($modal_mode) { ?>
  <div class="modal-body">
  <?php } ?>
    <fieldset><div class="control-group<?php echo isset($errors['owner_name']) ? ' error' : '';?>">
      <label class="control-label" for="owner_name">등록자 이름 *</label>
      <div class="controls">
        <?php
          if($current_user->id) {
        ?>
        <input type="hidden" id="owner_name" name="owner_name" value="<?php echo $current_user->name;?>" />
        <div class="text"><?php echo $current_user->name;?></div>
    <?php
      } else {
    ?>
    <input id="owner_name" type="text" class="span3" name="owner_name" value="<?php echo isset($image) ? $image->owner_name : ''?>" />
    <?php
      }
    ?>
      </div>
    </div>
    <div class="control-group<?php echo isset($errors['owner_email']) ? ' error' : '';?>">
      <label class="control-label" for="place_owner_email">등록자 이메일 *</label>
      <div class="controls">
        <?php
          if($current_user->id) {
        ?>
        <input type="hidden" id="place_owner_email" name="owner_email" value="<?php echo $current_user->email;?>" />
        <div class="text"><?php echo $current_user->email;?></div>
    <?php
      } else {
    ?>
        <input id="owner_email" type="text" class="span3" name="owner_email" value="<?php echo isset($image) ? $image->owner_email : ''?>" />
        <?php
      }
    ?>
      </div>
    </div>
    <div class="control-group<?php echo isset($errors['image']) ? ' error' : '';?>">
        <label class="control-label" for="image_image">사진 *</label>
        <div class="controls">
          <input id="image_image" type="file" class="span4" name="image" autocomplete="off">
        </div>
    </div>

    <div class="control-group<?php echo isset($errors['title']) ? ' error' : '';?>">
      <label class="control-label" for="image_title">이름 *</label>
      <div class="controls">
        <input type="text" id="image_title" class="span4" name="title" value="<?php echo isset($image) ? $image->title : ''?>" />
      </div>
    </div>
    <div class="control-group<?php echo isset($errors['address']) ? ' error' : '';?>">
      <label class="control-label" for="image_address">주소 *</label>
      <div class="controls">
        <input type="text" id="image_address" class="span4" name="address" value="<?php echo isset($image) ? $image->address : ''?>" />
        <?php
          if(!$modal_mode) {
        ?>
        <span class="help-inline"><a href="#myModal" role="button" class="btn" data-toggle="modal">좌표 입력하기</a></span>
        <?php
      }
    ?>
        <p class="help-block">
          구글 지도에서 해당 주소를 검색하여 추가합니다. 정확한 주소를 입력해 주셔야 정확한 위치에 추가됩니다.
        </p>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="description">설명</label>
      <div class="controls">
        <textarea id="description" class="span4" name="description"><?php echo isset($image) ? $image->description : ''?></textarea>
        <p class="help-block">
          최대 150자 내외로 장소에 대한 설명을 입력해주세요.
        </p>
      </div>
    </div>
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
      <a href="<?php echo site_url($site->id.'/manage');?>" class="btn">취소</a>
  <?php
  }
  ?>
  </div>
</form>


<?php
  if(!$modal_mode) {
?>
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
          <input class="span3" id="address_for_search" type="text" placeholder="주소" value="<?php echo isset($image) ? ($image->address_is_position == 'no' ? $image->address : '') : '';?>" />
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
      <?php if(isset($image->lat)) { ?> lat: <?php echo $image->lat;?>, <?php } ?>
      <?php if(isset($image->lng)) { ?> lng: <?php echo $image->lng;?>, <?php } ?>
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

<?php 
  } // modal_mode check end
?>
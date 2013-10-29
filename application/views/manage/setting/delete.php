<?php
  echo $this->view('/manage/slices/setting_header');
  $errors = array();
?>

<form id="deleteform" enctype="multipart/form-data" action="<?php echo site_url($map->permalink.'/manage/delete');?>" class="form-horizontal" method="post" onsubmit="return onMapDelete(this);">

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

     <h3>삭제</h3>
  </div> <!-- header -->

  <fieldset>
        <p class="alert alert-danger">
          지도를 삭제하시면 다시 복구하실 수 없으므로 신중하게 결정해주세요. <br />
          지도에 포함된 장소, 사진, 코스, 분류 및 설정파일등 모든 데이터가 삭제됩니다.
        </p>
        <hr />
        <p>
          아래 확인코드에 이 지도의 고유주소를 입력해주세요. <br />
          고유주소는 <strong><?php echo $map->permalink;?></strong> 입니다.
        </p>
  </fieldset>

  <div class="form-actions">
      <div class="input-prepend">
        <span class="add-on">확인코드</span>
        <input type="text" id="deleteCode" name="code" value="" /><button type="submit" id="deleteButton" class="btn btn-danger">삭제</button>  
      </div>
  </div>

</form>
<script type="text/javascript" src="<?php echo site_url('/js/plugin/jquery.textchange.js');?>"></script>
<script type="text/javascript">
  function onMapDelete(form) {
    var $deleteButton = $("#deleteButton");
    if($deleteButton.hasClass('disabled')) {
      return false;
    }

    if(form.code.value == '') {
      $(form.code).focus();
      return false;
    }

    if(form.code.value != "<?php echo $map->permalink;?>") {
      return false;
    }

    return true;
  }

  $(function() {
    $("#deleteCode").bind('textchange', function(event, prevText) {
      if($(this).val() == "<?php echo $map->permalink;?>") {
        $("#deleteButton").removeClass('disabled');
      } else {
        $("#deleteButton").addClass('disabled');
      }
    });

    $("#deleteButton").addClass('disabled');
  })
</script>

<?php
  echo $this->view('/manage/slices/setting_footer');
?>
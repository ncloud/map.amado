<?php
  echo $this->view('/manage/slices/setting_header');
  $errors = array();
?>

<form id="deleteform" enctype="multipart/form-data" action="<?php echo site_url($map->permalink.'/manage/delete');?>" class="form-horizontal" method="post">

  <div class="page-header">
     <h3>삭제</h3>
  </div> <!-- header -->

  <fieldset>
        <p>
          지도를 삭제하시면 다시 복구하실 수 없으므로 신중하게 결정해주세요. <br />
          지도에 포함된 장소, 사진, 코스, 분류 및 설정파일등 모든 데이터가 삭제됩니다.
        </p>
  </fieldset>

  <div class="form-actions">
      <div class="input-prepend">
        <span class="add-on">비밀번호</span>
        <input type="password" name="password" value="" /><button type="submit" class="btn btn-primary">삭제</button>  
      </div>
  </div>

</form>

<?php
  echo $this->view('/manage/slices/setting_footer');
?>
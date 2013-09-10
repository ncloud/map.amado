<?php
  echo $this->view('/manage/slices/setting_header');

  $modal_mode = isset($modal_mode) ? $modal_mode : false;
  
  $errors = array();
?>

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

  <?php
    if($modal_mode) {
  ?>
  <button type="button" class="close" data-dismiss="modal">×</button>
  <?php   
    } 
  ?>
    <h3>분류 편집</h3>

  </div>


<?php
  echo $this->view('/manage/slices/setting_footer');
?>

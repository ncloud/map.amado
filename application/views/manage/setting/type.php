<?php
  echo $this->view('/manage/slices/setting_header');

	$edit_mode = isset($edit_mode) ? $edit_mode : false;
	$modal_mode = isset($modal_mode) ? $modal_mode : false;
	
	$errors = array();
?>

<form id="addform" action="<?php echo site_url($site->permalink.'/manage/type/');?>" class="form-horizontal<?php echo $modal_mode ? ' modal-form' : '';?>" method="post">
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
  	<h3>분류 편집</h3>
  </div>
  <?php if($modal_mode) { ?>
  <div class="modal-body"> 
  <?php } ?>
	  <fieldset>
  		<ul id="type_list" class="type_list sortable unstyled">
  	        <li class="empty_type hide type">
  	          <div class="controls">
  	            분류가 비어 있습니다.
  	          </div>
  	        </li>
  		</ul>

        <div class="type_tool buttons">
            <a class="btn" href="#" onclick="Type.addWindow(); return false;">분류 추가</a>
        </div>

	  </fieldset>
  <?php if($modal_mode) { ?>
  </div>
  <?php } ?>
  <div class="<?php echo $modal_mode ? 'modal-footer' : 'form-actions';?>">
 	<button type="submit" class="btn btn-primary">변경사항 저장</button>
  <?php
  	if($modal_mode) {
  ?>
      <a href="#" class="btn" data-dismiss="modal">취소</a>
  <?php		
  	} else {
  ?>
      <a href="<?php echo site_url($site->permalink.'/manage');?>" class="btn">취소</a>
  <?php
	}
  ?>
  </div>
</form>

<script type="text/javascript" src="<?php echo site_url('/js/plugin/jquery.sortable.js');?>"></script>
<script type="text/javascript">
  $(function  () {
    var adjustment;
    $("ul.sortable").sortable({
      pullPlaceholder: false,
      itemSelector: 'li.type',
      // animation on drop
      // set item relative to cursor position
      onDragStart: function ($item, container, _super) {
        var offset = $item.offset(),
        pointer = container.rootGroup.pointer

        adjustment = {
        //  left: pointer.left - offset.left,
          top: pointer.top - offset.top
        }

        _super($item, container)
      },
      onDrop: function ($item, container, _super) {
        $item.removeClass("dragged").removeAttr("style");
        $("body").removeClass("dragging");

        Type.rebuildTypeNumber();
      },
      onDrag: function ($item, position) {
        $item.css({
        //  left: position.left - adjustment.left,
          top: position.top - adjustment.top
        })
      }
    })
  })
</script>

<script type="text/javascript">
  var Type = function(base) {
      var self = this;
      var index = 1;
      var names = [];

      this.$base = $(base);

      self.add = function(name, icon_id, id) {
      	  if(name == '') return false;

          if(typeof(icon_id) == 'undefined' || !icon_id) icon_id = '1';
          if(typeof(id) == 'undefined' || !id) id = '';

          // name check
          if($.inArray(name, names) >= 0) { 
             $('.top-center').notify({
                key: 'addform',
                message: { text: '모든 필수 입력항목을 입력해주세요.' },
                type:'error'
              }).show();

            return false; 
          }

          var $types = this.$base.find('li.type');
          var now = $types.length + 1;

          var $item  = $(

          '<li id="type' + index + '" class="type">' +
          '    <input type="hidden" name="type' + index + '_name" class="name" value="' + (name) + '" />' +
          '    <input type="hidden" name="type' + index + '_icon_id" class="icon_id" value="' + (icon_id) + '" />' +
          '    <input type="hidden" name="type' + index + '_id" class="icon_id" value="' + (id) + '" />' +
          '    <input type="hidden" name="type' + index + '_order" class="order" value="' + (now) + '" />' +
          '    <div class="text">' + name + '</div>' +
          '    <a href="#" class="close" data-dismiss="alert" onclick="Type.deleteType(' + index + '); return false;">&times;</a>' +
          '    <div class="clearfix"></div>' +
          '</li>'

          );

          this.$base.append($item);

          names.push(name);
          index ++;

          return true;
      }

      self.addWindow = function() {
        var $addWindow = self.$base.find('.add_window');
        var $addWindowInput = null;
        var typeahead = null;

        if($addWindow.length == 0) {
           $addWindow = $(
              '<li class="add_window control-group">' +
              '<label class="control-label">+</label>' +
              '<div class="controls">' +
              '  <input type="text" class="add_window_input" value="" />' +
              '</div>' +
              '</li>');
          
           $addWindow.appendTo(self.$base);
        }

         $addWindowInput = $addWindow.find('input[type=text]');

         $addWindowInput.keyup(function(event) {
            if(event.keyCode == 27) { // esc
              $addWindowInput.blur();
            } else if(event.keyCode == 13) { // return
                var name = $addWindowInput.val();
                if(name != '') {
	            	if(self.add(name)) {
	            		$addWindowInput.blur();
	            	} else {
	                    $addWindowInput.select();
	                }
	            }
            }
         }).blur(function(event) {
            self.hideAddWindow();
         });

         var $form = $addWindow.parents('form');
         $form.submit(function(event) {
            var result = true;
            $form.find('input:focus').each(function(index, data) {
              if($(data).hasClass('add_window_input')) {
                result = false;
                return;
              }
            });
            return result;
         });
        
        $addWindowInput.focus();
      }

      self.rebuildTypeNumber = function() {
        self.$base.find('.type').each(function(index, data) {
            $(this).find('.control-label').text(index+1);
            $(this).find('input.order').val(index+1);
        });
      }

      self.hideAddWindow = function() {
        this.$base.find('.add_window').remove();
      }

      self.checkType = function() {
        var $empty_type = this.$base.find('.empty_type');
        if(this.$base.find('.type').length == 0) {
            $empty_type.show();
        } else {
            $empty_type.hide();
        }

        $empty_type.removeClass('hide');
      }

      self.deleteType = function(type_id) {
        this.$base.find('#type' + type_id).remove();

        this.checkType();
      }
  }   

  var Type = new Type('#type_list');
<?php 
    foreach($types as $type) {
?>
  Type.add('<?php echo $type->name;?>', '<?php echo $type->icon_id;?>', '<?php echo $type->id;?>');
<?php
  }
?>
  Type.checkType();
</script>

<?php
  echo $this->view('/manage/slices/setting_footer');
?>
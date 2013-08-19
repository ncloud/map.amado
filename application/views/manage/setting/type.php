<?php
  echo $this->view('/manage/slices/setting_header');

	$edit_mode = isset($edit_mode) ? $edit_mode : false;
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
       <form id="editForm" action="<?php echo site_url($site->permalink.'/manage/type/');?>" class="form-horizontal<?php echo $modal_mode ? ' modal-form' : '';?>" method="post" onsubmit="Type.onSave(this); return false;">
  	   <fieldset>
    		<ul id="type_list" class="type_list sortable unstyled">
    	        <li class="empty_type hide type">
    	          <div class="controls">
    	            분류가 비어 있습니다.
    	          </div>
    	        </li>
    		</ul>
      </fieldset>
      </form>

      <hr />

      <div id="type_tools" class="type_tools">
        <div class="add_window">
          <input type="text" class="add_window_input" value="" />
          <input type="button" class="btn" value="추가" onclick="Type.addForAddWindow(); return false;" />
          <a class="close" href="#" onclick="Type.hideAddWindow(); return false;">&times;</a>
        </div>

        <div class="buttons">
          <a class="btn" href="#" onclick="Type.showAddWindow(); return false;">분류 추가</a>
        </div>
      </div>
  </div>

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
        Type.save();
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
  var Type = function() {
      var self = this;
      var index = 1;
      var names = [];

      this.$list = $('#type_list');
      this.$tools = $('#type_tools');

      this.$addWindow = this.$tools.find('.add_window');
      this.$addWindowInput = this.$addWindow.find('input[type=text]');
      this.$buttons = this.$tools.find('.buttons');

      self.add = function(name, icon_id, id) {
      	  if(name == '') return false;

          if(typeof(icon_id) == 'undefined' || !icon_id) icon_id = '1';
          if(typeof(id) == 'undefined' || !id) id = '';

          // name check
          if($.inArray(name, names) >= 0) { 
             $('.top-center').notify({
                key: 'editForm',
                message: { text: '이미 존재하는 분류명입니다. 다른 분류명을 입력해주세요.' },
                type:'error'
              }).show();
            return false; 
          }

          var $types = self.$list.find('li.type');
          var now = $types.length + 1;

          var $item  = $(
          '<li id="type' + index + '" class="type">' +
          '    <input type="hidden" name="type' + index + '_name" class="name" value="' + (name) + '" />' +
          '    <input type="hidden" name="type' + index + '_id" class="id" value="' + (id) + '" />' +
          '    <input type="hidden" name="type' + index + '_icon_id" class="icon_id" value="' + (icon_id) + '" />' +
          '    <input type="hidden" name="type' + index + '_order" class="order" value="' + (now) + '" />' +
          '    <div class="text">' + name + '</div>' +
          '    <div class="btn-group">' + 
          '        <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">' + 
          '          <span class="caret"></span>' + 
          '        </a>' + 
          '        <ul class="dropdown-menu pull-right">' + 
          '           <li><a data-toggle="modal" href="#myModal">변경</a></li>' +
          '           <li class="divider"></li>' +
          '           <li><a data-toggle="modal" href="#deleteModal" onclick="Type.setDeleteModal(' + index + ');">삭제</a></li>' + 
          '        </ul>' + 
          '      </div>' + 
          '    <div class="clearfix"></div>' +
          '</li>'

          );

          self.$list.append($item);

          names.push(name);
          index ++;

          return $item;
      }

      self.rebuildTypeNumber = function() {
        self.$list.find('.type').each(function(index, data) {
            $(this).find('.control-label').text(index+1);
            $(this).find('input.order').val(index+1);
        });
      }

      self.addForAddWindow = function() {
        var name = self.$addWindowInput.val();
        if(name != '') {
          if($item = self.add(name)) {
            self.hideAddWindow();
            self.saveForAdd($item);            
          } else {
            $addWindowInput.select();
          }
        }
      }

      self.showAddWindow = function() {
         self.$addWindowInput.val('');
         self.$addWindowInput.bind('keyup', function(event) {
            if(event.keyCode == 27) { // esc
              self.hideAddWindow();
            } else if(event.keyCode == 13) { // return
              self.addForAddWindow();
            }
         });

        self.$addWindow.show();
        self.$addWindowInput.focus();

        self.$buttons.hide();
      }

      self.hideAddWindow = function() {
        self.$addWindowInput.unbind('keyup');

        self.$addWindow.hide();
        self.$buttons.show();
      }

      self.checkType = function() {
        var $empty_type = self.$list.find('.empty_type');
        if(self.$list.find('.type').length == 0) {
            $empty_type.show();
        } else {
            $empty_type.hide();
        }

        $empty_type.removeClass('hide');
      }

      self.deleteType = function(type_id) {
        if(confirm('분류를 삭제하시겠습니까? 해당 분류를 사용하는 장소는 모두 분류없음으로 자동 변경됩니다.')) {
          self.$list.find('#type' + type_id).remove();
          self.checkType();
        }
      }

      self.onSave = function(form) {
        var $form = $(form);
        var datas = new Array();
        $form.find('input').each(function(index, input) {
          var $input = $(input);
          datas.push($input.attr('name') + '=' + encodeURIComponent($input.val()));
        });

        $.ajax({
          dataType: "json",
          type: "POST", 
          url: '<?php echo site_url($site->permalink.'/manage/type/');?>',
          data: datas.join('&'),
          success: function(data) {
            if(data.success) {
             /*$('.top-center').notify({
                key: 'editForm',
                message: { text: '수정했습니다' },
                type:'success'
              }).show(); */
            } else {

            }
          }
        });
        return false;
      }

      self.save = function() {
        var $form = $("#editForm");
        $form.submit();
      }

      self.saveForAdd = function($item) {
        // 값이 공백일때만 저장함..
        if($item.find('input.id').val() == '') {
            var name = $item.find('input.name').val();

            $.ajax({
                dataType: "json",
                url: '<?php echo site_url($site->permalink.'/manage/type/add/');?>/' + encodeURIComponent(name),
                success: function(data) {
                  if(data.success) {
                    $item.find('input.id').val(data.content.id);
                  }
                }
            });
        }
      }

      self.setDeleteModal = function(index) {
        $("#deleteModal").find('input[name=type_id]').val(index);
      }

      self.doDelete = function() {
        var $modal = $("#deleteModal");

        var index = $modal.find('input[name=type_id]').val();

        $("#deleteModal").modal("hide");        
        $("#type" + index).fadeOut();
      }
  }   

  var Type = new Type();
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

<style type="text/css">
  #deleteModal {}
    #deleteModal form { margin-bottom:0; }
</style>

<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteForm" action="<?php echo site_url($site->permalink.'/manage/type/delete');?>" method="post" onsubmit="Type.doDelete(); return false;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">분류 삭제</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="type_id" value="" />
          <p>
            삭제된 분류는 다시 복구할 수 없고, 분류에 해당하는 장소들은 자동으로 "분류없음"으로 변경됩니다.
            삭제하시겠습니까?
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
          <input type="submit" class="btn btn-danger" value="삭제" />
        </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
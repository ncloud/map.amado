<?php
  echo $this->view('/manage/slices/setting_header');

	$edit_mode = isset($edit_mode) ? $edit_mode : false;
	
	$errors = array();
?>

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
    
  	<h3>분류 설정</h3>
  </div>

       <form id="editForm" action="<?php echo site_url($map->permalink.'/manage/type/');?>" class="form-horizontal" method="post" onsubmit="Type.onSave(this); return false;">
  	   <fieldset>
    		<ul id="type_list" class="type_list sortable unstyled">
    	        <li class="empty_type type">
    	            분류가 비어 있습니다.
    	        </li>
    		</ul>
      </fieldset>
      </form>

      <hr />

      <div id="type_tools" class="type_tools">
        <div class="add_window input-append">
          <div class="btn-group">
              <a href="#" class="btn btn-gray dropdown-toggle" data-toggle="dropdown"><img id="typeIconImageForNew", class="add_window_icon" src="<?php echo site_url('/img/icons/1.png');?>" alt="" /> <b class="caret"></b></a>

              <ul class="dropdown-menu column-menu">
              <?php
                foreach($map_icon_ids as $map_icon_id) {
              ?>
                <li><a href="#" onclick="Type.changeIconID('#typeIconForNew', '#typeIconImageForNew', <?php echo $map_icon_id;?>); return false;"><img src="<?php echo site_url('/img/icons/'.$map_icon_id.'.png');?>" alt="" /></a></li>
              <?php 
                }
              ?>
              </ul>
          </div>

          <input type="hidden" id="typeIconForNew" class="icon_id" value="1" />
          <input type="text" class="add_window_input span5" value="" />
          <input type="button" class="btn" value="추가" onclick="Type.addForAddWindow(); return false;" />
          <a class="close" href="#" onclick="Type.hideAddWindow(); return false;">&times;</a>
        </div>

        <div class="buttons">
          <a class="btn" href="#" onclick="Type.showAddWindow(); return false;">분류 추가</a>
        </div>
      </div>
  </div>

<?php
  echo $this->view('/manage/slices/setting_footer');
?>

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editForm" class="form-horizontal" action="<?php echo site_url($map->permalink.'/manage/type/edit');?>" method="post" onsubmit="Type.doEditType(); return false;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">분류 변경</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="type_id" value="" />
          <input type="hidden" name="type_index" value="" />
          
          <div class="control-group">
            <label class="control-label" for="typeIcon">아이콘</label>
            <div class="controls">
              <input type="hidden" id="typeIcon" name="icon_id" />

            <div class="btn-group">
                <a href="#" class="btn btn-white dropdown-toggle" data-toggle="dropdown"><img  id="typeIconImage" class="add_window_icon icon_image" src="<?php echo site_url('/img/icons/1.png');?>" alt="" /> <b class="caret"></b></a>

                <ul class="dropdown-menu column-menu">
                <?php
                  foreach($map_icon_ids as $map_icon_id) {
                ?>
                  <li><a href="#" onclick="Type.changeIconID('#typeIcon', '#typeIconImage', <?php echo $map_icon_id;?>); return false;"><img src="<?php echo site_url('/img/icons/'.$map_icon_id.'.png');?>" alt="" /></a></li>
                <?php 
                  }
                ?>
                </ul>
            </div>
            </div>
          </div>

          <div class="control-group">
            <label class="control-label" for="typeName">이름</label>
            <div class="controls">
              <input type="text" id="typeName" name="name" />
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">취소</button>
          <input type="submit" class="btn btn-primary" value="변경" />
        </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteForm" action="<?php echo site_url($map->permalink.'/manage/type/delete');?>" method="post" onsubmit="Type.doDeleteType(); return false;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">분류 삭제</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" name="type_id" value="" />
          <input type="hidden" name="type_index" value="" />
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
      this.$addWindowIconId = this.$addWindow.find('input[type=hidden]');
      this.$addWindowIcon = this.$addWindow.find('img.add_window_icon');
      this.$addWindowInput = this.$addWindow.find('input[type=text]');
      this.$buttons = this.$tools.find('.buttons');


      self.add = function(name, icon_id, id, count) {
          if(name == '') return false;

          if(typeof(icon_id) == 'undefined' || !icon_id) icon_id = '1';
          if(typeof(id) == 'undefined' || !id) id = '';
          if(typeof(count) == 'undefined' || !count) count = '0';

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
          '    <div class="icon"><img src="<?php echo site_url('/img/icons');?>/' + (icon_id) + '.png" alt="" /></div>' + 
          '    <div class="text"><span class="name">' + name + '</span> <span class="badge">' + (count) + '</span></div>' +
          '    <div class="btn-group">' + 
          '        <a class="btn btn-gray btn-mini dropdown-toggle" data-toggle="dropdown" href="#">' + 
          '          <span class="caret only-caret"></span>' + 
          '        </a>' + 
          '        <ul class="dropdown-menu pull-right">' + 
          '           <li><a data-toggle="modal" href="#editModal" onclick="Type.setEditTypeModal(' + index + ');">변경</a></li>' +
          '           <li class="divider"></li>' +
          '           <li><a data-toggle="modal" href="#deleteModal" onclick="Type.setDeleteTypeModal(' + index + ');">삭제</a></li>' + 
          '        </ul>' + 
          '      </div>' + 
          '    <div class="clearfix"></div>' +
          '</li>'

          );

          self.$list.append($item);

          names.push(name);
          index ++;

          self.checkType();

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
        var icon_id = self.$addWindowIconId.val();

        if(name != '') {
          if($item = self.add(name, icon_id)) {
            self.hideAddWindow();
            self.saveForAdd($item);            
          } else {
            $addWindowInput.select();
          }
        }
      }

      self.changeIconID = function(target, targetForImage, id) {
          var $target = $(target);
          var $target_for_image = $(targetForImage);

          $target.val(id);

          var icon_url = "<?php echo site_url('/img/icons');?>/" + id + ".png";
          $target_for_image.attr('src', icon_url);
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
        if(self.$list.find('.type:not(.empty_type)').length == 0) {
            $empty_type.show();
        } else {
            $empty_type.hide();
        }
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
          url: '<?php echo site_url($map->permalink.'/manage/type/');?>',
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

      // 추가한 분류만 저장하도록...
      self.saveForAdd = function($item) {
        // 값이 공백일때만 저장함..
        if($item.find('input.id').val() == '') {
            var name = $item.find('input.name').val();
            var icon_id = $item.find('input.icon_id').val();

            $.ajax({
                dataType: "json",
                url: '<?php echo site_url($map->permalink.'/manage/type/add/');?>/' + encodeURIComponent(name) + '/' + icon_id,
                success: function(data) {
                  if(data.success) {
                    $item.find('input.id').val(data.content.id);
                  }
                }
            });
        }
      }

      self.setDeleteTypeModal = function(type_index) {
        var $deleteModal = $("#deleteModal");

        var type_id = $("#type" + type_index).find('input.id').val();

        $deleteModal.find('input[name=type_index]').val(type_index);
        $deleteModal.find('input[name=type_id]').val(type_id);
      }

      self.doDeleteType = function() {
        var $deleteModal = $("#deleteModal");
        
        var type_id = $deleteModal.find('input[name=type_id]').val();
        var type_index = $deleteModal.find('input[name=type_index]').val();

        $deleteModal.modal("hide");        
        $("#type" + type_index).fadeOut();

        $.ajax({
          dataType: "json",
          url: '<?php echo site_url($map->permalink.'/manage/type/delete/');?>/' + type_id,
          success: function(data) {
            if(data.success) {
              self.checkType();              
            }
          }
        });
      }

      self.setEditTypeModal = function(type_index) {
        var $editModal = $("#editModal");

        var type_id = $("#type" + type_index).find('input.id').val();
        var type_name = $("#type" + type_index).find('input.name').val();
        var type_icon_id = $("#type" + type_index).find('input.icon_id').val();

        $editModal.find('input[name=type_index]').val(type_index);
        $editModal.find('input[name=type_id]').val(type_id);

        $editModal.find('input[name=name]').val(type_name);
        $editModal.find('input[name=icon_id]').val(type_icon_id);

        $editModal.find('img.icon_image').attr('src', "<?php echo site_url('/img/icons');?>/" + type_icon_id + ".png");
      }

      self.doEditType = function() {        
        var $editModal = $("#editModal");

        var type_id = $editModal.find('input[name=type_id]').val();
        var type_index = $editModal.find('input[name=type_index]').val();
        var type_name = $editModal.find('input[name=name]').val();

        $editModal.modal("hide");    

        var $type = $("#type" + type_index);
        $type.find('input.name').val(type_name);    
        $type.find('div.text span.name').text(type_name);    

        var $form = $editModal.find('form');
        var datas = new Array();
        var not_save_names = ['type_id', 'type_index'];

        $form.find('input').each(function(index, input) {
          var $input = $(input);
          var name = $input.attr('name');
          if(name && $.inArray(name, not_save_names) == -1) {
            datas.push(name + '=' + encodeURIComponent($input.val()));
          }
        });
        
        $.ajax({
          dataType: "json",
          type: "POST",
          url: '<?php echo site_url($map->permalink.'/manage/type/edit/');?>/' + type_id,
          data: datas.join('&'),
          success: function(data) {
            if(data.success) {
              $("#type" + type_index).find('input.name').val(data.content.name);
              $("#type" + type_index).find('input.icon_id').val(data.content. icon_id);

              $("#type" + type_index).find('span.name').text(data.content.name);
              $("#type" + type_index).find('.icon img').attr('src', "<?php echo site_url('/img/icons');?>/" + data.content.icon_id + ".png");
            }
          }
        });
      }
  }   

  var Type = new Type();
<?php 
    foreach($types as $type) {
?>
  Type.add('<?php echo $type->name;?>', '<?php echo $type->icon_id;?>', '<?php echo $type->id;?>', '<?php echo $type->count;?>');
<?php
  }
?>
  Type.checkType();

  // Modal Event
  $('#editModal').on('shown', function () {
    $('#editModal').find('input[name=name]').focus();
  })
</script>
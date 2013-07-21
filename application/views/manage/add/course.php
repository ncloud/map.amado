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

    <div class="page-header">
      <h4>코스 목록</h4>
    </div>
    
    <ul id="course_list" class="course_list sortable unstyled">
      <li class="empty_course hide control-group">
        <div class="controls">
          코스가 비어 있습니다.
        </div>
      </li>
    </ul>

      <div class="course_tool buttons control-group">
        <div class="controls empty">
          <a class="btn" href="#" onclick="Course.addWindow(); return false;">장소 추가</a>
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

<script type="text/javascript" src="<?php echo site_url('/js/plugin/jquery.sortable.js');?>"></script>
<script type="text/javascript">
  $(function  () {
    var adjustment;
    $("ul.sortable").sortable({
      pullPlaceholder: false,
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

        Course.rebuildCourseNumber();
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
  var Course = function(base) {
      var self = this;
      var index = 1;

      this.$base = $(base);

      self.add = function(title, place_id, address) {
          if(typeof(place_id) == 'undefined' || !place_id) place_id = '';
          if(typeof(address) == 'undefined' || !address) address = '등록이 필요한 장소입니다';

          var now = this.$base.find('li.course').length + 1;

          var $item  = $(

          '<li id="course' + index + '" class="course control-group">' +
          '  <label class="control-label">' + now + '</label>' +
          '  <div class="controls">' +
          '    <input type="hidden" name="course' + index + '_title" class="title" value="' + (title) + '" />' +
          '    <input type="hidden" name="course' + index + '_id" class="id" value="' + (place_id) + '" />' +
          '    <input type="hidden" name="course' + index + '_order" class="order" value="' + (now) + '" />' +
          '    <div class="text">' + title + '</div>' +
          '    <div class="information">' + address + '</div>' +
          '    <a href="#" class="close" data-dismiss="alert" onclick="Course.deleteCourse(' + index + '); return false;">&times;</a>' +
          '  </div>' +
          '</li>'

          );

          this.$base.append($item);

          index ++;
      }

      self.addWindow = function() {
        var $addWindow = self.$base.find('.add_window');
        var $addWindowInput = null;
        var typeahead = null;
        var map_lists = null;

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
              var $typeahead = $addWindow.parent().find('ul.typeahead');
              if($typeahead.length == 0 || $typeahead.css('display') == 'none') {
                var title = $addWindowInput.val();
                var place_id = null,
                    place_address = null;

                if(typeof(map_lists[title]) != 'undefined') {
                  place_id = map_lists[title].id;
                  place_address = map_lists[title].address;
                }

                self.add(title, place_id, place_address);
                $addWindowInput.blur();
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

         typeahead = $addWindowInput.typeahead({
              matcher: function (item) {
                return true;
              },
              updater: function (item) {
                return item
              },
              source: function (query, process) {
                  return $.post("<?php echo site_url('/ajax/places/'.$site->id);?>", { query: query }, function (data) {
                      if(data.success) { 
                        map_lists = new Array();   
                        var datas = new Array();

                        $.each(data.data , function(index, data) {
                          map_lists[data.title] = data;
                          datas.push(data.title);
                        });
                        // TODO: 깜박임 최소화하기 
                        return process(datas);
                      } else {
                        return false;
                      }
                  }, "json");
                  var datas = new Array();

              }
          });
        
        $addWindowInput.focus();
      }

      self.rebuildCourseNumber = function() {
        self.$base.find('.course').each(function(index, data) {
            $(this).find('.control-label').text(index+1);
            $(this).find('input.order').text(index+1);
        });
      }

      self.hideAddWindow = function() {
        this.$base.find('.add_window').remove();
      }

      self.checkCourse = function() {
        var $empty_course = this.$base.find('.empty_course');
        if(this.$base.find('.course').length == 0) {
            $empty_course.show();
        } else {
            $empty_course.hide();
        }

        $empty_course.removeClass('hide');
      }

      self.deleteCourse = function(course_id) {
        this.$base.find('#course' + course_id).remove();

        this.checkCourse();
      }
  }   

  var Course = new Course('#course_list');
   <?php 
    foreach($course_targets as $course_target) {
   ?>
      Course.add('<?php echo $course_target->title;?>', '<?php echo $course_target->target_id;?>', '<?php echo $course_target->address;?>');
   <?php
    }
   ?>
</script>


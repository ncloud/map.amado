  <?php
  		$can_add = $map->add_role == 'guest' || 
            ($map->add_role == 'member' && in_array($current_user->role,array('member','workman','admin','super-admin'))) ||
            ($map->add_role == 'workman' && in_array($current_user->role,array('workman','admin','super-admin'))) ||
            ($map->add_role == 'admin' && in_array($current_user->role,array('admin','super-admin')));
  ?>
  
  <?php echo isset($error) && !empty($error) ? $error : ''; ?>

	<a class="logo" href="<?php echo site_url("/");?>">아마도.지도</a>
  <div class="toggle_wrap">

    <?php
      if($course_mode) {
    ?>
     <div class="group">
      <a class="course_btn<?php echo $map->default_menu == 'course' ? ' active' : '';?>" href="#" onclick="changeMenu('course'); return false;">코스</a>
      <a class="category_btn<?php echo $map->default_menu == 'type' ? ' active' : '';?>" href="#" onclick="changeMenu('category'); return false;">분류</a>
     </div>
    <?php
      }
    ?>

	   <a class="current_location_btn" href="#" onclick="currentLocation(); return false;">현재위치</a>
  </div>

    <!-- google map -->
    <div id="map"></div>
    
    <!-- right-side gutter -->
    <div class="menu" id="menu">        
  	  <div class="header" id="header">
        <div class="btn-group tool">
          <a class="btn btn-white dropdown-toggle" data-toggle="dropdown" href="#"><span>메뉴</span></a>
          <ul class="dropdown-menu">
            <li class="go_manage"><a href="<?php echo site_url('/'.$map->permalink.'/manage');?>">관리</a></li>
            <li class="go_menu only_mobile"><a href="#" onclick="showMenu(); return false;">메뉴</a></li>
         <?php
         	if($can_add) {
         ?>
            <li class="divider"></li>
            <li class="go_add_place"><a href="#" onclick="$('#modal_add').modal(); return false;">장소 추가</a></li>
            <li class="go_add_image"><a href="#" onclick="$('#modal_image_add').modal(); return false;">사진 추가</a></li>
         <?php
			}
		?>
          </ul>
        </div>

  	  	<a class="map" href="<?php echo site_url('/'.$map->permalink);?>"><?php echo $map->name;?></a>
        <div class="btn-group add">
       	<?php
          if($can_add) {
        ?>
          <a class="btn btn-white" href="#" onclick="$('#modal_add').modal();"><span>추가</span></a>
        <?php
			}
		?>
        </div>
  	  </div>
	  
	  <?php if($current_user->id) { ?>
	  <!--<div class="buttons" id="buttons">
			<a href="#modal_info" class="btn btn-large btn-info" data-toggle="modal"><i class="icon-info-sign icon-white"></i>About this Map</a>
            <a href="#modal_add" class="btn btn-large btn-success" data-toggle="modal"><i class="icon-plus-sign icon-white"></i>장소 추가하기</a>
	  </div>-->
	  <?php } ?>
	  
    <div class="menu_content">
	  <?php
	  	if($course_mode) {
	  ?>
		  <ul id="tab_menu" class="tab">
		  	<li class="course<?php echo $map->default_menu == 'course' ? ' selected' : '';?>"><a href="#" onclick="changeMenu('course'); showMenu(); return false;">코스</a></li>
		  	<li class="category<?php echo $map->default_menu == 'type' ? ' selected' : '';?>"><a href="#" onclick="changeMenu('category'); showMenu(); return false;">분류</a></li>
		  </ul>
		  
		  <ul class="list" id="list_by_course"<?php echo $map->default_menu == 'type' ? ' style="display:none;"' : '';?>>
		  <?php
        foreach($course_lists as $course) {
      ?>
        <li class="course course_<?php echo $course->id?>">
          <div class='item'>
            <div class="toggle" onClick="toggle('course','<?php echo $course->id;?>'); return false;" id="filter_course_<?php echo $course->id;?>"></div>
            <a href="#" onMouseOver="courseListMouseOver('<?php echo $course->id;?>');" onMouseOut="courseListMouseOut('<?php echo $course->id;?>');" onClick="toggleList('course','<?php echo $course->id;?>'); return false;"  class="info">
              <img src="<?php echo site_url('/img/icons/course/'.$course->icon);?>.png" alt='' /><?php echo $course->title;?> <span class='total'>(<?php echo count($course->targets);?>)</span>
              <span class="index"><?php echo $course->course_index;?></span>
            </a>
          </div>                
          <ul class='list-items list_course_<?php echo $course->id;?>'>
        <?php
          foreach($course->targets as $target) {                  
        ?>
            <li>
              <a href='#' onMouseOver="markerListMouseOver('course', '<?php echo $target->id;?>')" onMouseOut="markerListMouseOut('course', '<?php echo $target->id;?>')" onClick="goToMarker('course','<?php echo $target->id;?>');"><?php echo $target->title;?></a>
            </li>
        <?php
          }
        ?>
          </ul>
        </li>
      <?php
        }
      ?>
      </ul>
    <?php
		} // course_mode end
	  ?>
	
      <ul class="list" id="list_by_category"<?php echo $course_mode && $map->default_menu == 'course' ? ' style="display:none;"' : '';?>>
        <?php
          foreach($place_types as $type) {
            $markers_count = $count_by_type[$type->id];
            echo "<li class='category category_{$type->id}'>
                <div class='item'>
                  <div class='toggle' onClick=\"toggle('category', '{$type->id}'); return false;\" id='filter_category_{$type->id}'></div>
                  <a href='#' onClick=\"toggleList('category','{$type->id}'); return false;\" class='info'><img src='".site_url('/img/icons/'.$type->icon_id).".png' alt='' />{$type->name} <span class='total'>({$markers_count})</span></a>
                </div>
                <ul class='list-items list_category_{$type->id}'>";
			
			if($markers_count > 0) {
				      $markers = $place_lists_by_type[$type->id];
	            foreach($markers as $marker) {
                if($marker->attached != 'no') continue;
	            	$marker_id = $marker->id;
	              echo "<li class='type_{$marker->type_id}'>
	                    <a href='#' onMouseOver=\"markerListMouseOver('place', '{$marker_id}')\" onMouseOut=\"markerListMouseOut('place', '{$marker_id}')\" onClick=\"goToMarker('place','{$marker_id}');\">{$marker->title}</a>
	                  </li>";
	            }
			}
            echo "
                </ul>
              </li>
            ";
          }
        ?>
      </ul>

      <div class="close_panel"><a href="#" onclick="closeMenu(); return false;">닫기</a></div>
    </div>
    </div>
    
    <!-- more info modal -->
    <div class="modal hide" id="modal_info">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>이 지도는?</h3>
      </div>
      <div class="modal-body">
      
      </div>
      <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" style="float: right;">Close</a>
      </div>
    </div>
    
    
    <!-- add something modal -->
    <div class="modal hide add_modal" id="modal_add">
    	<?php echo $this->view('/manage/add/place', array('modal_mode'=>true, 'place_types'=>$only_place_types));?>
    </div>
    
    
    <!-- add image modal -->
    <div class="modal hide add_modal" id="modal_image_add">     
      <?php echo $this->view('/manage/add/image', array('modal_mode'=>true));?>
    </div>
    
    <!-- place modal -->
    <div class="modal hide" id="modal_place">
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">닫기</a>
      </div>
    </div>

    <!-- image modal -->
    <div class="modal hide" id="modal_image">
      <div class="modal-body">
        <img src="" alt="" />
      </div>
      <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">닫기</a>
      </div>
    </div>

	<script type="text/javascript" src="<?php echo site_url('/js/plugin/jquery.form.js');?>"></script>
    
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>
	<script type="text/javascript" src="<?php echo site_url('/js/plugin/gmap.js');?>"></script>
  <script type="text/javascript" src="<?php echo site_url('/js/plugin/gmap.label.js');?>"></script>
	<script type="text/javascript" src="<?php echo site_url('/js/plugin/context_menu.js');?>"></script>

    <script type="text/javascript">
      var gmap = null;
	    var contextMenu = null;
      var gmarkers = {};      
      var gcourses = {};
      var gpaths = {};
      var overlay = null;

      var markers = new Array();
      var course_markers = new Array();
      var images = new Array();

      var last_place_id = <?php echo $last_place_id;?>;

      function add_place(i, val, add_category_menu)
      {
          if(typeof(add_category_menu) == 'undefined') add_category_menu = false;

          i = parseInt(i);

          var id = parseInt(val.id);
          if(last_place_id < id) last_place_id = id;

          if(typeof(gmarkers['place_' + i]) != 'undefined') return false;

          var iconSize = new google.maps.Size(32,36);
          var iconCenter = new google.maps.Point(16,32);

          // format marker URI for display and linking
           var markerURL = val.url;
           if(markerURL.substr(0,7) != "http://") {
            markerURL = "http://" + markerURL; 
           } 
           var markerURL_short = markerURL.replace("http://", "");
           var markerURL_short = markerURL_short.replace("www.", "");

           var info = 
              "<div class='marker_title'>"+val.title+"</div>"
              + "<div class='marker_uri'><a target='_blank' href='"+markerURL+"'>"+markerURL_short+"</a></div>"
              + "<div class='marker_desc'>"+val.description+"</div>"
              + "<div class='marker_address'>"+val.address+"</div>";           

            // build this marker
            var markerImage = new google.maps.MarkerImage("<?php echo site_url('/img/icons/');?>/"+val.icon+".png", null, null, iconCenter, iconSize);
            var marker = gmap.addMarker({
                type: val.type,
                type_id: val.type_id,
                lat: val.lat,
                lng: val.lng,
                title: '',
                zIndex: 10 + i,
                icon: markerImage,
             //   infoWindow: { content: info },
                click: function(e) {
                  $("#modal_place").find('.modal-body').html(info);
                  $('#modal_place').modal('show');
                },
                mouseover: function() {
                    $("#label_place_"+i).show();//fadeIn('fast');
                },
                mouseout: function() { 
                    $("#label_place_"+i).hide();//fadeOut('fast');
                }
              });

            gmarkers['place_' + i] = marker;
            
            // add marker label
            var label = new Label({id: 'place_' + i, map:gmap.map, distance: {x:0, y:4}});
            
            label.bindTo('position', marker);
            label.set("text", val.title);
            label.bindTo('visible', marker);
            label.bindTo('clickable', marker);
            label.bindTo('zIndex', marker);

            if(add_category_menu) {
                var $category = $("#list_by_category .category_" + val.type_id);
                var $to = $category.find(".list_category_" + val.type_id);
                var html = '<li class="type_' + val.type_id + '">' + 
                            '<a href="#" onmouseover="markerListMouseOver(\'place\', \'' + i + '\')" onmouseout="markerListMouseOut(\'place\', , \'' + i + '\')" onclick="goToMarker(\'place\',, \'' + i + '\');">' + val.title + '</a>' +
                           '</li>';

                $to.append(html);

                var $total = $category.find('.item .info .total');
                var total_text = $total.text();
                total_text = parseInt(total_text.substr(1, total_text.length-1)) + 1;

                $total.text('(' + total_text + ')');
            }

            return marker;
      }

      function add_image(i, val) {          
          i = parseInt(i);
          var id = parseInt(val.id);
          if(last_place_id < id) last_place_id = id;

          var info = "<div class='marker_title'>"+val.title+"</div>"
              + "<div class='marker_desc'><img src='"+val.original_image+"' alt='' /></div>";
              
          var markerImage = new google.maps.MarkerImage(val.image, null, null, new google.maps.Point(15,15), new google.maps.Size(30,30));
          var marker = gmap.addMarker({
              type: val.type,
              type_id: val.type_id,
              lat: val.lat,
              lng: val.lng,
              title: '',
              zIndex: 10 + i,
              icon: markerImage,
              click: function(e) {
                  $("#modal_image").find('img').load(function() {
                    $('#modal_image').modal('show');
                  }).attr('src', val.original_image);
              },
              mouseover: function() {
                      $("#label_image_"+i).show();//fadeIn('fast');
                  },
              mouseout: function() { 
                  $("#label_image_"+i).hide();//fadeOut('fast');
              }
            });
         
          gmarkers['image_' + i] = marker;

          var label = new Label({id: 'image_' + i, map:gmap.map, distance: {x:0, y:20}});
          
          label.bindTo('position', marker);
          label.set("text", val.title);
          label.bindTo('visible', marker);
          label.bindTo('clickable', marker);
          label.bindTo('zIndex', marker);

          return marker;
      }

   
      $(document).ready(function(){
      	initialize();
		
        resizeList();

        checkNewPlaces();
      });
      
      $(window).resize(function() {
        resizeList();
      });
      
      function initialize() {
        <?php
            if($place_lists) {
              foreach($place_lists as $place) {
                if($place->attached == 'no') {
                  echo "markers[{$place->id}] = {id:'{$place->id}', type:'category', type_id:'{$place->type_id}', title:'{$place->title}', icon:'{$place->icon_id}', lat:'{$place->lat}', lng:'{$place->lng}', description:'{$place->description}', url:'{$place->url}', address:'{$place->address}'};"; 
                } else if($place->attached == 'image') {
                  echo "images.push({id:'{$place->id}', type:'category', type_id: 'image', title:'{$place->title}', image:'{$place->image_small}', original_image:'{$place->image}', lat:'{$place->lat}', lng:'{$place->lng}'});";
                }
              }
            }

            if($course_lists) {
              $course_markers = array();
              foreach($course_lists as $course) {
                $pos_index = 0;
                foreach($course->targets as $target) {
                    if(!isset($course_markers[$course->id])) $course_markers[$course->id] = array();
                    $course_markers[$course->id][$target->id] = array('type'=>'course', 'type_id'=>$course->id, 'parent'=>$course->id, 'pos'=>'', 'index'=>$course->course_index, 'title'=>$target->place_title, 'icon'=>$course->icon, 'color'=>$course->color, 'lat'=>$target->place_lat, 'lng'=>$target->place_lng, 'address'=>$target->place_address);
                    $pos_index ++;
                }
              }
              
              foreach($course_markers as $course_id => $course_marker) {
                $i = 0;
                foreach($course_marker as $target_id => $course) {
                  if($i==0) $course['pos'] = 'start';
                  else if($i==count($course_marker)-1) $course['pos'] = 'end';

                  if($i==0) {
                  echo "gcourses['$course_id'] = new Array();";
                  }

                  echo "course_markers[{$target_id}] = " . json_encode($course) . ";";
                  echo "gcourses['$course_id'].push(course_markers[{$target_id}]);";  
                  $i ++;
                }
              }
            }
        ?>

      gmap = new GMaps({
  		  div: '#map',  
  		  zoom: 16,
  		  lat: <?php echo $category_default->lat;?>,
  		  lng: <?php echo $category_default->lng;?>,
  		  
  		  mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl: false,
            panControl: false,
            draggableCursor:'default',
            zoomControl: true,
            zoomControlOptions: {
              style: google.maps.ZoomControlStyle.SMALL,
              position: google.maps.ControlPosition.LEFT_CENTER
            }
  		});
          
        zoomLevel = gmap.getZoom();
        google.maps.event.addListener(gmap.map, 'zoom_changed', function() {
          zoomLevel = gmap.getZoom();
        });

        // add markers
        jQuery.each(markers, function(i, val) {
          if(typeof(val) == 'undefined') return;

          val = $.extend(val, {url:''});
          var marker = add_place(i, val);

          <?php if($course_mode && $map->default_menu == 'course') { ?>
            marker.setVisible(false);
          <?php } ?>
        });

        jQuery.each(images, function(i, val) {
          if(typeof(val) == 'undefined') return;

          var marker = add_image(i, val);

          <?php if($course_mode && $map->default_menu == 'course') { ?>
          marker.setVisible(false);
          <?php } ?>
        });
        

        var paths = new Array();

        // add course markers
        jQuery.each(course_markers, function(i, val) {
          if(typeof(val) == 'undefined') return;

          val = $.extend(val, {url:''});

          if(typeof(paths[val.index]) == 'undefined') {
            paths[val.index] = {};
            paths[val.index].color = val.color;
            paths[val.index].data = new Array();
          }

          if(val.pos == 'start') {
            size = 28;
          } else {
            size = 20;
          }

          var iconSize = new google.maps.Size(size,size);
          var iconCenter = new google.maps.Point(size/2,size/2);

          paths[val.index].data.push([val.lat,val.lng]);

          // build this marker
          var markerImage = new google.maps.MarkerImage("<?php echo site_url('/img/icons/course');?>/"+val.icon+".png", null, null, iconCenter, iconSize);
          var marker = gmap.addMarker({
              type: val.type,
              type_id: val.type_id,
              lat: val.lat,
              lng: val.lng,
              title: '',
              zIndex: 10 + i,
              icon: markerImage,
              click: function(e) {

              },
              mouseover: function() {
                  $("#label_course_"+i).show();//fadeIn('fast');
              },
              mouseout: function() { 
                  $("#label_course_"+i).hide();//fadeOut('fast');
              }
            });

          gmarkers['course_' + i] = marker;         

          var label = new Label({id: 'icon_' + i, map:gmap.map, className:'icon_label', visible:true, verticalAlign:'middle', horizontalAlign:'center'});
          label.bindTo('position', marker);
          label.set("text", val.index);          
          label.bindTo('visible', marker);
          label.bindTo('zIndex', marker);

          
          // add marker label
          var label = new Label({id: 'course_' + i, map:gmap.map, add_zindex:100, distance:{x:0, y:(val.pos=='start' ? 13 : 10)}});
          
          label.bindTo('position', marker);
          label.set("text", val.title);
          label.bindTo('visible', marker);
          label.bindTo('clickable', marker);
          label.bindTo('zIndex', marker);

          <?php if($course_mode && $map->default_menu == 'type') { ?>
          marker.setVisible(false);
          <?php } ?>
        });

        for(var i=1;i<paths.length;i++) {
            var path = paths[i];
            var gpath = gmap.drawPolyline({
              path: path.data,
              strokeColor: path.color,
              strokeOpacity: 0.9,
              strokeWeight: 4
            });
          
          <?php if($course_mode && $map->default_menu == 'type') { ?>
            gpath.setVisible(false);
          <?php } ?>

            gpaths['path_' + i] = gpath;
        }

        overlay = gmap.drawOverlay({
          lat: <?php echo $category_default->lat;?>,
          lng: <?php echo $category_default->lng;?>,
          content: '<p class="map_overlay"></p>',
          verticalAlign: 'middle',
          horizontalAlign: 'center',
        });

        centerMap('<?php echo $course_mode  && $map->default_menu == 'course' ? 'course' : 'category';?>');
        
        // context menu
        var contextMenuOptions={};
        contextMenuOptions.classNames={menu:'context_menu', menuSeparator:'context_menu_separator'};
        
        var menuItems=[];        
        <?php
          if($can_add) {
        ?>
        menuItems.push({className:'context_menu_item', eventName:'add_here_click', label:'이곳에 장소 추가하기'});
        menuItems.push({className:'context_menu_item', eventName:'add_image_here_click', label:'이곳에 사진 추가하기'});
        menuItems.push({});
        <?php
          }
        ?>
        menuItems.push({className:'context_menu_item', eventName:'zoom_in_click', label:'지도 확대하기'});
        menuItems.push({className:'context_menu_item', eventName:'zoom_out_click', label:'지도 축소하기'});
        menuItems.push({});
        menuItems.push({className:'context_menu_item', eventName:'center_map_click', label:'이곳을 지도 가운데 옮기기'});
        contextMenuOptions.menuItems=menuItems;

        contextMenu = new ContextMenu(gmap.map, contextMenuOptions);

        google.maps.event.addListener(gmap.map, 'rightclick', function(mouseEvent){
            contextMenu.show(mouseEvent.latLng);
        });
        
        google.maps.event.addListener(contextMenu, 'menu_item_selected', function(latLng, eventName){
            switch(eventName){
                case 'add_here_click':
                    $('#modal_add').data({defaultLatLng:latLng}).modal();
                    break;
                case 'add_image_here_click':
                    $('#modal_image_add').data({defaultLatLng:latLng}).modal();
                    break;
                case 'zoom_in_click':
                    gmap.setZoom(gmap.getZoom()+1);
                    break;
                case 'zoom_out_click':
                    gmap.setZoom(gmap.getZoom()-1);
                    break;
                case 'center_map_click':
                    gmap.panTo(latLng);
                    break;
            }
        });
      } 

      function checkNewPlaces() {
        var reading_new = false;
        setInterval(function() {
          if(!reading_new) {
            reading_new = true;

            $.ajax({
              url:"<?php echo site_url('/ajax/check_places/' . $map->id);?>/" + last_place_id,
              dataType:"json",
              success:function(data) {
                reading_new = false;

                if(data.success) {
                  if(data.result_count > 0) {
                    $.each(data.result, function(index, result) {
                      if(result.attached == 'image') {
                         var place = result;
                         var image = {id: place.id, type:'category', type_id: 'image', title:place.title, image:place.image_small, original_image:place.image, lat:place.lat, lng:place.lng};
                         images.push(image);

                         add_image(images.length-1, image);

                         $('.bottom-left').notify({
                            key: 'newplace',
                            message: { text: place.owner_name + '님의 사진이 추가되었습니다.' },
                            type:'success'
                          }).show();
                      } else {
                         var place = result;
                         if(typeof(markers[place.id]) == 'undefined') {
                           markers[place.id] = {id: place.id, type:'category', type_id:place.type_id, title:place.title, icon:place.icon_id, lat:place.lat, lng:place.lng, description:place.description, url:place.url, address:place.address};

                           add_place(place.id, markers[place.id], true);

                           $('.bottom-left').notify({
                              key: 'newplace',
                              message: { text: place.owner_name + '님의 장소가 추가되었습니다.' },
                              type:'success'
                            }).show();
                         }
                      }
                    });
                  }
                }
              },
              error: function() {
                reading_new = false;
              }
            });
          }
        }, 3000);
      }
      
      function resizeList() {
        var header_height = $("#header").outerHeight();
        if(header_height == 1) header_height = 45;
        
        var tab_height = $("#tab_menu").outerHeight();

        newHeight = $(window).height() - header_height - tab_height;

        var $close_panel = $(".menu_content .close_panel");
        if($close_panel.is(':visible')) {
          newHeight -= $close_panel.height();
        }

        $('.list').css('height', newHeight + "px"); 
      }
      
      function goToMarker(type, marker_id) {
        if(marker_id) {
          gmap.panTo(gmarkers[type + '_' + marker_id].getPosition());
       //   map.setZoom(15);
          google.maps.event.trigger(gmarkers[type + '_' + marker_id], 'click');
        }

        closeMenuForMobile();
      }

      function toggle(type, type_id) {
        if($('#filter_'+type+"_"+type_id).is('.inactive')) {
          show(type, type_id); 
        } else {
          hide(type, type_id); 
        }
      }

      function hide(type, type_id) {
        $.each(gmarkers, function(i, val) {
	          if (gmarkers[i].type == type && gmarkers[i].type_id == type_id) {
	            gmarkers[i].setVisible(false);
	          }
	        }
	      );
        $("#filter_"+type+"_"+type_id).addClass("inactive");

        if(type == 'course') {
          $.each(gpaths, function(i, val) {
            if(i == 'path_'+type_id)
              gpaths[i].setVisible(false);
          });
        }
      }

      function show(type, type_id) {
        $.each(gmarkers, function(i, val) {
          if (gmarkers[i].type == type && gmarkers[i].type_id == type_id) {
            gmarkers[i].setVisible(true);
          }
        });
        
        $("#filter_"+type+"_"+type_id).removeClass("inactive");

        if(type == 'course') {
          $.each(gpaths, function(i, val) {
            if(i == 'path_'+type_id)
              gpaths[i].setVisible(true);
          });
        }
      }
      
      function toggleList(key, type_id) {
        $(".list .list_"+key+"_"+type_id).toggle();
      }

      function markerListMouseOver(type, marker_id) {
		    gmap.panTo(gmarkers[type + '_' + marker_id].getPosition());

        $("#label_"+type+"_"+marker_id).css("display", "inline");
      }
      
      function markerListMouseOut(type, marker_id) {
        $("#label_"+type+"_"+marker_id).css("display", "none");
      }

      function courseListMouseOver(course_id) {
        var start = gcourses[course_id][0];
        var latlng = new google.maps.LatLng(start.lat, start.lng);

        gmap.panTo(latlng);

        var projection = overlay.getProjection();
        var position = projection.fromLatLngToDivPixel(latlng);

        var $div = $(overlay.el);
        $div.css({left:(position.x) + 'px', top: (position.y) + 'px'}).find('.map_overlay').stop().css('opacity','1').show();
        $div.css('z-index', 1000);
      }

      function courseListMouseOut(course_id) {
        var start = gcourses[course_id][0];
        
        var $div = $(overlay.el);
        $div.find('.map_overlay').stop().fadeOut();
      }

      function changeMenu(menu) {
        var $group = $('.toggle_wrap .group');

        $group.find('a').removeClass('active');
        $group.find('a.' + menu +'_btn').addClass('active');

        $tab_menu = $("#tab_menu");
        $tab_menu.find('li').removeClass('selected');
        $tab_menu.find('li.' + menu).addClass('selected');

        var category_visible = (menu == 'category');
        var course_visible = menu == 'course';

        if(menu == 'course') {
          $.each(gpaths, function(i, val) {
            gpaths[i].setVisible(true);
          });
        } else {
          $.each(gpaths, function(i, val) {
            gpaths[i].setVisible(false);
          });
        }              
        $.each(gmarkers, function(i, val) {
            if($("." + gmarkers[i].type + "_" + gmarkers[i].type_id + " .toggle").hasClass('inactive')) {
              gmarkers[i].setVisible(false);
            } else {
              if (gmarkers[i].type == 'category') {
                  gmarkers[i].setVisible(category_visible);
              } else if (gmarkers[i].type == 'course') {
                  gmarkers[i].setVisible(course_visible);
              }
            }
          }
        );

        centerMap(menu);
      }

      function centerMap(menu) {
          var length = Object.keys(gmarkers).length;
          if(length == 0) return false;

          if(length == 1) {
            for(var key in gmarkers) break;

            var position = gmarkers[key].getPosition();
            var centerLat = position.lat();
            var centerLng = position.lng();
            var zoomLvl = 14; 

          } else if(length == 0) {
            // TODO
          } else {
            for(var i in gmarkers) {
              if(gmarkers[i].type != menu) continue;

              var position = gmarkers[i].getPosition();
              var this_lat = position.lat() || null;
              var this_lng = position.lng() || null;

              if((this_lat && !isNaN(this_lat)) && (this_lng && !isNaN(this_lng))) {
                  var minLat = minLat || this_lat;
                  var maxLat = maxLat || this_lat;
                  var minLng = minLng || this_lng;
                  var maxLng = maxLng || this_lng;

                  minLat = Math.min(minLat, this_lat);
                  maxLat = Math.max(maxLat, this_lat);
                  minLng = Math.min(minLng, this_lng);
                  maxLng = Math.max(maxLng, this_lng);
                }
              }


              var centerLat = minLat + ((maxLat - minLat) / 2);
              var centerLng = minLng + ((maxLng - minLng) / 2);

              var dist = (6371 *
                            Math.acos(
                              Math.sin(minLat / 57.2958) *
                                Math.sin(maxLat / 57.2958) + (
                                  Math.cos(minLat / 57.2958) *
                                  Math.cos(maxLat / 57.2958) *
                                  Math.cos(maxLng / 57.2958 - minLng / 57.2958)
                                  )
                                )
                            );
            
            var mapdisplay = 256;
            var zoomLvl = Math.floor(8 - Math.log(1.6446 * dist / Math.sqrt(2 * (mapdisplay * mapdisplay))) / Math.log (2));
            if(zoomLvl >= 19) zoomLvl = 19;
          }
          gmap.setCenter(centerLat, centerLng);
          gmap.setZoom(zoomLvl);
      }

      function showMenu()
      {
        var $active_button = $('.toggle_wrap .group a.active');
        
        toggleMenuForMobile(true);

        switch($active_button.text()) {
          case '코스':
            $("#list_by_course").show();
            $("#list_by_category").hide();
          break;
          case '분류':
            $("#list_by_category").show();
            $("#list_by_course").hide();
          break;
        }

        resizeList();
      }

      function closeMenu()
      {
        toggleMenuForMobile(false);
      }

      function toggleMenuForMobile(mode)
      {
        if(typeof(mode) == 'undefined') mode = 'auto';

        var $menu = $("#menu");
        if(mode == 'auto') {
          if($menu.hasClass('mobile_visible')) {
            $menu.removeClass('mobile_visible');
            $(".menu .go_menu").removeClass('checked');
          } else {
            $menu.addClass('mobile_visible');
            $(".menu .go_menu").addlass('checked');
          }
        } else {
          if(mode) { // show
            $menu.addClass('mobile_visible');
            $(".menu .go_menu").addClass('checked');
          } else { // hide
            $menu.removeClass('mobile_visible');
            $(".menu .go_menu").removeClass('checked');
          }
        }
      }

      function closeMenuForMobile()
      {
        var $menu = $("#menu");
        $menu.removeClass('mobile_visible');
      }

      function currentLocation()
      {
        GMaps.geolocate({
          success: function(position){
            if(gmap) gmap.setCenter(position.coords.latitude, position.coords.longitude);
          },
          error: function(error){
          },
          not_supported: function(){
          }
        });
      }
      
      // add modal form submit
      $('#modal_add').on('show', function (event) {
		       var $this = $(this);
		       var $form = $("#addform");
		       var address = "";
	           var data = $this.data();
	           if(typeof(data.defaultLatLng) != 'undefined' && data.defaultLatLng) {
	            address = data.defaultLatLng.lat() + ", " + data.defaultLatLng.lng();
	            data.defaultLatLng = null;
	           }

              $form.find('.error').removeClass('error');
	            
              $form.find( '[name=title]' ).val("");
	            $form.find( '[name=type_id]' ).val("");
	            $form.find( '[name=address]' ).val(address);
	            $form.find( '[name=url]' ).val("");
	            $form.find( '[name=description]' ).val("");
	            
	            $form.find("p").show();
	            $form.find("fieldset").show();
	            $form.find(".btn-primary").show();
      });

      $("#addform").ajaxForm({
      	dataType: 'json',
        success: function(data) {  
            $form = $("#addform");
            $form.find('.error').removeClass('error');

            if(data.success) {
               if(data.content.status != 'approved') {
                 $('.top-center').notify({
                    key: 'addform',
                    message: { html: '<h3>장소를 추가했습니다.</h3>' + '<p>관리자의 승인 후 실제 지도에 입력됩니다.</p>' },
                    type:'success'
                  }).show();
               } else {
                 $('.top-center').notify({
                    key: 'addform',
                    message: { html: '<h3>장소를 추가했습니다.</h3>' },
                    type:'success'
                  }).show();

                 var place = data.content;
                 markers[place.id] = {type:'category', type_id:place.type_id, title:place.title, icon:place.icon_id, lat:place.lat, lng:place.lng, description:place.description, url:place.url, address:place.address};

                 add_place(place.id, markers[place.id], true);
               }

              $("#modal_add").modal('hide');
            } else {
            	if(typeof(data.content) == 'object') {
            		$.each(data.content, function(index, content) {
            			$form.find("[name=" + index + "]").parents('.control-group').addClass('error');
            		});
	               $('.top-center').notify({
                    key: 'addform',
                    message: { text: '모든 필수 입력항목을 입력해주세요.' },
                    type:'error'
                  }).show();
            	} else {
                 $('.top-center').notify({
                    key: 'addform',
                    message: { text: '모든 필수 입력항목을 입력해주세요.' },
                    type:'error'
                 }).show();
            	}
            }

            $("#addform").modal('hide');
          }
      });
      
      
      $('#modal_image_add').on('show', function (event) {
		       var $this = $(this);
		       var $form = $("#addform_image");
		       var address = "";
	        
	           var data = $this.data();
	           if(typeof(data.defaultLatLng) != 'undefined' && data.defaultLatLng) {
              address = data.defaultLatLng.lat() + ", " + data.defaultLatLng.lng();
	            data.defaultLatLng = null;
	           }
	           
             $form.find( '[name=image]' ).val("");
	           $form.find( '[name=title]' ).val("");
	           $form.find( '[name=address]' ).val(address);
	           $form.find( '[name=description]' ).val("");
	            
	           $form.find("p").show();
	           $form.find("fieldset").show();
	           $form.find(".btn-primary").show();
      });

      $("#addform_image").ajaxForm({    
            dataType: 'json',
            success:function(data) {
              $form = $("#addform_image");
              $form.find('.error').removeClass('error');

                if(data.success) {
                 if(data.content.status != 'approved') {
                   $('.top-center').notify({
                      key: 'addform',
                      message: { html: '<h3>사진을 추가했습니다.</h3>' + '<p>관리자의 승인 후 실제 지도에 입력됩니다.</p>' },
                      type:'success'
                    }).show();
                 } else {
                   $('.top-center').notify({
                      key: 'addform',
                      message: { html: '<h3>사진을 추가했습니다.</h3>' },
                      type:'success'
                    }).show();
                 }

                $("#modal_image_add").modal('hide');

                 var place = data.content;
                 var image = {id: place.id, type:'category', type_id: 'image', title:place.title, image:place.image_small, original_image:place.image, lat:place.lat, lng:place.lng};
                 images.push(image);

                 add_image(images.length-1, image);

              } else {
                if(typeof(data.content) == 'object') {
                  $.each(data.content, function(index, content) {
                    $form.find("[name=" + index + "]").parents('.control-group').addClass('error');
                  });
                   $('.top-center').notify({
                      key: 'addform',
                      message: { text: '모든 필수 입력항목을 입력해주세요.' },
                      type:'error'
                    }).show();
                } else {
                   $('.top-center').notify({
                      key: 'addform',
                      message: { text: '모든 필수 입력항목을 입력해주세요.' },
                      type:'error'
                   }).show();
                }
                
              }

             $("#addform_image").modal('hide');
            }
      });
    </script>
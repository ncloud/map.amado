	
  <?php echo isset($error) && !empty($error) ? $error : ''; ?>

  <div class='notifications top-center'>
  </div>

	<a class="logo" href="<?php echo site_url("/");?>">아마도.지도</a>
	
    <!-- google map -->
    <div id="map"></div>
    
    <!-- right-side gutter -->
    <div class="menu" id="menu">        
	  
	  <div class="header" id="header">
	  	<a class="site" href="<?php echo site_url('/'.$site->permalink);?>"><?php echo $site->name;?></a>
	  </div>
	  
	  <?php if($current_user->id) { ?>
	  <!--<div class="buttons" id="buttons">
			<a href="#modal_info" class="btn btn-large btn-info" data-toggle="modal"><i class="icon-info-sign icon-white"></i>About this Map</a>
            <a href="#modal_add" class="btn btn-large btn-success" data-toggle="modal"><i class="icon-plus-sign icon-white"></i>장소 추가하기</a>
	  </div>-->
	  <?php } ?>
	  
	  <?php
	  	if($course_mode) {
	  ?>
		  <ul class="tab">
		  	<li class="selected"><a href="#">코스</a</li>
		  	<li><a href="#">분류</a></li>
		  </ul>
		  
		  <ul class="list" id="list_by_course">
		  	
		  	
		  </ul>
      <?php
		}
	  ?>
	  
      <ul class="list" id="list_by_category"<?php echo $course_mode ? ' style="display:none;"' : '';?>>
        <?php
          foreach($place_types as $type) {
            $markers_count = $count_by_type[$type->id];
            echo "<li class='category category_{$type->id}'>
                <div class='category_item'>
                  <div class='category_toggle' onClick=\"toggle('{$type->id}')\" id='filter_{$type->id}'></div>
                  <a href='#' onClick=\"toggleList('{$type->id}');\" class='category_info'><img src='".site_url('/img/icons/'.$type->id).".png' alt='' />{$type->name}<span class='total'> ({$markers_count})</span></a>
                </div>
                <ul class='list-items list-{$type->id}'>";
			
			if($markers_count > 0) {
				$markers = $place_lists_by_type[$type->id];
	            foreach($markers as $marker) {
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
    </div>
    
    <!-- more info modal -->
    <div class="modal hide" id="modal_info">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>이 지도는?</h3>
      </div>
      <div class="modal-body">
       asdflkjsdfl kjsadlkf jalskdj flksadjf lksjda lkjl
      </div>
      <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal" style="float: right;">Close</a>
      </div>
    </div>
    
    
    <!-- add something modal -->
    <div class="modal hide add_modal" id="modal_add">
    	<?php echo $this->view('/manage/add/place', array('modal_mode'=>true, 'place_types'=>$place_types));?>
    </div>
    
    
    <!-- add image modal -->
    <div class="modal hide add_modal" id="modal_image_add">     
      <?php echo $this->view('/manage/add/image', array('modal_mode'=>true));?>
    </div>

	<script type="text/javascript" src="<?php echo site_url('/js/plugin/jquery.form.js');?>"></script>
    
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>
	<script type="text/javascript" src="<?php echo site_url('/js/plugin/gmap.js');?>"></script>
	<script type="text/javascript" src="<?php echo site_url('/js/plugin/label.js');?>"></script>
	<script type="text/javascript" src="<?php echo site_url('/js/plugin/context_menu.js');?>"></script>

    <script type="text/javascript">
      var gmap = null;
	  var contextMenu = null;
      var gmarkers = {};      
   
      $(document).ready(function(){
      	initialize();
		
        resizeList() 
      });
      
      $(window).resize(function() {
        resizeList();
      });
      
      function initialize() {
        var markers = new Array();
        var images = new Array();
        <?php
            if($place_lists) {
              foreach($place_lists as $place) {
                if($place->attached == 'no') {
                  echo "markers[{$place->id}] = {title:'{$place->title}', icon:'{$place->icon_id}', lat:'{$place->lat}', lng:'{$place->lng}', description:'{$place->description}', url:'{$place->url}', address:'{$place->address}'};"; 
                } else if($place->attached == 'image') {
                  echo "images.push({title:'{$place->title}', type:'image', image:'{$place->image_small}', original_image:'{$place->image}', lat:'{$place->lat}', lng:'{$place->lng}'});";
                }
              }
            }
        ?>
      	
        gmap = new GMaps({
		  div: '#map',  
		  zoom: 16,
		  lat: <?php echo $default_lat;?>,
		  lng: <?php echo $default_lng;?>,
		  
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
        
        jQuery.each(images, function(i, val) {
          var info = "<div class='marker_title'>"+val.title+"</div>"
              + "<div class='marker_desc'><img src='"+val.original_image+"' alt='' /></div>";
              
          var markerImage = new google.maps.MarkerImage(val.image, null, null, new google.maps.Point(15,15), new google.maps.Size(30,30));
          var marker = gmap.addMarker({
      			  lat: val.lat,
      			  lng: val.lng,
      			  title: '',
      			  zIndex: 10 + i,
              icon: markerImage,
              infoWindow: {
              	content: info
              },
      			  click: function(e) {

      			  },
      			  mouseover: function() {
      	              $("#marker_image_"+i).show();//fadeIn('fast');
      	          },
  	          mouseout: function() { 
  	              $("#marker_image_"+i).hide();//fadeOut('fast');
  	          }
      			});
			   
          gmarkers['image_' + i] = marker;

          var label = new Label({id: 'image_' + i, map:gmap.map, distance: {x:0, y:15}});
          
          label.bindTo('position', marker);
          label.set("text", val.title);
          label.bindTo('visible', marker);
          label.bindTo('clickable', marker);
          label.bindTo('zIndex', marker);
        });
          
        // add markers
        jQuery.each(markers, function(i, val) {
          if(typeof(val) == 'undefined') return;
        
          infowindow = new google.maps.InfoWindow({
            content: ""
          });

          var iconSize = null;

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
          var markerImage = new google.maps.MarkerImage("<?php echo site_url('/img/icons/');?>/"+val.icon+".png", null, null, null, iconSize);
          var marker = gmap.addMarker({
      			  type: val.type,
      			  lat: val.lat,
      			  lng: val.lng,
      			  title: '',
      			  zIndex: 10 + i,
              icon: markerImage,
              infoWindow: {
              	content: info
              },
      			  click: function(e) {

      			  },
      			  mouseover: function() {
      	              $("#marker_place_"+i).show();//fadeIn('fast');
      	          },
      	          mouseout: function() { 
      	              $("#marker_place_"+i).hide();//fadeOut('fast');
      	          }
      			});

          gmarkers['place_' + i] = marker;
          
          // add marker label
          var label = new Label({id: 'place_' + i, map:gmap.map});
          
          label.bindTo('position', marker);
          label.set("text", val.title);
          label.bindTo('visible', marker);
          label.bindTo('clickable', marker);
          label.bindTo('zIndex', marker);
        });
        
        
        // context menu
        var contextMenuOptions={};
        contextMenuOptions.classNames={menu:'context_menu', menuSeparator:'context_menu_separator'};
        
        var menuItems=[];        
        menuItems.push({className:'context_menu_item', eventName:'add_here_click', label:'이곳에 장소 추가하기'});
        menuItems.push({className:'context_menu_item', eventName:'add_image_here_click', label:'이곳에 사진  추가하기'});
        menuItems.push({});
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
      
      function resizeList() {
        newHeight = $(window).height() - $("#header").outerHeight();
        $('.list').css('height', newHeight + "px"); 
      }
      
      function goToMarker(type, marker_id) {
        if(marker_id) {
          gmap.panTo(gmarkers[type + '_' + marker_id].getPosition());
       //   map.setZoom(15);
          google.maps.event.trigger(gmarkers[type + '_' + marker_id], 'click');
        }
      }

      function toggle(type_id) {
        if($('#filter_'+type_id).is('.inactive')) {
          show(type_id); 
        } else {
          hide(type_id); 
        }
      }

      function hide(type_id) {
        $.each(gmarkers, function(i, val) {
	          if (gmarkers[i].type == type_id) {
	            gmarkers[i].setVisible(false);
	          }
	        }
	      );
        $("#filter_"+type_id).addClass("inactive");
      }

      function show(type_id) {
        $.each(gmarkers, function(i, val) {
          if (gmarkers[i].type == type_id) {
            gmarkers[i].setVisible(true);
          }
        });
        
        $("#filter_"+type_id).removeClass("inactive");
      }
      
      function toggleList(type_id) {
        $(".list .list-"+type_id).toggle();
      }

      function markerListMouseOver(type, marker_id) {
		    gmap.panTo(gmarkers[type + '_' + marker_id].getPosition());

        $("#marker"+marker_id).css("display", "inline");
      }
      
      function markerListMouseOut(type, marker_id) {
        $("#marker"+marker_id).css("display", "none");
      }
      
      // add modal form submit
      $('#modal_add').on('show', function (event) {
		       var $this = $(this);
		       var $form = $("#addform");
		       var address = "";
	           var data = $this.data();
	           if(typeof(data.defaultLatLng) != 'undefined' && data.defaultLatLng) {
	            address = data.defaultLatLng.jb + ", " + data.defaultLatLng.kb;
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
               $('.top-center').notify({
                  key: 'addform',
                  message: { html: '<h3>장소를 추가했습니다.</h3>' + (data.content.status == 'approved' ? '새로고침하시면 실제 입력된 모습을 보실 수 있습니다.' : '관리자의 승인 후 실제 지도에 입력됩니다.') },
                  type:'success'
                }).show();

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
          }
      });
      
      
      $('#modal_image_add').on('show', function (event) {
		       var $this = $(this);
		       var $form = $("#addform_image");
		       var address = "";
	        
	           var data = $this.data();
	           if(typeof(data.defaultLatLng) != 'undefined' && data.defaultLatLng) {
              address = data.defaultLatLng.jb + ", " + data.defaultLatLng.kb;
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
              console.log(data);

              $form = $("#addform_image");
              $form.find('.error').removeClass('error');

              if(data.success) {
                 $('.top-center').notify({
                    key: 'addform',
                    message: { html: '<h3>사진을 추가했습니다.</h3>' + (data.content.status == 'approved' ? '새로고침하시면 실제 입력된 모습을 보실 수 있습니다.' : '관리자의 승인 후 실제 지도에 입력됩니다.') },
                    type:'success'
                  }).show();

                $("#modal_image_add").modal('hide');
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
            }
      });
    </script>
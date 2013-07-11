	
    
    <?php echo isset($error) && !empty($error) ? $error : ''; ?>
    
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
                  <a href='#' onClick=\"toggleList('{$type->id}');\" class='category_info'><img src='./img/icons/{$type->id}.png' alt='' />{$type->name}<span class='total'> ({$markers_count})</span></a>
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
    <div class="modal hide" id="modal_add">
      <form action="add.php" method="post" id="modal_addform" class="form-horizontal">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">×</button>
          <h3>추가하기</h3>
        </div>
        <div class="modal-body">
          <div id="result"></div>
          <fieldset>
          <?php if($current_user->id) { ?>
          	<input type="hidden" id="add_owner_name" name="owner_name" value="관리자" />
          	<input type="hidden" id="add_owner_email" name="owner_email" value="owner@domain.com" />
          <?php } else { ?>
            <div class="control-group">
              <label class="control-label" for="add_owner_name">신청자 이름</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="owner_name" id="add_owner_name" maxlength="100">
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_owner_email">신청자 이메일</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="owner_email" id="add_owner_email" maxlength="100">
              </div>
            </div>
          <?php } ?>
            <div class="control-group">
              <label class="control-label" for="add_title">장소(공간) 이름</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="title" id="add_title" maxlength="100" autocomplete="off">
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="input01">장소(공간) 종류</label>
              <div class="controls">
                <select name="type" id="add_type" class="input-xlarge">
                	<?php foreach($place_types as $type) { ?>
                  	<option value="<?php echo $type->id;?>"><?php echo $type->name;?></option>
                  	<?php } ?>
                </select>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_address">장소(공간) 주소</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="address" id="add_address">
                <p class="help-block">
                  구글 지도에서 해당 주소를 검색하여 추가합니다. 정확한 주소를 입력해 주셔야 정확한 위치에 추가됩니다.
                </p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_uri">장소(공간) URL</label>
              <div class="controls">
                <input type="text" class="input-xlarge" id="add_uri" name="uri" placeholder="http://">
                <p class="help-block">
                  장소(공간)에서 운영하고 있는 홈페이지, 페이스북등 대표 주소를 입력해주세요. 예: "http://www.yoursite.com"
                </p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_description">장소(공간) 설명</label>
              <div class="controls">
                <textarea class="input-xlarge" id="add_description" name="description" maxlength="150"></textarea>
                <p class="help-block">
                  최대 150자 내외로 장소(공간)에 대한 설명을 입력해주세요.
                </p>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">신청하기</button>
          <a href="#" class="btn" data-dismiss="modal" style="float: right;">닫기</a>
        </div>
      </form>
    </div>
    
    
    <!-- add image modal -->
    <div class="modal hide" id="modal_image_add">
      <form enctype="multipart/form-data" method="post" action="add_image.php" id="modal_image_addform" class="form-horizontal">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">×</button>
          <h3>사진 추가하기</h3>
        </div>
        <div class="modal-body">
          <div id="result"></div>
          <fieldset>
          <?php if($current_user->id) { ?>
            <input type="hidden" id="add_image_owner_name" name="owner_name" value="관리자" />
            <input type="hidden" id="add_image_owner_email" name="owner_email" value="owner@domain.com" />
          <?php } else { ?>
            <div class="control-group">
              <label class="control-label" for="add_image_owner_name">신청자 이름</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="owner_name" id="add_image_owner_name" maxlength="100">
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_image_owner_email">신청자 이메일</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="owner_email" id="add_image_owner_email" maxlength="100">
              </div>
            </div>
          <?php } ?>
            <div class="control-group">
              <label class="control-label" for="add_image_file">장소(공간) 사진</label>
              <div class="controls">
                <input type="file" class="input-xlarge" name="image" id="add_image_file" autocomplete="off">
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_image_title">장소(공간) 이름</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="title" id="add_image_title" maxlength="100" autocomplete="off">
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_image_address">장소(공간) 주소</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="address" id="add_image_address">
                <p class="help-block">
                  구글 지도에서 해당 주소를 검색하여 추가합니다. 정확한 주소를 입력해 주셔야 정확한 위치에 추가됩니다.
                </p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="add_image_description">장소(공간) 설명</label>
              <div class="controls">
                <textarea class="input-xlarge" id="add_image_description" name="description" maxlength="150"></textarea>
                <p class="help-block">
                  최대 150자 내외로 장소(공간)에 대한 설명을 입력해주세요.
                </p>
              </div>
            </div>
          </fieldset>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">신청하기</button>
          <a href="#" class="btn" data-dismiss="modal" style="float: right;">닫기</a>
        </div>
      </form>
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
            foreach($place_lists as $place) {
              echo 
               "markers[{$place->id}] = {title:'{$place->title}', icon:'{$place->icon_id}', lat:'{$place->lat}', lng:'{$place->lng}', description:'{$place->description}', uri:'".$place->uri."', address:'".$place->address."'};"; 
            }

           // images
            foreach($image_lists as $image) {
             echo "images.push({title:'{$image->title}', type:'image', image:'./uploads/30x30_{$image->image}', original_image:'./uploads/{$image->image}', lat:'{$image->lat}', lng:'{$image->lng}'});";
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
        google.maps.event.addListener(map, 'zoom_changed', function() {
          zoomLevel = gmap.getZoom();
        });
        
        jQuery.each(images, function(i, val) {
          var info = "<div class='marker_title'>"+val.title+"</div>"
              + "<div class='marker_desc'><img src='"+val.original_image+"' alt='' /></div>";
              
          var markerImage = new google.maps.MarkerImage(val.image, null, null, null, new google.maps.Size(30,30));
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
	              $("#marker"+i).show();//fadeIn('fast');
	          },
	          mouseout: function() { 
	              $("#marker"+i).hide();//fadeOut('fast');
	          }
			});
			
          gmarkers['image_' + i] = marker;
        });
          
        // add markers
        jQuery.each(markers, function(i, val) {
          if(typeof(val) == 'undefined') return;
        
          infowindow = new google.maps.InfoWindow({
            content: ""
          });

          var iconSize = null;

        // format marker URI for display and linking
         var markerURI = val.uri;
         if(markerURI.substr(0,7) != "http://") {
        	markerURI = "http://" + markerURI; 
         } 
         var markerURI_short = markerURI.replace("http://", "");
         var markerURI_short = markerURI_short.replace("www.", "");
      
         var info = 
	          "<div class='marker_title'>"+val.title+"</div>"
	          + "<div class='marker_uri'><a target='_blank' href='"+markerURI+"'>"+markerURI_short+"</a></div>"
	          + "<div class='marker_desc'>"+val.description+"</div>"
	          + "<div class='marker_address'>"+val.address+"</div>";	         
	             
          // build this marker
          var markerImage = new google.maps.MarkerImage("./img/icons/"+val.icon+".png", null, null, null, iconSize);
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
	              $("#marker"+i).show();//fadeIn('fast');
	          },
	          mouseout: function() { 
	              $("#marker"+i).hide();//fadeOut('fast');
	          }
			});

          gmarkers['place_' + i] = marker;
          
          // add marker label
          var label = new Label({id: i, map:gmap.map});
          
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
        var $form = $("#modal_addform");
        var address = "";
           var data = $this.data();
           if(typeof(data.defaultLatLng) != 'undefined' && data.defaultLatLng) {
            address = data.defaultLatLng.kb + ", " + data.defaultLatLng.jb;
            data.defaultLatLng = null;
           }
        
            $form.find( '#add_title' ).val("");
            $form.find( '#add_type' ).val("");
            $form.find( '#add_address' ).val(address);
            $form.find( '#add_uri' ).val("");
            $form.find( '#add_description' ).val("");
            
            $form.find("#result").html("").removeClass('alert alert-danger');              
        
            $form.find("p").show();
            $form.find("fieldset").show();
            $form.find(".btn-primary").show();
      });

      $("#modal_addform").ajaxForm({
        success: function(data) {
            var content = $( data ).find( '#content' );
            
            // if submission was successful, show info alert
            if(data == "success") {
              $("#modal_addform #result").html("We've received your submission and will review it shortly. Thanks!"); 
              $("#modal_addform #result").addClass("alert alert-info");
              $("#modal_addform p").css("display", "none");
              $("#modal_addform fieldset").css("display", "none");
              $("#modal_addform .btn-primary").css("display", "none");
              
            // if submission failed, show error
            } else {
              $("#modal_addform #result").html(data); 
              $("#modal_addform #result").addClass("alert alert-danger");
            }
          }
      });
      
      
      // add modal form submit
      $('#modal_image_add').on('show', function (event) {
        var $this = $(this);
        var $form = $("#modal_image_addform");
        var address = "";
        
           var data = $this.data();
           if(typeof(data.defaultLatLng) != 'undefined' && data.defaultLatLng) {
            address = data.defaultLatLng.kb + ", " + data.defaultLatLng.jb;
            data.defaultLatLng = null;
           }
        
            $form.find( '#add_image_title' ).val("");
            $form.find( '#add_image_address' ).val(address);
            $form.find( '#add_image_description' ).val("");
            
            $form.find("#result").html("").removeClass('alert alert-danger');              
        
            $form.find("p").show();
            $form.find("fieldset").show();
            $form.find(".btn-primary").show();
      });

      $("#modal_image_addform").ajaxForm({
            success:function(data) {
                var content = $( data ).find( '#content' );
                
                // if submission was successful, show info alert
                if(data == "success") {
                  $("#modal_image_addform #result").html("We've received your submission and will review it shortly. Thanks!"); 
                  $("#modal_image_addform #result").addClass("alert alert-info");
                  $("#modal_image_addform p").css("display", "none");
                  $("#modal_image_addform fieldset").css("display", "none");
                  $("#modal_image_addform .btn-primary").css("display", "none");
                  
                // if submission failed, show error
                } else {
                  $("#modalimage__addform #result").html(data); 
                  $("#modal_image_addform #result").addClass("alert alert-danger");
                }
            }
      });
    </script>
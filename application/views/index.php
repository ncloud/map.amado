
    <script type="text/javascript">
      var map;
      var infowindow = null;
      var gmarkers = {};
      var highestZIndex = 0;  
      var agent = "default";
      var zoomControl = true;

      // detect browser agent
      $(document).ready(function(){
        if(navigator.userAgent.toLowerCase().indexOf("iphone") > -1 || navigator.userAgent.toLowerCase().indexOf("ipod") > -1) {
          agent = "iphone";
          zoomControl = false;
        }
        if(navigator.userAgent.toLowerCase().indexOf("ipad") > -1) {
          agent = "ipad";
          zoomControl = false;
        }
      }); 
      

      // resize marker list onload/resize
      $(document).ready(function(){
        resizeList() 
      });
      $(window).resize(function() {
        resizeList();
      });
      
      // resize marker list to fit window
      function resizeList() {
        newHeight = $('html').height() - $("#header").outerHeight();
        $('.list').css('height', newHeight + "px"); 
      }


      // initialize map
      function initialize() {
        // markers array: name, type (icon), lat, long, description, uri, address
        var markers = new Array();
        var images = new Array();
        <?php
            foreach($place_lists as $place) {
              echo 
               "markers[{$place->id}] = (['{$place->title}', '{$place->icon_id}', '{$place->lat}', '{$place->lng}', '{$place->description}', '".$place->uri."', '".$place->address."']);"; 
            }

           // images
            foreach($image_lists as $image) {
             echo "images.push(['{$image->title}', 'image', './uploads/30x30_{$image->image}', './uploads/{$image->image}', '{$image->lat}', '{$image->lng}', '{$image->start_date}']);";
            }
        ?>
        
        // set map options
        var myOptions = {
          zoom: 16,
          center: new google.maps.LatLng(<?php echo $default_lat;?>,<?php echo $default_lng;?>),
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          streetViewControl: false,
          mapTypeControl: false,
          panControl: false,
          draggableCursor:'default',
          zoomControl: zoomControl,
          zoomControlOptions: {
            style: google.maps.ZoomControlStyle.SMALL,
            position: google.maps.ControlPosition.LEFT_CENTER
          }
        };
        map = new google.maps.Map(document.getElementById('map_canvas'), myOptions);
        zoomLevel = map.getZoom();

        // prepare infowindow
        infowindow = new google.maps.InfoWindow({
          content: "holding..."
        });

        // only show marker labels if zoomed in
        google.maps.event.addListener(map, 'zoom_changed', function() {
          zoomLevel = map.getZoom();
        });
        
        jQuery.each(images, function(i, val) {
          var markerImage = new google.maps.MarkerImage(val[2], null, null, null, new google.maps.Size(30,30));
          var marker = new google.maps.Marker({
            position: new google.maps.LatLng(val[4], val[5]),
            map: map,
            title: '',
            clickable: true,
            infoWindowHtml: '',
            zIndex: 1,
            icon: markerImage
          });
          gmarkers['image_' + i] = marker;

          google.maps.event.addListener(marker, 'click', function () {
            infowindow.setContent(
              "<div class='marker_title'>"+val[0]+"</div>"
              + "<div class='marker_desc'><img src='"+val[3]+"' alt='' /></div>"
            );
            infowindow.open(map, this);
          });
        });
          
        // add markers
        jQuery.each(markers, function(i, val) {
          if(typeof(val) == 'undefined') return;
        
          infowindow = new google.maps.InfoWindow({
            content: ""
          });

          // show smaller marker icons on mobile
          if(agent == "iphone") {
            var iconSize = new google.maps.Size(31,42);
          } else {
            iconSize = null;
          }

          // build this marker
          
          var markerImage = new google.maps.MarkerImage("./img/icons/"+val[1]+".png", null, null, null, iconSize);
          var marker = new google.maps.Marker({
            position: new google.maps.LatLng(val[2], val[3]),
            map: map,
            title: '',
            clickable: true,
            infoWindowHtml: '',
            zIndex: 10 + i,
            icon: markerImage
          });
          marker.type = val[1];
          gmarkers['place_' + i] = marker;

          // add marker hover events (if not viewing on mobile)
          if(agent == "default") {
            google.maps.event.addListener(marker, "mouseover", function() {
           //   this.old_ZIndex = this.getZIndex(); 
           //   this.setZIndex(9999); 
              $("#marker"+i).show();//fadeIn('fast');
           //   $("#marker"+i).css("z-index", "99999");
            });
            google.maps.event.addListener(marker, "mouseout", function() { 
              //if (this.old_ZIndex && zoomLevel <= 15) {
             //   this.setZIndex(this.old_ZIndex); 
                $("#marker"+i).hide();//fadeOut('fast');
              //}
            }); 
          }

          // format marker URI for display and linking
          var markerURI = val[5];
          if(markerURI.substr(0,7) != "http://") {
            markerURI = "http://" + markerURI; 
          }
          var markerURI_short = markerURI.replace("http://", "");
          var markerURI_short = markerURI_short.replace("www.", "");

          // add marker click effects (open infowindow)
          google.maps.event.addListener(marker, 'click', function () {
            infowindow.setContent(
              "<div class='marker_title'>"+val[0]+"</div>"
              + "<div class='marker_uri'><a target='_blank' href='"+markerURI+"'>"+markerURI_short+"</a></div>"
              + "<div class='marker_desc'>"+val[4]+"</div>"
              + "<div class='marker_address'>"+val[6]+"</div>"
            );
            infowindow.open(map, this);
          });

          // add marker label
          var latLng = new google.maps.LatLng(val[2], val[3]);
          var label = new Label({
            map: map,
            id: i
          });
          label.bindTo('position', marker);
          label.set("text", val[0]);
          label.bindTo('visible', marker);
          label.bindTo('clickable', marker);
          label.bindTo('zIndex', marker);
        });

        
        // context menu
        //  create the ContextMenuOptions object
        var contextMenuOptions={};
        contextMenuOptions.classNames={menu:'context_menu', menuSeparator:'context_menu_separator'};
        
        //  create an array of ContextMenuItem objects
        var menuItems=[];        
        menuItems.push({className:'context_menu_item', eventName:'add_here_click', label:'이곳에 장소 추가하기'});
        menuItems.push({className:'context_menu_item', eventName:'add_image_here_click', label:'이곳에 사진  추가하기'});
        menuItems.push({});
        menuItems.push({className:'context_menu_item', eventName:'zoom_in_click', label:'지도 확대하기'});
        menuItems.push({className:'context_menu_item', eventName:'zoom_out_click', label:'지도 축소하기'});
        menuItems.push({});
        menuItems.push({className:'context_menu_item', eventName:'center_map_click', label:'이곳을 지도 가운데 옮기기'});
        contextMenuOptions.menuItems=menuItems;

        //  create the ContextMenu object
        var contextMenu = new ContextMenu(map, contextMenuOptions);

        //  display the ContextMenu on a Map right click
        google.maps.event.addListener(map, 'rightclick', function(mouseEvent){
            contextMenu.show(mouseEvent.latLng);
        });
        
        //  listen for the ContextMenu 'menu_item_selected' event
        google.maps.event.addListener(contextMenu, 'menu_item_selected', function(latLng, eventName){
            //  latLng is the position of the ContextMenu
            //  eventName is the eventName defined for the clicked ContextMenuItem in the ContextMenuOptions
            switch(eventName){
                case 'add_here_click':
                    $('#modal_add').data({defaultLatLng:latLng}).modal();
                    break;
                case 'add_image_here_click':
                    $('#modal_image_add').data({defaultLatLng:latLng}).modal();
                    break;
                case 'zoom_in_click':
                    map.setZoom(map.getZoom()+1);
                    break;
                case 'zoom_out_click':
                    map.setZoom(map.getZoom()-1);
                    break;
                case 'center_map_click':
                    map.panTo(latLng);
                    break;
            }
        });
        
      } 

      // zoom to specific marker
      function goToMarker(type, marker_id) {
        if(marker_id) {
          map.panTo(gmarkers[type + '_' + marker_id].getPosition());
       //   map.setZoom(15);
          google.maps.event.trigger(gmarkers[type + '_' + marker_id], 'click');
        }
      }

      // toggle (hide/show) markers of a given type (on the map)
      function toggle(type_id) {
        if($('#filter_'+type_id).is('.inactive')) {
          show(type_id); 
        } else {
          hide(type_id); 
        }
      }

      // hide all markers of a given type
      function hide(type_id) {
        $.each(gmarkers, function(i, val) {
	          if (gmarkers[i].type == type_id) {
	            gmarkers[i].setVisible(false);
	          }
	        }
	      );
        $("#filter_"+type_id).addClass("inactive");
      }

      // show all markers of a given type
      function show(type_id) {
        $.each(gmarkers, function(i, val) {
          if (gmarkers[i].type == type_id) {
            gmarkers[i].setVisible(true);
          }
        });
        
        $("#filter_"+type_id).removeClass("inactive");
      }
      
      // toggle (hide/show) marker list of a given type
      function toggleList(type_id) {
        $(".list .list-"+type_id).toggle();
      }


      // hover on list item
      function markerListMouseOver(type, marker_id) {
		map.panTo(gmarkers[type + '_' + marker_id].getPosition());

        $("#marker"+marker_id).css("display", "inline");
      }
      function markerListMouseOut(type, marker_id) {
        $("#marker"+marker_id).css("display", "none");
      }

      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
    
  <?php echo isset($error) && !empty($error) ? $error : ''; ?>
    
    <!-- google map -->
    <div id="map_canvas"></div>
    
    <!-- right-side gutter -->
    <div class="menu" id="menu">        
	  
	  <div class="header" id="header">
	  	<h1>아마도 지도 <span>성북동</span></h1>
	  	<!--<a href="#modal_info" data-toggle="modal"></a>-->
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
    
    <script>
      // add modal form submit
      $('#modal_add').on('show', function (event) {
        var $this = $(this);
        var $form = $("#modal_addform");
        var address = "";
        
           var data = $this.data();
           if(typeof(data.defaultLatLng) != 'undefined' && data.defaultLatLng) {
            address = data.defaultLatLng.kb + ", " + data.defaultLatLng.lb;
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
            address = data.defaultLatLng.kb + ", " + data.defaultLatLng.lb;
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
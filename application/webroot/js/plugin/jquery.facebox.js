/*
 * Facebox (for jQuery)
 * version: 1.2 (05/05/2008)
 * @requires jQuery v1.2 or later
 *
 */

(function($) {
  $.facebox = function(data, klass) {
    $.facebox.loading()
    
    if(typeof(data.settings) != 'undefined') {
    	$.extend($.facebox.settings, data.settings);
    }
    
    if (data.ajax) fillFaceboxFromAjax(data.ajax, klass)
    else if (data.image) fillFaceboxFromImage(data.image, klass)
    else if (data.div) fillFaceboxFromHref(data.div, klass)
    else if ($.isFunction(data)) data.call($)
    else $.facebox.reveal(data, klass)
  }

  /*
   * Public, $.facebox methods
   */

  $.extend($.facebox, {
    settings: {},
    
    oHeight: 0,

    loading: function() {
      init()
      showOverlay()
		
      $('#facebox').hide()
      $('#facebox .content').empty()
      
      if($("#facebox_loading").length == 0) {
     	 $('body').append('<div id="facebox_loading" class="loading"><img src="'+$.facebox.settings.loadingImage+'"/></div>')
      }

      $('#facebox').css({
        top:	$.facebox.settings.top,//getPageScroll()[1] + (getPageHeight() / 10),
        left:	$(window).width() / 2 - ($('#facebox .popup').width() / 2)
      });
      
      $(window).resize(function() {
      	$.facebox.repos();
      });
      
      $(document).bind('keydown.facebox', function(e) {
        if (e.keyCode == 27) { // esc key
        	if($.facebox.settings.autoHideEvent) $.facebox.close()
        }
        return true
      })
      $(document).trigger('loading.facebox')
    },
    
    resize: function(w, h) {
    	 var $facebox = $('#facebox');
     	 var $content = $('#facebox .content')
     	 
     	 if(typeof(w) == 'number') $content.width(w);
     	 if(typeof(h) == 'number')  $content.height(h);
     	 
     	 $facebox.css('left', $(window).width() / 2 - ($facebox.width() / 2))
    },
    
    repos: function() {
    	 var $facebox = $('#facebox');
     	 
     	 $facebox.css('left', $(window).width() / 2 - ($facebox.width() / 2))
     	 
     	 var check_height = $facebox.height() + parseInt($facebox.css('top'));
     	 if(check_height > $(window).height() - 20) {
     	 	var top =  ($(window).height() / 2) - ($facebox.height() / 2);
     	 	if(top < 20) top = 20;
     	 	
     	 	$facebox.animate({'top': top + 'px'});
     	 }
     	 
     	 if($facebox.height() + parseInt($facebox.css('top')) > $(window).height()) {
     	 	$("body").addClass('fixed_for_facebox');
     	 } else {
     	 	$("body").removeClass('fixed_for_facebox');
     	 }
    },
    
    resetpos: function() {
    	$('#facebox').animate({top: $.facebox.settings.top + 'px'});
    },

    reveal: function(data, klass) {
      $(document).trigger('beforeReveal.facebox')
      $("#facebox").show();
      
      if (klass) $('#facebox .content').addClass(klass)
       
      $('#facebox .content').css('width', $.facebox.settings.width).append(data)
      $('#facebox .popup').show()

      $('#facebox').css('left', $(window).width() / 2 - ($('#facebox .popup').width() / 2)).css('top', $.facebox.settings.top)
      $('#facebox .close').click($.facebox.close)
          
	  if($.facebox.settings.simple) {
	    	$('#facebox .popup').addClass('simple_popup');
	  } else {
	    	$('#facebox .popup').removeClass('simple_popup');
	  }
      
      //$('#facebox_loading').fadeOut(function() { $("#facebox_loading").remove(); });
      $("#facebox_loading").remove();
      
      $(document).trigger('reveal.facebox').trigger('afterReveal.facebox')

	  if($.facebox.settings.onReady) $.facebox.settings.onReady();
	  
      $.facebox.repos();
    },

    close: function(is_close) {
      if(typeof(is_close) == 'undefined') is_close = false; 
    
      $("body").removeClass("fixed_for_facebox");
	  
	  $(document).unbind('keydown.facebox')
	  $('#facebox').fadeOut(function() {
	      $("#facebox_loading").remove();
	      $('#facebox .content').removeClass().addClass('content')
	      $("body").removeClass("fixed_for_facebox");
	      $(document).trigger('afterClose.facebox')
	  })
	    
	  if($.facebox.settings.onClose) $.facebox.settings.onClose(is_close);
		  
      hideOverlay()
    
      return false
    },
    
    gotoHide: function() {
    	$("#facebox").animate({top:$(window).height() + 'px'}, 'fast');
    },
    
    resetSettings: function() {
	    $.facebox.settings = {
	      opacity      : 0.5,
	      overlay      : true,
	      top		   : 160,
	      width		   : 380,
	      simple	   : false,
	      autoHideEvent : true, // overlay click, esc key click
	      loadingImage : '/img/plugin/facebox/loading.gif',
	      imageTypes   : [ 'png', 'jpg', 'jpeg', 'gif' ],
	      faceboxHtml  : '<div id="facebox" style="display:none;"><div class="popup"><div class="content"></div></div></div>',
	      onReady: null,
	      onClose: null
	    };
    }
  })

  /*
   * Public, $.fn methods
   */

  $.fn.facebox = function(settings) {
    if ($(this).length == 0) return

    init(settings)

    function clickHandler() {
      $.facebox.loading(true)

      // support for rel="facebox.inline_popup" syntax, to add a class
      // also supports deprecated "facebox[.inline_popup]" syntax
      var klass = this.rel.match(/facebox\[?\.(\w+)\]?/)
      if (klass) klass = klass[1]

      fillFaceboxFromHref(this.href, klass)
      return false
    }

    return this.bind('click.facebox', clickHandler)
  }

  /*
   * Private methods
   */

  // called one time to setup facebox on this page
  function init(settings) {
    $.facebox.resetSettings();

    if ($.facebox.settings.inited) return true
    else $.facebox.settings.inited = true

    $(document).trigger('init.facebox')
    makeCompatible()

    var imageTypes = $.facebox.settings.imageTypes.join('|')
    $.facebox.settings.imageTypesRegexp = new RegExp('\.(' + imageTypes + ')$', 'i')

    if (settings) $.extend($.facebox.settings, settings)

    if($("#facebox").length == 0)
      $('body').append($.facebox.settings.faceboxHtml)

    var preload = [ new Image() ]
    preload[0].src = $.facebox.settings.loadingImage

    $('#facebox').find('.b:first, .bl').each(function() {
      preload.push(new Image())
      preload.slice(-1).src = $(this).css('background-image').replace(/url\((.+)\)/, '$1')
    })

    $('#facebox .close').click(function() { $.facebox.close(true); } );
  }

  // getPageScroll() by quirksmode.com
  function getPageScroll() {
    var xScroll, yScroll;
    if (self.pageYOffset) {
      yScroll = self.pageYOffset;
      xScroll = self.pageXOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {	 // Explorer 6 Strict
      yScroll = document.documentElement.scrollTop;
      xScroll = document.documentElement.scrollLeft;
    } else if (document.body) {// all other Explorers
      yScroll = document.body.scrollTop;
      xScroll = document.body.scrollLeft;
    }
    return new Array(xScroll,yScroll)
  }

  // Adapted from getPageSize() by quirksmode.com
  function getPageHeight() {
    var windowHeight
    if (self.innerHeight) {	// all except Explorer
      windowHeight = self.innerHeight;
    } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
      windowHeight = document.documentElement.clientHeight;
    } else if (document.body) { // other Explorers
      windowHeight = document.body.clientHeight;
    }
    return windowHeight
  }

  // Backwards compatibility
  function makeCompatible() {
    var $s = $.facebox.settings

    $s.loadingImage = $s.loading_image || $s.loadingImage
    $s.closeImage = $s.close_image || $s.closeImage
    $s.imageTypes = $s.image_types || $s.imageTypes
    $s.faceboxHtml = $s.facebox_html || $s.faceboxHtml
  }

  // Figures out what you want to display and displays it
  // formats are:
  //     div: #id
  //   image: blah.extension
  //    ajax: anything else
  function fillFaceboxFromHref(href, klass) {
    // div
    if (href.match(/#/)) {
      var url    = window.location.href.split('#')[0]
      var target = href.replace(url,'')
      if (target == '#') return
      $.facebox.reveal($(target).html(), klass)

    // image
    } else if (href.match($.facebox.settings.imageTypesRegexp)) {
      fillFaceboxFromImage(href, klass)
    // ajax
    } else {
      fillFaceboxFromAjax(href, klass)
    }
  }

  function fillFaceboxFromImage(href, klass) {
    var image = new Image()
    image.onload = function() {
      $.facebox.reveal('<div class="image"><img src="' + image.src + '" /></div>', klass)
    }
    image.src = href
  }

  function fillFaceboxFromAjax(href, klass) {
    $.get(href, function(data) { $.facebox.reveal(data, klass) })
  }

  function skipOverlay() {
    return $.facebox.settings.overlay == false || $.facebox.settings.opacity === null
  }

  function showOverlay() {
    if (skipOverlay()) return

    if ($('#facebox_overlay').length == 0)
      $("body").append('<div id="facebox_overlay" class="facebox_hide"></div>')

    $('#facebox_overlay').hide().addClass("facebox_overlayBG")
      .css('opacity', $.facebox.settings.opacity)
      .click(function() { if($.facebox.settings.autoHideEvent) $.facebox.close(); })
    //  .fadeIn(200)
      .show()
    return false
  }

  function hideOverlay() {
    if (skipOverlay()) return

    $('#facebox_overlay').fadeOut(200, function(){
      $("#facebox_overlay").removeClass("facebox_overlayBG")
      $("#facebox_overlay").addClass("facebox_hide")
      $("#facebox_overlay").remove()
    })

    return false
  }

})(jQuery);

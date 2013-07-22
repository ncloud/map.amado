
	function delay(callback, ms) {
	  var timer = 0;
	  timer = setTimeout(function() {
	  	clearTimeout(timer);
	  	callback();
	  }, ms);
	};

	function sliceText(text) {
		var font_cho = Array(
		'ㄱ', 'ㄲ', 'ㄴ', 'ㄷ', 'ㄸ',
		'ㄹ', 'ㅁ', 'ㅂ', 'ㅃ', 'ㅅ', 'ㅆ',
		'ㅇ', 'ㅈ', 'ㅉ', 'ㅊ', 'ㅋ', 'ㅌ', 'ㅍ', 'ㅎ' );

		var font_jung = Array(
		'ㅏ', 'ㅐ', 'ㅑ', 'ㅒ', 'ㅓ',
		'ㅔ', 'ㅕ', 'ㅖ', 'ㅗ', 'ㅘ', 'ㅙ',
		'ㅚ', 'ㅛ', 'ㅜ', 'ㅝ', 'ㅞ', 'ㅟ',
		'ㅠ', 'ㅡ', 'ㅢ', 'ㅣ' );

		var font_jong = Array(
		'', 'ㄱ', 'ㄲ', 'ㄳ', 'ㄴ', 'ㄵ', 'ㄶ', 'ㄷ', 'ㄹ',
		'ㄺ', 'ㄻ', 'ㄼ', 'ㄽ', 'ㄾ', 'ㄿ', 'ㅀ', 'ㅁ',
		'ㅂ', 'ㅄ', 'ㅅ', 'ㅆ', 'ㅇ', 'ㅈ', 'ㅊ', 'ㅋ', 'ㅌ', 'ㅍ', 'ㅎ' );

		var completeCode = text.charCodeAt(0);
		var uniValue = completeCode - 0xAC00;

		var jong = uniValue % 28;
		var jung = ( ( uniValue - jong ) / 28 ) % 21;
		var cho = parseInt (( ( uniValue - jong ) / 28 ) / 21);

		if(cho < 0) {
			if($.inArray(text, font_cho)) return text;
		} else {
			return font_cho[cho] + font_jung[jung] + font_jong[jong];
		}
	}

	function sliceTexts(text) {
		var result = '';
		for(var i=0;i<text.length;i++) {
			result += sliceText(text[i]);
		}
		return result;
	}
    
    var numberFormat = function(number, decimals, dec_point, thousands_sep) {
	     number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
	    var n = !isFinite(+number) ? 0 : +number,
	        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	        s = '',
	        toFixedFix = function (n, prec) {
	            var k = Math.pow(10, prec);
	            return '' + Math.round(n * k) / k;
	        };
	    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
	    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
	    if (s[0].length > 3) {
	        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	    }
	    if ((s[1] || '').length < prec) {
	        s[1] = s[1] || '';
	        s[1] += new Array(prec - s[1].length + 1).join('0');
	    }
	    return s.join(dec);
    }
    
    var restyleText = function(input) {
	        var input2 = numberFormat(input).replace(',','.');
	        var input_count = input2.split('.').length - 1;
	        if(input_count != '0'){
	            if(input_count== '1'){
	            	if(input2.substr(input2.length - 3, 1) == '0') {
	                	return input2.substr(0, input2.length - 4) + 'K';
	            	} else {
	                	return input2.substr(0, input2.length - 2) + 'K';
	                }
	            } else if(input2 == '2'){
	            	if(input2.substr(input2.length - 7, 1) == '0') {
	                	return input2.substr(0, input2.length - 8) + 'K';
	            	} else {
	                	return input2.substr(0, input2.length - 6) + 'M';
	                }
	            } else if(input2 == '3'){
	            	if(input2.substr(input2.length - 11, 1) == '0') {
	                	return input2.substr(0,  input2.length - 12) + 'B';
	               	} else {
	                	return input2.substr(0,  input2.length - 10) + 'B';
	               	}
	            } else {
	                return '';
	            }
	        } else {
	            return input;
	        }
    }
    
    var stringToTime = function(date) {
    	return Math.round(new Date(date).getTime() / 1000);
    }
    
	var toTimeString = function(diffInSecs) {
	   // Math.max makes sure that you'll get '00:00' if start > end.
	   
	   var diffInMinutes = Math.max(0, Math.floor(diffInSecs / 60));
	   var diffInHours = Math.max(0, Math.floor(diffInMinutes / 60));
	   
	   diffInSecs = diffInSecs % 60;
	   diffInMinutes = diffInMinutes % 60;
	   
	   var result = [
	       ('0'+diffInHours).slice(-2),
	       ('0'+diffInMinutes).slice(-2),
	       ('0'+diffInSecs).slice(-2)
	   ].join(':');
	   
	   if(result.substr(0,3) == '00:') result = result.substr(3);
	   
	   return result;
	}	
	
	var isNumber = function(s) {
	  s += ''; // 문자열로 변환
	  s = s.replace(/^\s*|\s*$/g, ''); // 좌우 공백 제거
	  if (s == '' || isNaN(s)) return false;
	  return true;
	}
	
	var replaceAll = function(str,orgStr,repStr) {
		if(typeof(str) == 'string') {
	   	 return str.split(orgStr).join(repStr);
	   	} else {
	     return str;
	    }
	}
	
	/* thanks stackoverflow and Lasnv : http://stackoverflow.com/questions/3452546/javascript-regex-how-to-get-youtube-video-id-from-url */
	var youtubeParser = function(url){
		if(url.indexOf('youtu') == -1) return false;
		
		url = url.replace('youtu.be/v','youtu.be/vv'); // for youtu.be/vsdfasdf
		
	    var regExp = /^.*((youtu.be\/)|(youtube.com\/)|(www.youtube.com\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
	    var match = url.match(regExp);

	    if (match&&match[7].length==11){
	        return match[7];
	    } else {
	        return false;
	    }
	}
	
	var soundcloudParser = function(url) {
		if(url.indexOf('soundcloud') == -1) return false;
		
		 var regExp = /^http:\/\/(www\.)?soundcloud\.com\/(.*)/;
		 var match = url.match(regExp);
		 
		 if(match&&match[2]) {
		 	return match[2];
		 } else {
		 	return false;
		 }
	}
	
	var addslashes = function( str ) {  
	    return (str+'').replace(/([\\"'])/g, "\\$1").replace(/\0/g, "\\0");  
	} 
	
	var reload = function() {
		window.location.reload();
	}
	
	var go = function(url) {
		window.location.href = url;
	}
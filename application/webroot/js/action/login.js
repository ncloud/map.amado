var Login = function() {
	this.amado = function(email, password, callback) {
		var input_data = {email:email, password:password};

		$.ajax({
			type:'POST',
			url:service.url + '/login/do', 
			data:input_data,
			dataType:'json',
			success:function(data) {
				callback(data);
			}
		});
		
	},
	this.amado_join = function(email, password, username, callback) {
		var input_data = {email:email, password:password, username:username};

		$.ajax({
			type:'POST',
			url: service.url + '/join/do', 
			data:input_data,
			dataType:'json',
			success:function(data) {
				callback(data);
			},
			error:function(data) {
				console.log(data);
			}
		});
	},
    this.facebook = function(redirect_uri) {
    	if(typeof(redirect_uri) == 'undefined') redirect_uri = window.location;

		FB.login(function(response) {
		  if (response.status == 'connected') {
		  /*	if (response.scope == null) {
		     	FB.logout();
		    } else {*/
				window.location = service.url + '/login/facebook/?redirect_uri=' + encodeURIComponent(redirect_uri);
		  //  }
		  } else {
			// user cancelled login
		  }
		},{scope:'email'});
	}
}

var login = new Login();

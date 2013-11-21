	
	// for no cache
	require.config({ 
			paths: {
				async: 'lib/async'
			},
			urlArgs: "v=" +  (new Date()).getTime() 
		});
	
	requirejs.config( {
		packages: [
                {
                        name: 'jquery.form',
                        location: '../js/plugin',
                        main: 'jquery.form.js'
                }
        ],
	    shim: {
	        'template!view/map': [ 
	        	'style!css/bootstrap-custom',
	        	'style!css/bootstrap-custom-responsive',
	        	'style!css/map',
	        	'style!css/map-responsive',
	        	'jquery.form', 
	        ]
	    }
	} );

	define([ 'jquery', 'backbone', 'model/map', 'view/map' ], 
		function($, backbone, M_Map, MapView) {

			// ifCond
			Handlebars.registerHelper('ifCond', function(v1, v2, options) {
			  if(v1 === v2) {
			    return options.fn(this);
			  }
			  return options.inverse(this);
			});

		    return {
		        launch: function() {
					var m_map = new M_Map({id:1});
					var map = new MapView({ model:m_map });

					$('body').append(map.render().el);
		        }
		    };
	} );

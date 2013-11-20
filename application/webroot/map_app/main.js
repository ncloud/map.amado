	
	// for no cache
	require.config({ 
			paths: {
				async: 'lib/async'
			},
			urlArgs: "v=" +  (new Date()).getTime() 
		});
	
	requirejs.config( {
		paths: [
			{
				async: 'lib/async'
			}
		],
		packages: [
                {
                        name: 'jquery.form',
                        location: '../js/plugin',
                        main: 'jquery.form.js'
                },
                {
                        name: 'gmap',
                        location: '../js/plugin',
                        main: 'gmap.js'
                },
                {
                        name: 'gmap.label',
                        location: '../js/plugin',
                        main: 'gmap.label.js'
                },
                {
                        name: 'gmap.menu',
                        location: '../js/plugin',
                        main: 'gmap.menu.js'
                }
        ],
	    shim: {
	        'template!view/map': [ 
	        	'style!css/bootstrap-custom',
	        	'style!css/bootstrap-custom-responsive',
	        	'style!css/map',
	        	'style!css/map-responsive',
	        	'../../js/plugin/jquery.form', 
	        	'../../js/plugin/gmap', 
	        	'../../js/plugin/gmap.label', 
	        	'../../js/plugin/context_menu'
	        	]
	    }
	} );

	define([ 'jquery', 'backbone', 'model/map', 'view/map', 'async!https://maps.googleapis.com/maps/api/js?sensor=true' ], 
		function($, backbone, M_MapView, MapView) {

			// ifCond
			Handlebars.registerHelper('ifCond', function(v1, v2, options) {
			  if(v1 === v2) {
			    return options.fn(this);
			  }
			  return options.inverse(this);
			});

		    return {
		        launch: function() {
					var m_map = new M_MapView({id:1});
					var map = new MapView({ model:m_map });

					$('body').append(map.render().el);
		        }
		    };
	} );

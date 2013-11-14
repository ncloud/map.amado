
	define([ 'jquery', 'backbone' ], function($, backbone) {
	    return {
	        launch: function() {
	            console.log('start');

				require( [ 'view/map' ], function( MapView ) {
				    var map = new MapView();
				    $('body').append(map.render().el);
				} );
	        }
	    };
	} );

define( [ 'backbone' ], function( Backbone ) {
    return Backbone.Model.extend( {
		urlRoot: '/ajax/map_data',
        initialize: function() {
        }
    } );
} );
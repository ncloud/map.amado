
define( [ 'module', 'backbone' ], function( module, Backbone ) {
    return Backbone.Model.extend( {
		urlRoot: '/ajax/map_data',
        initialize: function() {
        }
    } );
} );
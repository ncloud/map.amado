define( [ 'backbone' ], function( Backbone ) {

	return Backbone.View.extend( {
	    tagName: 'div',
	    className: 'content',

	    render: function() {
	    	this.$el.html('sdfsdf');
	        return this;
	    }

	});
} );
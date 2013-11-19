define( [ 'backbone', 'template!view/map' ], function( Backbone, mapTemplate ) {

	return Backbone.View.extend( {
		getModelData: false,
	    tagName: 'div',
	    className: 'content',
	    initialize: function () {     
	    	var self = this;
	    	_.bindAll(this, "render"); // make sure 'this' refers to this View in the success callback below

			this.model.fetch({
				success:function() {
					self.getModelData = true;
					self.render();
				}
			});
		},
	    render: function() {
	    	var model = this.model;
			
			if(this.getModelData) {
				var params = {};

	    		params.map = model.get('map');
	    		params.place = model.get('place');
	    		params.course = model.get('course');
	    		params.user = model.get('user');

	    		var new_types = [];
	    		$.each(params.place.types, function(index, type) {
	    			type.markers = new Array();
	    			new_types[type.id] = type;
	    		});

	    		$.each(params.place.places, function(index, place) {
	    			new_types[place.type_id].markers.push(place);
	    		});

	    		params.place.types = new Array();
	    		$.each(new_types, function(index, type) {
	    			if(typeof(type) == 'undefined') return;

	    			params.place.types.push(type);
	    		});

	    		// add param
	    		params.course_mode = params.course.courses.length > 0;
	    		params.can_add = true;

	    		this.$el.html( mapTemplate( params ) );
			}

	        return this;
	    }

	});
} );
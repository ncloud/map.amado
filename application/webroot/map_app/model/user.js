// model/user
define( [ 'backbone' ], function( Backbone ) {

    return Backbone.Model.extend( {

        defaults: {
            name: '홍길동',
            age: 40
        },

        initialize: function() {
            // Model이 생성되면 실행된다.
            console.log( 'Created!' );
        }
    } );
} );
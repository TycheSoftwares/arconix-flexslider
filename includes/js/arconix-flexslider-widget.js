/*
ARCONIX FLEXSLIDER JS
--------------------------
*/
jQuery( document ).ready( function() {

    jQuery( '.widget_post_type' ).change( function() {
        for( var i = 0 ; i < jQuery( '.widget_post_type' ).length ; i++ ) {
            if( jQuery( '.widget_post_type' )[ i ].value == 'post' ) {
                jQuery( '.category' )[ i ].style.display = 'block';
                jQuery( '.tag' )[ i ].style.display = 'block';
            }
        }
    } );
} );
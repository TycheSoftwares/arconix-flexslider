/*
ARCONIX FLEXSLIDER JS
--------------------------

PLEASE DO NOT make modifications to this file directly as it will be overwritten on update.
Instead, save a copy of this file to your theme directory. It will then be loaded in place
of the plugin's version and will maintain your changes on upgrade
*/
jQuery(document).ready(function() {
    // Slider
    jQuery('.arconix-flexslider-slider .flexslider').flexslider( {
        animation:      'slide',
        pauseOnHover:   true,
    } );

    // Carousel
    jQuery('.arconix-flexslider-carousel .flexslider').flexslider( {
        animation:      'slide',
        animationLoop:  false,
        itemWidth:      210,
        itemMargin:     5,
        minItems:       2,
        maxItems:       4,
        slideshow:      false
    } );
    
    jQuery('.owl-carousel').owlCarousel({
        navigation:     true,
        singleItem:     true
    })
    
} );
/*
ARCONIX FLEXSLIDER JS
--------------------------

PLEASE DO NOT make modifications to this file directly as it will be overwritten on update.
Instead, save a copy of this file to your theme directory. It will then be loaded in place
of the plugin's version and will maintain your changes on upgrade
*/
jQuery(document).ready(function() {

    var _autoPlay = false;
    var _stopOnHover = false;

    // If user has selected slideshow we change the variable values here
    if ( jQuery('.owl-carousel').hasClass('arconix-slideshow') ) {
        _autoPlay = true;
        _stopOnHover = true;
    }

    // Slider
    jQuery('.owl-carousel.arconix-slider').owlCarousel({
        singleItem:         true,
        slideSpeed:         500,
        autoHeight:         true,
        navigation:         true,
        navigationText:     false,
        autoPlay:           _autoPlay,
        stopOnHover:        _stopOnHover,
    });

    // Carousel
    jQuery('.owl-carousel.arconix-carousel').owlCarousel({
        navigationText:     false,
        slidespeed:         400,
        navigation:         true,
        items:              4,
        autoPlay:           _autoPlay,
        stopOnHover:        _stopOnHover,
    });

} );
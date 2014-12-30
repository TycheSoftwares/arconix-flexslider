/*
ARCONIX FLEXSLIDER JS
--------------------------

PLEASE DO NOT make modifications to this file directly as it will be overwritten on update.
Instead, save a copy of this file to your theme directory. It will then be loaded in place
of the plugin's version and will maintain your changes on upgrade
*/
jQuery(document).ready(function() {
    // Slider
    jQuery('.owl-carousel.arconix-slider').owlCarousel({
        slidespeed:         400,
        autoHeight:         true,
        transitionStyle:    "fade",
        navigation:         true,
        singleItem:         true
    });

    jQuery('.owl-carousel.arconix-carousel').owlCarousel({
        slidespeed:     400,
        navigation:     true,
        items:          4,
    });

} );
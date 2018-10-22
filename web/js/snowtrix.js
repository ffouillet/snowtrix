$(document).ready(function(){

    function enableDesktopMenu() {

        var menu = $('nav:not(#enable-nav)');
        var menuDesktopId = "snowtrix-menu";
        var menuMobileId = "menu";

        if($(window).width() > 768) {
            if($(menu).attr('id') != menuDesktopId) {
                $('nav#enable-nav').hide(); // Hide link to show default menu
                $(menu).show();
                $(menu).appendTo('header'); // Add menu into header
                $(menu).attr('id',menuDesktopId); // Change menu id so that it does not default menu css rules
                $(menu).find('.close').hide(); // close hide button.
            }
        }
        else {
            if($(menu).attr('id') != menuMobileId) {
                $('nav#enable-nav').show(); // Hide link to show default menu
                $(menu).appendTo('body'); // Add menu into header
                $(menu).attr('id',menuMobileId); // Change menu id so that it does not default menu css rules
                $(menu).find('.close').show(); // close hide button.
            }
        }
    }

    enableDesktopMenu();

    $(window).on('resize', function(){
        enableDesktopMenu();
    });

    // Closes flash messages
    $('.close-flash-message').on('click',function(e){
        e.preventDefault();
        $(this).parent().fadeOut();

    });

    // Hide flash messages if there are.
    setTimeout(function(){
        $('.flash-notice').fadeOut();
    },8000);


});
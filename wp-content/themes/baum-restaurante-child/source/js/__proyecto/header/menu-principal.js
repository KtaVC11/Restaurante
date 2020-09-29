

var left = $('.main-navigation').innerWidth;

// Abre el menu principal
$('.toggleButton').on('click', function () {
    menu_mobile_open();
});

// Cierra el menu principal
$('.close-nav, #nav-first li').on('click', function () {
    if ($('.menu-is-open').length > 0) {
        menu_mobile_close();
    }
});



/*
 ** function para ABRIR el menu
 **/
function menu_mobile_open() {
    $('.close-nav').addClass('open');
    $('body').addClass('body-overflow');
    $('.site-header-menu').addClass('menu-is-open');
    $('.main-navigation').animate({
        "left": "0px"
    }, 800, function () {
        console.log('%c El Menu ha sido abierto', 'background: #ffcc00; color: #000000');
    });
}


/*
 ** function para CERRAR el menu principal
 */
function menu_mobile_close() {
    $('.close-nav').removeClass('open');
    $('.main-navigation').animate({
        "left": "-450px"
    }, 500, function () {
        $('body').removeClass('body-overflow');
        $('.site-header-menu').removeClass('menu-is-open');
        console.log('%c El Menu ha sido cerrado', 'background: #222; color: #bada55');
    });

}



// Cierra el menu al darle clic al overlay
$(document).on('click', '.site-header-menu', function (e) {
    ww = window.innerWidth;
    if (ww < 768) {
        if ($('body').hasClass('body-overflow')) {
            if (!$(e.target).closest('.main-navigation').length) {
                menu_mobile_close();
            }
        }
    }
});



/**
 ** Login / Register
 **/


// Mostar formulario de Regsitro
$(document).on('click', '#registro-btn', function (e) {
    e.preventDefault();
    cuenta_registro(this);
});

// Mostar formulario de Login
$(document).on('click', '#login-btn', function (e) {
    e.preventDefault();
    cuenta_login(this);
});


/*
 **  Menu desplegable de mi cuenta logeado
 **/
$(document).on('click', '.toggle-account', function () {
    $(this).toggleClass('open');
    $(".menu-edit-account").toggle();
});


$(document).on('click', 'body', function (e) {
    if (!$(e.target).closest('.cuenta-wrapper').length && $('.menu-edit-account').is(':visible')) {
        $('.menu-edit-account').hide();
        $(".toggle-account").removeClass('open');
    }
});


// LOGIN
function cuenta_login(target) {
    $('#register-woocommerce').fadeOut("slow", function () {
        $('#login-woocommerce').fadeIn("slow", function () {
            $("html, body").animate({scrollTop: 0}, 300);
        });
    });
}
// REGISTRO
function cuenta_registro(target) {
    $('#login-woocommerce').fadeOut("slow", function () {
        $('#register-woocommerce').fadeIn("slow", function () {
            $("html, body").animate({scrollTop: 0}, 300);
        });
    });
}
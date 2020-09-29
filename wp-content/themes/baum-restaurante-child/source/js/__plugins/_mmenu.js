/**
 ** mmenu init
 **/
var mmenu_args = {
    extensions: [
        "fx-panels-slide-100",
        "pagedim-black",
        "position-right",
        "position-front",
    ],
    close: false,
    navbar: false,
    onClick: {
        setSelected: true
    },
    keyboardNavigation: {
        enable: true,
        enhance: true
    }
};

// declaracion de cada instancia:  [nombre] - [selector jquery] - [argumentos*]
setTimeout(function () {
    sidebar_contacto = $('#sidebar-contacto').mmenu(mmenu_args).data('mmenu'); // Contacto
}, 5);




// Inserta el icono de cerrar el mmenu .mmenu-close-button
$(".mm-menu").each(function () {
    $(this).find(".mm-panel").append('<span class="mmenu-close-button"><i class="fas fa-times"></i></span>');
});


// Cierra el mmenu mmenu-sidebar-contacto
$(document).on("click", "#sidebar-contacto .mmenu-close-button", function () {
    sidebar_contacto.close();
    return false;
});


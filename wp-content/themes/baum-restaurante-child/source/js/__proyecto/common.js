    /** 
     ** Custom anchor menu 
     **/
    /*
    $('.menu-menu-principal-container li a[href*=#]:not([href=#])').click(function () {
        if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html,body').animate({
                    scrollTop: target.offset().top - $('.site-header').height()
                }, 1200);
                return false;
            }
        }
    });
    */

    /**
     ** Print click loader
     **/
    $(document).on("printLinkComplete", function () {
        $("body").removeClass("bm-loader");
    });

    /**
     ** Custom checked checkboxes
     **/
    $(document).on("click", 'input[type="checkbox"]', function () {
        if ($(this).is(":checked")) {
            $(this)
                .parent('label[class*="checkbox"]')
                .addClass("checked");
        } else {
            $(this)
                .parent('label[class*="checkbox"]')
                .removeClass("checked");
        }
    });

    /**
     ** Allow only numbers in input type text
     **/
    numbersOnly(".input-container input.input-text");

    /**
     ** Custom checked checkboxes
     **/
    $(document).on('change', 'input[type="checkbox"]', function () {
        if ($(this).prop('checked')) {
            $(this).closest('label[class*="checkbox"]').addClass('checked');
        } else {
            $(this).closest('label[class*="checkbox"]').removeClass('checked');
        }
    });

    function check_checkboxes() {
        $('input[type="checkbox"]').each(function () {
            if ($(this).prop('checked') && !$(this).closest('.checkbox').hasClass('checked')) {
                $(this).closest('.checkbox').addClass('checked')
            }
        });
    }
    check_checkboxes();

    /**
     ** Check for Cedula tipo
     **/
    function check_cedula_tipo() {
        if ($('#cedula_tipo_field').length && !$('input[name="cedula_tipo"]').is(':checked')) {
            $('#cedula_tipo_cedula_fisica').prop('checked', true);
        }
    }
    check_cedula_tipo();

    /**
    ** Fix issue with check_checkboxes
    ** 04-09-2020 - KMA
    **/
    $('#collapse_message').on('shown.bs.collapse', function() {
        $('.product_note-field').find('.custom-checkbox').addClass('checked');
    });

    $('#collapse_message').on('hidden.bs.collapse', function() {
        $('.product_note-field').find('.custom-checkbox').removeClass('checked');
    });

    /**
    ** Fix Checkout field
    ** 04-09-2020 - KMA
    **/
    $(window).on('load resize', resize_email_field);

    function resize_email_field() {
        if ($('#billing_phone_field.form-row-first').length) {
            if ($(window).width() > 600) {
                $('#billing_phone_field.form-row-first').height($('#billing_email_field.form-row-last').height());
            } else {
                $('#billing_phone_field.form-row-first').height('auto');
            } 
        }
    }
    resize_email_field();

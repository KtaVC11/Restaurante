    /**
     ** Internal selects to select2
     **/
    function runSelect2(selector) {
        $(selector).select2({
            width: '100%',
            minimumResultsForSearch: Infinity
        });
    }
    runSelect2("select:not(.hide, .hidden, .country_select)");

    /**
     ** Select2 methods select
     **/
    function change_select_shipping_to_select2() {
        $(
            ".select-select2, select.shipping_methods, .woocommerce-checkout select.shipping_method, select#shipping-pickup-store-select"
            ).select2({
            width: '100%',
            minimumResultsForSearch: Infinity
        });
    }
    change_select_shipping_to_select2();

    /**
     ** After update checkout
     **/
    $(document.body).on("update_checkout, updated_checkout", function () {
        change_select_shipping_to_select2();
    });

    /**
     ** Update cart amount
     **/
    $(document.body).on("updated_cart_totals", function () {
        change_select_shipping_to_select2();
    });

    /**
     ** Placeholder to select2 input search
     **/
    function placeholder_input_search(selector) {
        setTimeout(function () {
            $(selector).on("select2:open", function () {
                $(".select2-search__field").attr(
                    "placeholder",
                    $(this)
                    .find("option:first")
                    .text()
                    );
            });
            $(selector).on("select2:close", function () {
                $(".select2-search__field").attr("placeholder", null);
            });
        }, 20);
    }
    placeholder_input_search(
        "select.country_to_state, select.state_select, select.city_select"
        );

    /**
     ** Ejecuta el Select2 en el Quickview
     **/
    $(document).on("success_modal", function () {
        if (!$(".xoo-qv-panel").hasClass("current-modal")) {
            runSelect2(".xoo-qv-summary select:not(.hide)");
        }
    });

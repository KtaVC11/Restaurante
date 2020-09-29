    /**
     ** litepicker
     **/
    if ($('#datepicker').length > 0) {
        // Date Picker config
        // var date =
        //     typeof baumdivi_ajax != 'undefined' &&
        //     typeof baumdivi_ajax.litepicker_today != 'undefined' && baumdivi_ajax.litepicker_today != '' ? new Date(baumdivi_ajax.litepicker_today) : new Date();

        var options = {
            element: document.getElementById('datepicker'),
            format: 'DD MMMM, YYYY',
            lang: 'es-CR',
            mobileFriendly: true,
            onShow: function () {},
        };
        
        if (baumchild_ajax.excluir_fechas != undefined) 
            options.lockDays = baumchild_ajax.excluir_fechas;

        if (baumchild_ajax.fecha_inicio != undefined) {
            options.minDate = baumchild_ajax.fecha_inicio;
        }

        if (baumchild_ajax.fecha_inicial != undefined) {
            options.startDate = baumchild_ajax.fecha_inicial;
        }

        if (baumchild_ajax.fecha_fin != undefined)
            options.maxDate = baumchild_ajax.fecha_fin;

        // if (baumchild_ajax.exclude_weekend != undefined)
        //     options.disableWeekends = baumchild_ajax.exclude_weekend;

        var picker = new Litepicker(options);
    }

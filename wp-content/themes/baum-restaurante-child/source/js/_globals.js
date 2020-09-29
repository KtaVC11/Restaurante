	/**
	** Mega menu breakpoint
	**/
	var mega_menu_breakpoint = $('#mega-menu-primary').data('breakpoint');
    var ww = window.innerWidth;

	/**
	** Close mobile menu
	**/
	function close_mobile_menu() {
		if ($('.menu-toggle').hasClass('toggle-open')) {
			$('.menu-toggle.toggle-open').trigger('click');
		}
	}

	/**
	** Validate fields only numbers
	**/
	function numbersOnly(s) {
		$(s).on("keydown", function(e) {
			-1 !== $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) ||
			(/65|67|86|88/.test(e.keyCode) &&
				(e.ctrlKey === true || e.metaKey === true) &&
				(!0 === e.ctrlKey || !0 === e.metaKey)) ||
			(35 <= e.keyCode && 40 >= e.keyCode) ||
			((e.shiftKey || 48 > e.keyCode || 57 < e.keyCode) &&
				(96 > e.keyCode || 105 < e.keyCode) &&
				e.preventDefault());
		});
	}
	
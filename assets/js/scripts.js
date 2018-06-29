var ajaxRequests = 0;

function ajaxRequestLoading() {
	if ( ajaxRequests > 0 ) {
		$('#loadingmask').stop(true, true).fadeIn(0);
	} else {
		$('#loadingmask').stop(true, true).fadeOut(0);
	}

}


window.onerror = function() {
	$('#loadingmask').stop(true, true).fadeOut(0);
};


$(document).ajaxSend(function(event, request, settings) {

	if ( settings.url.indexOf("hiddenajax") != -1 ) {

	} else {
		ajaxRequests = ajaxRequests + 1;
		ajaxRequestLoading();
	}

});

$(document).ajaxComplete(function(event, request, settings) {
	if ( settings.url.indexOf("hiddenajax") != -1 ) {

	} else {
		ajaxRequests = ajaxRequests - 1;
		ajaxRequestLoading();
	}


});


$(document).ready(function() {

	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();

	$(document).on("hide.bs.modal", "#modal-window", function() {
		$.bbq.removeState("settings");
		$.bbq.removeState("ticket-ID");
	});

	//$('#loadingmask').stop(true,true).fadeOut(500);

	$(document).ajaxError(function(event, jqxhr, settings, thrownError) {
		//console.log(settings.url.indexOf("true"))
		if ( jqxhr.status == 401 ) {
			alert("Sorry, you arent logged in");
			window.location.href = "/login";
		} else if ( jqxhr.status == 403 ) {
			alert("Sorry, you dont have access to this page");
			window.location.href = "/front";
		} else if ( thrownError === 'abort' ) {
		} else if ( settings.url.indexOf("hiddenajax") != -1 ) {

		} else {
			alert("An error occurred: " + jqxhr.status + "\nError: " + thrownError);
		}
	});


	$(document).on("click", ".debugger-method .label", function(e) {
		e.stopPropagation();
		var $this = $(this);
		var $parent = $this.parent();
		$(".debugger-method").not($parent).find(">ul").hide();
		$parent.find("> ul").toggle();
	});


	$(window).on("resize", function() {

		$(".fixed-column").each(function(){
			if ($(this).attr("id")=="main-progress-area"){
				$(this).addClass("fade-in")
			}
			$(this).css({"width": $(this).parent().width()}).addClass("position-fixed");
		})
	}).trigger("resize");


	function scroll() {
		var $toolbar = $('#toolbar');
		if ($toolbar.length){
			var $toolbarArea = $('#toolbar-area');
			var $mainMenu = $("#main-nav-bar");

			if ( $(window).scrollTop() >= $toolbarArea.offset().top - $mainMenu.outerHeight() ) {
				$('#toolbar').addClass('fixed-top').css({top: $mainMenu.outerHeight() - 1});
				$toolbarArea.css({'min-height': $toolbar.outerHeight()});
			} else {
				$('#toolbar').removeClass('fixed-top').css({top: 0});
				$toolbarArea.css({'min-height': 0});
			}
		}


	}

	$(document).on("scroll", function() {
		scroll();
	});



});


function _debugger(debug) {


}

var datetimepickerOptions = {
	inline: true,
	sideBySide: true,
	format: "YYYY-MM-DD HH:mm:00",
	icons: {
		time: "fa fa-clock-o",
		date: "fa fa-calendar",
		up: "fa fa-arrow-up",
		down: "fa fa-arrow-down",

		previous: 'fa fa-chevron-left',
		next: 'fa fa-chevron-right',
		today: 'fa fa-screenshot',
		clear: 'fa fa-trash',
		close: 'fa fa-remove',
	},
};


$(document).ajaxComplete(function(event, request, settings) {
	$.doTimeout('heartbeat', 60 * 5 * 1000, function() {
		$.get("/heartbeat?hiddenajax=true&r=" + Math.random());

	});
});

$(document).ready(function() {
	resize();
	$(window).on('resize', function() {
		$.doTimeout("window-resize", 250, function() {
			resize();
		});
	});

	$(window).scroll(function(event) {
		scroll();
		// Do something
	});

	$(".select2").select2();




});


toastr.options = {
	"closeButton": true,
	"debug": false,
	"newestOnTop": false,
	"progressBar": true,
	"positionClass": "toast-top-center",
	"preventDuplicates": true,
	"onclick": null,
	"showDuration": "300",
	"hideDuration": "1000",
	"timeOut": "3000",
	"extendedTimeOut": "1000",
	"showEasing": "swing",
	"hideEasing": "linear",
	"showMethod": "fadeIn",
	"hideMethod": "fadeOut",
};
toastr.options['positionClass'] = 'toast-bottom-right';


$.fn.modal.Constructor.prototype.enforceFocus = function() {
};

function resize() {
	var wh = $(window).height();
	var ww = $(window).width();
	var mh = wh - $("#navbar-header").outerHeight() - 6;


}


function validationErrors(data, $form) {

	if ( !$.isEmptyObject(data['errors']) ) {

		var i = 0;
		$(".invalid-feedback", $form).remove();
		$(".is-invalid", $form).removeClass('is-invalid');
		var errorText = "";

		$.each(data.errors, function(k, v) {
			k = k.replace("[", "\\[");
			k = k.replace("]", "\\]");
			i = i + 1;
			var $field = $("#" + k);
			//console.info(k)
			var $block = $field.closest(".form-group, .input-group");

			$field.addClass("is-invalid");
			if ( $field.parent().hasClass("input-group") ) {
				//$field = $field.parent();
			}


			if ( v != "" ) {

				$field.after('<span class="invalid-feedback">' + v + '</span>');
			}
			if ( $block.hasClass("has-feedback") ) {
				$field.after('<span class="fa fa-times form-control-feedback form-validation" aria-hidden="true"></span>');
			}

			if ( v == "" ) {
				v = "Required";
				$field.after('<span class="invalid-feedback">' + v + '</span>');
			}

			errorText = errorText + " - " + k + ": " + v + "</br>";
		});


		$("button[type='submit'],button[data-type='submit']", $form)
			.addClass("btn-red")
			.html("(" + i + ") Error(s) Found");




		if ( i > 1 ) {
			errorText = "There were " + i + " errors saving the form<br>" + errorText;
		} else {
			errorText = "There was an error saving the form<br>" + errorText;
		}

		toastr["error"](errorText, "Error", {
			timeOut: 10000,
			"progressBar": true,
		});


	} else {
		toastr["success"]("Record Saved", "Success");

	}

	//submitBtnCounter($form);


}

function submitBtnCounter($form) {
	var c = $(".has-error", $form).length;
	var $btn = $("button[type='submit']", $form);
	if ( c ) {
		$btn.addClass("btn-danger").html("(" + c + ") Error(s) Found");
	} else {

		var tx = $btn.attr("data-text") || "Save";

		$btn.html(tx).removeClass("btn-danger");
	}
}



$(document).ready(function() {
	getData();

	$(document).on("submit", "#search-form", function(e) {
		e.preventDefault();
		$.bbq.pushState({
			"search": $("#search").val(),
			"page": "",
		});
		getData();
	});
	$(document).on("reset", "#search-form", function(e) {
		e.preventDefault();
		$.bbq.pushState({
			"search": "",
			"page": "",
		});
		$("#search").val("");
		getData();
	});

	$(document).on("click",".record",function(){
		var $this = $(this);
		$.bbq.pushState({ID:$this.attr("data-id")});

		getData();


	})
	$(document).on("click",".refresh",function(e){
		e.preventDefault()
		getData();
	})
	$(document).on("click",".test-btn-remove",function(e){
		e.preventDefault();
		if (confirm("Are you sure you want to remove this item?")){
			$(this).closest("article").remove();
		}

	})
	$(document).on("click",".test-btn-new",function(e){
		e.preventDefault();
		$("#test-area").jqoteapp($("#template-details-tests"),{"record":{},"all":{}});

	})
	$(document).on("click",".btn-remove-record",function(e){
		e.preventDefault();
		if (confirm("Are you sure you want to remove this record?")) {
			$.post("/save/settings_indicators/delete?ID=" + $.bbq.getState("ID"), {}, function(result) {
				result = result.RESPONSE;

				if ( !result.errors ) {
					$.bbq.pushState({"ID": ""});
					getData();
					toastr["success"]("Record Deleted", "Success");
				} else {
					toastr["error"](result.errors.join(" | "), "Error", {
						timeOut: 10000,
						"progressBar": true,
					});
				}

			})
		}

	})



	$(document).on("submit","#record-form",function(e){
		e.preventDefault();

		var test = [];

		$("#test-area article").each(function(){
			var $this = $(this)
			var v = {
				top: $this.find("input[name='top']").val(),
				bottom: $this.find("input[name='bottom']").val(),
				status: $this.find("select[name='status']").val(),
				score: $this.find("input[name='score']").val(),
			}


			test.push(v);

		});

		test = {
			"status":"",
			"levels":test
		}

		$("#test").val(JSON.stringify(test))

		var data = $(this).serialize();
		$.post("/save/settings_indicators/save?ID="+$.bbq.getState("ID"), data, function(result) {
			result = result.RESPONSE;
			validationErrors(result,$(this))
			if( !result.errors ) {
				$.bbq.pushState({"ID":""});
				getData();

			} else {

			}

		})

	})


});

function getData() {

	$.getData("/data/settings_indicators", $.bbq.getState(), function(data) {

		if (data.details){
			$("#content-area").jqotesub($("#template-details"), data);
		} else {
			$("#content-area").jqotesub($("#template-content"), data);
		}


		$(".summernote").summernote({
			minHeight: 100,
			toolbar: [
				// [groupName, [list of button]]
				[
					'style',
					[
						'bold',
						'italic',
						'underline',
						'clear',
					],
				],
				[
					'font',
					[
						'strikethrough',
						'superscript',
						'subscript',
					],
				],
				[
					'fontsize',
					['fontsize'],
				],
				[
					'color',
					['color'],
				],
				[
					'para',
					[
						'ul',
						'ol',
						'paragraph',
					],
				],
			],
		});

		$(".select2").select2();



	}, "data");

}

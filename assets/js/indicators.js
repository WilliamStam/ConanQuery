$(document).ready(function() {
	getData();


	$(document).on("mouseenter", ".table-cards tbody tr", function() {
		var $this = $(this).addClass("active");
		$("#hover-area").html($this.find(".hover-data").html());
		$(".table-cards tbody tr.active").not($this).removeClass("active")
		chartify($("#hover-area").find(".history-chart"))
	});
	$(document).on("mouseleave", ".table-cards tbody tr", function() {
		var $this = $(this);
		$(".table-cards tbody tr.active").not($this).removeClass("active")
		//$("#hover-area").html("")
	});




});

function getData() {

	$.getData("/data/indicators", $.bbq.getState(), function(data) {


		$("#content-area").jqotesub($("#template-content"), data);


		$(".table-cards tbody").first().find("tr").first().trigger("mouseenter");





	}, "data");

}

function chartify($this) {

	var datasets_ = [];

	$this.find("div").each(function(){
		datasets_.push(
			{
				label: $(this).attr("title"),
				backgroundColor: $(this).attr("data-bg"),
				borderColor: $(this).attr("data-bg"),
				data: $(this).text().split(","),
				fill:  $(this).attr("data-fill")=='1'?true:false,
			}
		)
	});



	var config = {
		type: 'line',
		data: {
			labels: [
				'Jul',
				'Aug',
				'Sep',
				'Oct',
				'Nov',
				'Dec',
				'Jan',
				'Feb',
				'Mar',
				'Apr',
				'May',
				'Jun',
			],
			datasets: datasets_,

		},
		options: {
			responsive: true,
			title: {
				display: false,
			},
			tooltips: {
				mode: 'index',
				intersect: false,
			},
			hover: {
				mode: 'nearest',
				intersect: true,
			},
			scales: {
				xAxes: [
					{
						display: true,
						scaleLabel: {
							display: false,
							labelString: 'Month',
						},
					},
				],
				yAxes: [
					{
						display: true,
						scaleLabel: {
							display: false,
							labelString: 'Value',
						},
					},
				],
			},
		},

	};

	var ctx = $this.get(0).getContext('2d');
	window.myLine = new Chart(ctx, config);


}

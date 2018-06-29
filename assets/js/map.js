$(document).ready(function() {
	getData();







});

function getData() {

	$.getData("/data/map", $.bbq.getState(), function(data) {
		$("#content-area").jqotesub($("#template-content"), data);


		mapit()



	}, "data");

}


function mapit(){
		var map = new L.Map("map", {
			scrollWheelZoom: false,
		});
		map.attributionControl.setPrefix('');
		var osm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: 'Map © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
			maxZoom: 18
		});
		map.addLayer(osm);

		// municipalities
		var url = "http://mapit.code4sa.org/areas/MDB-levels:MN-TSH|WD.geojson"; // tswane municipality
		var url = "http://mapit.code4sa.org/areas/MDB-levels:MN-KZN238|WD.geojson"; // Alfred Duma LM
		var url = "http://mapit.code4sa.org/areas/MDB-levels:MN-LIM345|WD.geojson"; // Collins Chabane LM (Lim345)


		// distict munis
		var url = "http://mapit.code4sa.org/areas/MDB:DC44.geojson"; // Alfred Nzo DM
		var url = "http://mapit.code4sa.org/areas/MDB:DC29.geojson"; // Ilembe DM

	var url = "http://mapit.code4sa.org/areas/MDB-levels:MN-LIM345|WD,MDB-levels:MN-KZN238|WD.geojson"; // Collins Chabane LM (Lim345)
		//multi muni wards



	var url = "http://mapit.code4sa.org/areas/MDB:TSH,MDB:KZN238,MDB:DC29.geojson"; // Ilembe DM

		$.getJSON(url)
			.then(function(data) {
				// use the geojson as a layer on the map
				var area = new L.GeoJSON(data, {style: {weight: 2.0}});
				map.addLayer(area);
				map.fitBounds(area.getBounds());
			});

}

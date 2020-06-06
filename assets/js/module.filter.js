var moduleFilter = Object.create(siteModule.prototype);

moduleFilter.collapseAt = 1000;

moduleFilter.name = "Filter";

moduleFilter.results = null;
moduleFilter.map = null;
moduleFilter.cluster = null;
moduleFilter.markers = null;

moduleFilter.config = {
	
	filters : {
		url : {
			options : "api.php?action=filters",
			results : "api.php?action=search",
			event	: "api.php?action=event",
		},
		fields :  [

			{ 
				id  : "attack_type",
				node : "attacks"
			},
			{ 
				id  : "weapon_type",
				node: "weapons"
			},
			{ 
				id  : "country",
				node: "countries"
			},
			{ 
				id  : "period",
				node: "periods"
			},
			{ 
				id  : "region",
				node: "regions"
			}


		],
	},

	graphs : [	
		{
			id		: 'graph_targets',
			node	: 'graph_1',
			type	: 'line',
			options : {
				responsive: true,
				hoverMode: 'index',

				tooltips: {
//					mode: 'index',
				},
/*
                scales: {
					yAxes: [{
						ticks: {
							callback: function(value, index, values) {
								return value.numberFormat();
							}
						},
					}],
				},
*/
			},
			chart	 : null
		},
		{
			id		: 'graph_attacks',
			node	: 'graph_2',
			type	: 'line',
			options : {
				responsive: true,
				hoverMode: 'index',

				tooltips: {
					mode: 'index',
				},

                scales: {
					yAxes: [{
						ticks: {
							callback: function(value, index, values) {
								return value.numberFormat();
							}
						},
					}],
				},

			},
			chart	 : null
		},
		{
			id		: 'graph_incidents',
			node	: 'graph_3',
			type	: 'bar',
			options : {
				responsive: true,
				hoverMode: 'index',

                scales: {
					xAxes: [{
						stacked: true,
					}],

					yAxes: [{
						ticks: {
							callback: function(value, index, values) {
								return value.numberFormat();
							}
						},
					}],
				},

				tooltips: {
					mode: 'index',
				}	
			},
			chart	 : null
		},

		{
			id		: 'graph_region',
			node	: 'graph_4',
			type	: 'pie',
			options : {
				responsive: true,
				hoverMode: 'index',

                /*scales: {
					xAxes: [{
						stacked: true,
					}],

					yAxes: [{
						ticks: {
							callback: function(value, index, values) {
								return value.numberFormat();
							}
						},
					}],
				},

				tooltips: {
					mode: 'index',
				}*/	
			},
			chart	 : null
		}


	]

}

moduleFilter.addListener = function(id) {
	
	document.getElementById(id).addEventListener('change', function() {
		moduleFilter.refreshResults();
	});
}

moduleFilter.addDefaultListeners = function() {
	document.getElementById("read-current-location").addEventListener(
		'click', 
		moduleFilter.readBrowserLocation
	);

	document.getElementById("close-location").addEventListener(
		'click', 
		moduleFilter.hideEventOnMap
	);

	document.getElementById("tab-show-map").addEventListener(
		'click', 
		moduleFilter.showMapTab
	);

	document.getElementById("tab-show-graphs").addEventListener(
		'click', 
		moduleFilter.showGraphsTab
	);

}


moduleFilter.readBrowserLocation = function(event) {
	event.preventDefault();

	if (moduleFilter.map === null){
		alert("Map not initialized");
		return false;
	}
	
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(
			function(position){
				var location = new google.maps.LatLng(
					position.coords.latitude, 
					position.coords.longitude
				);

				moduleFilter.map.setCenter(location);							
				moduleFilter.map.setZoom(10);							
			
			}
		);
	}
}

moduleFilter.initMap = function() {

	if (moduleFilter.map !== null) {
		return false;
	}

	var mapOptions = {
		center					: { 
			lat: -34.397, 
			lng: 150.644	
		},
			
		zoom					: 2,
		scrollwheel				: false,
		mapTypeControlOptions	: {
			mapTypeIds	: [
				google.maps.MapTypeId.ROADMAP, 'gmap-id'
			],
		},
	};

	if (!moduleFilter.map) {
		moduleFilter.map = new google.maps.Map(
			document.getElementById('gmap-id'), 
			mapOptions
		);
	}

	return true;
}


moduleFilter.initFields = function() {

	var _this = this;

	fetch(this.config.filters.url.options)
		.then(data => data.json())
		.then(function(data) {
			if (!data.status || data.status != 'success'){
				return null;
			}
			
			for (var fieldIndex in _this.config.filters.fields) {
				_this.updateFilterOptions(
					_this.config.filters.fields[fieldIndex].id,
					data.options[_this.config.filters.fields[fieldIndex].node]
				);

				_this.addListener(_this.config.filters.fields[fieldIndex].id);
			}
				
		});

}

moduleFilter.showEventOnMap = function(id) {

	var _this		= moduleFilter,
		variables	= new FormData();
		variables.append("event" , id);


	fetch(
		_this.config.filters.url.event,
		{
			method			: 'POST',
			cache			: 'no-cache',
			credentials		: 'same-origin',
			body			: variables
		})

		.then(data =>data.json())
		.then(function(data) {		

			var element = document.getElementById("location-holder");

			if (!element.classList.contains("active")) {
				element.classList.add("active");
			}	


		})
		.catch(function (err) {
			console.warn('Something went wrong.', err);
		});		


}

moduleFilter.hideEventOnMap = function( event ) {
	event.preventDefault();

	var element = document.getElementById("location-holder");

	if (element.classList.contains("active")) {
		element.classList.remove("active");
	}		
}

moduleFilter.refreshResults = function() {
	
	var variables = new FormData(),
		_this = moduleFilter;

			
	for (var fieldIndex in _this.config.filters.fields){
		variables.append(
			_this.config.filters.fields[fieldIndex].id,
			document.getElementById(_this.config.filters.fields[fieldIndex].id).value
		);
	}

	fetch(
		_this.config.filters.url.results,
		{
			method			: 'POST',
			cache			: 'no-cache',
			credentials		: 'same-origin',
			body			: variables
		})

		.then(data =>data.json())
		.then(function(data) {		
				_this.results = data;

				_this.updateMap();
				_this.updateGraphs();
		})
		.catch(function (err) {
			console.warn('Something went wrong.', err);
		});		

}

moduleFilter.updateMap = function () {

	moduleFilter.initMap();

	//clear cluster if exists
	if (moduleFilter.cluster !== null && moduleFilter.markers !== null) {
	
		moduleFilter.cluster.removeMarkers(moduleFilter.markers);
	}

	//clear markers if exists
	if (moduleFilter.markers !== null) {
		for (var markerIndex in moduleFilter.markers) {
			moduleFilter.markers[markerIndex].setMap(null);
		}
		moduleFilter.markers = null;
	}

	if (moduleFilter.results.results) {
		moduleFilter.markers = moduleFilter.results.results.map(
			function(location, i) {					
				var loc = {
					position: { 
						lat: parseFloat(location.lat) , 
						lng: parseFloat(location.long) 
					}
				}

			var marker = new google.maps.Marker(
				loc
			);

			marker.dataId = location.event_id;

				marker.addListener(
					'click', 
					function( event) {
						//moduleFilter.map.setCenter(marker.getPosition());
						moduleFilter.showEventOnMap(marker.dataId);
					}
				);

				return marker;
			}
		);	

		if (moduleFilter.cluster === null) {
			moduleFilter.cluster = new MarkerClusterer(
				moduleFilter.map,
				[],
				{
					maxZoom: 15,
					imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
				}
			);
	
		}

		moduleFilter.cluster.addMarkers(moduleFilter.markers);

	} 
}

moduleFilter.updateFilterOptions = function(id , options) {
	var 
		element = document.getElementById(id);

	if (!element){
		return null;
	}

	//remove old options except the first one ( [select] )
	element.options.length = 1;
	
	for (var optionIndex in options ){
		if (options[optionIndex].value){
			element.options[element.options.length] = new Option(
				options[optionIndex].title,
				options[optionIndex].value
			);
		}
	}
}

moduleFilter.showMapTab = function(event) {
	var
		map = document.getElementById("tab-holder-map"),
		charts = document.getElementById("tab-holder-graphs");

	event.preventDefault();

	if (!map.classList.contains("active")) {
		map.classList.add("active");
	}	

	if (charts.classList.contains("active")) {
		charts.classList.remove("active");
	}	
}

moduleFilter.showGraphsTab = function() {
	var
		map = document.getElementById("tab-holder-map"),
		charts = document.getElementById("tab-holder-graphs");

	event.preventDefault();

	if (!charts.classList.contains("active")) {
		charts.classList.add("active");
	}	

	if (map.classList.contains("active")) {
		map.classList.remove("active");
	}	

}


moduleFilter.updateGraphs = function() {
	var _this = moduleFilter;

	for (var graphIndex in _this.config.graphs) {

		if (_this.results.graphs[_this.config.graphs[graphIndex].node]){
			if (!_this.config.graphs[graphIndex].chart) {	
				_this.config.graphs[graphIndex].chart = new Chart(
					document.getElementById(_this.config.graphs[graphIndex].id).getContext('2d'), 
					{
						type	: _this.config.graphs[graphIndex].type,
						data	: _this.results.graphs[_this.config.graphs[graphIndex].node],
						options	: _this.config.graphs[graphIndex].options
					}
				);
			} else {
				
				_this.config.graphs[graphIndex].chart.data = _this.results.graphs[_this.config.graphs[graphIndex].node];
				_this.config.graphs[graphIndex].chart.update();

			}
		}
	}
}

moduleFilter.runOnLoad = function() {	
	this.addDefaultListeners();
	this.initMap();
	this.initFields();	
}

moduleFilter.runOnResize = function() {		
}

site.registerModule(moduleFilter);

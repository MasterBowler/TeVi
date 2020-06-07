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
			api			: "api.php",
			options		: "api.php?action=filters",
			results		: "api.php?action=search",
			event		: "api.php?action=event",
			countries	: "api.php?action=countries",
		},
		fields :  [
			{ 
				id  : "victims",
				node : "victims"
			},

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
					}]
				},

				tooltips: {
					mode: 'index',
				}
			},
			chart	 : null
		}


	]

}

moduleFilter.addListenerIfExists = function(id , event , callback )  {
	var element = document.getElementById(id);

	if (element) {
		element.addEventListener(
			event,
			callback
		);
	}
}

moduleFilter.updateCountriesList = function() {
	
	var _this = moduleFilter;

	//change the index of countries before doing the request
	document.getElementById("country").selectedIndex  = 0;

	fetch(_this.config.filters.url.countries + "&region=" + document.getElementById("region").value)
		.then(data => data.json())
		.then(function(data) {
			if (!data.status || data.status != 'success'){
				return null;
			}
			
			_this.updateFilterOptions(
				"country",
				data.countries
			);
				
		});
}

moduleFilter.addDefaultListeners = function() {
	var _this = moduleFilter;

	_this.addListenerIfExists(
		"read-current-location",
		'click', 
		_this.readBrowserLocation
	);

	_this.addListenerIfExists(
		"close-location",
		'click', 
		_this.hideEventOnMap
	);

	_this.addListenerIfExists(
		"tab-show-map",
		'click', 
		_this.showMapTab
	);

	_this.addListenerIfExists(
		"tab-show-graphs",
		'click', 
		_this.showGraphsTab
	);

	_this.addListenerIfExists(
		"region",
		'change', 
		_this.updateCountriesList
	);


	var buttons = document.getElementsByClassName("download-button");

	if (buttons.length) {
		for (var buttonIndex = 0; buttonIndex < buttons.length ; buttonIndex++){
			buttons[buttonIndex].addEventListener(
				'click', 
				_this.downloadChart
			);
		}
	}

}


moduleFilter.readBrowserLocation = function(event) {
	event.preventDefault();

	if (moduleFilter.map === null){
		alert("Map not initialized");
		return false;
	}

	if (document.location.protocol != 'https:') {
		alert("You must load the site from https:// in order to use browser location");
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
	var _this = moduleFilter;

	if (_this.map !== null) {
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

	if (!_this.map) {
		_this.map = new google.maps.Map(
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

				_this.addListenerIfExists(
					_this.config.filters.fields[fieldIndex].id,
					'change',
					function() {
						setTimeout(_this.refreshResults , 50);
						
					}
				);
			}


			_this.refreshResults();
				
		});

}

moduleFilter.getValue = function(value , def ) {
	def = def || "-";

	if (value) {

		if (Array.isArray(value)){
			var data = [];

			for ( var valueIndex in value) {
				if (value[valueIndex])  {
					data.push(value[valueIndex]);
				}
			}

			if (data.length) {
				return data.join(", " );
			} else {
				return def;
			}
		}

		return value;

	} else {
		return def;
	}
}

moduleFilter.getEventHTML = function(event) {
	var _this	= moduleFilter ,
		date	= new Date(event.date * 1000),
		html	= `<div class="result-element">
		<h4 class="title">` + date.toDateString() + `</h4>
		<div class="generic-text inverted-color">
			<ul>
				<li><span class="icon icomoon icomoon icon-earth"></span><b>Location:</b> ` + _this.getValue([event.country_text , event.city]) +`</li>
				<li><span class="icon icomoon icomoon icon-users4"></span><b>Group:</b> <span class="yellow">` + _this.getValue(event.gname) + `</span></li>
				<li><span class="icon icomoon icomoon icon-user-cancel2"></span><b>Fatalities:</b> ` + _this.getValue(event.nkill) + `</li>
				<li><span class="icon icomoon icomoon icon-user-check2"></span><b>NonFatalities:</b> ` + _this.getValue(event.nwound) + `</li>
				<li><span class="icon icomoon icomoon icon-bomb"></span><b>Weapon:</b> ` + _this.getValue([event.weapon_type_text, event.weapon_subtype_text]) + `</li>
			</ul>
	
			<p>` + _this.getValue(event.summary) + `</p>
		</div>
	
		<ul class="links">` +
			(event.weapon_type_text ? `<li><a target="_blank" href="https://en.wikipedia.org/w/index.php?search=` + encodeURIComponent(event.weapon_type_text) + `"><span class="icon icomoon icon-wikipedia"></span></a></li>` : '')+
			(event.weapon_type_text ? `<li><a target="_blank" href="https://google.com/search?q=` + encodeURIComponent(event.weapon_type_text) + `"><span class="icon icomoon icon-google"></span></a></li>` : '') +
		`</ul>
	</div>`;
	
	return html;
}

moduleFilter.showEventOnMap = function(info) {

	var _this		= moduleFilter,
		variables	= new FormData(),
		holder		= document.getElementById("location-item-content");


	variables.append("lat" , info.lat);
	variables.append("ids" , info.ids);
	variables.append("long" , info.long);

	for (var fieldIndex in _this.config.filters.fields){
		variables.append(
			_this.config.filters.fields[fieldIndex].id , 
			document.getElementById(_this.config.filters.fields[fieldIndex].id).value
		);
	}

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

			var element = document.getElementById("location-holder"),
				content = "";

			if (!element.classList.contains("active")) {
				element.classList.add("active");
			}	

			for (var eventIndex in data.events) {
				content += _this.getEventHTML(data.events[eventIndex]);
			}

			holder.innerHTML = content;

			//console.log(data);


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
	
	var _this	= moduleFilter,
		params	= "?action=search";

			
	for (var fieldIndex in _this.config.filters.fields){
		params += "&" + _this.config.filters.fields[fieldIndex].id + "=" + encodeURI(document.getElementById(_this.config.filters.fields[fieldIndex].id).value);		
	}

	fetch(
		_this.config.filters.url.api + params,
		{
			method			: 'GET',
			cache			: 'no-cache',
			credentials		: 'same-origin'
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

	var
		_this	= moduleFilter,
		body	= document.getElementsByTagName("body")[0];

	//dont refresh map if not active
	if (!body.classList.contains('with-map')){
		return false;
	}

	_this.initMap();

	//clear cluster if exists
	if (_this.cluster !== null && _this.markers !== null) {	
		_this.cluster.removeMarkers(_this.markers);
	}

	//clear markers if exists
	if (_this.markers !== null) {
		for (var markerIndex in _this.markers) {
			_this.markers[markerIndex].setMap(null);
		}
		_this.markers = null;
	}

	if (_this.results.results) {
		_this.markers = _this.results.results.map(
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

				marker.info = {
					'lat'  : location.lat,
					'long' : location.long,
					'ids' : location.ids
				};

				marker.addListener(
					'click', 
					function( event) {
						_this.map.setCenter(marker.getPosition());
						_this.showEventOnMap(marker.info);
					}
				);

				return marker;
			}
		);	

		if (_this.cluster === null) {
			_this.cluster = new MarkerClusterer(
				_this.map,
				[],
				{
					maxZoom: 15,
					imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
				}
			);
	
		}

		_this.cluster.addMarkers(_this.markers);

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
		_this	= moduleFilter,
		body	= document.getElementsByTagName("body")[0],
		filter	= document.getElementById("victims");

	event.preventDefault();

	if (!body.classList.contains("with-map")) {
		body.classList.add("with-map");				
	}	

	if (body.classList.contains("with-graph")) {
		body.classList.remove("with-graph");				
	}	

	filter.disabled = false;

	_this.updateMap();
	_this.updateGraphs();

}

moduleFilter.showGraphsTab = function() {
	var
		_this	= moduleFilter,
		body	= document.getElementsByTagName("body")[0],
		filter	= document.getElementById("victims");

	event.preventDefault();

	if (!body.classList.contains("with-graph")) {
		body.classList.add("with-graph");				
	}	

	if (body.classList.contains("with-map")) {
		body.classList.remove("with-map");				
	}	

	filter.disabled = true;

	_this.updateMap();
	_this.updateGraphs();

}


moduleFilter.updateGraphs = function() {
	var
		_this	= moduleFilter,
		body	= document.getElementsByTagName("body")[0];

	//dont refresh charts if not active
	if (!body.classList.contains('with-graph')){
		return false;
	}


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

moduleFilter.downloadChart = function(event) {
	var _this			= moduleFilter,
		button			= event.target,
		downloadType	= button.getAttribute('data-download'),
		chartID			= button.getAttribute('data-target'),
		filename		= button.getAttribute('data-filename'),
		chartElement	= document.getElementById(chartID),
		chart			= null;


	switch (downloadType) {
		case 'png':

			var dataURL = chartElement.toDataURL('image/png'),
				tempLink = document.createElement('a');

				tempLink.href = dataURL;
				tempLink.download = filename + '.png';
				
				document.body.appendChild(tempLink);
				tempLink.click();

				tempLink.remove();				
		break;	

		case 'svg':

			for (var chartIndex in _this.config.graphs) {
				if (_this.config.graphs[chartIndex].id == chartID) {
					chart = _this.config.graphs[chartIndex];
				}
			}

			let tmpChart = chart.options;

			tmpChart.animation = false;
			tmpChart.responsive = false;

			var svgContext = C2S(1000,1000);
			mySvg = new Chart(
				svgContext, 
				{
					type	: chart.type,
					data	: _this.results.graphs[chart.node],
					options	: tmpChart					
				}
			);

			var dataURL = "data:image/svg+xml;charset=utf-8," + encodeURIComponent(svgContext.getSerializedSvg(true)),
				tempLink = document.createElement('a');

				tempLink.href = dataURL;
				tempLink.download = filename + '.svg';
				
				document.body.appendChild(tempLink);
				tempLink.click();

				tempLink.remove();				
		break;

		case 'csv':

			for (var chartIndex in _this.config.graphs) {
				if (_this.config.graphs[chartIndex].id == chartID) {
					chart = _this.config.graphs[chartIndex].chart;
				}
			}

			if (chart !== null) {
				//https://jsfiddle.net/canvasjs/pcsgz06b/;
				var tempLink	= document.createElement('a'),
					content		= _this.chart2CSV(chart);

				if (!content.match(/^data:text\/csv/i)) {
					content = 'data:text/csv;charset=utf-8,' + content;
				}

				tempLink.href = encodeURI(content);
				tempLink.download = filename + '.csv';
					
				document.body.appendChild(tempLink);
				tempLink.click();

				tempLink.remove();				
			}


		break;
	}
}

moduleFilter.chart2CSV = function(chart) {
	
	var
		 columnDelimiter	= ',',
		lineDelimiter		= '\n',
		content				= "";

	
	//generate header
	var line = ["Details"];

	for (var dataSetIndex in chart.data.datasets) {
		line.push(chart.data.datasets[dataSetIndex].label);
	}
	content += line.join(columnDelimiter) + lineDelimiter;

	for (var labelIndex in chart.data.labels) {
		var line	= [chart.data.labels[labelIndex]];

		for (var dataSetIndex in chart.data.datasets) {

			if (chart.data.datasets[dataSetIndex].data[labelIndex]){
				line.push(chart.data.datasets[dataSetIndex].data[labelIndex]);
			} else {
				line.push("");
			}			
		}		

		content += line.join(columnDelimiter) + lineDelimiter;
	}
		
	return content;
}

moduleFilter.initChartPlugins = function() {
	var backgroundColor = 'white';
	Chart.plugins.register({
		beforeDraw: function(c) {
			var ctx = c.chart.ctx;
			ctx.fillStyle = backgroundColor;
			ctx.fillRect(0, 0, c.chart.width, c.chart.height);
		}
	});
}

moduleFilter.runOnLoad = function() {	
	//initialize only if i have the map on the page
	if (document.getElementById('gmap-id')) {
		this.addDefaultListeners();
		this.initChartPlugins();
		this.initMap();
		this.initFields();	
	}
}

moduleFilter.runOnResize = function() {		
}

site.registerModule(moduleFilter);

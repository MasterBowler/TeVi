var moduleFilter = Object.create(siteModule.prototype);

moduleFilter.collapseAt = 1000;

moduleFilter.name = "Navigation";

moduleFilter.config = {
	
	filters : {
		url : {
			options : "api.php?action=filters",
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


		]
	}
}

moduleFilter.initClasses = function() {

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
			}
				
		});


	for (var filterIndex in this.config.filters){ 
		console.log(this.config.filters[filterIndex]);

		this.updateFilterOptions(this.config.filters[filterIndex]);
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

moduleFilter.initButtons = function() {

}

moduleFilter.runOnLoad = function() {	
	this.initClasses();	
}

moduleFilter.runOnResize = function() {		
}

site.registerModule(moduleFilter);

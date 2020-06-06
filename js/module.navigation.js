var moduleNavigation = Object.create(siteModule.prototype);

moduleNavigation.collapseAt = 1000;

moduleNavigation.name = "Navigation";

moduleNavigation.initClasses = function() {

	var vWidth	= window.innerWidth,
		body	= document.getElementsByTagName('body')[0];
	
	if ((vWidth > this.collapseAt) && body.classList.contains('collapsed')) {
		body.classList.remove("collapsed");
		return true;		
	}

	if ((vWidth <= this.collapseAt) && !body.classList.contains('collapsed')) {
		body.classList.add("collapsed");
		return true;		
	}		
}

moduleNavigation.initButtons = function() {
	var 
		mainNavButton	= document.getElementsByClassName("menu-button")[0],
		secNavButton	= document.getElementsByClassName("top-menu-button")[0],
		body			= document.getElementsByTagName('body')[0];

	secNavButton.addEventListener('click', function() {
		body.classList.toggle('show-sec-menu');
	});

	mainNavButton.addEventListener('click', function() {
		body.classList.toggle('show-main-menu');
	});
}

moduleNavigation.runOnLoad = function() {	
	this.initClasses();	
	this.initButtons();
}

moduleNavigation.runOnResize = function() {	
	this.initClasses();	
}

site.registerModule(moduleNavigation);

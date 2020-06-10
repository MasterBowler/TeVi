
site = function() {
}

site.debug		= false;
site.extensions = [];

site.registerModule = function(obj, callback) {
	this.extensions.push(obj);

	if (callback) {
		callback();
	}
}

site.runOnLoad = function() {
	if (site.debug)	console.log("Executing onload functions:");

	if (site.extensions.length) { 
		site.extensions.forEach(function(obj) {

			if (typeof obj.runOnLoad != "function") 
				return false;

			if (site.debug)	console.log(obj);

			obj.runOnLoad();
		});
	}
}


site.runOnResize = function() {

	if (site.debug)	console.log("Executing onresize functions:");

	if (site.extensions.length) { 
		site.extensions.forEach(function(obj) {

			if (typeof obj.runOnResize != "function") 
				return false;

			if (site.debug)	console.log(obj);
			obj.runOnResize();

		});
	}
}

site.runOnScroll = function() {
	if (site.debug)	console.log("Executing onscroll functions:");

	if (site.extensions.length) { 
		site.extensions.forEach(function(obj) {

			if (typeof obj.runOnScroll != "function") 
				return false;

			if (site.debug)	console.log(obj);
			obj.runOnScroll();
		});
	}
}

site.debug = false;

window.addEventListener('resize', site.runOnResize);
window.addEventListener('scroll', site.runOnScroll);
window.addEventListener('load', site.runOnLoad);


function siteModule() {	
}

siteModule.prototype.name = "";
siteModule.prototype.runOnLoad = null;
siteModule.prototype.runOnResize = null;
siteModule.prototype.runOnScroll = null;
var modules = [
		"core" , 
		"module.navigation",
		"module.contact"
	],

	head = document.getElementsByTagName('head')[0];

for (module in modules){
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "assets/js/" + modules[module] + ".js";
	
	head.appendChild(script);
}
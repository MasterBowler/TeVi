var moduleContact = Object.create(siteModule.prototype);


moduleContact.name = "Contact";
moduleContact.url = "send.php";

moduleContact.submit = function(event) {
	event.preventDefault();

	var variables = new FormData();

	variables.append("first-name" , document.getElementById("first-name").value);
	variables.append("last-name" , document.getElementById("last-name").value);
	variables.append("university" , document.getElementById("university").value);
	variables.append("class" , document.getElementById("class").value);
	variables.append("email" , document.getElementById("email").value);
	variables.append("message" , document.getElementById("message").value);

	fetch(
		moduleContact.url,
		{
			method			: 'POST',
			cache			: 'no-cache',
			credentials		: 'same-origin',
			body			: variables
		})
		.then(data =>data.json())
		.then(function(data) {		
				moduleContact.parseFormResponse(data);
		})
		.catch(function (err) {
			console.warn('Something went wrong.', err);
		});		
}

moduleContact.parseFormResponse = function(response) {

	//remove all errors if exists
	var paras = document.getElementsByClassName('form-line-error');

	while(paras[0]) {
		paras[0].parentNode.removeChild(paras[0]);
	}

	if (!response.status){
		moduleContact.mainMessage("error" , "Unknown error");
		return false;
	}
	
	if (response.status == "error"){ 
		for ( field in response.fields ) {

			var fieldHolder = document.getElementsByClassName("form-" + field)[0],
				errorHolder = document.createElement('div');


			errorHolder.className = "form-line-error";
			errorHolder.innerHTML = response.fields[field];
		
			fieldHolder.appendChild(errorHolder);						
		}

		if (response.msg){
			moduleContact.mainMessage("error" , response.msg);
		}
	} else {
		moduleContact.mainMessage("success" , response.msg);
	}
}

moduleContact.mainMessage = function(type , text) {
	var messageHolder = document.getElementsByClassName("form-response")[0];
		messageData = document.createElement('div');

	messageData.className = type;
	messageData.innerHTML = text;

	//delete any previous error/sucess message if exists
	messageHolder.innerHTML = "";
	messageHolder.appendChild(messageData);
}

moduleContact.removeErrorMessage = function(event) {
	event.target.parentNode.parentNode.querySelectorAll(".form-line-error").forEach(e => e.parentNode.removeChild(e));
}

moduleContact.initListeners = function() {

	var  
		form = document.getElementsByClassName("form-contact")[0];

	if (form && form.length){
		fields = form.querySelectorAll(".form-item");

		form.addEventListener(
			"submit",
			this.submit
		);

		for (i = 0 ; i < fields.length ; i++){

			fields[i].addEventListener(
				"click",
				moduleContact.removeErrorMessage
			);
		}	
	}
	
}

moduleContact.runOnLoad = function() {	
	this.initListeners();	
}

site.registerModule(moduleContact);

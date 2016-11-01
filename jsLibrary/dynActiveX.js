// When the page loads: 
window.onload = function() {
	if (document.getElementsByTagName) { 
		// Get all the tags of type object in the page. 
		var objs = document.getElementsByTagName("object"); 
		for (i=0; i < objs.length; i++) { 
			// Get the HTML content of each object tag 
			// and replace it with itself. 
			objs[i].outerHTML = objs[i].outerHTML; 
			} 
		} 
	} 
	
// When the page unloads: 
window.onunload = function() { 
	if (document.getElementsByTagName) { 
		//Get all the tags of type object in the page. 
		var objs = document.getElementsByTagName("object"); 
		for (i=0; i < objs.length; i++) { 
			// Clear out the HTML content of each object tag 
			// to prevent an IE memory leak issue. 
			objs[i].outerHTML = ""; 
			} 
		} 
	} 

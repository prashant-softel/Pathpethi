function OpenDocument(URL)
	{
		//URL = URL.replace(/ /g, "%20");
		//alert(URL);

		var form = document.createElement("form");
	    var element1 = document.createElement("input"); 
    	
	    form.setAttribute("target", "_blank");
	    form.method = "POST";
	    form.action = "W2S_DocViewer.php";   

	    element1.value=URL;
	    element1.name="path";
	    element1.id="path";
	    element1.style.visibility= "hidden";
	    form.appendChild(element1);  

	    document.body.appendChild(form);

	    form.submit();
	    document.getElementById("path").style.visibility = "hidden";
	}
var map;

try {
	var latitude = convertDMStoDD(exif.GPSLatitude);
	var longitude = convertDMStoDD(exif.GPSLongitude);

	let boolean = false;
	let button = document.querySelector('#clickable');
	button.addEventListener('click', (e) => {
		if(!boolean) {
			document.querySelector('#map').style.display = "none";
			document.querySelector('bold').innerHTML = '[+]';
			boolean = true;
		}
		else {
			document.querySelector('#map').style.display = "block";
			document.querySelector('bold').innerHTML = '[-]';
			boolean = false;
		}
	});
}
catch(e) {
	document.querySelector('bold').innerHTML = 'Wrong format.';
}

function convertDMStoDD(coord) {
	let tmpDirection = coord.slice(-1);
	let direction = String(tmpDirection);
	
	let splitArray = coord.split(" ");
	let tmpDegrees = splitArray[0]

	let tmpMinutes = splitArray[2].substring(0, splitArray[2].length - 1);

	let tmpSeconds = splitArray[3].substring(0, splitArray[3].length - 1);

	let degrees = parseFloat(tmpDegrees);
	let minutes = parseFloat(tmpMinutes);
	let seconds = parseFloat(tmpSeconds);

	var dd = degrees + minutes/60 + seconds/(60*60);

	if (direction == "S" || direction == "W") {
		dd = dd * -1;
	}
	return dd
}

function initMap() {

	map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: latitude, lng: longitude},
          zoom: 8});

	var myLatLng = new google.maps.LatLng(latitude,longitude);
	var marker = new google.maps.Marker({
    	position: myLatLng,
    	title: '',
    	map: map
    });
}
/*
function exploitRes (resultats,statut) {
  if (statut !== google.maps.places.PlacesServiceStatus.OK) {
	 return;	// on s'arrête si l'application rencontre une erreur
  } 
  else {
     createMarkers(resultats); //sinon on place les marqueurs
  }
}

function createMarkers (places) {
	var bounds = new google.maps.LatLngBounds();

	for (var i = 0, place; place = places[i]; i++) {// on parcours tous les éléments trouvés
    	var image = {
		  url: place.icon,							// Propriétés de l'icone de l'image
		  size: new google.maps.Size(71, 71),
      	  origin: new google.maps.Point(0, 0),
      	  anchor: new google.maps.Point(17, 34),
      	  scaledSize: new google.maps.Size(25, 25)
    	};
    
    	var marker = new google.maps.Marker({	// On place le marker correpsondant à la position place.geometry.location
      		map: map,							// --> coordonnées de l'élément récupéré
      		icon: image,	
      		title: place.name,
      		position: place.geometry.location
    	});
    }
}



function metadata() {
		var tmpLat = doc.getElementsByTagNameNS(pdf,'GPSLatitude') // EXTRACTION DES COORDONNEES
		var latMap = String(tmpLat[0].textContent);

		var tmpLng = doc.getElementsByTagNameNS(pdf,'GPSLongitude') // EXTRACTION DES COORDONNEES
		var lngMap = String(tmpLng[0].textContent);

		situeMap(latMap,lngMap,titreMap); // La fonction situeMap est appelée dans la fonction méta data afin de ne pas avoir à gérer de variables globales

		/*
		//création de tailleImage
		var balise = document.createElement('p');
		balise.setAttribute('itemprop','fileFormat');
		var data = doc.getElementsByTagNameNS(pdf,'PixelXDimension');
		var tmp = "Taille de l'image: "+data[0].textContent;
		var data = doc.getElementsByTagNameNS(pdf,'PixelYDimension');
		tmp = tmp+'x'+data[0].textContent;
		var contenu = document.createTextNode(tmp);
		balise.appendChild(contenu);
		div2.appendChild(balise);

		//création de coordsGPS
		var balise = document.createElement('p');
		balise.setAttribute('itemprop','locationCreated');
		var data = doc.getElementsByTagNameNS(pdf,'GPSLatitude');
		var tmp = 'Coordonnées GPS: '+data[0].textContent;
		latitude = String(data[0].textContent)
		var data = doc.getElementsByTagNameNS(pdf,'GPSLongitude');
		tmp = tmp+' ; '+data[0].textContent;
		longitude = String(data[0].textContent)
		var contenu = document.createTextNode(tmp);
		balise.appendChild(contenu);
		div2.appendChild(balise);
		*/
		//Ville,pays,credit,date
		/*
		var NS = ['City','Country','Credit','DateCreated']
		var info = ['Ville: ','Pays: ','Credit: ','Date: ']
		var micro = ['locationCreated','locationCreated','creator','dateCreated']

		for(var i=0;i<4;i++){
			var balise = document.createElement('p');
			balise.setAttribute('itemprop',micro[i]);
			var data = doc.getElementsByTagNameNS(photoshop,NS[i]);
			if(existant(data[0])){ //si l'élément est présent on l'affiche
				var contenu = document.createTextNode(info[i]+data[0].textContent);
				balise.appendChild(contenu);
				div2.appendChild(balise);
			}
			else{ //sinon on affiche un message d'erreur
				var contenu = document.createTextNode(info[i]+"Cette information n'est pas disponible.");
				balise.appendChild(contenu);
				div2.appendChild(balise);
			}
		}
		*/

		//source
		/*
		var balise = document.createElement('p');
		balise.setAttribute('itemprop','sourceOrganization');
		var data = doc.getElementsByTagNameNS(photoshop,'Source');
		if(existant(data[0])){
				var contenu = document.createTextNode('Source: '+data[0].textContent);
				balise.appendChild(contenu);
				div2.appendChild(balise);
		}
		else{
			var contenu = document.createTextNode("Source: Cette information n'est pas disponible.");
			balise.appendChild(contenu);
			div2.appendChild(balise);
		}
		*/
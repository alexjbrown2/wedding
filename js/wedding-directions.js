

function initMap() {
	var control = document.getElementById('floating-panel');
        control.style.display = 'block';
	
	   new AutocompleteDirectionsHandler(map);

        function AutocompleteDirectionsHandler(map) {
        this.originPlaceId = null;
        var originInput = document.getElementById('origin-input');

        var originAutocomplete = new google.maps.places.Autocomplete(
            originInput, {placeIdOnly: true});
      }
	var directionsDisplay = new google.maps.DirectionsRenderer;
        var directionsService = new google.maps.DirectionsService;
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 7,
          center: {lat: 41.85, lng: -87.65}
        });


        directionsDisplay.setMap(map);
        directionsDisplay.setPanel(document.getElementById('right-panel'));
	
	var onChangeHandler = function() {
          calculateAndDisplayRoute(directionsService, directionsDisplay);

        };

	
	function startmap(){
	document.getElementById('map').classList.add('map-visible');
	document.querySelector('.directions-container .section').classList.add('auto-height');

	
        var control = document.getElementById('floating-panel');
        control.style.display = 'block';
        //map.controls[google.maps.ControlPosition.TOP_CENTER].push(control);

        
        //document.getElementById('origin-input').addEventListener('onfocusout', onChangeHandler);
        //document.getElementById('end').addEventListener('change', onChangeHandler);
        
	}
	document.getElementById('get-directions').addEventListener('click', startmap, false);
	document.getElementById('get-directions').addEventListener('click',  onChangeHandler);

	}
	

      function calculateAndDisplayRoute(directionsService, directionsDisplay) {
        var start = document.getElementById('origin-input').value;
        var end = document.getElementById('end').value;
        directionsService.route({
          origin: start,
          destination: end,
          travelMode: 'DRIVING'
        }, function(response, status) {
          if (status === 'OK') {
            directionsDisplay.setDirections(response);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
      }

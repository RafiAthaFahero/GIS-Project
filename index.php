<?php
include "functions.php";
if(isset($_POST["addPlace"])) {
    if(add($_POST) <= 0) {
        echo "<script>alert('Error')</script>";
    }
}

$places = query("SELECT * FROM places");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <style>
        #map {
            height: 100%;
            
        }
        .custom-map-control-button {
            background-color: #fff;
            border: 0;
            border-radius: 20px;
            box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
            margin: 10px;
            padding: 0 0.5em;
            /* font: 400 18px Roboto, Arial, sans-serif; */
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            height: 40px;
            width: 40px;
            cursor: pointer;
        }

        /* 
        * Optional: Makes the sample page fill the window. 
        */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .pac-card {
            background-color: #F6F1F1;
            color:black;
            border: 0;
            border-radius: 20px;
            box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
            padding: 0 0.5em;
            margin: 10px;
            font: 400 18px Roboto, Arial, sans-serif;
            overflow: hidden;
            font-family: Roboto;
            width: 300px;
            padding: 0;
        }
        #pac-container {
            padding-bottom: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #pac-input {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 70%;
            height: 20px;
            outline: none;
        }
        #pac-input:focus {
            border-color: #C7E9B0;
        }

        #title {
            color: #fff;
            background-color: #C7E9B0;
            font-size: 25px;
            font-weight: 500;
            padding: 6px 12px;
            box-sizing: border-box;
            width: 100%;
        }
        #pac-container label {
            width: 80%;
            color: grey;
        }
        #button {
            background-color: #C7E9B0;
            color: black;
            border: 0;
            padding: 10px;
            box-sizing: border-box;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
            height: 25px;
            border-radius: 20px;
            width: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #editMarker {
            position: fixed;
            width: 30px;
            height: 40px;
            z-index: 999;
            display: none;
        }

        form {
            display: flex;
            flex-direction: column;
        }
        form button {
            align-self: center;
        }


/*  */

.navbar {
    background-color: green;
            color: white;
            padding: 10px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-right: 10px;
        }

/*  */




    </style>
</head>
<body>


<div class="navbar" style="display: flex; justify-content: center;">
    <a href="#">Home</a>
    <a href="#">About</a>
    <a href="#">Contact</a>
    
</div>

    <img src="location-mark.png" id="editMarker" draggable="false">
    <div id="map"></div>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBq5_gCfn5vlroLlUUdiY7BnkXumIql3tU&callback=createMap&libraries=places&v=weekly" defer></script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBq5_gCfn5vlroLlUUdiY7BnkXumIql3tU&callback=initMap" defer></script> -->
    <script>
        let map;
        let service;
        let infoWindow;
        let marker;
        let directionsService;
        let directionsRenderer;

        let isAddingPlace = false;
        
        let fromGeometry;
        let toGeometry;

        const editMarker = document.getElementById("editMarker");
        const places = <?php echo json_encode($places) ?>;

        document.addEventListener("mousemove", e => {
            if(!isAddingPlace)
                return;
            editMarker.style.left = e.pageX + 1.5 + "px";
            editMarker.style.top = e.pageY + 1.5 + "px";
        })

        function createMap() {
          const cikarang = new google.maps.LatLng(-6.262193, 107.54164);
        
          infoWindow = new google.maps.InfoWindow();
/////////

var mapOptions = {
        center: cikarang, // Set your desired center coordinates
        zoom: 12, // Set the initial zoom level
        mapTypeId: google.maps.MapTypeId.ROADMAP, // Set the default map type to "roadmap"
        
        styles: [
            {
                featureType: "all",
                elementType: "labels.text",
                stylers: [
                    { visibility: "off" } // Hide all text labels on the map
                ]
            },
            {
                featureType: "road",
                elementType: "geometry",
                stylers: [
                    { hue: "#ff9900" }, // Set the color of roads to a vintage hue
                    { saturation: -100 } // Desaturate the road color
                ]
            }
            // Add more custom map styles here as needed
        ]
    };


////////

          map = new google.maps.Map(document.getElementById("map"), mapOptions), 



          marker = new google.maps.Marker({
            map,
            position: cikarang,
            visible: false
          });
          directionsService = new google.maps.DirectionsService();
          directionsRenderer = new google.maps.DirectionsRenderer();
          directionsRenderer.setMap(map);
          

          // Widgets
          const locationButton = document.createElement("button");
          const locationImage = document.createElement("img");
          locationImage.width = 40;
          locationImage.height = 40;
          locationImage.src = "https://media.istockphoto.com/id/1261917621/vector/map-pin-icon-for-your-web-site-and-mobile-app.jpg?s=612x612&w=0&k=20&c=kpqmtnYMH1A1Oyn39shNEbwyhn9gYc1tFsMY2B9Xodo=";
          
          locationButton.append(locationImage);
          locationButton.classList.add("custom-map-control-button");
          map.controls[google.maps.ControlPosition.TOP_LEFT].push(locationButton);
          locationButton.addEventListener("click", addPlace)
            
            map.addListener("click", (mapsMouseEvent) => {
                if(!isAddingPlace)
                    return;
                const pos = mapsMouseEvent.latLng.toJSON();
                console.log(pos);
                infoWindow.setPosition(pos);
                infoWindow.setContent(`
                    <form action="" method="POST">
                        <input type="hidden" name="lat" value="${pos.lat}">
                        <input type="hidden" name="lng" value="${pos.lng}">

                        <label for="title">Place name: </label>
                        <input type="text" name="name">
                        
                        <label for="title">Ratings: </label>
                        <span><input type="number" name="ratings" min="0" max="5">/5</span>

                        <button id="button" name="addPlace" type="submit">Add</button>
                    </form>
                    `);
                infoWindow.open(map);
            });
            
            // Autocomplete & find location
            const card = document.createElement("div");
            card.className = "pac-card";
            card.innerHTML = `
                <div id="pac-container">
                    <div id="title">Find Places</div>
                    <br>
                    <label for="pac-input">From:</label>
                    <input id="pac-input" type="text" class="from-auto" placeholder="Enter a location" />
                    <br>
                    <label for="pac-input">To:</label>
                    <input id="pac-input" type="text" class="to-auto" placeholder="Enter a location" />
                    <br>
                    <button id="button" style="width: 80%" type="button">Get direction</button>
                </div>
            `;
            map.controls[google.maps.ControlPosition.LEFT_CENTER].push(card);
            const autocompleteOptions = {
                fields: ["formatted_address", "geometry", "name"],
                strictBounds: false,
                types: ["establishment"],
            };
            const fromAutoInput = card.querySelector(".from-auto");
            const toAutoInput = card.querySelector(".to-auto");
            const getDirectionButton = card.querySelector("#button");
            const fromAutocomplete = new google.maps.places.Autocomplete(fromAutoInput, autocompleteOptions);
            const toAutocomplete = new google.maps.places.Autocomplete(toAutoInput, autocompleteOptions);

            // Bind the map's bounds (viewport) property to the autocomplete object,
            // so that the autocomplete requests use the current map bounds for the
            // bounds option in the request.
            fromAutocomplete.bindTo("bounds", map);
            toAutocomplete.bindTo("bounds", map);
            fromAutocomplete.addListener("place_changed", () => {
                const place = fromAutocomplete.getPlace();

                if (!place.geometry || !place.geometry.location) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }
                fromGeometry = place.geometry;
            })

            toAutocomplete.addListener("place_changed", () => {
                const place = toAutocomplete.getPlace();

                if (!place.geometry || !place.geometry.location) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }
                toGeometry = place.geometry;
            })
            getDirectionButton.onclick = () => {
                if(!fromGeometry || !toGeometry) 
                    return alert("No specific location selected");
                getDirection(fromGeometry.location.toJSON(), toGeometry.location.toJSON())
            }
            // Markers for near facility
            findNearbyHealthFacility();
            if(places)
                places.forEach(place => {
                    const obj = {
                        geometry: {
                            location: new google.maps.LatLng(place.lat, place.lng)
                        },
                        name: place.name,
                        ratings: place.ratings
                    }
                    console.log(obj)
                    createMarker(obj);
                });
        }

        // 
        function setMapType(mapType) {
    if (map) {
        if (mapType === 'roadmap') {
            map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
        } else if (mapType === 'satellite') {
            map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
        }
    }
}





        function addPlace(e) {
            e.target.style.opacity = ".5"
            if(isAddingPlace)
                return cancelPlace(e);
            isAddingPlace = true;
            editMarker.style.display = "block"
        }
        
        function cancelPlace(e) {
            e.target.style.opacity = "1"
            isAddingPlace = false;
            editMarker.style.display = "none"
        }
        function createMarker(place) {
            if (!place.geometry || !place.geometry.location)
                return;
            console.log("creating markers");
            const marker = new google.maps.Marker({
                map,
                position: place.geometry.location,
            });

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const { lat, lng } = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };
                    console.log(position);
                    const rad = function(x) {
                        return x * Math.PI / 180;
                    };

                    const getDistance = function(p1, p2) {
                        var R = 6378137; // Earth’s mean radius in meter
                        var dLat = rad(p2.lat() - p1.lat);
                        var dLong = rad(p2.lng() - p1.lng);
                        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                            Math.cos(rad(p1.lat)) * Math.cos(rad(p2.lat())) *
                            Math.sin(dLong / 2) * Math.sin(dLong / 2);
                        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                        var d = R * c;
                        return d; // returns the distance in meter
                    };
                    const distance = getDistance({ lat, lng }, place.geometry.location);

                    google.maps.event.addListener(marker, "click", () => {
                        infoWindow.setPosition(place.geometry.location);
                        infoWindow.setContent(`
                        <b>${place.name}</b> 
                        <br>
                        <span>Rating ${place.rating || 0}/5 (${place.user_ratings_total || 0})</span>
                        <br>
                        <span>${Math.floor(distance)} meters away from you.</span>
                        <br>
                        <button id="button" onclick="getDirection({ lat: ${lat}, lng: ${lng} }, { lat: ${place.geometry.location.lat()}, lng: ${place.geometry.location.lng()} })">Get Directions</button>
                        `);
                        infoWindow.open(map);
                    });

                }, 
                () => {
                    handleLocationError(true, infoWindow, map.getCenter());
                }
            );
        }

        function findNearbyHealthFacility() {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };
                    var request = {
                        location: pos,
                        radius: '3000',
                        type: ['hospital', 'doctor', 'dentist', 'drugstore', 'pharmacy']
                    };
            
                    service = new google.maps.places.PlacesService(map);
                    service.nearbySearch(request, nearbySearchCallback);
                },
                () => {
                    handleLocationError(true, infoWindow, map.getCenter());
                }
            );
        }
        function getDirection(p1, p2) {
            directionsService
            .route({
                origin: {
                    query: `${p1.lat},${p1.lng}`
                },
                destination: {
                    query: `${p2.lat},${p2.lng}`
                },
                travelMode: google.maps.TravelMode.DRIVING,
            })
            .then((response) => {
                directionsRenderer.setDirections(response);
            })
            .catch((e) => window.alert("Directions request failed due to " + e));
        }

        function nearbySearchCallback(results, status) {
            if (status == google.maps.places.PlacesServiceStatus.OK) {
                for (var i = 0; i < results.length; i++) {
                    createMarker(results[i]);
                }
            }
        }
        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(
                browserHasGeolocation
                ? "Error: The Geolocation service failed."
                : "Error: Your browser doesn't support geolocation."
            );
            infoWindow.open(map);
        }

        
        window.createMap = createMap;
    </script>
</body>
</html>
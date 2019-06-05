/* ~~~ #################### ~~~ */
/* ~~~ MapBox Embedded Maps ~~~ */
/* ~~~ #################### ~~~ */
// This allows the .remove() function to work later on

if (!('remove' in Element.prototype)) {
    Element.prototype.remove = function() {
        if (this.parentNode) {
            this.parentNode.removeChild(this);
        }
    };
}
mapboxgl.accessToken = 'pk.eyJ1IjoibWljaGFlbGJhbGFzIiwiYSI6ImNqbnh4MWlyZTBoMWUzd214b2FpamVoeGEifQ.Ge_dAJ0FJovNlqhvxYDGZg';
// Initialize large results map
var map = new mapboxgl.Map({
                           container: 'map', style: 'mapbox://styles/mapbox/streets-v10',
                           center: [-79.9192, 43.2609], // starting position
                           zoom: 12 // starting zoom
                           });


// Add zoom and rotation controls to the results map.
map.addControl(new mapboxgl.NavigationControl());

var parking_spots = {
    "type": "FeatureCollection",
    "features": [
                 {
                 "type": "Feature",
                 "geometry": {
                 "type": "Point",
                 "coordinates": [
                                 -79.91781,
                                 43.25839
                                 ]
                 },
                 "properties": {
                 "name": "Trendy Outdoor Parking",
                 "price": "$26.50",
                 "rating": "4.1 Stars",
                 "spots": "1",
                 "address": "University Ave",
                 "city": "Hamilton",
                 "country": "Canada",
                 "postalCode": "L8S 4S2"
                 }
                 },
                 {
                 "type": "Feature",
                 "geometry": {
                 "type": "Point",
                 "coordinates": [
                                 -79.92151,
                                 43.25707
                                 ]
                 },
                 "properties": {
                 "name": "Luxurious Parking Garage",
                 "price": "$31.99",
                 "rating": "4.4 Stars",
                 "spots": "2",
                 "address": "6 Aylett St.",
                 "city": "Hamilton",
                 "country": "Canada",
                 "postalCode": "L8S 2Z1"
                 }
                 },
                 {
                 "type": "Feature",
                 "geometry": {
                 "type": "Point",
                 "coordinates": [
                                 -79.924997,
                                 43.248555
                                 ]
                 },
                 "properties": {
                 "name": "Parking for Motorcycles",
                 "price": "$17.50",
                 "rating": "3.7 Stars",
                 "spots": "23",
                 "address": "118 Hillview St",
                 "city": "Hamilton",
                 "country": "Canada",
                 "postalCode": "L8S 2Z5"
                 }
                 },
                 {
                 "type": "Feature",
                 "geometry": {
                 "type": "Point",
                 "coordinates": [
                                 -79.927816,
                                 43.253236
                                 ]
                 },
                 "properties": {
                 "name": "Park with Car Wash",
                 "price": "$19.30",
                 "rating": "3.9 Stars",
                 "spots": "3",
                 "address": "200 Whitnet Ave",
                 "city": "Hamilton",
                 "country": "Canada",
                 "postalCode": "L8S 2G7"
                 }
                 },
                 {
                 "type": "Feature",
                 "geometry": {
                 "type": "Point",
                 "coordinates": [
                                 -79.912742,
                                 43.252423
                                 ]
                 },
                 "properties": {
                 "name": "Cheap Parking",
                 "price": "$7.00",
                 "rating": "2.9 Stars",
                 "spots": "6",
                 "address": "Brantford Rail Trail",
                 "city": "Hamilton",
                 "country": "Canada",
                 "postalCode": "ON L8P"
                 }
                 },
                 {
                 "type": "Feature",
                 "geometry": {
                 "type": "Point",
                 "coordinates": [
                                 -79.912593,
                                 43.255913
                                 ]
                 },
                 "properties": {
                 "name": "Park in my Driveway",
                 "price": "$14.99",
                 "rating": "1.7 Stars",
                 "spots": "1",
                 "address": "147 Haddon Ave S",
                 "city": "Hamilton",
                 "country": "Canada",
                 "postalCode": "L8S 1X7"
                 }
                 }
                 ]};

//Add a layer that contains parking spot data and describes how it should be rendered
map.on('load', function(e) {
       //Add the data to the map as a layer
       map.addLayer({
                    id: 'locations',
                    type: 'symbol',
                    //Add a GeoJSON source containing place coordinates and information
                    source: {
                    type: 'geojson',
                    data: parking_spots
                    },
                    layout: {
                    'icon-image': 'car-15',
                    'icon-allow-overlap': true,
                    }
                    });
       });



// Define Interactivity Functions
function flyToGarage(currentFeature) {
    map.flyTo({
              center: currentFeature.geometry.coordinates,
              zoom: 18
              });
}

function createPopUp(currentFeature) {
    var popUps = document.getElementsByClassName('mapboxgl-popup');
    // Check if there is already a popup on the map and if so, remove it
    if (popUps[0]) popUps[0].remove();
    
    if (currentFeature.properties.spots == 1) {
        var popupDesc = ('<h3>' + currentFeature.properties.name + '</h3>' + '<h4>' + currentFeature.properties.price + ' | ' + currentFeature.properties.rating + ' | ' + currentFeature.properties.spots + ' spot' + '<h5><a href=parking.html>See More</a></h4>');
    } else {
        var popupDesc = ('<h3>' + currentFeature.properties.name + '</h3>' + '<h4>' + currentFeature.properties.price + ' | ' + currentFeature.properties.rating + ' | ' + currentFeature.properties.spots + ' spots' + '<h5><a href=parking.html>See More</a></h4>');
    }
    
    var popup = new mapboxgl.Popup({ closeOnClick: true })
    .setLngLat(currentFeature.geometry.coordinates)
    .setHTML(popupDesc)
    .addTo(map);
}

// Add Event Listener for when a user clicks on the map
map.on('click', function(e) {
       // Query all the rendered points in the view
       var features = map.queryRenderedFeatures(e.point, {layers: ['locations']});
       if (features.length) {
       var clickedPoint = features[0];
       // 1. Fly to the point
       flyToGarage(clickedPoint);
       // 2. Close all other popups and display popup for clicked store
       createPopUp(clickedPoint);
       // Find the index of the parking_spots.features that corresponds to the clickedPoint that fire the event listener
       var selectedFeature = clickedPoint.properties.address;
       
       for (var i = 0; i < parking_spots.features.length; i++) {
       	if (parking_spots.features[i].properties.address === selectedFeature) {
       		selectedFeatureIndex = i;
       	}
       }
   }
});
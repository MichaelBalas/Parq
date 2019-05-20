/* ~~~ #################### ~~~ */
/* ~~~ MapBox Embedded Maps ~~~ */
/* ~~~ #################### ~~~ */

mapboxgl.accessToken = 'pk.eyJ1IjoibWljaGFlbGJhbGFzIiwiYSI6ImNqbnh4MWlyZTBoMWUzd214b2FpamVoeGEifQ.Ge_dAJ0FJovNlqhvxYDGZg';

var small_map = new mapboxgl.Map({
	container: 'small-map', 
	style: 'mapbox://styles/mapbox/streets-v10',
	center: [-79.92151, 43.25707], // starting position
	zoom: 14 // starting zoom
});

// Define Interactivity Functions
function flyToGarage(map, currentFeature) {
	map.flyTo({
		center: currentFeature.geometry.coordinates,
		zoom: 18
	});
}

small_map.on('load', function(e) {
					//Add the data to the map as a layer
					small_map.addLayer({
						id: 'locations',
						type: 'symbol',
						//Add a GeoJSON source containing place coordinates and information
						source: {
							type: 'geojson',
							data:
							{
								"type": "Point",
								"coordinates": [-79.92151, 43.25707] 
							}
						},
						layout: {
							'icon-image': 'car-15',
							'icon-allow-overlap': true,
						}
					});
				});

		// Add Event Listener for when a user clicks on the map
		small_map.on('click', function(e) {
			// Query all the rendered points in the view
			var features = small_map.queryRenderedFeatures(e.point, {layers: ['locations']});
			if (features.length) {
				var clickedPoint = features[0];
				// 1. Fly to the point
				flyToGarage(small_map, clickedPoint);
			}
		});


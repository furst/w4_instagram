jQuery(document).ready(function() {

	/* -------------------------------------------------------------- */
	/* User-search
	/* -------------------------------------------------------------- */

	var UserSearch = {
		init: function() {
			var self = this;

			this.container = jQuery('#user-list');
			this.error = jQuery('.w4-instagram-error');
			this.url = 'https://api.instagram.com/v1/users/search';
			this.count = 5;
			this.query = jQuery('.username-setting');
			this.userId = jQuery('.user-id-setting');
			this.loaderField = jQuery('.loader-field');
			this.loader = jQuery('<div></div>').addClass('loader');

			// lyssna på när det skrivs
			this.query.on('input', function() {
				if (self.query.val().length > 3) {
					// Hämta data från instagram vid mer än tre bokstäver
					self.fetch(jQuery(this).val()).then(function(results) {
						self.userId.val(results.data[0].id);
						self.map(results);
						self.append();
						self.loader.remove();
					}, function() {
						self.error.text('An error occured');
						console.log('failed');
					});
				}
			});

			jQuery('html').click(function() {
  				self.container.empty();
			});
		},

		fetch: function(query) {
			var self = this;

			this.loaderField.append(this.loader);

			// Hämta data från instagram
			return jQuery.ajax({
				url: this.url,
				data: {
					q: query,
					count: this.count,
					access_token: accessToken
				},
				dataType: 'jsonp'
			}).promise();
		},

		// Rensa svar från instagram
		map: function(results) {
			this.users = jQuery.map(results.data, function(user) {
				return {
					fullname: user.full_name,
					username: user.username,
					id: user.id
				};
			});
		},

		// Lägg till på DOM:en
		append: function() {
			var self = this;
			this.container.empty();
			var ul = jQuery('<ul></ul>');

			jQuery.each(this.users, function(index, user) {
				var li = jQuery('<li></li>');
				var a = jQuery('<a href="#"></a>').data('id', user.id).addClass('user-link');
				a.text(user.username);
				li.append(a);
				ul.append(li);
			});

			this.container.append(ul);

			jQuery('.user-link').on('click', function(e) {
				self.query.val(jQuery(this).text());
				self.userId.val(jQuery(this).data('id'));

				e.preventDefault();
			});
		}
	};

	UserSearch.init();

	// Kör endast om canvas finns
	if (jQuery('#map-canvas').length > 0) {

		/* -------------------------------------------------------------- */
		/* Location-search
		/* -------------------------------------------------------------- */

		var LocationSearch = {
			init: function() {
				this.url = 'https://api.instagram.com/v1/media/search';
				this.distance = 5000;
				this.mapContainer = jQuery('#map-canvas');
				this.loader = jQuery('<div></div>').addClass('loader');
			},

			// Hämta data från instagram
			fetch: function(lat, lng, distance) {
				var self = this;

				this.init();

				this.mapContainer.append(this.loader);

				return jQuery.ajax({
					url: this.url,
					data: {
						lat: lat,
						lng: lng,
						distance: distance,
						access_token: accessToken
					},
					dataType: 'jsonp'
				}).promise();
			},
		};

		/* -------------------------------------------------------------- */
		/* Map
		/* -------------------------------------------------------------- */

		var Map = {
			init: function() {
				var self = this;

				this.map;

				this.markers = Array();
				this.circle = Array();

				this.radius = 5000;

				this.defaultLat = 59.313217;
				this.defaultLng = 18.080838;
				this.locationCoords = jQuery('.location-coords-setting');
				this.locationDistance = jQuery('.location-distance-setting');
				this.error = jQuery('.w4-instagram-error');

				google.maps.event.addDomListener(window, 'load', this.fetchMap().then(function() {

					// Börja lyssna på event
	  				self.placeSearch();

	  				self.getLocations();

	  				self.saveLocation();

	  				self.selectRadius();
				}));

				self.addCrosshair();
			},

			// Hämta karta
			fetchMap: function() {

				var self = this;
				var dfd = jQuery.Deferred();

				var mapOptions = {
					zoom: 12,
				    center: new google.maps.LatLng(this.defaultLat, this.defaultLng),
				    mapTypeId: google.maps.MapTypeId.ROADMAP,
				    minZoom: 12,
					maxZoom: 22,
					zoomControl:true,
					zoomControlOptions: {
						style:google.maps.ZoomControlStyle.DEFAULT
					},
					panControl:false,
					mapTypeControl:false,
					scaleControl:false,
					streetViewControl:false,
					overviewMapControl:false,
					rotateControl:false
				};

				this.map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

				dfd.resolve();

				// När kartan är hämtad, sätt ut pinnar med data
				LocationSearch.fetch(this.defaultLat, this.defaultLng, self.radius).then(function(results) {
					self.pin(results);
				}, function() {
					self.error.text('An error occured');
					console.log('Failed');
				});

				self.addCircle();

				return dfd.promise();
			},

			// Lägg till pinne
			pin: function(results) {
				var self = this;

				jQuery.each(results.data, function(index, data) {
					var latLng = new google.maps.LatLng(data.location.latitude, data.location.longitude);

					var contentString = '<a target="_blank" style="display:block;" href="' + data.link + '">' +
					'<img src="' + data.images.thumbnail.url + '" alt="photo by ' + data.user.username + '">' +
					'</a>' +
					'Photo by @<a target="_blank" href="http://instagram.com/' + data.user.username + '">' + data.user.username + '</a>'
					;

					var infowindow = new google.maps.InfoWindow({
	      				content: contentString
	  				});

					var marker = new google.maps.Marker({
			      		position: latLng,
			      		map: self.map,
			      		title: data.location.name
			  		});

			  		self.markers.push(marker);

			  		google.maps.event.addListener(marker, 'click', function() {
	    				infowindow.open(self.map, marker);
	  				});
				});
			},

			// Lägg till cirkel
			addCircle: function() {
				var self = this;

				this.circle.push(new google.maps.Circle({
					map: self.map,
					radius: self.radius,
					center: self.map.getCenter(),
					strokeColor: '#61a8e4',
					strokeOpacity: 0.9,
					strokeWeight: 1,
					fillColor: '#94c6f0',
					fillOpacity: 0.35,
				}));
			},

			// Sök på en plats och hämta den platsen på kartan
			placeSearch: function() {
				var self = this;

	  			var input = (document.getElementById('pac-input'));
	  			self.map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

	  			var searchBox = new google.maps.places.SearchBox((input));

	  			google.maps.event.addListener(searchBox, 'places_changed', function() {
	    			var places = searchBox.getPlaces();

	    			var bounds = new google.maps.LatLngBounds();
	    			for (var i = 0, place; place = places[i]; i++) {
	      				bounds.extend(place.geometry.location);
	    			}

	    			self.map.fitBounds(bounds);
	  			});
			},

			// Hämta platser från instagram beroende på kordinater
			getLocations: function() {
				var self = this;

	  			jQuery('.get-locations').on('click', function(event) {
	  				self.clearOverlays();

	  				var latLng = self.map.getCenter();

					self.addCircle();

					LocationSearch.fetch(latLng.lat(), latLng.lng(), self.radius).then(function(results) {
						self.pin(results);
						jQuery('.loader').remove();
					}, function() {
						self.error.text('Ett oväntat fel inträffade');
						console.log('Failed');
					});

	  				event.preventDefault();
	  			});
			},

			// Lägg till kors i mitten av kartan
			addCrosshair: function() {
				var self = this;

				var crosshair = new google.maps.MarkerImage(
    				'../wp-content/plugins/w4_instagram/img/crosshair.png',
    				new google.maps.Size(19, 19),
    				new google.maps.Point(0,0),
    				new google.maps.Point(9, 9)
    			);

  				var crosshairShape = {
    				coords: [32,32,32,32],
    				type: 'rect'
  				};

   				var crosshairMarker = new google.maps.Marker({
    				position: this.map.getCenter(),
    				map: this.map,
    				icon: crosshair, 
    				shape: crosshairShape,
    				optimized: false,
    				zIndex: 5
  				});

   				google.maps.event.addListener(this.map, 'bounds_changed', function() {
   					crosshairMarker.setPosition(self.map.getCenter());
   				});
			},

			// Lägg till kordinater i inputfält i wp
			saveLocation: function() {
				var self = this;

				jQuery('.save-location').on('click', function(event) {
	  				var latLng = self.map.getCenter();

	  				self.locationCoords.val(latLng.lat() + ', ' + latLng.lng());
	  				self.locationDistance.val(self.radius);

	  				event.preventDefault();
	  			});
			},

			// Ändra radius-sök
			selectRadius: function() {
				var self = this;

				jQuery('.radius-select').on('change', function(event) {
	  				self.radius = parseInt(this.value);

	  				event.preventDefault();
	  			});
			},

			// Ta bort markörer
			clearOverlays: function() {
			  	for (var i = 0; i < this.markers.length; i++ ) {
			    	this.markers[i].setMap(null);
			  	}
			  	this.markers.length = 0;

			  	for (var i = 0; i < this.circle.length; i++ ) {
			    	this.circle[i].setMap(null);
			  	}
			  	this.circle.length = 0;
			}
		};

		Map.init();
	}

	// Toggle instruktioner
	jQuery('.info-toggle').on('click', function() {
		jQuery('.hide').toggle(300);
	});
});


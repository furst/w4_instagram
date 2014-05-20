// TODO: ändra css-klassnamn

jQuery(document).ready(function() {

	// --------- User search

	// TODO:
	// * Gå ner i dropdown med piltangenter?
	// * Visa grönt eller rött vid rätt/fel username?

	var UserSearch = {
		init: function() {
			var self = this;

			this.container = jQuery('#user-list');
			this.url = 'https://api.instagram.com/v1/users/search';
			this.count = 5;
			this.query = jQuery('.username-setting');
			this.userId = jQuery('.user-id-setting');
			this.loaderField = jQuery('.loader-field');
			this.loader = jQuery('<div></div>').addClass('loader');

			this.query.on('input', function() {
				if (self.query.val().length > 3) {
					self.fetch(jQuery(this).val()).then(function(results) {
						self.map(results);
						self.append();
						self.loader.remove();
					}, function() {
						// fixa
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

		map: function(results) {
			this.users = jQuery.map(results.data, function(user) {
				return {
					fullname: user.full_name,
					username: user.username,
					id: user.id
				};
			});
		},

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

	// --------- Location search

	if (jQuery('#map-canvas').length > 0) {

		var LocationSearch = {
			init: function() {
				this.url = 'https://api.instagram.com/v1/locations/search';
				this.distance = 5000;
				this.mapContainer = jQuery('#map-canvas');
				this.loader = jQuery('<div></div>').addClass('loader');
			},

			fetch: function(lat, lng) {
				var self = this;

				this.init();

				this.mapContainer.append(this.loader);

				return jQuery.ajax({
					url: this.url,
					data: {
						lat: lat,
						lng: lng,
						distance: this.distance,
						access_token: accessToken
					},
					dataType: 'jsonp'
				}).promise();
			},
		};

		var Map = {
			init: function() {
				var self = this;

				this.map;

				this.defaultLat = 59.313217;
				this.defaultLng = 18.080838;
				this.locationId = jQuery('.location-id-setting');
				this.locationName = jQuery('.location-name-setting');

				google.maps.event.addDomListener(window, 'load', this.fetchMap().then(function() {
					self.zoom();
					self.drag();

					// Create the search box and link it to the UI element.
	  				var input = (document.getElementById('pac-input'));
	  				self.map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

	  				var searchBox = new google.maps.places.SearchBox((input));

	  				// Listen for the event fired when the user selects an item from the
	  				// pick list. Retrieve the matching places for that item.
	  				google.maps.event.addListener(searchBox, 'places_changed', function() {
	    				var places = searchBox.getPlaces();

	    				// For each place, get the icon, place name, and location.
	    				var bounds = new google.maps.LatLngBounds();
	    				for (var i = 0, place; place = places[i]; i++) {
	      					bounds.extend(place.geometry.location);
	    				}

	    				self.map.fitBounds(bounds);
	  				});
				}));
			},

			fetchMap: function() {

				var self = this;
				var dfd = jQuery.Deferred();

				var mapOptions = {
					zoom: 20,
				    center: new google.maps.LatLng(this.defaultLat, this.defaultLng),
				    mapTypeId: google.maps.MapTypeId.ROADMAP,
				    minZoom: 20,
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

				LocationSearch.fetch(this.defaultLat, this.defaultLng).then(function(results) {
					self.pin(results);
				}, function() {
					// fixa
					console.log('Failed');
				});

				return dfd.promise();
			},

			drag: function() {
				var self = this;

				google.maps.event.addListener(this.map, 'dragend', function() {
					var latLng = self.map.getCenter();
				    
				    LocationSearch.fetch(latLng.lat(), latLng.lng()).then(function(results) {
						self.pin(results);
						jQuery('.loader').remove();
					}, function() {
						// fixa
						console.log('Failed');
					});
				});
			},

			zoom: function() {
				var self = this;

				google.maps.event.addListener(this.map, 'zoom_changed', function() {
					var latLng = self.map.getCenter();
				    
				    LocationSearch.fetch(latLng.lat(), latLng.lng()).then(function(results) {
						self.pin(results);
						jQuery('.loader').remove();
					}, function() {
						// fixa
						console.log('Failed');
					});
				});
			},

			pin: function(results) {
				var self = this;

				jQuery.each(results.data, function(index, data) {
					var latLng = new google.maps.LatLng(data.latitude, data.longitude);

					var infowindow = new google.maps.InfoWindow({
	      				content: data.name
	  				});

					var marker = new google.maps.Marker({
			      		position: latLng,
			      		map: self.map,
			      		title: data.name
			  		});

			  		google.maps.event.addListener(marker, 'click', function() {
	    				infowindow.open(self.map, marker);
	    				self.locationId.val(data.id);
	    				self.locationName.val(data.name);
	  				});
				});
			}
		};

		Map.init();
	}

	jQuery('.info-toggle').on('click', function() {
		jQuery('.hide').toggle(300);
	});
});


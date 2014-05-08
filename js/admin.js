// TODO: ändra klassnamn

jQuery(document).ready(function() {

	// --------- User search

	var userCon = jQuery('#user-con');
	var userInput = jQuery('.user-id-setting');

	jQuery('.user-search').on('click', function(e) {
		userCon.empty();
		var query = jQuery('.query').val();
		var url = 'https://api.instagram.com/v1/users/search?';
		
		jQuery.get(url, {q: query, access_token: accessToken })
			.done(function(data) {
				jQuery.each(data.data, function(index, data) {
					var row = jQuery('<div class="user-row"></div>');
					var p = jQuery('<p></p>').text(data.full_name + ', ' + data.username + ', ' + data.id);
					var a = jQuery('<a href="#"></a>').text('Add').data('id', data.id).addClass('user-link');
					row.append(p)
					row.append(a);
					userCon.append(row);
				});

				addUser();
			})
			.fail(function() {
    			userCon.append(jQuery('<p></p>').text('Ett fel inträffade'));
  			});

		e.preventDefault();
	});

	function addUser() {
		jQuery('a.user-link').on('click', function(e) {
			userInput.val(jQuery(this).data('id'));

			e.preventDefault();
		});
	}

	// --------- Location search

	var locationCon = jQuery('#location-con');
	var locationInput = jQuery('.location-id-setting');

	jQuery('.location-search').on('click', function(e) {
		locationCon.empty();
		var latQuery = jQuery('.lat-query').val();
		var lngQuery = jQuery('.lng-query').val();
		var url = 'https://api.instagram.com/v1/locations/search?';
		
		jQuery.get(url, {lat: latQuery, lng: lngQuery, access_token: accessToken })
			.done(function(data) {
				jQuery.each(data.data, function(index, data) {
					var row = jQuery('<div class="user-row"></div>');
					var p = jQuery('<p></p>').text(data.id + ', ' + data.latitude + ', ' + data.longitude + ', ' + data.name);
					var a = jQuery('<a href="#"></a>').text('Add').data('id', data.id).addClass('location-link');
					row.append(p)
					row.append(a);
					locationCon.append(row);
				});

				addLocation();
			})
			.fail(function() {
    			locationCon.append(jQuery('<p></p>').text('Ett fel inträffade'));
  			});

		e.preventDefault();
	});

	function addLocation() {
		jQuery('a.location-link').on('click', function(e) {
			locationInput.val(jQuery(this).data('id'));

			e.preventDefault();
		});
	}

	
});
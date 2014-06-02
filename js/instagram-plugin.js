;(function($) {

	var defaults = {
		get: false,
		tagName: false,
		userId: false,
		accessToken: false
	};

	var Instafeed = {

		$element: {},
		config: {},

		init: function(options, element) {
			Instafeed.config = $.extend({}, defaults, options);

			Instafeed.$element = $(element);

			this.hasError = false;

			var self = this;

			// Option variables
			this.groundUrl = 'https://api.instagram.com/v1/';
			this.endpoint;

			this.buildEndpoint();

			this.url = this.groundUrl + this.endpoint;

			if (this.hasError) {
				console.log(this.errorMessage);
				return;
			} else {
				this.fetch().then(function(results) {
					self.map(results);
					self.template();
					self.render();
				}, function() {
					console.log('Failed');
				});
			}
		},

		buildEndpoint: function() {

			if (!this.config.get) {
				this.error('Attribute "get" is missing.');
				return;
			}

			if (this.config.get == 'hashtag') {
				if (!this.config.tagName) {
					this.error('Attribute "tagName" is missing.');
					return;
				}
	    		this.endpoint = 'tags/' + this.config.tagName + '/media/recent';
	    	} else if (this.config.get == 'user') {
				if (!this.config.userId) {
					this.error('Attribute "userId" is missing.');
					return;
				}
	    		this.endpoint = 'users/' + this.config.userId + '/media/recent';
	    	} else {
	    		this.error('Attribute "get" is invalid.');
				return;
	    	}
		},

		fetch: function() {

			return jQuery.ajax({
				url: this.url,
				data: {
					access_token: this.config.accessToken
				},
				dataType: 'jsonp'
			}).promise();
		},

		map: function(results) {
			this.medias = $.map(results.data, function(media) {
				return {
					image: media.images.standard_resolution.url,
					thumbnail: media.images.thumbnail.url,
					caption: (media.caption == null) ? '' : media.caption.text,
					created: media.created_time,
					link: media.link
				};
			});
		},

		template: function() {
			images = $('<div></div>').addClass('images');

			$.each(this.medias, function(index, media) {
				var image = $('<a></a>').attr('target', '_blank').attr('href', media.link);
				image.append($('<img>').attr('src', media.image));
				images.append(image);
			});

			this.imagesElement = images;
		},

		render: function() {
			this.$element.append(this.imagesElement);
		},

		error: function(message) {
			this.hasError = true;
			this.errorMessage = message;
		}
	};

	$.fn.instafeed = function(options) {
		var instafeed = Object.create(Instafeed);
		instafeed.init(options, this);

		return this;
	};

}(jQuery));

jQuery(document).ready(function() {
	jQuery('#instagram-user').instafeed({
		get: 'user',
		userId: w4options.userId,
		accessToken: '2894385.206f6a8.b3063bf9fc0143d8aacd3e490b82fbbc'
	});
});





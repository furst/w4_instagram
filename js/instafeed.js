jQuery(document).ready(function() {

	var Instafeed = {

		init: function() {
			var self = this;
			this.media = w4media;
			this.$element = jQuery('.w4-instagram');
			this.hashtags = Array();

			this.render();
			this.renderHashtags();

			jQuery('.hashtag').on('click', function(event) {
				var click = this;
				jQuery('.images').fadeOut(500, function() {
					var $images = jQuery(this);
					$images.empty();
					self.$element.append(jQuery('<div></div>').text('Laddar...').addClass('loader'));
					self.render(jQuery(click).text());
					jQuery(document).on('imagesLoaded', function() {
						jQuery('.loader').remove();
						$images.fadeIn(500);
					});
				});
				
				event.preventDefault();
			});
		},

		getHashtags: function(tagName) {
			if (this.hashtags.indexOf(tagName) == -1) {
				this.hashtags.push(tagName);
			}
		},

		render: function(hashtag) {
			var self = this;
			var images = jQuery('.images');
			if (images.length == 0) {
				var images = jQuery('<div></div>').addClass('images');
			}

			var hashtagClick = hashtag;

			jQuery.each(this.media, function(index, media) {

				self.getHashtags(media.tagName);

				if (hashtagClick === undefined) {
					hashtagClick = self.hashtags[0];
				}

				if (media.tagName == hashtagClick) {
					var template = w4template.template;

					var con;

					if (template == 1) {
						con = self.standardTemplate(media);
					} else if(template == 2) {
						con = self.smallTemplate(media);
					} else {
						con = self.standardTemplate(media);
					}

					images.append(con);
				}
			});

			this.waitForImages();

			this.$element.append(images);
		},

		waitForImages: function() {
			var image = jQuery('.image');
			var imageCount = image.length;
			count = 1;

			image.load(function() {
				if (imageCount == count) {
					jQuery.event.trigger('imagesLoaded');
				}
				count++;
			});
		},

		renderHashtags: function() {
			var self = this;

			jQuery.each(this.hashtags, function(index, hashtag) {
				var a = jQuery('<a></a>').attr('href', '#').text(hashtag).addClass('hashtag');
				self.$element.prepend(a);
			});
		},

		standardTemplate: function(media) {
			var a = jQuery('<a></a>').attr('href', media.link).attr('target', '_blank');
			var img = jQuery('<img>').attr('src', media.image).addClass('image');
			a.append(img);
			return a;
		},

		smallTemplate: function(media) {
			var a = jQuery('<a></a>').attr('href', media.link).attr('target', '_blank');
			var img = jQuery('<img>').attr('src', media.thumbnail).addClass('image');
			a.append(img);
			return a;
		}
	}

	Instafeed.init();
});





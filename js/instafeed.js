jQuery(document).ready(function() {

	var Instafeed = {

		init: function() {
			var self = this;
			this.media = w4media;
			this.$element = jQuery('.w4-instagram');
			this.hashtags = Array();

			this.render();

			if (this.media[0].sorting == 'hashtag') {
				this.renderHashtags();
			}

			// Trigga event när alla bilder är laddade
			this.waitForImages();
			
			// Vid klick på hashtag-länk, animera och hämta nya bilder
			jQuery('.hashtag').on('click', function(event) {
				var click = this;
				jQuery('.images').fadeOut(500, function() {
					var $images = jQuery(this);
					$images.empty();
					self.$element.append(jQuery('<div></div>').text('Laddar...').addClass('loader'));
					self.render(jQuery(click).text());
					jQuery(document).on('w4ImagesLoaded', function() {
						jQuery('.loader').remove();
						$images.fadeIn(500);
					});
				});
				
				event.preventDefault();
			});
		},

		// Hämta hashtags och lista
		getHashtags: function(tagName) {
			if (this.hashtags.indexOf(tagName) == -1) {
				this.hashtags.push(tagName);
			}
		},

		// Rendera media
		render: function(hashtag) {
			var self = this;
			var images = jQuery('.images');
			var template = w4template.template;
			if (images.length == 0) {
				var images = jQuery('<div></div>').addClass('images');
			}

			var hashtagClick = hashtag;

			// För varje bild, hämta korrekt template och rendera den.
			// Vid hashtag, filtrera beroende på taggnamn
			jQuery.each(this.media, function(index, media) {

				self.getHashtags(media.tagName);

				if (hashtagClick === undefined) {
					hashtagClick = self.hashtags[0];
				}

				if (media.tagName == hashtagClick) {
					
					var con;

					if (template == 2) {
						con = self.smallTemplate(media);
					} else if(template == 3) {
						con = self.customTemplate(media);
					} else {
						con = self.standardTemplate(media);
					}

					images.append(con);
				}
			});

			// Trigga event när alla bilder är laddade
			this.waitForImages();

			this.$element.append(images);
		},

		// Trigga event när alla bilder är laddade
		waitForImages: function() {
			var image = jQuery('.images').find('img');
			var imageCount = image.length;
			count = 1;

			image.load(function() {
				if (imageCount == count) {
					jQuery.event.trigger('w4ImagesLoaded');
				}
				count++;
			});
		},

		// Rendera hashtagslista, taggarna är hämtade och filtrerade ur objektet med alla bilder
		renderHashtags: function() {
			var self = this;

			jQuery.each(this.hashtags, function(index, hashtag) {
				var li = jQuery('<li></li>');
				var a = jQuery('<a></a>').attr('href', '#').text(hashtag).addClass('hashtag');
				li.append(a);
				self.$element.prepend(li);
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
		},

		customTemplate: function(media) {
			var template = w4template.custom;

			// Filtrera beroende på tagg
			template = template
				.replace(/\{\{image\}\}/, media.image)
				.replace(/\{\{thumbnail\}\}/, media.thumbnail)
				.replace(/\{\{href\}\}/, media.link)
				.replace(/\{\{caption\}\}/, media.caption)
				.replace(/\{\{username\}\}/, media.username)
				.replace(/\{\{created\}\}/, media.created);

			html = jQuery.parseHTML(template);

			return html;
		}
	};

	Instafeed.init();
});





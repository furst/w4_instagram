jQuery(document).ready(function() {

	var firstHashtag = jQuery('.hashtag').first().text();

	var feed = new Instafeed({
        get: 'tagged',
        tagName: 'test',
        accessToken: accessToken,
        target: 'instagram'
    });
    feed.run();

    // Jquery ett krav?
    jQuery('a.hashtag').on('click', function(e) {

    	var instagram = jQuery('#instagram');
    	
    	feed = new Instafeed({
	        get: 'tagged',
	        tagName: jQuery(this).text(),
	        accessToken: accessToken,
	        target: 'instagram',
	        before: function() {
	        	instagram.empty();
    			instagram.append('<span class="loading">Loading...</span>');
	        },
	        after: function() {
	        	jQuery('.loading').remove();
	        }
	    });
	    feed.run();

    	e.preventDefault();
    });
});













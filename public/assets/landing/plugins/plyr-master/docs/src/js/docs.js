// ==========================================================================
// Docs example
// ==========================================================================

/*global plyr*/

// Setup the player
plyr.setup('.js-media-player', {
	debug: 				true,
	title: 				'Video demo',
	iconUrl: 			'../dist/plyr.svg',
	tooltips: 	{
		controls: 		true
	},
	captions: {
		defaultActive: 	true
	}
});
plyr.loadSprite('dist/docs.svg');

// General functions
(function() {
	var buttons = document.querySelectorAll('[data-source]'),
		types = {
			video: 		'video',
			audio: 		'audio',
			youtube: 	'youtube',
			vimeo: 		'vimeo'
		},
		currentType = window.location.hash.replace('#', ''),
		historySupport = (window.history && window.history.pushState);

	// Bind to each button
	for (var i = buttons.length - 1; i >= 0; i--) {
		buttons[i].addEventListener('click', function() {
			var type = this.getAttribute('data-source');

			newSource(type);

			if (historySupport) {
				history.pushState({ 'type': type }, '', '#' + type);
			}
		});
	}

	// List for backwards/forwards
	window.addEventListener('popstate', function(event) {
		if(event.state && 'type' in event.state) {
			newSource(event.state.type);
		}
	});

	// On load
	if(historySupport) {
		var video = !currentType.length;

		// If there's no current type set, assume video
		if(video) {
			currentType = types.video;
		}

		// Replace current history state
		if(currentType in types) {
			history.replaceState({ 'type': currentType }, '', (video ? '' : '#' + currentType));
		}

		// If it's not video, load the source
		if(currentType !== types.video) {
			newSource(currentType, true);
		}
	}

	// Toggle class on an element
	function toggleClass(element, className, state) {
        if (element) {
            if (element.classList) {
                element.classList[state ? 'add' : 'remove'](className);
            }
            else {
                var name = (' ' + element.className + ' ').replace(/\s+/g, ' ').replace(' ' + className + ' ', '');
                element.className = name + (state ? ' ' + className : '');
            }
        }
    }

	// Set a new source
	function newSource(type, init) {
		// Bail if new type isn't known, it's the current type, or current type is empty (video is default) and new type is video
		if(!(type in types) || (!init && type == currentType) || (!currentType.length && type == types.video)) {
			return;
		}

		// Get plyr instance
		var player = document.querySelector('.js-media-player').plyr;

		switch(type) {
			case types.video:
				player.source({
					type:       'video',
					title: 		'View From A Blue Moon',
					sources: [{
						src:    'https://cdn.selz.com/plyr/1.5/View_From_A_Blue_Moon_Trailer-HD.mp4',
						type:   'video/mp4'
					},
					{
						src:    'https://cdn.selz.com/plyr/1.5/View_From_A_Blue_Moon_Trailer-HD.webm',
						type:   'video/webm'
					}],
					poster:     'https://cdn.selz.com/plyr/1.5/View_From_A_Blue_Moon_Trailer-HD.jpg',
					tracks:     [{
						kind:   'captions',
						label:  'English',
						srclang:'en',
						src:    'https://cdn.selz.com/plyr/1.5/View_From_A_Blue_Moon_Trailer-HD.en.vtt',
						default: true
					}]
				});
				break;

			case types.audio:
				player.source({
					type:       'audio',
					title: 		'Kishi Bashi &ndash; &ldquo;It All Began With A Burst&rdquo;',
					sources: [{
						src:    'https://cdn.selz.com/plyr/1.5/Kishi_Bashi_-_It_All_Began_With_a_Burst.mp3',
						type:   'audio/mp3'
					},
					{
						src:    'https://cdn.selz.com/plyr/1.5/Kishi_Bashi_-_It_All_Began_With_a_Burst.ogg',
						type:   'audio/ogg'
					}]
				});
				break;

			case types.youtube:
				player.source({
					type:       'video',
					title: 		'View From A Blue Moon',
					sources: [{
				        src:    'bTqVqk7FSmY',
				        type:   'youtube'
				    }]
				});
				break;

			case types.vimeo:
				player.source({
					type:       'video',
					title: 		'View From A Blue Moon',
					sources: [{
				        src:    '143418951',
				        type:   'vimeo'
				    }]
				});
				break;
		}

		// Set the current type for next time
		currentType = type;

		// Remove active classes
		for (var x = buttons.length - 1; x >= 0; x--) {
			toggleClass(buttons[x].parentElement, 'active', false);
		}

		// Set active on parent
		toggleClass(document.querySelector('[data-source="'+ type +'"]').parentElement, 'active', true);
	}
})();

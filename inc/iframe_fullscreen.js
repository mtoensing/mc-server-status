/* eslint-disable no-alert, no-unused-vars, no-undef */
function handleFullscreen( id ) {
	const userAgent = window.navigator.userAgent;
	if ( supportsFullscreenAPI() ) {
		// Use the Fullscreen API if supported
		goFullscreen( id );
	} else {
		// Fallback or notification if Fullscreen API is not supported
		window.open( document.getElementById( id ).src, '_blank' );
	}
}

function goFullscreen( id ) {
	const element = document.getElementById( id );
	// Attempt to request fullscreen mode
	if ( element.requestFullscreen ) {
		element.requestFullscreen();
	} else if ( element.webkitRequestFullscreen ) {
		// Safari
		element.webkitRequestFullscreen();
	} else if ( element.mozRequestFullScreen ) {
		// Firefox
		element.mozRequestFullScreen();
	} else if ( element.msRequestFullscreen ) {
		// IE
		element.msRequestFullscreen();
	}
}

// Helper function to check for Fullscreen API support
function supportsFullscreenAPI() {
	return (
		document.fullscreenEnabled ||
		document.webkitFullscreenEnabled ||
		document.mozFullScreenEnabled ||
		document.msFullscreenEnabled
	);
}

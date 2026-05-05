/* eslint-disable no-alert, no-unused-vars, no-undef */
function copyToClipboard( text ) {
	if ( navigator.clipboard && navigator.clipboard.writeText ) {
		navigator.clipboard.writeText( text ).then(
			function () {
				alert( 'Copied to clipboard: ' + text );
			},
			function () {
				copyToClipboardFallback( text );
			}
		);
		return;
	}

	copyToClipboardFallback( text );
}

function copyToClipboardFallback( text ) {
	// Create a temporary input element
	const tempInput = document.createElement( 'input' );
	tempInput.style = 'position: absolute; left: -1000px; top: -1000px';
	tempInput.value = text;
	document.body.appendChild( tempInput );
	tempInput.select();
	document.execCommand( 'copy' );
	document.body.removeChild( tempInput );

	// Optionally, display a message to the user indicating the text was copied
	alert( 'Copied to clipboard: ' + text );
}

document.addEventListener( 'DOMContentLoaded', function () {
	document.addEventListener( 'click', function ( event ) {
		if ( ! event.target.classList.contains( 'mcsi-copy-address' ) ) {
			return;
		}

		event.preventDefault();

		const address = event.target.getAttribute( 'data-address' );
		if ( ! address ) {
			return;
		}

		const port = event.target.getAttribute( 'data-port' );
		copyToClipboard( port ? address + ':' + port : address );
	} );
} );

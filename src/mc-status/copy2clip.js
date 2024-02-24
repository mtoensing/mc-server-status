/* eslint-disable no-alert, no-unused-vars, no-undef */
function copyToClipboard( text ) {
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

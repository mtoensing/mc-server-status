import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import {
	BlockControls,
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { TextControl, Panel, PanelBody, PanelRow } from '@wordpress/components';
import './editor.scss';

export default function Edit( { attributes, setAttributes } ) {
	const controls = <BlockControls group="block"></BlockControls>;

	const controlssidebar = (
		<InspectorControls>
			<Panel>
				<PanelBody>
					<PanelRow>
						<TextControl
							label={ __( 'Address', 'mc-server-status' ) }
							help={ __(
								'The Minecraft server address, excluding the protocol (e.g., "http" or "https").',
								'mc-server-status'
							) }
							value={ attributes.address }
							onChange={ ( value ) => {
								// Function to remove http:// or https:// from the start of the URL
								const removeHttp = ( url ) => {
									// Regular expression to match http:// or https:// at the start of the string
									const regex = /^(http:\/\/|https:\/\/)/;
									return url.replace( regex, '' ); // Replace with empty string if found
								};

								// Use the removeHttp function to preprocess the value
								const processedValue = removeHttp( value );

								// Save the processed value
								setAttributes( {
									address: processedValue,
								} );
							} }
						/>
					</PanelRow>
					<PanelRow>
						<TextControl
							label={ __( 'Port', 'mc-server-status' ) }
							help={ __(
								'Port of the Minecraft Server',
								'mc-server-status'
							) }
							value={ attributes.port }
							onChange={ ( value ) =>
								setAttributes( {
									port: value,
								} )
							}
						/>
					</PanelRow>
					<PanelRow>
						<TextControl
							label={ __( 'Dynmap URL', 'mc-server-status' ) }
							help={ __(
								'The url of a Dynmap with http(s)',
								'mc-server-status'
							) }
							value={ attributes.dynurl }
							onChange={ ( value ) =>
								setAttributes( {
									dynurl: value,
								} )
							}
						/>
					</PanelRow>
				</PanelBody>
			</Panel>
		</InspectorControls>
	);

	return (
		<p { ...useBlockProps() }>
			{ controls }
			{ controlssidebar }
			<ServerSideRender
				block="mc-server-status/mc-status"
				attributes={ attributes }
			/>
		</p>
	);
}

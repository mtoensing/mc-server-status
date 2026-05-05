import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import {
	BlockControls,
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	Button,
	Panel,
	PanelBody,
	PanelRow,
	TextareaControl,
	TextControl,
} from '@wordpress/components';
import './editor.scss';

export default function Edit( { attributes, setAttributes } ) {
	const controls = <BlockControls group="block"></BlockControls>;

	const controlssidebar = (
		<InspectorControls>
			<Panel>
				<PanelBody title={ __( 'Basic Settings', 'mc-server-status' ) }>
					<PanelRow>
						<TextControl
							label={ __( 'Server Name', 'mc-server-status' ) }
							help={ __(
								'Custom display name for the server.',
								'mc-server-status'
							) }
							value={ attributes.serverName }
							onChange={ ( value ) =>
								setAttributes( {
									serverName: value,
								} )
							}
						/>
					</PanelRow>
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
				</PanelBody>
				<PanelBody
					title={ __( 'Server Details', 'mc-server-status' ) }
					initialOpen={ false }
				>
					<PanelRow>
						<TextareaControl
							label={ __( 'Description', 'mc-server-status' ) }
							help={ __(
								'Optional description shown in the server table.',
								'mc-server-status'
							) }
							value={ attributes.serverDescription }
							onChange={ ( value ) =>
								setAttributes( {
									serverDescription: value,
								} )
							}
							rows={ 4 }
						/>
					</PanelRow>
					<PanelRow>
						<div style={ { width: '100%' } }>
							<MediaUploadCheck>
								<MediaUpload
									onSelect={ ( media ) =>
										setAttributes( {
											serverIcon: media.url,
										} )
									}
									allowedTypes={ [ 'image' ] }
									value={ attributes.serverIcon }
									render={ ( { open } ) => (
										<div>
											<Button
												onClick={ open }
												variant="secondary"
											>
												{ attributes.serverIcon
													? __(
															'Change Server Icon',
															'mc-server-status'
													  )
													: __(
															'Upload Server Icon',
															'mc-server-status'
													  ) }
											</Button>
											{ attributes.serverIcon && (
												<div
													style={ {
														marginTop: '10px',
													} }
												>
													<img
														src={
															attributes.serverIcon
														}
														alt={ __(
															'Server Icon',
															'mc-server-status'
														) }
														style={ {
															display: 'block',
															height: 'auto',
															maxWidth: '64px',
														} }
													/>
													<Button
														onClick={ () =>
															setAttributes( {
																serverIcon: '',
															} )
														}
														variant="link"
														isDestructive
														style={ {
															marginTop: '5px',
														} }
													>
														{ __(
															'Remove',
															'mc-server-status'
														) }
													</Button>
												</div>
											) }
										</div>
									) }
								/>
							</MediaUploadCheck>
						</div>
					</PanelRow>
				</PanelBody>
				<PanelBody
					title={ __( 'Additional Links', 'mc-server-status' ) }
					initialOpen={ false }
				>
					<PanelRow>
						<TextControl
							label={ __(
								'Modpack Download URL',
								'mc-server-status'
							) }
							help={ __(
								'Optional URL for downloading a modpack.',
								'mc-server-status'
							) }
							value={ attributes.modpackUrl }
							onChange={ ( value ) =>
								setAttributes( {
									modpackUrl: value,
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

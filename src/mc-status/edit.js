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
							label={ __( 'Address', 'minecraft-server-info' ) }
							help={ __(
								'The Minecraft Server address without https.',
								'minecraft-server-info'
							) }
							value={ attributes.address }
							onChange={ ( value ) =>
								setAttributes( {
									address: value,
								} )
							}
						/>
					</PanelRow>
					<PanelRow>
						<TextControl
							label={ __( 'Port', 'minecraft-server-info' ) }
							help={ __( 'Port', 'minecraft-server-info' ) }
							value={ attributes.port }
							onChange={ ( value ) =>
								setAttributes( {
									port: value,
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
				block="minecraft-server-info/mc-status"
				attributes={ attributes }
			/>
		</p>
	);
}

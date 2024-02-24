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
							label={ __(
								'Address',
								'minecraft-server-info-block'
							) }
							help={ __(
								'The Minecraft Server address without https.',
								'minecraft-server-info-block'
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
							label={ __(
								'Port',
								'minecraft-server-info-block'
							) }
							help={ __(
								'Port of the Minecraft Server',
								'minecraft-server-info-block'
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
							label={ __(
								'Dynmap URL',
								'minecraft-server-info-block'
							) }
							help={ __(
								'The url of a Dynmap with http(s)',
								'minecraft-server-info-block'
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
				block="minecraft-server-info-block/mc-status"
				attributes={ attributes }
			/>
		</p>
	);
}

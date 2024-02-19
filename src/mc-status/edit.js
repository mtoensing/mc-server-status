import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';

export default function Edit( attributes ) {
	return (
		<p { ...useBlockProps() }>
			<ServerSideRender
				block="minecraft-server-info/mc-status"
				attributes={ attributes }
			/>
		</p>
	);
}

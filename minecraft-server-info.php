<?php
/**
 * Plugin Name:       Minecraft Server Info
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       minecraft-server-info
 *
 * @package           minecraft-server-info
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function minecraft_server_info_init() {
	register_block_type( __DIR__ . '/build',[
		'render_callback' => __NAMESPACE__ . '\\render_callback_msi'
	]);
}

/**
 * Renders a Table of Contents block for a post
 * @param array $attributes An array of attributes for the Table of Contents block
 * @return string The HTML output for the Table of Contents block
 */
function render_callback_msi($attributes){
	return  "<p>Hello from PHP</p>";
}

add_action( 'init', 'minecraft_server_info_init' );

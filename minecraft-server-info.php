<?php
/**
 * Plugin Name:       Minecraft Server Info
 * Description:       Show information about a Minecraft Server as a block.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Marc Tönsing
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       minecraft-server-info
 *
 * @package           minecraft-server-info
 */
 require __DIR__ . '/inc/MinecraftData.php';
 require dirname(__FILE__). '/src/mc-players/callback.php';
 require dirname(__FILE__). '/src/mc-status/callback.php';


if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function minecraft_server_info_init()
{

    register_block_type(plugin_dir_path(__FILE__) . 'build/mc-status', [
        'render_callback' => 'render_status'
    ]);

    /*
    register_block_type(plugin_dir_path(__FILE__) . 'build/mc-players', [
        'render_callback' => 'render_players'
    ]);
    */

}
add_action('init', 'minecraft_server_info_init');


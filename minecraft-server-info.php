<?php
/**
 * Plugin Name:       Minecraft Server Info
 * Description:       Show information about a Minecraft Server as a block.
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

 require __DIR__ . '/inc/MinecraftData.php';

 use MSI\MinecraftData;


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
    register_block_type(__DIR__ . '/build', [
        'render_callback' => __NAMESPACE__ . '\\render_callback_msi'
    ]);
}
add_action('init', 'minecraft_server_info_init');

/**
 * Renders a Table of Contents block for a post
 * @param array $attributes An array of attributes for the Table of Contents block
 * @return string The HTML output for the Table of Contents block
 */
function render_callback_msi($attributes)
{
    $hostname = 'mc.marc.tv';

	$html = retrieveData($hostname, false);

    return  $html;
}


function retrieveData($hostname, $pingonly = true, $port = 25565) {
    $data = new MSI\MinecraftData($hostname, $port, $pingonly);
    
    // Check the server status and prepare a message
    $serverStatus = $data->IsOnline ? 'Server is online' : 'Server is offline';
    
    // Prepare output with HTML formatting for clarity
    $output = "<strong>Server Status:</strong> {$serverStatus}<br />";
    $output .= "<strong>MOTD:</strong> {$data->Motd}<br />";
    $output .= "<strong>Max Players:</strong> {$data->PlayersMax}<br />";
    $output .= "<strong>Players Online:</strong> {$data->PlayersOnline}<br />";
    if ($data->IsOnline) {
        $output .= "<strong>Server Version:</strong> {$data->ServerVersion}<br />";
        // Optionally, list players if any are online and if you've stored them
        
    }
    
    return $output;
}

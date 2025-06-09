<?php
/**
 * Plugin Name:       MC Server Status
 * Plugin URI:        https://toensing.com
 * Description:       Show information about a Minecraft Server in a block.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           1.5.0
 * Author:            Marc Tönsing
 * Author URI: 		  https://toensing.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mc-server-status
 *
 * @package           mc-server-status
 */

if (! defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

require __DIR__ . '/inc/MinecraftData.php';

/**
 * Retrieves Minecraft server data and updates cached information.
 */
function mcsi_retrieveData($hostname, $attributes, $port = 25565)
{
    // Sanitize the hostname and port
    $hostname = sanitize_text_field($hostname);
    $port = intval($port);

    // Generate the unique cache key for this server
    $serverDataKey = mcsi_get_server_cache_key($hostname, $port, 'server_data_');
    $playerDataKey = mcsi_get_server_cache_key($hostname, $port, 'player_data_');

    $data = new MCSI\MinecraftData($hostname, $port);
    $isOnline = $data->IsOnline ?? false;

    if ($isOnline) {
        // Update and cache server and player data using the salted keys
        $serverData = [
            'IsOnline' => $isOnline,
            'Motd' => $data->Motd ?? 'N/A',
            'ServerVersion' => $data->ServerVersion ?? 'N/A',
            'PlayersMax' => $data->PlayersMax ?? 0,
            'PlayersOnline' => $data->PlayersOnline ?? 0,
            'timestamp' => time()
        ];
        update_option($serverDataKey, serialize($serverData));
        mcsi_updatePlayerData($data->Players ?? [], $playerDataKey);
    } else {
        // Retrieve cached server data if available
        $serverData = unserialize(get_option($serverDataKey, '')) ?: [
            'IsOnline' => false,
            'Motd' => 'N/A',
            'ServerVersion' => 'N/A',
            'PlayersMax' => 0,
            'PlayersOnline' => 0,
        ];
    }

    // Add 'IsOnline' status dynamically to the serverData array
    $serverData['IsOnline'] = $isOnline;

    return mcsi_renderServerData($serverData, $isOnline ? $data->Players : [], $hostname, $port, $attributes);

}

/**
 * Updates or initializes the player data including their last seen time.
 */
function mcsi_updatePlayerData($currentPlayers, $playerDataKey)
{
    $savedPlayers = get_option($playerDataKey, []);
    if (!is_array($savedPlayers)) {
        $savedPlayers = [];
    }

    $currentPlayerIds = array_column($currentPlayers, 'id');
    $now = time();

    foreach ($currentPlayers as $player) {
        $savedPlayers[$player['id']] = [
            'name' => $player['name'],
            'lastSeen' => $now,
        ];
    }

    foreach ($savedPlayers as $id => $player) {
        if (!in_array($id, $currentPlayerIds) && (!isset($player['lastSeen']) || $player['lastSeen'] == $now)) {
            $savedPlayers[$id]['lastSeen'] = $now;
        }
    }

    update_option($playerDataKey, $savedPlayers);
}

/**
 * Renders the server data including player information.
 */
function mcsi_renderServerData($serverData, $currentPlayers, $hostname, $port, $attributes)
{

    enqueue_msib_frontend();

    $wpTimezone = wp_timezone();

    $dynurl = '';
    $dynurl_domain = '';
    if(isset($attributes['dynurl'])) {
        $dynurl = esc_url($attributes['dynurl']);
        $parsedUrl = wp_parse_url($dynurl);
        $dynurl_domain = $parsedUrl['host'];
    }


    $align_class = isset($attributes['align']) ? 'align' . $attributes['align'] : '';

    // Generate the unique cache key for player data of this server
    $playerDataKey = mcsi_get_server_cache_key($hostname, $port, 'player_data_');

    // Use the unique key to retrieve player data for the current server
    $savedPlayers = get_option($playerDataKey, []);
    if (!is_array($savedPlayers)) {
        $savedPlayers = [];
    }

    // Count the total number of unique players ever seen
    $totalPlayersEverSeen = count($savedPlayers);

    // Determine currently online players
    $onlinePlayerIds = array_column($currentPlayers, 'id');
    $currentOnlineCount = count($onlinePlayerIds);

    // Separate players into online and offline for sorting and counting
    $onlinePlayers = [];
    $offlinePlayers = [];
    foreach ($savedPlayers as $id => $player) {
        if (in_array($id, $onlinePlayerIds)) {
            $onlinePlayers[$id] = $player;
        } else {
            $offlinePlayers[$id] = $player;
        }
    }

    // Sort offline players by "last seen" in descending order
    uasort($offlinePlayers, function ($a, $b) {
        return $b['lastSeen'] <=> $a['lastSeen'];
    });

    // Server metadata output
    $output = "<figure class='wp-block-table is-style-stripes ". $align_class . "'><table class='minecraftserverinfo " . ($serverData['IsOnline'] ? "isonline" : "") . "'>";
    $output .= "<tr><td><strong>" . __('Status', 'mc-server-status') . "</strong></td><td class='status'>" . ($serverData['IsOnline'] ? 'Online' : 'Offline') . "</td></tr>";
    $output .= "<tr><td><strong>" . __('Address', 'mc-server-status') . "</strong></td><td>" . $hostname . " <small><a style='cursor: pointer' onclick='copyToClipboard(\"" . $hostname . "\")' >" . __('Copy', 'mc-server-status') . "</a></small></td></tr>";
    $output .= "<tr><td><strong>" . __('MOTD', 'mc-server-status') . "</strong></td><td>{$serverData['Motd']}</td></tr>";
    $output .= "<tr><td><strong>" . __('Version', 'mc-server-status') . "</strong></td><td>{$serverData['ServerVersion']}</td></tr>";

    if ($dynurl != '') {
        $output .= "<tr><td colspan='2'><iframe height='250' width='100%' src='{$dynurl}' id='dynmap'></iframe>";        
        $output .= "<p><div class='wp-block-button has-custom-font-size has-small-font-size'><a onclick='handleFullscreen(\"dynmap\");' class='wp-block-button__link wp-element-button'>" . __('Show map in fullscreen', 'mc-server-status') . "</a></div></p>";
    }
    
    $output .= "</table></figure>";

    if(count($offlinePlayers) > 0) {
        $output .= "<figure class='wp-block-table is-style-stripes ". $align_class . "'><table class='minecraftserverinfo " . ($serverData['IsOnline'] ? "isonline" : "") . "'>";
        // Player table with dynamic online count and total players ever seen
        $output .= "<tr class='playerhead'><th><strong>" . __('Players', 'mc-server-status') . "</span><span class='text-muted'> ($currentOnlineCount/$totalPlayersEverSeen)</span></th><th>" . __('Last seen', 'mc-server-status') . "</th></tr>";

        // List online players
        foreach ($onlinePlayers as $id => $player) {
            $output .= mcsi_formatPlayerRow($id, $player, true, $wpTimezone);
        }
        // List offline players (sorted by last seen)
        foreach ($offlinePlayers as $id => $player) {
            $output .= mcsi_formatPlayerRow($id, $player, false, $wpTimezone);
        }

        $output .= "</table></figure>";
    }

    return $output;
}



/**
 * Helper function to format a player row.
 */
/**
 * Helper function to format a player row.
 */
function mcsi_formatPlayerRow($id, $player, $isOnline, $wpTimezone)
{
    // Sanitize the ID since it's used in the URL
    $id = sanitize_key($id); // Assuming $id is a string/alphanumeric key
    $avatarURL = esc_url("https://mc-heads.net/avatar/{$id}");
    $avatarURL = esc_url("https://mc-heads.net/avatar/{$id}");
    $playerName = esc_html($player['name']);

    // Ensure you have the current timestamp in the WordPress-configured timezone
    $current_time = new DateTime("now", $wpTimezone);

    // Assuming $player['lastSeen'] is a Unix timestamp, convert it to a DateTime object in the WordPress-configured timezone
    $last_seen_time = new DateTime("@{$player['lastSeen']}");
    $last_seen_time->setTimezone($wpTimezone);

    // Calculate the human-readable time difference
    $last_seen_diff = human_time_diff($last_seen_time->getTimestamp(), $current_time->getTimestamp());

    // Prepare the display format
    $lastSeenFormat = $isOnline ? "<span class='playeronline'>Online</span>" : sprintf(
        /* translators: %s: human-readable time difference */
        __('%s ago', 'mc-server-status'),
        $last_seen_diff
    );

    // Modify the row class based on the online status
    $rowClass = $isOnline ? " class='player'" : " class='player text-muted'";

    $row = "<tr{$rowClass}>";
    $row .= "<td><img src='{$avatarURL}' alt='{$playerName}s Avatar' width='18' height='18'> {$playerName} </td>";
    $row .= "<td>{$lastSeenFormat}</td>";
    $row .= "</tr>";

    return $row;
}


/**
 * Renders a Table of Contents block for a post.
 */
function mcsi_render_status($attributes)
{
    $address = $attributes['address'] ?? '';
    $port = $attributes['port'] ?? '25565';
    $html = mcsi_retrieveData($address, $attributes, $port);
    return $html;
}

function mcsi_get_server_cache_key($hostname, $port, $prefix = '')
{
    $sanitizedHostname = preg_replace('#^https?://#', '', rtrim($hostname, '/'));
    $cacheKey = $prefix . 'minecraft_data_' . md5($sanitizedHostname . '_' . $port);
    return $cacheKey;
}

function enqueue_msib_frontend()
{
    wp_enqueue_script(
        'msib-script',
        plugin_dir_url(__FILE__) . 'inc/copy2clip.js',
        array(),
        '1.3.0',
        true
    );

    wp_enqueue_script(
        'fullscreen-script',
         plugin_dir_url(__FILE__) . 'inc/iframe_fullscreen.js', 
         array(), '1.3.0',
          true
    );

}


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
        'render_callback' => 'mcsi_render_status'
    ]);

    /*
    register_block_type(plugin_dir_path(__FILE__) . 'build/mc-players', [
        'render_callback' => 'render_players'
    ]);
    */

}
add_action('init', 'minecraft_server_info_init');

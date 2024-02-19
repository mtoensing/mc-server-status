<?php 


// Assuming the MSI namespace and MinecraftData class are defined elsewhere as provided earlier

/**
 * Initializes scheduled events for updating Minecraft server data.
 */
function setup_minecraft_cron_job() {
    if (!wp_next_scheduled('update_minecraft_data')) {
        wp_schedule_event(time(), 'hourly', 'update_minecraft_data');
    }
}

add_action('init', 'setup_minecraft_cron_job');

/**
 * Handles the scheduled event to update Minecraft server data.
 */
function update_minecraft_server_data() {
    retrieveData('mc.marc.tv', 25565); // Replace with your server details
}

add_action('update_minecraft_data', 'update_minecraft_server_data');

/**
 * Retrieves Minecraft server data and updates cached information.
 */
function retrieveData($hostname, $port = 25565) {
    $data = new MSI\MinecraftData($hostname, $port);
    
    // Fetch the server status in real-time
    $isOnline = $data->IsOnline ?? false;
    
    // Always fetch the latest server data for the online check
    $serverData = unserialize(get_option('minecraft_server_data', '')) ?: [
        'Motd' => 'N/A',
        'ServerVersion' => 'N/A',
        'PlayersMax' => 0,
        'PlayersOnline' => 0,
    ];
    
    // Update the online status in real-time
    $serverData['IsOnline'] = $isOnline;
    
    if ($isOnline) {
        // Update the rest of the server data if online
        $serverData['Motd'] = $data->Motd ?? 'N/A';
        $serverData['ServerVersion'] = $data->ServerVersion ?? 'N/A';
        $serverData['PlayersMax'] = $data->PlayersMax ?? 0;
        $serverData['PlayersOnline'] = $data->PlayersOnline ?? 0;
        $serverData['timestamp'] = time();
        
        // Update player data with current online status
        updatePlayerData($data->Players ?? []);
    }
    
    // Save the updated server data except for the online status
    update_option('minecraft_server_data', serialize($serverData));
    
    return renderServerData($serverData, $data->Players ?? [], $hostname);
}




/**
 * Updates or initializes the player data including their last seen time.
 */
function updatePlayerData($currentPlayers) {
    $savedPlayers = get_option('minecraft_player_data', []);
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

    update_option('minecraft_player_data', $savedPlayers);
}

/**
 * Renders the server data including player information.
 */
function renderServerData($serverData, $currentPlayers, $hostname) {
    // Set WordPress timezone to match the site's settings
    $wpTimezone = wp_timezone();

    // Retrieve and prepare player data
    $savedPlayers = get_option('minecraft_player_data', []);
    if (!is_array($savedPlayers)) {
        $savedPlayers = [];
    }

    // Determine currently online players and update the max players ever seen if necessary
    $onlinePlayerIds = array_column($currentPlayers, 'id');
    $currentOnlineCount = count($onlinePlayerIds);
    $maxPlayersEverSeen = max($currentOnlineCount, get_option('minecraft_max_players_ever_seen', 0));
    update_option('minecraft_max_players_ever_seen', $maxPlayersEverSeen);

    // Separate players into online and offline for sorting
    $onlinePlayers = [];
    $offlinePlayers = [];
    foreach ($savedPlayers as $id => $player) {
        if (in_array($id, $onlinePlayerIds)) {
            $onlinePlayers[$id] = $player;
        } else {
            $offlinePlayers[$id] = $player;
        }
    }

    // Sort offline players by "last seen" in ascending order
    uasort($offlinePlayers, function($a, $b) {
        return $a['lastSeen'] <=> $b['lastSeen'];
    });

    // Server metadata output
    $output = "<figure class='wp-block-table is-style-stripes'><table class='wp-block-table minecraftserverinfo " . ($serverData['IsOnline'] ? "isonline" : "") . "'>";
    $output .= "<tr><td><strong>Server Status:</strong></td><td class='status'>" . ($serverData['IsOnline'] ? 'Online' : 'Offline') . "</td></tr>";
    $output .= "<tr><td><strong>Server Address:</strong></td><td>". $hostname . "</td></tr>";
    $output .= "<tr><td><strong>MOTD:</strong></td><td>{$serverData['Motd']}</td></tr>";
    $output .= "<tr><td><strong>Server Version:</strong></td><td>{$serverData['ServerVersion']}</td></tr>";
    // Dynamically display the current and maximum players
    $output .= "</table></figure>";

    // Player table with dynamic online count
    $output .= "<figure class='wp-block-table is-style-stripes'><table class='minecraftserverinfo'>";
    $output .= "<thead><tr><th colspan='2'><strong>Players <span class='text-muted'>($currentOnlineCount/$maxPlayersEverSeen online)</span></strong></th></tr></thead>";

    // First, list online players
    foreach ($onlinePlayers as $id => $player) {
        $output .= formatPlayerRow($id, $player, true, $wpTimezone);
    }
// Then, list offline players (now sorted and correctly handled)
foreach ($offlinePlayers as $id => $player) {
    // Ensure we're passing the correct structure and values to formatPlayerRow
    $output .= formatPlayerRow($id, $player, false, $wpTimezone);
}

    $output .= "</table></figure>";
    return $output;
}

/**
 * Helper function to format a player row.
 */
function formatPlayerRow($id, $player, $isOnline, $wpTimezone) {
    $avatarURL = "https://mc-heads.net/avatar/{$id}";
    $playerName = $player['name'];
    $lastSeenFormat = $isOnline ? "<span class='playeronline'>Online</span>" : "Last Seen: " . (new DateTime('@' . $player['lastSeen']))->setTimezone($wpTimezone)->format("Y-m-d H:i:s");

    $row = "<tr>";
    $row .= "<td><img src='{$avatarURL}' alt='{$playerName}'s Avatar' width='18' height='18'> {$playerName}</td>";
    $row .= "<td>{$lastSeenFormat}</td>";
    $row .= "</tr>";

    return $row;
}





/**
 * Renders a Table of Contents block for a post.
 */
function render_status($attributes) {
    $html = retrieveData('mc.marc.tv', 25565); // Replace with your server details
    return $html;
}

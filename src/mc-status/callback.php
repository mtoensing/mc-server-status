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
    $isOnline = $data->IsOnline ?? false;
    
    if ($isOnline) {
        $serverData = [
            'IsOnline' => $isOnline,
            'Motd' => $data->Motd ?? 'N/A',
            'ServerVersion' => $data->ServerVersion ?? 'N/A',
            'PlayersMax' => $data->PlayersMax ?? 0,
            'PlayersOnline' => $data->PlayersOnline ?? 0,
            'Players' => $data->Players ?? [],
            'timestamp' => time()
        ];
        updatePlayerData($data->Players ?? []); // Update the player data with current online status
    } else {
        $serverData = unserialize(get_option('minecraft_server_data', '')) ?: [];
        $serverData['IsOnline'] = $isOnline;
    }

    update_option('minecraft_server_data', serialize($serverData));
    
    return renderServerData($serverData, $data->Players ?? []);
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
function renderServerData($serverData, $currentPlayers) {
    // Retrieve saved player data
    $savedPlayers = get_option('minecraft_player_data', []);
    if (!is_array($savedPlayers)) {
        $savedPlayers = [];
    }

    // Determine currently online players
    $onlinePlayerIds = array_column($currentPlayers, 'id');

    // Here you would add rows for server metadata like MOTD, version, etc.
    $output = "<table class='minecraftserverinfo " . ($serverData['IsOnline'] ? "isonline" : "") . "'>";
    $output .= "<tr><td><strong>Server Status:</strong></td><td class='status'>" . ($serverData['IsOnline'] ? 'online' : 'offline') . "</td></tr>";
    $output .= "<tr><td><strong>MOTD:</strong></td><td>{$serverData['Motd']}</td></tr>";
    $output .= "<tr><td><strong>Server Version:</strong></td><td>{$serverData['ServerVersion']}</td></tr>";
    $output .= "<tr><td><strong>Max Players:</strong></td><td>{$serverData['PlayersMax']}</td></tr>";
    $output .= "<tr><td><strong>Players Online:</strong></td><td>{$serverData['PlayersOnline']}</td></tr>";
    $output .= "</table>";
    $output .= "<table class='minecraftserverinfo'>";
    $output .= "<thead><tr><th colspan='3'><strong>Players <span class='text-muted'>(2/49 online)</span></strong></th></tr></thead>"; 

    foreach ($savedPlayers as $id => $player) {
        $avatarURL = "https://mc-heads.net/avatar/{$id}";
        $isOnline = in_array($id, $onlinePlayerIds);
        $playerName = $player['name'];
        $lastSeen = isset($player['lastSeen']) ? date("Y-m-d H:i:s", $player['lastSeen']) : "Unknown";

        $output .= "<tr>";
        $output .= "<td><img src='{$avatarURL}' alt='{$playerName}'s Avatar' width='30' height='30'></td>";
        $output .= "<td>{$playerName}</td>";
        // Show "Online" for online players, "Last Seen" timestamp for others
        $output .= "<td>" . ($isOnline ? "Online" : "Last Seen: {$lastSeen}") . "</td>";
        $output .= "</tr>";
    }

    $output .= "</table>";
    return $output;
}


/**
 * Renders a Table of Contents block for a post.
 */
function render_status($attributes) {
    $html = retrieveData('mc.marc.tv', 25565); // Replace with your server details
    return $html;
}

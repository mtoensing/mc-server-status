<?php 

/**
 * Renders a Table of Contents block for a post
 * @param array $attributes An array of attributes for the Table of Contents block
 * @return string The HTML output for the Table of Contents block
 */
function render_status($attributes)
{
    $hostname = 'mc.marc.tv';

	$html = retrieveData($hostname, false);

    return  $html;
}


function retrieveData($hostname, $port = 25565) {
    $data = new MSI\MinecraftData($hostname, $port);
    
    // Always check and update the online status in real-time
    $isOnline = $data->IsOnline;
    
    // If the server is online, fetch fresh data and update cache
    if ($isOnline) {
        $serverData = [
            'Motd' => $data->Motd,
            'ServerVersion' => $data->ServerVersion,
            'PlayersMax' => $data->PlayersMax,
            'PlayersOnline' => $data->PlayersOnline,
            'timestamp' => time() // Add a timestamp for cache expiration
        ];
        
        // Serialize and save the fresh data
        update_option('minecraft_server_data', serialize($serverData));
    } else {
        // Server is offline, attempt to retrieve cached data for other metadata
        $cachedData = get_option('minecraft_server_data');
        if ($cachedData !== false) {
            $serverData = unserialize($cachedData);
        } else {
            // No cached data available, use placeholders
            $serverData = [
                'Motd' => 'N/A',
                'ServerVersion' => 'N/A',
                'PlayersMax' => 0,
                'PlayersOnline' => 0,
            ];
        }
    }
    
    // Regardless of the server's online status, always add/update the IsOnline status
    $serverData['IsOnline'] = $isOnline;

    // Render and return the data
    return renderServerData($serverData);
}

function renderServerData($data) {
    // Determine the table class based on server status
    $tableClass = $data['IsOnline'] ? 'isonline' : '';

    // Start the table with or without the "isonline" class
    $output = "<table class='minecraftserverinfo {$tableClass}'>";
    $output .= "<tr><td><strong>Server Status:</strong></td><td class='status'>" . ($data['IsOnline'] ? 'online' : 'offline') . "</td></tr>";
    $output .= "<tr><td><strong>MOTD:</strong></td><td>{$data['Motd']}</td></tr>";
    $output .= "<tr><td><strong>Server Version:</strong></td><td>{$data['ServerVersion']}</td></tr>";
    $output .= "<tr><td><strong>Max Players:</strong></td><td>{$data['PlayersMax']}</td></tr>";
    $output .= "<tr><td><strong>Players Online:</strong></td><td>{$data['PlayersOnline']}</td></tr>";
    // Close the table
    $output .= "</table>";

    return $output;
}

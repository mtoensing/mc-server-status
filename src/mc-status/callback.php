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
    
    // Check the server status
    $serverStatus = $data->IsOnline ? 'Server is online' : 'Server is offline';
    
    // Determine the table class based on server status
    $tableClass = $data->IsOnline ? 'isonline' : '';

    // Start the table with or without the "isonline" class
    $output = "<table class='{$tableClass}'>";
    $output .= "<tr><td><strong>Server Status:</strong></td><td>{$serverStatus}</td></tr>";
    $output .= "<tr><td><strong>MOTD:</strong></td><td>{$data->Motd}</td></tr>";
    $output .= "<tr><td><strong>Server Version:</strong></td><td>{$data->ServerVersion}</td></tr>";
    $output .= "<tr><td><strong>Max Players:</strong></td><td>{$data->PlayersMax}</td></tr>";
    $output .= "<tr><td><strong>Players Online:</strong></td><td>{$data->PlayersOnline}</td></tr>";
    
    if ($data->IsOnline && !empty($data->Players)) {
        foreach ($data->Players as $player) {
            $avatarURL = "https://mc-heads.net/avatar/{$player['id']}";
            $playerName = $player['name'];
            $lastSeen = "Not Available"; // Placeholder for last seen info

            // Now each player's info will be in a single row within the main table
            $output .= "<tr>";
            $output .= "<td><img src='{$avatarURL}' alt='{$playerName}'s Avatar' width='30' height='30'></td>";
            $output .= "<td>{$playerName}</td>";
            $output .= "<td>Last Seen: {$lastSeen}</td>";
            $output .= "</tr>";
        }
    }
    
    // Close the table
    $output .= "</table>";
    
    return $output;
}




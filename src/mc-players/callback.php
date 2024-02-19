<?php 

/**
 * Renders a Table of Contents block for a post
 * @param array $attributes An array of attributes for the Table of Contents block
 * @return string The HTML output for the Table of Contents block
 */
function render_players($attributes)
{
    $hostname = 'mc.marc.tv';

	$html = retrieveDataPlayers($hostname, false);

    return  $html;
}


function retrieveDataPlayers($hostname, $pingonly = true, $port = 25565) {
    $data = new MSI\MinecraftData($hostname, $port, $pingonly);
    
    // Check the server status and prepare a message
    $serverStatus = $data->IsOnline ? 'Server is online' : 'Server is offline';
    
    $output = '';
    // Prepare output with HTML formatting for clarity
   /* $output = "<strong>Server Status:</strong> {$serverStatus}<br />";
    $output .= "<strong>MOTD:</strong> {$data->Motd}<br />";
    $output .= "<strong>Max Players:</strong> {$data->PlayersMax}<br />";
    $output .= "<strong>Players Online:</strong> {$data->PlayersOnline}<br />";

    */
    
    if ($data->IsOnline && !empty($data->Players)) {
        // Server version could be displayed regardless of players being online
        $output .= "<strong>Server Version:</strong> {$data->ServerVersion}<br />";

        // List players
        $playerNames = array_map(function($player) {
            return $player['name']; // Assuming you stored player data with 'name' keys
        }, $data->Players);

        $playerList = implode(", ", $playerNames); // Convert player names array to a comma-separated string
        $output .= "<strong>Players:</strong> {$playerList}<br />";
    } else {
        // Display server version even if no players are online
        $output .= "<strong>Server Version:</strong> {$data->ServerVersion}<br />";
    }
    
    return $output;
}

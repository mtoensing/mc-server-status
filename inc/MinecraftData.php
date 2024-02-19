<?php

namespace MSI;

require __DIR__ . '/MinecraftPing.php';
require __DIR__ . '/MinecraftPingException.php';

use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;

class MinecraftData
{
    public int $PlayersOnline = 0; // Initialize with default value
    public int $PlayersMax = 0; // Initialize with default value
    public string $ServerVersion = ""; // Initialize with default value
    public string $Motd = ""; // Default message
    public bool $IsOnline = false; // To track server status
    public array $Players = [];

    public function __construct(string $Hostname, int $Port = 25565, bool $PingOnly = true)
    {
        $Query = null; // Declare $Query to ensure it's defined for the finally block

        try {
            $Query = new MinecraftPing($Hostname, $Port);

            $data = $Query->Query();
            $this->PlayersOnline = (int) $data['players']['online'];
            $this->PlayersMax = (int) $data['players']['max'];
            $this->ServerVersion = (string) $data['version']['name'];
            $this->ServerVersion = (string) $data['version']['name'];

            // Check if description is an array and handle it
            if (is_array($data['description'])) {
                // This is a simplified example. Adjust based on the actual array structure
                $this->Motd = isset($data['description']['text']) ? (string) $data['description']['text'] : 'N/A';
            } else {
                $this->Motd = (string) $data['description'];
            }

            // Extract players
            if (isset($data['players']['sample']) && is_array($data['players']['sample'])) {
                foreach ($data['players']['sample'] as $player) {
                    $this->Players[] = [
                        'id' => $player['id'],
                        'name' => $player['name']
                    ];
                }
            }

            $this->IsOnline = true; // Server is online
        } catch (MinecraftPingException $e) {
            // Handle exception, for example, log it or just set the server as offline
            // You might want to log $e->getMessage() for debugging
            $this->IsOnline = false; // Server is offline, no need to change $Motd as it's already "Server is offline"
        } finally {
            if ($Query) {
                $Query->Close();
            }
        }
    }
}

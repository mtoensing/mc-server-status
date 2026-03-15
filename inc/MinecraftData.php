<?php
namespace MCSI;

if ( ! defined( 'ABSPATH' ) ) exit;

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
            if (!is_array($data)) {
                throw new MinecraftPingException('Query returned unexpected data type.');
            }

            $players = $data['players'] ?? [];
            if (!is_array($players)) {
                $players = [];
            }

            $version = $data['version'] ?? [];
            if (!is_array($version)) {
                $version = [];
            }

            $this->PlayersOnline = isset($players['online']) ? (int) $players['online'] : 0;
            $this->PlayersMax = isset($players['max']) ? (int) $players['max'] : 0;
            $this->ServerVersion = isset($version['name']) ? (string) $version['name'] : '';

            $description = $data['description'] ?? '';
            if (is_array($description)) {
                $this->Motd = isset($description['text']) ? (string) $description['text'] : 'N/A';
            } else {
                $this->Motd = (string) $description;
            }

            // Extract players sample list
            if (isset($players['sample']) && is_array($players['sample'])) {
                foreach ($players['sample'] as $player) {
                    if (!is_array($player)) {
                        continue;
                    }

                    $this->Players[] = [
                        'id' => isset($player['id']) ? (string) $player['id'] : '',
                        'name' => isset($player['name']) ? (string) $player['name'] : '',
                    ];
                }
            }

            $this->IsOnline = true;
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

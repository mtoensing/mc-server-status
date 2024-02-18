<?php

namespace MSI;

require __DIR__ . '/MinecraftPing.php';
require __DIR__ . '/MinecraftPingException.php';

use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;

class MinecraftData{
    public int $PlayersOnline;
    public int $PlayersMax;
    public string $ServerVersion;
    public string $Motd;
	

    public function __construct( string $Hostname, int $Port = 25565, bool $PingOnly = true ){

        try
        {
            $Query = new MinecraftPing( $Hostname, $Port );
            if ($PingOnly == true) {
                $data =  $Query->QueryOldPre17();
                $this->PlayersOnline = $data['Players'];
                $this->Motd = $data['HostName'];
            } else {
                $data =  $Query->Query();
                $this->PlayersOnline = $data['players']['online'];
                $this->Motd = $data['description'];
            }
    
        }
        catch( MinecraftPingException $e )
        {
            echo $e->getMessage();
        }
        finally
        {
            if( $Query )
            {
                $Query->Close();
            }
        }

    }
}
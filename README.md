Nrpe Client
====

Library for connecting to Nagios Remote Plugin Executor

Usage:

    $commander  = new \Cubex\Nrpe\NrpeCommander('hostname');
    $command    = new \Cubex\Nrpe\Commands\CheckLoad();
    $result     = $commander->sendCommand($command);

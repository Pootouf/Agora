<?php

namespace AGORA\Game\MorpionBundle\Command;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use AGORA\Game\MorpionBundle\Controller\MorpionSocket;

/**
 * Créé une commande pour démarrer la WebSocket avec Ratchet
 * Cette commande = php bin/console start-morpion
 */
class SocketCommand extends Command {
    protected function configure() {
        // the short description shown while running "php bin/console list"
        $this->setHelp("Starts socket for Morpion Game")
            // the full command description shown when running the command with
            ->setDescription('Starts socket for Morpion Game')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln([
            'Morpion socket',// A line
            '============',// Another line
        ]);

        /**
         * On utilise le WsServer Composant de Ratchet. 
         * Permettant de réaliser la communication entre le serveur et les navigateurs web.
         */
        
        $container = $this->getApplication()->getKernel()->getContainer();
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new MorpionSocket($container)
                )
            ),
            8091
        );
    
        $server->run();
    }
}

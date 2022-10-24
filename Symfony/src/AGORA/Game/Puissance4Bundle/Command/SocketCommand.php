<?php

namespace AGORA\Game\Puissance4Bundle\Command;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use AGORA\Game\Puissance4Bundle\Controller\Puissance4Socket;

/**
 * Créé une commande pour démarrer la WebSocket avec Ratchet
 * Cette commande = php bin/console start-Puisscan4
 */
class SocketCommand extends Command {
    protected function configure() {
        // the short description shown while running "php bin/console list"
        $this->setHelp("Starts socket for Puissance Game")
            // the full command description shown when running the command with
            ->setDescription('Starts socket for puisscance4 Game')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln([
            'Puissance4 socket',// A line
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
                    new Puissance4Socket($container)
                )
            ),
            8092 // Antoine : changé en 8092
        );
    
        $server->run();
    }
}

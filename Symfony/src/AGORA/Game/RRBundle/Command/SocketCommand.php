<?php
namespace AGORA\Game\RRBundle\Command;

use Symfony\Component\Console\Command\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use AGORA\Game\RRBundle\Controller\RRSocket;

class SocketCommand extends Command {

    protected function configure() {
        $this->setName('sockets:start-rr')
            // the short description shown while running "php bin/console list"
            ->setHelp("Starts socket for Russian Railroads Game")
            // the full command description shown when running the command with
            ->setDescription('Starts socket for Russian Railroads Game');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln([
            'RR socket',// A line
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
                    new RRSocket($container)
                )
            ),
            8095
        );
    
        $server->run();
    }

}
?>
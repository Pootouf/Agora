<?php
/**
 * Created by PhpStorm.
 * User: guillaume
 * Date: 20/04/2018
 * Time: 15:49
 */

namespace AGORA\Game\Socket\Command;

use AGORA\Game\Socket\Socket;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//TODO A SUPPRIMER !!!!!! Cette commande n'est jamais utilisé.
class SocketCommand extends Command {
    protected function configure() {
        $this->setName('sockets:gameSocket')
            // the short description shown while running "php bin/console list"
            ->setHelp("Lance les sockets des jeux.")
            // the full command description shown when running the command with
            ->setDescription('Starts the game socket')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln([
            'GameSocket',// A line
            '============',// Another line
        ]);


        $container = $this->getApplication()->getKernel()->getContainer();
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Socket($container)
                )
            ),
            8090
        );

        $server->run();
    }
}
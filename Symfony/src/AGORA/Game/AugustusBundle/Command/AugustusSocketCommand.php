<?php
// myapplication/src/sandboxBundle/Command/SocketCommand.php
// Change the namespace according to your bundle
namespace AGORA\Game\AugustusBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// Include ratchet libs
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

use AGORA\Game\AugustusBundle\Controller\AugustusSocket;


class AugustusSocketCommand extends Command
{
    protected function configure()
    {
        // the short description shown while running "php bin/console list"
        $this->setHelp("Starts the game Augustus")
            // the full command description shown when running the command with
            ->setDescription('Starts the Augustus socket demo')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '  Augustus socket  ',// A line
            '===================',// Another line
            ' Starting Augustus ',// Empty line
        ]);

        
        $container = $this->getApplication()->getKernel()->getContainer();
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new AugustusSocket($container)
                )
            ),
            8088
        );

        $server->run();
    }
}
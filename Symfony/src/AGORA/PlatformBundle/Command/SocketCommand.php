<?php
// myapplication/src/sandboxBundle/Command/SocketCommand.php
// Change the namespace according to your bundle
namespace AGORA\PlatformBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// Include ratchet libs
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

// Change the namespace according to your bundle
use AGORA\PlatformBundle\Sockets\Chat;

class SocketCommand extends Command
{
    protected function configure()
    {
        // the short description shown while running "php bin/console list"
        $this->setHelp("Starts chat websocket of the Agora platform")
            // the full command description shown when running the command with
            ->setDescription('Starts the chat websocket of the Agora platform')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Chat socket',// A line
            '============',// Another line
            'Starting chat, open your browser and enjoy !.',// Empty line
        ]);
        
        $container = $this->getApplication()->getKernel()->getContainer();
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat($container)
                )
            ),
            8084
        );
        
        $server->run();
    }
}
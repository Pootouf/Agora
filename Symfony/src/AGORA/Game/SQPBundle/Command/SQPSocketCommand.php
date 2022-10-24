<?php
// myapplication/src/sandboxBundle/Command/SocketCommand.php
// Change the namespace according to your bundle
namespace AGORA\Game\SQPBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// Include ratchet libs
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

// Change the namespace according to your bundle
use AGORA\Game\SQPBundle\Sockets\SQP;


class SQPSocketCommand extends Command
{
    protected function configure()
    {
        // the short description shown while running "php bin/console list"
        $this->setHelp("Starts the game of 6 qui prend socket demo")
            // the full command description shown when running the command with
            ->setDescription('Starts the 6qp socket demo')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '6 qui prend socket',// A line
            '============',// Another line
            'Starting 6 qui prend, open your browser and enjoy !.',// Empty line
        ]);

        //La magie !!! Récupère la classe dans Modele/SQPAPI.php
        $sqpapi = $this->getApplication()->getKernel()->getContainer()->get('agora_game_sqp.sqpapi');
        $sqpLog = $this->getApplication()->getKernel()->getContainer()->get('agora_game_sqp.sqpLog');

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new SQP($sqpapi, $sqpLog)
                )
            ),
            8086
        );

        $server->run();
    }
}
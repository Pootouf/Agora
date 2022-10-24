<?php


namespace AGORA\Game\SplendorBundle\Command;


use AGORA\Game\SplendorBundle\Socket\SplendorSocket;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SplendorSocketCommand extends Command {

    protected function configure() {
        $this->setHelp("Starts the game of Splendor socket demo")
            ->setDescription('Start Splendor Socket');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln([
            'Splendor socket',// A line
            '============',// Another line
            'Starting Splendor, open your browser and enjoy !.',// Empty line
        ]);

        $service = $this->getApplication()->getKernel()->getContainer()->get('agora_game.splendor');
        $splendorLog = $this->getApplication()->getKernel()->getContainer()->get('agora_game.splendorLog');

        $server = IoServer::factory(
            new HttpServer(new WsServer(new SplendorSocket($service, $splendorLog))),
            8089
        );

        $server->run();

    }


}
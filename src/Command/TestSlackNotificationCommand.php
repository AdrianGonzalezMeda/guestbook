<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

#[AsCommand(
    name: 'app:test:slack-notification',
    description: 'Envía una notificación de prueba a Slack',
)]
class TestSlackNotificationCommand extends Command
{
    private ChatterInterface $chatter;

    public function __construct(ChatterInterface $chatter)
    {
        parent::__construct();
        $this->chatter = $chatter;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->chatter->send(new ChatMessage('¡Notificación de prueba desde Symfony (Slack Webhook)!'));
        } catch (\Exception $e) {
            $output->writeln('<error>Error al enviar la notificación a Slack: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $output->writeln('<info>Mensaje enviado correctamente a Slack</info>');

        return Command::SUCCESS;
    }
}

<?php

namespace App\EventSubscriber;

use App\Repository\ConferenceRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $conferenceRepository;

    public function __construct(Environment $twig, ConferenceRepository $conferenceRepository)
    {
        $this->twig = $twig;
        $this->conferenceRepository = $conferenceRepository;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $this->twig->addGlobal('conferences', $this->conferenceRepository->findAll());
    }

    public static function getSubscribedEvents(): array
    {
        return [];
        /* This subscriber its no longer used. Because we use the cache instead, creating a new
        route to render the header with the conferences. Enabling the ESI (Edge Side Includes)
        to have diferent cache solutions fot specific parts of the page.
        The route is /conference_header and the template is conference/header.html.twig. 
        */
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}

<?php

namespace App\MessageHandler;

use App\Api\Client\SpamChecker as ClientSpamChecker;
use App\Base\Constant\CommentWorkflow;
use App\Base\Services\ImageOptimizer;
use App\Message\CommentMessage;
use App\Notification\CommentReviewNotification;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class CommentMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClientSpamChecker $spamChecker,
        private CommentRepository $commentRepository,
        private MessageBusInterface $bus,
        private WorkflowInterface $commentStateMachine,
        private NotifierInterface $notifier,
        #[Autowire('%admin_email%')] private string $adminEmail,
        private ImageOptimizer $imageOptimizer,
        #[Autowire('%photo_dir%')] private string $photoDir,
        private ?LoggerInterface $logger = null,
    ) {}

    public function __invoke(CommentMessage $message)
    {
        $comment = $this->commentRepository->find($message->getId());
        
        if (!$comment) {
            return;
        }

        if ($this->commentStateMachine->can($comment, CommentWorkflow::TRANSITION_ACCEPT)) {
            $score = $this->spamChecker->getSpamScore($comment, $message->getContext());
            $transition = match ($score) {
                2 => CommentWorkflow::TRANSITION_REJECT_SPAM,
                1 => CommentWorkflow::TRANSITION_MIGHT_BE_SPAM,
                default => CommentWorkflow::TRANSITION_ACCEPT,
            };
            $this->commentStateMachine->apply($comment, $transition);
            $this->entityManager->flush();
            $this->bus->dispatch($message);
        } elseif (
            $this->commentStateMachine->can($comment, CommentWorkflow::TRANSITION_PUBLISH)
            || $this->commentStateMachine->can($comment, CommentWorkflow::TRANSITION_PUBLISH_HAM)
        ) {
            $recipient = new Recipient($this->adminEmail);
            $this->notifier->send(
                new CommentReviewNotification($comment, $message->getReviewUrl()),
                $recipient
            );
        } elseif ($this->commentStateMachine->can($comment, CommentWorkflow::TRANSITION_OPTIMIZE)) {
            if ($comment->getPhotoFilename()) {
                $this->imageOptimizer->resize($this->photoDir . '/' . $comment->getPhotoFilename());
            }
            $this->commentStateMachine->apply($comment, CommentWorkflow::TRANSITION_OPTIMIZE);
            $this->entityManager->flush();
        } elseif ($this->logger) {
            $this->logger->debug(
                'Dropping comment message',
                ['comment' => $comment->getId(), 'state' => $comment->getState()]
            );
        }
    }
}

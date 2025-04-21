<?php

namespace App\Notification;

use App\Entity\Comment;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class CommentReviewNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        private Comment $comment,
        string $importance = self::IMPORTANCE_MEDIUM
    ) {
        parent::__construct('New comment posted', ['email']);
        $this->importance($importance);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $email = (new NotificationEmail())
        ->htmlTemplate('emails/comment_notification.html.twig')
        ->to($recipient->getEmail())
        ->context(['comment' => $this->comment])
        ->importance($this->getImportance());

        return new EmailMessage($email, $transport);
    }
}
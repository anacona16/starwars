<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class UserLoggedInEvent implements EventSubscriberInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var String
     */
    private $emailFrom;

    /**
     * @var string
     */
    private $notificationEmail;

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer, $emailFrom, $notificationEmail)
    {
        $this->mailer = $mailer;
        $this->emailFrom = $emailFrom;
        $this->notificationEmail = $notificationEmail;
    }

    /**
     * Sending an email once the user logs in.
     *
     * @param AuthenticationEvent $event
     * @param $eventName
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function onAuthenticationSuccess(AuthenticationEvent $event, $eventName) {
        if (empty(($this->emailFrom) || empty($this->notificationEmail)) {
            return;
        }

        $token = $event->getAuthenticationToken();
        $username = $token->getUserIdentifier();

        $email = (new Email())
            ->from($this->emailFrom)
            ->to($this->notificationEmail)
            ->subject('Alert!')
            ->text(sprintf('The user %s just logged in the system!', $username));

        $this->mailer->send($email);
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }
}

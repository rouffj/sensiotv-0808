<?php

namespace App\EventSubscriber;

use App\Event\UserRegisteredEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailConfirmationSubscriber implements EventSubscriberInterface
{
    public function onUserRegistered(UserRegisteredEvent $event)
    {
        $emailContent = [
            'from' => 'sensiotv@gmail.com',
            'to' => $event->getUser()->getEmail(),
            'subject' => 'Your account has been created successfully ' . $event->getUser()->getFirstName(),
        ];

        dump($emailContent);
    }

    public static function getSubscribedEvents()
    {
        return [
            'user_registered' => 'onUserRegistered',
        ];
    }
}

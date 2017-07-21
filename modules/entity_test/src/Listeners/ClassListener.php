<?php
namespace Drupal\cjgratacos\entity_test\Listeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ClassListener implements EventSubscriberInterface
{
    public function sayHello($event) {
        var_dump($event);
        die;
    }

    public static function getSubscribedEvents()
    {
     return [
        KernelEvents::REQUEST => "sayHello"
     ];
    }

}
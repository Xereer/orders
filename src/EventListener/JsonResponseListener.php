<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class JsonResponseListener
{
    public function onKernelView(ViewEvent $event): void
    {
        $result = $event->getControllerResult();

        if ($result instanceof Response) {
            return;
        }

        $event->setResponse(
            new JsonResponse(
                [
                    'success' => true,
                    'rows' => $result
                ]
            )
        );
    }
}

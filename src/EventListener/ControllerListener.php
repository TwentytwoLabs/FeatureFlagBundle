<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use TwentytwoLabs\FeatureFlagBundle\Attribute\Feature;
use TwentytwoLabs\FeatureFlagBundle\Model\FeatureInterface;

class ControllerListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();
        if (is_object($controller) && method_exists($controller, '__invoke')) {
            $controller = [$controller, '__invoke'];
        }

        $className = $controller[0]::class;
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($controller[1]);

        $features = [];
        foreach ($this->resolveFeatures($class, $method) as $key => $feature) {
            if (isset($features[$key])) {
                throw new \UnexpectedValueException(sprintf('Feature "%s" is defined more than once in %s::%s', $key, $className, $controller[1]));
            }

            $features[$key] = $feature;
        }

        $request = $event->getRequest();
        $request->attributes->set('_features', array_merge($request->attributes->get('_features', []), $features));
    }

    private function resolveFeatures(\ReflectionClass $class, \ReflectionMethod $method): iterable
    {
        foreach ($class->getAttributes(Feature::class) as $attribute) {
            /** @var Feature $feature */
            $feature = $attribute->newInstance();

            yield $feature->getName() => $feature->toArray();
        }

        foreach ($method->getAttributes(Feature::class) as $attribute) {
            /** @var Feature $feature */
            $feature = $attribute->newInstance();

            yield $feature->getName() => $feature->toArray();
        }
    }
}

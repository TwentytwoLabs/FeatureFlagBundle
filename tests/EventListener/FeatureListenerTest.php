<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use TwentytwoLabs\FeatureFlagBundle\EventListener\FeatureListener;
use TwentytwoLabs\FeatureFlagBundle\Manager\ChainedFeatureManager;
use TwentytwoLabs\FeatureFlagBundle\Tests\Fixtures\Controller\FooController;

/**
 * @codingStandardsIgnoreFile
 *
 * @SuppressWarnings(PHPMD)
 */
class FeatureListenerTest extends TestCase
{
    private ChainedFeatureManager $manager;

    protected function setUp(): void
    {
        $this->manager = $this->createMock(ChainedFeatureManager::class);
    }

    public function testShouldValidateEvent(): void
    {
        static::assertSame(['kernel.controller' => 'onKernelController'], FeatureListener::getSubscribedEvents());
    }

    public function testShouldNotCheckFeaturesBecauseFeaturesIsEmpty(): void
    {
        $this->manager->expects($this->never())->method('isEnabled');

        $kernel = $this->createMock(HttpKernelInterface::class);

        $attributes = $this->createMock(ParameterBag::class);
        $attributes->expects($this->once())->method('get')->with('_features', [])->willReturn([]);

        $request = $this->createMock(Request::class);
        $request->attributes = $attributes;

        $event = new ControllerEvent($kernel, new FooController(), $request, null);
        $controller = new FeatureListener($this->manager);
        $controller->onKernelController($event);
    }

    public function testShouldNotCheckFeaturesBecauseFeatureNotExist(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->manager->expects($this->once())->method('isEnabled')->willReturn(false);

        $kernel = $this->createMock(HttpKernelInterface::class);

        $attributes = $this->createMock(ParameterBag::class);
        $attributes
            ->expects($this->once())
            ->method('get')
            ->with('_features', [])
            ->willReturn(['bar' => ['feature' => 'bar', 'enabled' => true]])
        ;

        $request = $this->createMock(Request::class);
        $request->attributes = $attributes;

        $event = new ControllerEvent($kernel, new FooController(), $request, null);
        $controller = new FeatureListener($this->manager);
        $controller->onKernelController($event);
    }

    public function testShouldCheckFeatures(): void
    {
        $this->manager->expects($this->once())->method('isEnabled')->willReturn(true);

        $kernel = $this->createMock(HttpKernelInterface::class);

        $attributes = $this->createMock(ParameterBag::class);
        $attributes
            ->expects($this->once())
            ->method('get')
            ->with('_features', [])
            ->willReturn(['bar' => ['feature' => 'bar', 'enabled' => true]])
        ;

        $request = $this->createMock(Request::class);
        $request->attributes = $attributes;

        $event = new ControllerEvent($kernel, new FooController(), $request, null);
        $controller = new FeatureListener($this->manager);
        $controller->onKernelController($event);
    }
}

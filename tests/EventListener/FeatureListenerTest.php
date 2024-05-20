<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use TwentytwoLabs\FeatureFlagBundle\EventListener\FeatureListener;
use TwentytwoLabs\FeatureFlagBundle\Manager\ChainedFeatureManager;
use TwentytwoLabs\FeatureFlagBundle\Tests\Fixtures\Controller\FooController;

final class FeatureListenerTest extends TestCase
{
    private ChainedFeatureManager|MockObject $manager;

    protected function setUp(): void
    {
        $this->manager = $this->createMock(ChainedFeatureManager::class);
    }

    public function testShouldValidateEvent(): void
    {
        $this->assertSame(['kernel.controller' => 'onKernelController'], FeatureListener::getSubscribedEvents());
    }

    public function testShouldNotCheckFeaturesBecauseFeaturesIsEmpty(): void
    {
        $this->manager->expects($this->never())->method('isEnabled');

        $kernel = $this->createMock(HttpKernelInterface::class);

        $attributes = $this->createMock(ParameterBag::class);
        $attributes->expects($this->once())->method('get')->with('_features', [])->willReturn([]);

        $request = $this->createMock(Request::class);
        $request->attributes = $attributes;

        $controller = $this->getListener();
        $controller->onKernelController(new ControllerEvent($kernel, new FooController(), $request, null));
    }

    public function testShouldNotCheckFeaturesBecauseFeatureNotExist(): void
    {
        $this->expectException(GoneHttpException::class);

        $this->manager->expects($this->once())->method('isEnabled')->with('bar')->willReturn(false);

        $kernel = $this->createMock(HttpKernelInterface::class);

        $attributes = $this->createMock(ParameterBag::class);
        $attributes
            ->expects($this->once())
            ->method('get')
            ->with('_features', [])
            ->willReturn([['feature' => 'bar', 'enabled' => true]])
        ;

        $request = $this->createMock(Request::class);
        $request->attributes = $attributes;

        $controller = $this->getListener();
        $controller->onKernelController(new ControllerEvent($kernel, new FooController(), $request, null));
    }

    public function testShouldCheckFeatures(): void
    {
        $matcher = $this->exactly(2);
        $this->manager
            ->expects($matcher)
            ->method('isEnabled')
            ->willReturnOnConsecutiveCalls(true, false)
            ->willReturnCallback(function (string $name) use ($matcher) {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertSame('bar', $name),
                    2 => $this->assertSame('baz', $name),
                    default => throw new \Exception(sprintf('Method "isEnabled" should call %d times', 2)),
                };

                return match ($matcher->numberOfInvocations()) {
                    1 => true,
                    2 => false,
                    default => throw new \Exception(sprintf('Method "isEnabled" should call %d times', 2)),
                };
            })
        ;

        $kernel = $this->createMock(HttpKernelInterface::class);

        $attributes = $this->createMock(ParameterBag::class);
        $attributes
            ->expects($this->once())
            ->method('get')
            ->with('_features', [])
            ->willReturn([['feature' => 'bar'], ['feature' => 'baz', 'enabled' => false]])
        ;

        $request = $this->createMock(Request::class);
        $request->attributes = $attributes;

        $controller = $this->getListener();
        $controller->onKernelController(new ControllerEvent($kernel, new FooController(), $request, null));
    }

    private function getListener(): FeatureListener
    {
        return new FeatureListener($this->manager);
    }
}

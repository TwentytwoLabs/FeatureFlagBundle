<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Tests\EventListener;

use TwentytwoLabs\FeatureFlagBundle\Tests\Fixtures\Controller\FooController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use TwentytwoLabs\FeatureFlagBundle\EventListener\ControllerListener;
use TwentytwoLabs\FeatureFlagBundle\Tests\Fixtures\Controller\DefaultController;

/**
 * @codingStandardsIgnoreFile
 *
 * @SuppressWarnings(PHPMD)
 */
class ControllerListenerTest extends TestCase
{
    public function testShouldValidateEvent(): void
    {
        $this->assertSame(
            ['kernel.controller' => 'onKernelController'],
            ControllerListener::getSubscribedEvents()
        );
    }

    public function testShouldNotResolveFeatureBecauseFeatureNotExist(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Feature "foo" is defined more than once in TwentytwoLabs\FeatureFlagBundle\Tests\Fixtures\Controller\DefaultController::attributeFooError');

        $attributes = $this->createMock(ParameterBag::class);
        $attributes->expects($this->never())->method('set');
        $attributes->expects($this->never())->method('get');

        $kernel = $this->createMock(HttpKernelInterface::class);

        $request = $this->createMock(Request::class);
        $request->attributes = $attributes;

        $listener = new ControllerListener();
        $listener->onKernelController(
            new ControllerEvent(
                $kernel,
                [new DefaultController(), 'attributeFooError'],
                $request,
                null
            )
        );
    }

    public function testShouldResolveFeatureWithClass(): void
    {
        $attributes = $this->createMock(ParameterBag::class);
        $attributes
            ->expects($this->once())
            ->method('set')
            ->with(
                '_features',
                ['foo' => ['feature' => 'foo', 'enabled' => false], 'bar' => ['feature' => 'bar', 'enabled' => true]]
            )
        ;
        $attributes
            ->expects($this->once())
            ->method('get')
            ->with('_features', [])
            ->willReturn(['bar' => ['feature' => 'bar', 'enabled' => true]])
        ;

        $kernel = $this->createMock(HttpKernelInterface::class);

        $request = $this->createMock(Request::class);
        $request->attributes = $attributes;

        $listener = new ControllerListener();
        $listener->onKernelController(
            new ControllerEvent(
                $kernel,
                new FooController(),
                $request,
                null
            )
        );
    }

    public function testShouldResolveFeatureWithMethod(): void
    {
        $attributes = $this->createMock(ParameterBag::class);
        $attributes
            ->expects($this->once())
            ->method('set')
            ->with(
                '_features',
                ['foo' => ['feature' => 'foo', 'enabled' => true], 'bar' => ['feature' => 'bar', 'enabled' => true]]
            )
        ;
        $attributes
            ->expects($this->once())
            ->method('get')
            ->with('_features', [])
            ->willReturn(['bar' => ['feature' => 'bar', 'enabled' => true]])
        ;

        $kernel = $this->createMock(HttpKernelInterface::class);

        $request = $this->createMock(Request::class);
        $request->attributes = $attributes;

        $listener = new ControllerListener();
        $listener->onKernelController(
            new ControllerEvent(
                $kernel,
                [new DefaultController(), 'attributeFooEnabled'],
                $request,
                null
            )
        );
    }
}

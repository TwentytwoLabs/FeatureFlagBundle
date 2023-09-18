<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use TwentytwoLabs\FeatureFlagBundle\Exception\ConfigurationException;

abstract class AbstractStorageFactory implements StorageFactoryInterface
{
    protected function validate(string $storageName, array $options): array
    {
        $resolver = new OptionsResolver();
        $this->configureOptionResolver($resolver);

        try {
            return $resolver->resolve($options);
        } catch (\Exception $e) {
            $message = sprintf(
                'Error while configure storage %s. Verify your configuration at "twenty-two-labs.feature-flags.storages.%s.options". %s',
                $storageName,
                $storageName,
                $e->getMessage()
            );

            throw new ConfigurationException($message, $e->getCode(), $e);
        }
    }

    abstract protected function configureOptionResolver(OptionsResolver $resolver): void;
}

<?php

declare(strict_types=1);

namespace TwentytwoLabs\FeatureFlagBundle\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use TwentytwoLabs\FeatureFlagBundle\Storage\ArrayStorage;
use TwentytwoLabs\FeatureFlagBundle\Storage\StorageInterface;

class ArrayStorageFactory extends AbstractStorageFactory
{
    public function createStorage(string $storageName, array $options = []): StorageInterface
    {
        return new ArrayStorage($this->transform($this->validate($storageName, $options)));
    }

    protected function configureOptionResolver(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('features')
            ->setAllowedTypes('features', ['array'])
        ;
    }

    private function transform(array $options): array
    {
        foreach ($options['features'] as $name => $features) {
            $feature = ['name' => $name, 'enabled' => true, 'description' => null];

            if (\is_bool($features)) {
                $feature['enabled'] = $features;
            }

            if (\is_array($features)) {
                $feature = $features + $feature;
            }

            $options['features'][$name] = $feature;
        }

        return $options;
    }
}

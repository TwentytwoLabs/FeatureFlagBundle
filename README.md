# FeatureFlagBundle

The FeatureFlagBundle is a bundle to manage features flags in your Symfony applications.

## Compatibility

This bundle is tested with at least all maintained Symfony version.

## Documentation

###  Install it

Install extension using [composer](https://getcomposer.org):

```bash
composer require twentytwo-labs/feature-flag-bundle
```

If you don't use Flex, enable the bundle in your `config/bundles.php` file:

```php
<?php

return [
    // ...
    
    TwentytwoLabs\FeatureFlagBundle\TwentytwoLabsFeatureFlagBundle::class => ['all' => true],
];
```

###  Configuration

To configure and register a feature manager you need a factory service. You may also need to change some options to the factory.

```yaml
# ...
twentytwo_labs_feature_flag:
    default_manager: default
    managers:
        default:
            factory: 'twenty-two-labs.feature-flags.factory.array'
            options:
                features:
                    my_feature_1: false
                    my_feature_2: true
                    my_feature3: '%env(bool:FEATURE_ENVVAR)%'
```

The factories that come with this bundle can be found in the table below.

| Factory service id                          | Options    |
|---------------------------------------------|------------|
| twenty-two-labs.feature-flags.factory.array | `features` |

#### Example configuration

```yaml
# ...
twentytwo_labs_feature_flag:
    default_manager: default
    managers:
        default:
            factory: twenty-two-labs.feature-flags.factory.array
            options:
                features:
                    my_feature_1:
                        enabled: false
                        description: MyFeature1 description text
                    my_feature_2:
                        enabled: true
                        description: MyFeature2 description text
                    my_feature3:
                        enabled: '%env(bool:FEATURE_ENVVAR)%'
                        description: MyFeature3 description text
```

You can declare multiple managers. Multiple providers is useful if you want to use different storage providers or to isolate your features flags.

```yaml
# ...
twentytwo_labs_feature_flag:
    default_manager: manager_foo
    managers:
        manager_foo:
            factory: twenty-two-labs.feature-flags.factory.array
            options:
                features:
                    my_feature_1:
                        enabled: false
                        description: MyFeature1 description text
                    my_feature_2:
                        enabled: true
                        description: MyFeature2 description text
                    my_feature3:
                        enabled: '%env(bool:FEATURE_ENVVAR)%'
                        description: MyFeature3 description text
        manager_bar:
            factory: twenty-two-labs.feature-flags.factory.array
            options:
                features:
                    my_feature_4:
                        enabled: false
                        description: MyFeature4 description text
                    my_feature_5: []
                    my_feature_6: ~
                    my_feature_7: false
```

When several managers are defined, they are registered in the Symfony dependency injection container as services with the following naming convention: `twentytwo_labs_feature_flag.manager.<manager_name>`.

For example, the `manager_bar` is accessible with the following service name: `twentytwo_labs_feature_flag.manager.manager_bar`.

Manager storage are also registered in the Symfony dependency injection container as services with the following naming convention: `twentytwo_labs_feature_flag.storage.<manager_name>`.

#### Use it as a service

The bundle adds a global `twentytwo_labs_feature_flag.manager` service you can use in your PHP classes.

In the case you have defined several managers, the service use the `ChainedFeatureManager` class to chain all declared managers.

```php
use App\Controller;
// ...

class MyController extends Controller
{
    public function myAction(FeatureManager $featureManager): Response
    {
        if ($featureManager->isEnabled('my_feature_1')) {
            // my_feature_1 is enabled
        }

        if ($featureManager->isDisabled('my_feature_2')) {
            // my_feature_2 is not enabled
        }

        // ...
    }
}
```

#### In your Twig templates

You can also check a flag in your templates:

```twig
{% if isFeatureEnabled('my_feature_1') %}
    {% include 'feature1_template.html.twig' %}
{% endif %}

{% if isFeatureDisabled('my_feature_2') %}
    {% include 'feature2_template.html.twig' %}
{% endif %}
```

#### In the routing configuration

The package allows you to restrict a controller access by adding some configuration in your routing definition.

```yaml
# app/config/routing.yml
my_first_route:
    path: /my/first/route
    defaults:
        _controller: AppBundle:Default:index
        _features:
            - { feature: my_feature_key, enabled: false } # The action is accessible if "my_feature_key" is disabled

my_second_route:
    path: /my/second-route
    defaults:
        _controller: AppBundle:Default:second
        _features:
            - { feature: foo } # The action is accessible if "foo" is enabled ...
            - { feature: bar, enabled: true } # ... and "bar" feature is also enabled
```

#### As a controller attribute

You can also restrict a controller access with attributes :

```php
#[Feature(name: "foo", enabled: true)]
class MyController extends Controller
{
    #[Feature(name: "foo")]
    public function annotationFooEnabledAction(): Response
    {
        return new Response('MyController::annotationFooEnabledAction');
    }

    #[Feature(name: "foo", enabled: true)]
    public function annotationFooEnabledBisAction(): Response
    {
        return new Response('MyController::annotationFooEnabledAction');
    }

    #[Feature(name: "foo", enabled: false)]
    public function annotationFooDisabledAction(): Response
    {
        return new Response('MyController::annotationFooDisabledAction');
    }
}
```

### Implement your own storage provider

1. First your need to create your storage provider class which implement the `TwentytwoLabs\FeatureFlagBundle\Storage\StorageInterface` interface
2. After your need to create a factory class which implement the `TwentytwoLabs\FeatureFlagBundle\Factory\StorageFactoryInterface` interface and create your custom storage
3. Register it in the Symfony dependency injection container
4. Specify the storage you want to use in a manager configuration

```yaml
twentytwo_labs_feature_flag:
    manager:
        manager_name:
            storage: your.custom.service.name
            options:
                # arguments need to create the storage service
```

## License

This library is published under [MIT license](LICENSE)

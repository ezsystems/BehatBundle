# BehatBundle features

## Example usages

### API examples
See [BehatBundle examples](https://github.com/ezsystems/BehatBundle/tree/7.0/features/examples) to see how to use Behat sentences to:
- create Languages, Content items, Content Types
- create Users with complex permissions
- create given YAML configuration

### Logging into the repository

Use the [TestContext](../Context/Api/TestContext.php) to log in to the Repository and perform API calls as given user. You can tag your scenario with `@admin` to be automatically logged in as admin user before the scenario starts.

### Browser examples

Look at [AdminUI feature files](https://github.com/ezsystems/ezplatform-admin-ui/tree/1.5/features/standard) to see example browser tests for AdminUI. If you want to reuse these Steps in your code in addition to the Context that defines them you also need to include:
- `EzSystems\EzPlatformAdminUi\Behat\Helper\Hooks`
- `EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext`

#### Improved drag and drop

Selenium does not support drag and drop interactions between iframes. To achieve that you can use the `UtilityContext::moveWithHover` method (which also supports hover simulation between the actions). The method accepts JS expression such as `document.evaluate` or `document.querySelector` to find elements between which the interaction will happen.

Before you start using that you need to inject the [dragMock script](../Resources/public/js/scripts/drag-mock.js) into your templates: one way of doing this is described in [Webpack Encore configuration doc](https://doc.ezplatform.com/en/latest/guide/bundles/#configuration-from-a-bundle). See [ez.config.manager.js](../Resources/encore/ez.config.manager.js) for an example.

## BehatBundle extension

With `EzBehatExtension` enabled you can inject services into your Context classes using the `@injectService` annotation.

Instead of writing:
```
// Behat configuration 
default:
    suites:
        default:
            contexts:
                - FeatureContext:
                    session:   '@session'
    extensions:
        Behat\Symfony2Extension: ~

// Context class
<?php

namespace FeatureContext;

use Behat\Behat\Context\Context;
use Symfony\Component\HttpFoundation\Session\Session;

class FeatureContext implements Context
{
    public function __construct(Session $session)
    {
        // $session is your Symfony @session
    }
}
```

you can write:
```
// Behat configuration
default:
    suites:
        default:
            contexts:
                - FeatureContext
    extensions:
        Behat\Symfony2Extension: ~
        EzSystems\PlatformBehatBundle\ServiceContainer\EzBehatExtension: ~

// Context class

<?php

namespace FeatureContext;

use Behat\Behat\Context\Context;
use Symfony\Component\HttpFoundation\Session\Session;

class FeatureContext implements Context
{
    /**
    * @injectService $service1 @session
    */
    public function __construct(Session $session)
    {
        // $session is your Symfony2 @session
    }
}
```

This makes maintenence easier when the same Context is used in multiple Behat suites.

## Debugging tools

Use the [Hooks](https://github.com/ezsystems/ezplatform-admin-ui/blob/1.5/src/lib/Behat/Helper/Hooks.php) Context class to access browser and server logs after Scenario failure, which combined with the screenshots feature makes debugging failures (even on CI) much easier.

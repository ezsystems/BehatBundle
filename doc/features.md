# BehatBundle features

## Example usages

### API examples
See [BehatBundle examples](https://github.com/ezsystems/BehatBundle/tree/master/features/examples) to see how to use Behat sentences to:
- create Languages, Content Items, Content Types
- create Users with complex permissions
- set given YAML configuration

## Logging into the repository

Use the [TestContext](../Context/Api/TestContext.php) to login into repository and perform API calls as given user. You can tag your scenario with `@admin` to be automatically logged in as admin user before the scenario starts.

### Browser examples

Look at [AdminUI feature files](https://github.com/ezsystems/ezplatform-admin-ui/tree/master/features/standard) to see example browser tests for AdminUI. If you want to reuse these Steps in your code in addition to the Context that defines them you also need to include:
- EzSystems\EzPlatformAdminUi\Behat\Helper\Hooks
- EzSystems\EzPlatformAdminUi\Behat\Helper\UtilityContext

## BehatBundle extension

With `EzBehatExtension` Behat extension enabled you can inject services into your Context classes using annotations.

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

This makes maintenence easier when the same Context is used in multiple Behat suites

## Debugging tools

Use [Hooks](https://github.com/ezsystems/ezplatform-admin-ui/blob/1.5/src/lib/Behat/Helper/Hooks.php) Context class for access to browser and server logs after Scenario failure, which combined together with screenshots feature makes debugging failures (even on CI) much easier.





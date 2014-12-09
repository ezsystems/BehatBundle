# eZ Publish Behat Bundle

Behat Bundle brings the Behavior Driven Development (as known as BDD) into
the eZ Publish.

Using latest Behat 3 and defining sentences with Turnip language, making scenarios it is easier than ever!

**Important** - Behat Bundle is still improving (a work in progress) so it's possible that deep changes may occur


## How to install

BehatBundle should be installed by default (unless `--no-dev` was used in composer). To verity this, jump to [How to run tests](#how-to-run-tests)
If it is not installed, you can:

1. Get Behat bundle:
  * Through [composer](http://getcomposer.org), run on console:
    * `$ php composer.phar require "ezsystems/behatbundle": "*"`
  * Through `git clone`:
    * `$ git clone https://github.com/ezsystems/BehatBundle vendor/ezsystems/behatbundle/EzSystems/BehatBundle`
  * Download from GitHub:
    * download [BehatBundle](https://github.com/ezsystems/BehatBundle) and unzip it in `<ezpublish-root>/vendor/ezsystems/behatbundle/EzSystems/BehatBundle`
2. Add Behat Bundle to load list (under dev)
  * Edit `<ezpublish-root>/ezpublish/EzPublishKernel.php`
  * Add `new EzSystems\BehatBundle\EzSystemsBehatBundle(),` to `$bundles` in `EzPublishKernel::registerBundles()`

_Notice_:
* behaviour tests should be done in production environment, since the scenarios should reflect end user interaction with the system.

## Browser testing

When javascript interacion isn't needed to test content/browser behavior, [Goutte](https://github.com/fabpot/goutte) is used (through [MinkGoutteDriver](https://github.com/Behat/MinkGoutteDriver)), which is much faster than using a real browser.

However, when a real browser is needed, (because a given scenario requires the use of javascript, for example), [Goutte](https://github.com/fabpot/goutte) is not sufficient. In this case, [Sahi](http://sahipro.com/) or [Selenium2](http://www.seleniumhq.org/) must be used (again through each driver
[MinkSahiDriver](https://github.com/Behat/MinkSahiDriver) and [MinkSelenium2Driver](https://github.com/Behat/MinkSelenium2Driver)).

So either one of these must be installed and running ([Sahi](http://sahipro.com/) is the one enabled by default), the configured browser is also required [Firefox](https://www.mozilla.org/firefox) is the default). These settings can be easily changed on `behat.yml` at:

```yaml
default:
    extensions:
        Behat\MinkExtension:
            javascript_session: sahi
            browser_name: firefox
```


## How to run tests

Simply run:
* `$ php bin/behat --profile <profile> [--suite <suite>]`

Defined profiles (and suites):
* `setupWizard`
  * `demoContent` - make a Demo with content installation
      * `demoContentNonUniqueDB` - in cases where more than 1 database system is installed on machine
    * `demoClean` - make a clean installation of Demo
      * `demoCleanNonUniqueDB` - same as`demoContentNonUniqueDB`
* `demo`:
    * `content` - asserts the demo content is correct
* `rest`:
  * `fullJson` - runs all tests with JSON body type and [Buzz driver](https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Bundle/EzPublishRestBundle/Features/Context/RestClient/BuzzDriver.php)
  * `fullXml` - same has `fullJson` but with XML body type
    * `guzzle` - runs all tests with JSON body type but using [Guzzle driver](https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Bundle/EzPublishRestBundle/Features/Context/RestClient/GuzzleDriver.php)

_Notice_:
* `setupWizard` profile can only be run when installation is not done and it can't be repeated
* `setupWizard` has the database definitions set on the feature files, so you need to have the dabase system, database and user created (take a look at [demo.feature](https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Bundle/EzPublishLegacyBundle/Features/SetupWizard/demo.feature))



## Known issues

* [SetupWizard](https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Bundle/EzPublishLegacyBundle/Features/Context/SetupWizard/Context.php) profile needs _PROD_ enviroment defined (otherwise it will find duplicate html elements)
# eZ Publish Behat Bundle

Behat Bundle brings the Behavior Driven Development (as known as BDD) into
the eZ Publish.

Using latest Behat 3 and defining sentences with Turnip language, making scenarios it is easier than ever!

**Important** - Behat Bundle is still improving (a work in progress) so it's possible that deep changes may occur


## How to install

It should already be installed (unless `--no-dev` was used in composer), to verity this, jump to [How to run tests](#how-to-run-tests)

If not, it's easy to do:

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

On most cases we use [Goutte](https://github.com/fabpot/goutte) (through [MinkGoutteDriver](https://github.com/Behat/MinkGoutteDriver)) to test the browser behaviours, which is great because it is lightning fast!

However when you need to test with a real
browser, or you have some (needed) javascript that will be executed on that scenario, [Goutte](https://github.com/fabpot/goutte) isn't enough, so we use [Sahi](http://sahipro.com/) or [Selenium2](http://www.seleniumhq.org/) (again through each driver 
[MinkSahiDriver](https://github.com/Behat/MinkSahiDriver) and [MinkSelenium2Driver](https://github.com/Behat/MinkSelenium2Driver))

So those need to be installed and running when using default settings ([Sahi](http://sahipro.com/) is the one enabled by default). [Firefox](https://www.mozilla.org/firefox) is also needed. These settings can be easily changed on `behat.yml` at:

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

## Structure

BehatBundle:
```
    .
    ├── Context/                    - Base contexts are present here
    │   ├── Api/                    - Master context for API's (REST, PAPI, Commands, ...)
    │   ├── Browser/                - Master context for browser interfaces (Demo, Legacy, PlatforUI, ...)
    │   │   └── SubContext/         - Generic sub contexts for browser interfaces
    │   └── Object/                 - Object 'controllers' contexts
    ├── doc/                        - Documentation
    ├── Helper/                     - Helpers to ease the work on contexts
    └── ObjectManager/              - Object managers to prepare (and assert) the system
```

Other bundles:
```
    Features/
    ├── FeatureGroupA/            - Related features folder with feature files
    │   ├── FeatureA.feature
    │   ├── ...
    │   └── FeatureN.feature
    ├── .../
    ├── FeatureGroupN/
    └── Context                 - Context files and all coding files
        ├── <Bundle>.php          - Main context file (ex: Rest.php)
        ├── SubContext/           - Generic sub contexts in related to the main context
        ├── Helper/               - Needed helpers
        ├── OtherContextA/        - Sometimes the main context is sub divided (ex: Legacy is divided in SetupWizard and Admin til the moment)
        │ ├── Context.php         - The context class (usually called Context.php)
        │ └── SubContext/         - Its sub contexts
        ├── ...
        └── OtherContextN/
```


## Creating your own test suites


### Extending

[EzContext](Context/EzContext.php) is the base class for all [eZ Publish](http://ez.no/Products/The-eZ-Publish-Platform) BDD testing which will give you the kernel and all methods/sentences to manipulate objects (see already available at [Object contexts](https://github.com/ezsystems/BehatBundle/tree/master/Context/Object)). This is the way to go if you want only the kernel and the object managers.

To test browser interfaces you can extend [Browser context](Context/Browser/Context.php), which has several interactions defined on [CommonActions](Context/Browser/SubContext/CommonActions.php)

For REST interaction there is [REST context](https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Bundle/EzPublishRestBundle/Features/Context/Rest.php), all basic operations are already defined.

For a completely clean context you don't need anything but Behat/Mink and implement [KernelAwareContext](https://github.com/Behat/Symfony2Extension/blob/master/src/Behat/Symfony2Extension/Context/KernelAwareContext.php) if you need access to kernel (take a look at how it's done in [EzContext](Context/EzContext.php).


### Profiles

Again, it is an easy task:

1. Go to your bundle, add the structure mentioned on [Structure](#structure) (this is *not* a requirement)
2. Then you just need to pick what you want to test, since your context must extend a class from BehatBundle (if you want skip some boilerplate)
3. Now add the profile to `behat.yml` it should look like:
```yaml
    yourProfileName:
        suites:
            yourSuiteName:
                contexts:
                    - Namespace\To\Your\Context:
                        parameterA: valueA
                        parameterB: valueB
                    - Namespace\To\Another\Context
                paths:
                    - path/to/your/features/directory
                    - a/totally/different/path
                filters:
                    tags: @someTag && ~@undesiredTag
```

You're ready to go, create your feature files and then run:
 * `$ php bin/behat --profile yourProfileName [--suite yourSuiteName]`

Once more, for more details you can check [Behat Yaml configuration](http://docs.behat.org/en/latest/guides/5.suites.html) at [Behat official website](http://behat.org/en/latest)


## Known issues

* [SetupWizard](https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Bundle/EzPublishLegacyBundle/Features/Context/SetupWizard/Context.php) profile needs _PROD_ enviroment defined (otherwise it will find duplicated html elements)
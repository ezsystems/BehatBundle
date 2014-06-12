## eZ Publish Behat Bundle


**Important** - Behat Bundle is a work in progress (it can dramatically change)


### Behat Bundle, what is it for?

Behat Bundle brings the Behavior Driven Development (as known as BDD) into
the eZ Publish.


### How to install

Probably it's already installed, to check if it is, jump to [How to run tests](#how-to-run-tests)

If not, it's easy to do:

1. Get Behat bundle:
  * Through [composer](http://getcomposer.org), run on console:
    * `$ php composer.phar require "ezsystems/behatbundle": "*"`
  * Through Git:
    * `$ git clone https://github.com/ezsystems/BehatBundle vendor/ezsystems/behatbundle/EzSystems/BehatBundle`
  * Download from GitHub:
    * download BehatBundle and unzip it in `<ezpublish-root>/vendor/ezsystems/behatbundle/EzSystems/BehatBundle`
2. Add Behat Bundle to load list
  * Edit `<ezpublish-roo>/ezpublish/EzPublishKernel.php`
  * Add `new EzSystems\BehatBundle\EzSystemsBehatBundle(),` to `$bundles` in `EzPublishKernel::registerBundles()`


### Configure Behat Yaml

The `behat.yml` is located on the eZ Publish root folder.

You need to change:
  * `base_url` (and `rest_url` in case of testing rest) to your website url
  * `javascript_session` if you need to interact with JS (JavaScript), you can choose:
    * [Selenium2](http://docs.seleniumhq.org/download/)
    * [Sahi](http://sahi.co.in/sahi-open-source/)

For more information you can check [Behat Yaml configuration](http://docs.behat.org/guides/7.config.html)
at [Behat official website](http://behat.org)

If you're having problems in setup the Selenium2/Sahi, take a look at the [.travis.yml](./.travis.yml).


### How to run tests

Simply run:
  * $ php bin/behat --profile <profile>

Defined profiles:
  * `demo` (todo)   - tests Demo interface
  * `admin` (todo)  - tests legacy Admin2 interface
  * `restApi`       - tests REST API
  * `publicApi` (todo)  - tests Public API
  * `command` (todo)    - test eZ Publish commands
  * `installDemoContent`- it run Setup Wizard and install Demo with content
  * `installDemoClean`  - it run Setup Wizard and install Demo without content
  * `demoContent` - it checks the Demo installation content (only)


### Make your testing profile

Again, it a easy task:

1. Go to your bundle, add this structure (this is *not* a requirement):
  * Main folder for BDD
    * `<YourBundle>/BddTests`
  * Context folder with sentences implementations
    * `<YourBundle>/BddTests/Context`
    * `<YourBundle>/BddTests/Context/<YourBundle>Context.php`
  * Feature folder with scenarios
    * `<YourBundle>/BddTests/Feature`
    * `<YourBundle>/BddTests/Feature/some-feature.feature`
    * `<YourBundle>/BddTests/Feature/another-feature.feature`
2. Then you just need to pick what you want to test, since your context must extend a class from BehatBundle:
  * Browser - extend `EzSystems\BehatBundle\Context\BrowserContext`
  * Any API - extend `EzSystems\BehatBundle\Context\ApiContext`
    * this is a clean context with only the object creation for given steps
  * RestAPI with our sentences - extend `eZ\Bundle\EzPublishRestBundle\BddTests\Context\RestContext`
  * PublicAPI with our sentences - (todo)
3. Now add the profile to `behat.yml` it should look like:
```yaml
someProfileName:
    context:
        class: <YourBundleNamespace>\BddTests\<YourBundle>Context
    paths:
        features: <PathToYourBundle>/BddTests/Feature
```

You're ready to go, create your feature files and then run:
 * `$ php bin/behat --profile someProfileName`


Once more, for more details you can check [Behat Yaml configuration](http://docs.behat.org/guides/7.config.html)
at [Behat official website](http://behat.org)

# Running tests

## Configuration

In order to use BehatBundle you need to use `behat` Symfony environment (which is defined in eZ Platform by default). It is also recommended to run in enabled debug mode, which gives you more detailed browser screenshots in case of failure.

The standard behat configuration file is [behat.yml.dist](https://github.com/ezsystems/ezplatform/blob/2.5/behat.yml.dist), located in eZ Platform's main directory. There you can:
- in the `Behat\MinkExtension` section:
  - set the URL of your website for browser testing (`base_url` parameter)
  - set driver configuration (for example `wd_host` for Selenium Server)
  - see [MinkExtension documentation](https://github.com/Behat/MinkExtension/blob/master/doc/index.rst) for more information
- in the `Bex\Behat\ScreenshotExtension` section:
  - set your Cloudinary account details (`cloud_name` and `preset` parameter) to specify where the screenshots are uploaded
  - see [Cloudinary screenshot driver doc](https://github.com/ezsystems/behat-screenshot-image-driver-cloudinary/blob/master/README.md) for more details about the Cloudinary integration and [elvetemedve/behat-screenshot](https://github.com/elvetemedve/behat-screenshot) for other screenshots configuration options 
- in the `Behat\Symfony2Extension` section:
  - set Symfony environment (`env` parameter)
  - enable Symfony debug (`debug` parameter)
  - See [Symfony2Extension documentation](https://github.com/Behat/Symfony2Extension/blob/master/doc/index.rst) for more information

Behat profiles and suites are not defined in this file, but imported from files specified at the bottom.

### Running browser tests

If you want to run browser tests you need to have Selenium Server runnning. One way of doing this is running our Docker stack, which has built-in support for it: see [eZ Platform Docker blueprints](https://github.com/ezsystems/ezplatform/blob/master/doc/docker/README.md#behat-and-selenium-use) documentation for more information.

Another way is to use the Selenium Server Docker container and setting it up manually. Look at [ezplatform's .env file](https://github.com/ezsystems/ezplatform/blob/master/.env#L17) for the currently used version.

It can be set up using:
`docker run -p 4444:4444 -p 5900:5900 --shm-size=1gb -d --name=containerName selenium/standalone-chrome-debug:3.141.59`

Where: 
- 4444 is the port where Selenium Server will be accessible 
- 5900 is the port where the VNC client is accessible (to preview running tests) 
- shm-size is related to Chrome containers requiring more memory (see [Selenium container configuration](https://github.com/ezsystems/ezplatform/blob/master/doc/docker/selenium.yml#L16))

After the container is set up correctly you need to adjust the configuration of `selenium2` driver in `behat.yml.dist` file

## Running tests

### Running standard Behat tests

BehatBundle comes with a wrapper for the standard Behat runner: [ezbehat](../bin/ezbehat.sh) to make running tests in parallel easier.

Use:
```
# standard Behat runner
bin/ezbehat --mode=standard --profile=profileName --tags=exampleTag
bin/ezbehat -m=standard -p=profileName -s=suiteName -t=exampleTag
```
```
# parallel Behat runner
bin/ezbehat -m=parallel -p=profileName -s=suiteName
bin/ezbehat --profile=profileName --suite=suiteName
```

Running Behat feature files in parallel (on the available number of CPUs) is the default option when mode is not specified. See the script documentation for more examples.

## Existing test profiles and suites

By convention profiles and suites are defined in the `behat_suites.yml` file in each bundle, if they exist. See [BehatBundle suites](../behat_suites.yml) and [AdminUI suites](https://github.com/ezsystems/ezplatform-admin-ui/blob/master/behat_suites.yml) for examples.

In order to run them, execute:
- `bin/ezbehat --profile=behat --suite=examples` (BehatBundle usage examples)
- `bin/ezbehat --profile=adminui --suite=adminui` (all AdminUI tests)

## Previewing browser tests

The Selenium Server container comes with VNC server that allows you to preview browser tests when they're running. It runs on port 5900 and is protected by password `secret`. 

See [Docker Selenium documentation on debugging](https://github.com/SeleniumHQ/docker-selenium#debugging) for more details.

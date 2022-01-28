Feature: Set up compatibility layer

    @compatibilityLayer
  Scenario: Set up new repository and make the default one unaccessible
    # Given I append configuration to "webpack_encore.builds" in "config/packages/ibexa_assets.yaml"
    # """
    #     ezplatform: '%kernel.project_dir%/public/assets/ezplatform/build'
    # """
    # And I append configuration to "framework.assets.packages" in "config/packages/ibexa_assets.yaml"
    # """
    #     ezplatform:
    #         json_manifest_path: '%kernel.project_dir%/public/assets/ezplatform/build/manifest.json'
    # """
    # And  I create a file "ez.webpack.config.js" with contents
    # """
    #     const path = require('path');
    #     const bundles = require('./var/encore/ibexa.config.js');
    #     const ibexaConfigManager = require('./ibexa.webpack.config.manager.js');
    #     const configManagers = require('./var/encore/ibexa.config.manager.js');

    #     module.exports = (Encore) => {
    #         Encore.setOutputPath('public/assets/ezplatform/build')
    #             .setPublicPath('/assets/ezplatform/build')
    #             .addExternals({
    #                 react: 'React',
    #                 'react-dom': 'ReactDOM',
    #                 moment: 'moment',
    #                 'popper.js': 'Popper',
    #                 alloyeditor: 'AlloyEditor',
    #                 'prop-types': 'PropTypes',
    #             })
    #             .enableSassLoader()
    #             .enableReactPreset()
    #             .enableSingleRuntimeChunk();

    #         bundles.forEach((configPath) => {
    #             const addEntries = require(configPath);

    #             addEntries(Encore);
    #         });

    #         const eZConfig = Encore.getWebpackConfig();

    #         eZConfig.name = 'ezplatform';

    #         eZConfig.module.rules[4].oneOf[1].use[1].options.url = false;
    #         eZConfig.module.rules[1].oneOf[1].use[1].options.url = false;

    #         configManagers.forEach((configManagerPath) => {
    #             const configManager = require(configManagerPath);

    #             configManager(eZConfig, ibexaConfigManager);
    #         });

    #         Encore.reset();

    #         return eZConfig;
    #     };
    # """
    # And I set configuration to "ibexa\.compatibility_layer\.%project_edition%" in "config/routes/ibexa_compatibility_layer.yaml"
    # """
    #     resource: '@IbexaCompatibilityLayerBundle/Resources/config/deprecated_routing_%project_edition%.yaml'
    # """
    And I apply the patch
"""
From 07821467c6143055eb6b61d25288253ecfea69f4 Mon Sep 17 00:00:00 2001
From: =?UTF-8?q?Marek=20Noco=C5=84?= <mnocon@users.noreply.github.com>
Date: Fri, 28 Jan 2022 12:46:07 +0100
Subject: Enable CompatibilityLayer

This reverts commit 67757bbf915f17512e07ccc2e6ef35ba9d18b30a.
---
 src/Kernel.php    | 3 ++-
 webpack.config.js | 4 +++-
 2 files changed, 5 insertions(+), 2 deletions(-)

diff --git a/src/Kernel.php b/src/Kernel.php
index 4e189f8..13e7d8c 100644
--- a/src/Kernel.php
+++ b/src/Kernel.php
@@ -1,10 +1,10 @@
-
 <?php
 
 declare(strict_types=1);
 
 namespace App;
 
+use Ibexa\Bundle\CompatibilityLayer\Kernel\BundleCompatibilityTrait;
 use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
 use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
 use Symfony\Component\HttpKernel\Kernel as BaseKernel;
@@ -13,6 +13,7 @@ use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
 class Kernel extends BaseKernel
 {
     use MicroKernelTrait;
+    use BundleCompatibilityTrait;
 
     protected function configureContainer(ContainerConfigurator $container): void
     {
diff --git a/webpack.config.js b/webpack.config.js
index d53b0b5..84825b7 100644
--- a/webpack.config.js
+++ b/webpack.config.js
@@ -1,6 +1,8 @@
 const Encore = require('@symfony/webpack-encore');
 const path = require('path');
+const getEzConfig = require('./ez.webpack.config.js');
 const getIbexaConfig = require('./ibexa.webpack.config.js');
+const eZConfig = getEzConfig(Encore);
 const ibexaConfig = getIbexaConfig(Encore);
 const customConfigs = require('./ibexa.webpack.custom.configs.js');
 
@@ -36,7 +38,7 @@ Encore.addEntry('welcome_page', [
 Encore.addEntry('app', './assets/app.js');
 
 const projectConfig = Encore.getWebpackConfig();
-module.exports = [ ibexaConfig, ...customConfigs, projectConfig ];
+module.exports = [ eZConfig, ibexaConfig, ...customConfigs, projectConfig ];
 
 // uncomment this line if you've commented-out the above lines
 // module.exports = [ eZConfig, ibexaConfig, ...customConfigs ];
-- 
2.34.1
"""

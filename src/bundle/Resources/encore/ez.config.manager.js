const path = require('path');

module.exports = (eZConfig, eZConfigManager) => {

    const dragMockScriptPath = '../public/js/scripts/drag-mock.js';
    const seleniumDebugStylePath = '../public/css/selenium-debug.css';

    eZConfigManager.add({
        eZConfig,
        entryName: 'ezplatform-admin-ui-layout-css',
        newItems: [
            path.resolve(__dirname, seleniumDebugStylePath)
        ]
    });

    eZConfigManager.add({
        eZConfig,
        entryName: 'ezplatform-admin-ui-security-base-css',
        newItems: [
            path.resolve(__dirname, seleniumDebugStylePath)
        ]
    });

    if (eZConfig.entry['ezplatform-page-builder-edit-js']) {
        const dragMockScriptPath = '../public/js/scripts/drag-mock.js';
        eZConfigManager.add({
            eZConfig,
            entryName: 'ezplatform-page-builder-edit-js',
            newItems: [
                path.resolve(__dirname, dragMockScriptPath),
            ],
        });
    }
    
    if (eZConfig.entry['ezplatform-form-builder-common-js']) {
        eZConfigManager.add({
            eZConfig,
            entryName: 'ezplatform-form-builder-common-js',
            newItems: [
                path.resolve(__dirname, dragMockScriptPath),
            ],
        });
    }

    if (eZConfig.entry['ezcommerce-shop-pagelayout-css']) {
        eZConfigManager.add({
            eZConfig,
            entryName: 'ezcommerce-shop-pagelayout-css',
            newItems: [
                path.resolve(__dirname, seleniumDebugStylePath)
            ]
        });
    }
};

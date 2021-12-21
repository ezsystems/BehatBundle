const path = require('path');

module.exports = (eZConfig, eZConfigManager) => {
    const addEntry = ([entryName, newItems]) => {
        if (eZConfig.entry[entryName]) {
            eZConfigManager.add({
                eZConfig,
                entryName,
                newItems,
            });
        }
    };
    const seleniumDebugStylePath = path.resolve(__dirname, '../public/css/selenium-debug.css');
    const dragMockScriptPath = path.resolve(__dirname, '../public/js/scripts/drag-mock.js');
    const transitionListenerScriptPath = path.resolve(__dirname, '../public/js/scripts/transition-listener.js');
    const scriptsMap = {
        'ibexa-commerce-shop-pagelayout-css': [seleniumDebugStylePath],
        'ibexa-admin-ui-content-type-edit-js': [dragMockScriptPath],
        'ibexa-admin-ui-layout-css': [seleniumDebugStylePath],
        'ibexa-admin-ui-layout-js': [transitionListenerScriptPath],
        'ibexa-admin-ui-security-base-css': [seleniumDebugStylePath],
        'ibexa-form-builder-common-js': [dragMockScriptPath],
        'ibexa-page-builder-edit-js': [dragMockScriptPath],
    };

    Object.entries(scriptsMap).forEach(addEntry);
};

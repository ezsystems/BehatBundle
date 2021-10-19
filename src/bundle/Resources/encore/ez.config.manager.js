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
        'ezcommerce-shop-pagelayout-css': [seleniumDebugStylePath],
        'ezplatform-admin-ui-content-type-edit-js': [dragMockScriptPath],
        'ezplatform-admin-ui-layout-css': [seleniumDebugStylePath],
        'ezplatform-admin-ui-layout-js': [transitionListenerScriptPath],
        'ezplatform-admin-ui-security-base-css': [seleniumDebugStylePath],
        'ezplatform-form-builder-common-js': [dragMockScriptPath],
        'ezplatform-page-builder-edit-js': [dragMockScriptPath],
    };

    Object.entries(scriptsMap).forEach(addEntry);
};

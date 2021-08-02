const path = require('path');

module.exports = (eZConfig, eZConfigManager) => {
    eZConfigManager.add({
        eZConfig,
        entryName: 'ezplatform-admin-ui-layout-css',
        newItems: [
            path.resolve(__dirname, '../public/css/debug.css')
        ]
    });

    eZConfigManager.add({
        eZConfig,
        entryName: 'ezplatform-admin-ui-security-base-css',
        newItems: [
            path.resolve(__dirname, '../public/css/debug.css')
        ]
    });

    if (!eZConfig.entry['ezplatform-page-builder-edit-js'] || !eZConfig.entry['ezplatform-form-builder-common-js']) {
        return;
    }
    
    const dragMockScriptPath = '../public/js/scripts/drag-mock.js';
    eZConfigManager.add({
        eZConfig,
        entryName: 'ezplatform-page-builder-edit-js',
        newItems: [
            path.resolve(__dirname, dragMockScriptPath),
        ],
    });
    eZConfigManager.add({
        eZConfig,
        entryName: 'ezplatform-form-builder-common-js',
        newItems: [
            path.resolve(__dirname, dragMockScriptPath),
        ],
    });
};

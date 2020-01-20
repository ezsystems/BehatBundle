const path = require('path');

module.exports = (eZConfig, eZConfigManager) => {
    const scriptPath = '../public/js/scripts/drag-mock.js';
    eZConfigManager.add({
        eZConfig,
        entryName: 'ezplatform-page-builder-edit-js',
        newItems: [
            path.resolve(__dirname, scriptPath),
        ],
    });
    eZConfigManager.add({
        eZConfig,
        entryName: 'ezplatform-form-builder-common-js',
        newItems: [
            path.resolve(__dirname, scriptPath),
        ],
    });
};

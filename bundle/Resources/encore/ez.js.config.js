const path = require('path');

module.exports = (Encore) => {
    Encore.addEntry(
        'intprog-feature-flag-js',
        [
            path.resolve(__dirname, '../assets/javascript/dashboard.js'),
        ]
    )
};

const path = require('path');

module.exports = (Encore) => {
    Encore.addStyleEntry(
        'intprog-feature-flag-dashboard-css',
        [path.resolve(__dirname, '../assets/scss/dashboard.scss')]
    )
};

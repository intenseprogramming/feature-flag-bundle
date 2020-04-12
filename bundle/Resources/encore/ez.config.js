const addJSEntries = require('./ez.js.config');
const addCSSEntries = require('./ez.css.config');

module.exports = (Encore) => {
    addJSEntries(Encore);
    addCSSEntries(Encore);
};

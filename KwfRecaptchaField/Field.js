var $ = require('jquery');
var fieldRegistry = require('kwf/commonjs/frontend-form/field-registry');
var Field = require('kwf/commonjs/frontend-form/field/field');
var kwfExtend = require('kwf/commonjs/extend');

var Recaptcha = kwfExtend(Field, {
    initField: function() {
        var config = this.form.getFieldConfig(this.getFieldName());
        $.getScript('https://www.google.com/recaptcha/api.js?onload=' + config.callback + '&render=' + config.key);

        window[config.callback] = (function() {
            this.setToken(config.key, config.actionName);
        }).bind(this);

        this.form.on('submitSuccess', function() {
            this.setToken(config.key, config.actionName);
        }, this);
    },

    getFieldName: function() {
        return this.el.data('fieldName');
    },

    setToken: function (key, actionName) {
        window.grecaptcha.execute(key, {action: actionName}).then((function(token) {
            this.el.find('input').val(token);
        }).bind(this));
    }
});

fieldRegistry.register('kwfRecaptchaFieldField', Recaptcha);
module.exports = Recaptcha;

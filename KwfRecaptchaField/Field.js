var $ = require('jquery');
var fieldRegistry = require('kwf/frontend-form/field-registry');
var Field = require('kwf/frontend-form/field/field');
var kwfExtend = require('kwf/extend');
var recaptchaLoader = require('kwfRecaptchaField/KwfRecaptchaField/loader');

var Recaptcha = kwfExtend(Field, {
    initField: function() {
        var config = this.form.getFieldConfig(this.getFieldName());

        recaptchaLoader(config.key, (function() {
            this.setToken(config.key, config.actionName);
        }).bind(this));

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

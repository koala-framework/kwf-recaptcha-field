# kwf-recaptcha-field
Koala Framework Form Field for Google Recaptcha protection

### Installation

* Create new reCAPTCHA API key on [here](https://www.google.com/recaptcha/admin)
* Add keys to `config.ini`

        ...
        kwfRecaptchaField.key = xxx
        kwfRecaptchaField.secret = xxx
        ...

* register js field in `Web.defer.js`

        require('kwfRecaptchaField/KwfRecaptchaField/Field');

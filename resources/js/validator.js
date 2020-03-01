import veeRu from 'vee-validate/dist/locale/ru';
import VeeValidate, { Validator } from 'vee-validate';


// Localize takes the locale object as the second argument (optional) and merges it.
Validator.localize('ru', veeRu);
Validator.extend('phone', {
    getMessage: (field) => 'Номер некорректен.',
    validate: function (value) {
        var v = value.match(/\d+/g).join("");
        return v.length === 10;
    }
});

Vue.use(VeeValidate);
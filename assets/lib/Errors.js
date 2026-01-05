export default class Errors {
    static showErrors($form, errors) {
        let $errorsBlock = $form.find('.errors');

        if (Array.isArray(errors)) {
            for (let fieldName in errors) {
                errors[fieldName].forEach((error) => {
                    this.showError($errorsBlock, fieldName, error);
                });
            }
        }

        if (typeof errors === 'string') {
            this.showError($errorsBlock, '', errors);
        }
    }

    static showError($block, fieldName, error) {
        $block
            .append(
                `<div class="alert alert-danger" role="alert">${fieldName}: ${error}</div>`
            )
            .css('display', 'none')
            .slideDown(this.animationSpeed);
    }

    static hideErrors($form, callback = null) {
        $form.find('.alert-danger').slideUp(this.animationSpeed, (evt) => {
            $form.find('.alert-danger').remove();

            if (callback) callback();
        });
    }
}

import { Controller } from '@hotwired/stimulus';
import { Toast } from 'bootstrap';
import Errors from '../../lib/Errors.js';

export default class extends Controller {
    static targets = ['form'];
    static animationSpeed = 200;

    #$form = null;

    connect() {
        this.#init();
    }

    #init() {
        if (!this.#$form) this.#$form = $(this.formTarget);

        $('.toast .toast-body').text(trans('inventory.access.successful'));

        this.#$form.off('submit').on('submit', (evt) => {
            evt.preventDefault();
            this.#doPost($(evt.target));
        });
    }

    #doPost($form) {
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: new FormData($form.get(0)),
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.status) {
                    Errors.hideErrors($form);
                    new Toast($('.toast').get(0)).show();
                }
            },
            error: (response) => {
                Errors.hideErrors($form, () => {
                    Errors.showErrors($form, response.responseJSON.errors);
                });
            },
        });
    }
}

import { Controller } from '@hotwired/stimulus';
import { Modal, Toast } from 'bootstrap';
import Errors from '../../lib/Errors.js';

export default class extends Controller {
    #$form = null;

    connect() {
        this.#$form = $(this.element);

        this.#$form.on('submit', (evt) => {
            evt.preventDefault();

            this.#doPost();
        });
    }

    #doPost() {
        $.ajax({
            url: this.#$form.attr('action'),
            method: 'POST',
            data: new FormData(this.#$form.get(0)),
            processData: false,
            contentType: false,
            success: (response) => {
                console.log(response);

                if (response.status) {
                    Errors.hideErrors(this.#$form);
                    new Toast($('.toast').get(0)).show();
                    Modal.getInstance($('#mergeInventory').get(0)).hide();

                    let $list = $('#inventory-container');
                    $list.attr('src', $list.attr('src'));
                }
            },
            error: (response) => {
                console.log(response);

                if (response.responseJSON.errors) {
                    Errors.hideErrors(this.#$form, () => {
                        Errors.showErrors(this.#$form, response.responseJSON.errors);
                    });
                }
            },
        });
    }
}

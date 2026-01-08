import { Controller } from '@hotwired/stimulus';
import { Toast } from 'bootstrap';
import Errors from '../../lib/Errors.js';

export default class extends Controller {
    static targets = ['form'];
    static animationSpeed = 200;

    connect() {
        this.#init();

        $(this.formTarget).on('submit', (evt) => {
            evt.preventDefault();
            this.#doPost($(evt.target));
        });
    }

    #init() {
        $('.toast .toast-body').text(trans('inventory.update.successful'));
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
                    new Toast($('.toast').get(0)).show();
                    Errors.hideErrors($form);

                    let $list = $('#inventory-container');
                    $list.attr('src', $list.attr('src'));
                }
            },
            error: (response) => {
                console.log(response);

                if (response.responseJSON.status === 'conflict') {
                    let mine = JSON.parse(response.responseJSON.mine);
                    let theirs = JSON.parse(response.responseJSON.theirs);
                }

                if (response.responseJSON.errors) {
                    Errors.hideErrors($form, () => {
                        Errors.showErrors($form, response.responseJSON.errors);
                    });
                }
            },
        });
    }
}

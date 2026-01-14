import { Controller } from '@hotwired/stimulus';
import { Toast } from 'bootstrap';
import Errors from '../../lib/Errors.js';

export default class extends Controller {
    static targets = ['form'];
    static animationSpeed = 200;

    connect() {
        this.#init();
    }

    #init() {
        $('.toast .toast-body').text(trans('inventory.create.create_successful'));

        $(this.formTarget)
            .off('submit')
            .on('submit', (evt) => {
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
                    new Toast($('.toast').get(0)).show();
                    Errors.hideErrors($form);
                    this.#clearFields($form);

                    let $list = $('#inventories-list');
                    let src = $list.attr('src');
                    $list.attr('src', src);
                }
            },
            error: (response) => {
                Errors.hideErrors($form, () => {
                    Errors.showErrors($form, response.responseJSON.errors);
                });
            },
        });
    }

    #clearFields($form) {
        $form.find('[type="text"]').each((idx, el) => {
            $(el).val(null);
        });

        $form.find('select').each((idx, el) => {
            if (!$(el).attr('name').includes('category')) if (el.tomselect) el.tomselect.clear();
        });

        $('[type="file"]').val(null);
        $('[type="file"]').trigger('change');
        window.editors[$form.find('textarea').attr('id')].value('');
    }
}

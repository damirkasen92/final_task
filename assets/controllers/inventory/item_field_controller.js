import { Controller } from '@hotwired/stimulus';
import { Toast } from 'bootstrap';
import Errors from "../../lib/Errors.js";

export default class extends Controller {
    #$form = null;
    #duration = 300;

    connect() {
        this.#$form = $('[name="item_field"]');
        this.#init();
    }

    delete(evt) {
        let $btn = $(evt.target);
        let url = $btn.data('delete');
        let $checkboxes = $('[type="checkbox"]:checked');
        let itemFieldIds = Array.from($checkboxes).map((el) => $(el).val());

        $.ajax({
            url,
            method: 'DELETE',
            data: {
                itemFieldIds,
            },
            success: (response) => {
                console.log(response);
                if (!response.status) return;

                $checkboxes.parents('tr').find('td').wrapInner("<div class='slide'></div>");
                $('.slide').slideUp(() => {
                    $checkboxes.parents('tr').remove();
                });
            }
        });
    }

    #init() {
        this.#$form.on('submit', (evt) => {
            evt.preventDefault();

            $.post({
                url: this.#$form.attr('action'),
                data: new FormData(this.#$form[0]),
                processData: false,
                contentType: false,
                success: (response) => {
                    Errors.hideErrors(this.#$form);
                    this.#refreshContainer();
                    /** */
                    let toast = new Toast($('.toast')[0]);
                    $('.toast').find('.toast-body').text('Item Field was created successfully');
                    toast.show();
                    /** */

                    this.#$form.find('[type="text"]').val(null);
                    easyMDE.value('');
                },
                error: (response) => {
                    if (this.#$form.find(".errors").children().length) {
                        Errors.hideErrors(this.#$form, () => {
                            Errors.showErrors(this.#$form, response.responseJSON.errors);
                        });
                    } else Errors.showErrors(this.#$form, response.responseJSON.errors);
                }
            });
        });
    }

    #refreshContainer() {
        let $container = $('#inventory-item-fields');
        let src = $container.attr('src');
        $container.attr('src', src);
    }
}

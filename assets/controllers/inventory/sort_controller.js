import { Controller } from '@hotwired/stimulus';
import { Toast } from 'bootstrap';
import Sortable from 'sortablejs';

export default class extends Controller {
    #sortable;
    #$toast;
    #Toast;
    #duration = 200;

    connect() {
        this.#$toast = $('.toast');
        this.#Toast = new Toast(this.#$toast.get(0));
        this.#sortable = $('#sortable').get(0);

        new Sortable(this.#sortable, {
            animation: this.#duration,
            onEnd: (evt) => {
                $.ajax({
                    data: {
                        inventoryId: $('[data-inventory-id]').attr(
                            'data-inventory-id'
                        ),
                        order: this.#reorderList(),
                    },
                    url: $('[data-reorder-path]').data('reorder-path'),
                    method: 'PATCH',
                    success: (response) => {
                        // console.log(response);
                    },
                    error: (response) => {
                        if (evt.oldIndex !== evt.newIndex) {
                            this.#returnElement(evt);
                            this.#showToast(trans('sort.error'));
                        }
                    },
                });
            },
        });
    }

    #reorderList() {
        const map = {};

        $(this.#sortable)
            .children()
            .each((idx, el) => {
                $(el).attr('data-order', idx + 1);
                map[$(el).attr('data-id')] = $(el).attr('data-order');
            });

        return map;
    }

    #returnElement(evt) {
        $(evt.item).slideUp(this.#duration, () => {
            if (evt.newIndex > evt.oldIndex) {
                $('#sortable').children().eq(evt.oldIndex).before(evt.item);
            } else {
                $('#sortable').children().eq(evt.oldIndex).after(evt.item);
            }

            $(evt.item).slideDown(this.#duration);
        });
    }

    #showToast(message) {
        this.#$toast.find('.toast-body').text(message);
        this.#Toast.show();
    }
}

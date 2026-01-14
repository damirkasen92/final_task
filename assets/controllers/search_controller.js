import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input'];

    #$input = null;
    #timer = null;
    #delay = 500;

    connect() {
        if (!this.#$input) {
            this.#$input = $(this.inputTarget);
        }

        this.#$input.off('input').on('input', (e) => {
            clearTimeout(this.#timer);

            this.#timer = setTimeout(() => {
                $('#inventories-list, #items_list').attr('src', this.#$input.data('path') + '?q=' + e.target.value);
            }, this.#delay);
        });
    }
}

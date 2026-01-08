import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input'];

    #timer = null;
    #delay = 500;

    connect() {
        const $input = $(this.inputTarget);

        $input.on('input', async (e) => {
            clearTimeout(this.#timer);

            this.#timer = setTimeout(() => {
                $('#inventories-list').attr(
                    'src',
                    $input.data('path') + '?q=' + e.target.value
                );
            }, this.#delay);
        });
    }
}

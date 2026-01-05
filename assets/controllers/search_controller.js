import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input'];

    #timer = null;

    connect() {
        const $input = $(this.inputTarget);

        $input.on('input', async (e) => {
            clearTimeout(this.#timer);

            this.#timer = setTimeout(() => {
                $('#inventories-list').attr(
                    'src',
                    'inventories/list?q=' + e.target.value
                );
            }, 500);
        });
    }
}

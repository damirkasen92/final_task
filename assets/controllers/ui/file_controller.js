import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    #duration = 200;
    #$element = null;

    connect() {
        if (!this.#$element) {
            this.#$element = $(this.element);
        }

        this.#$element.off('change').on('change', (e) => {
            const file = e.target.files[0];
            const $preview = $('#preview');
            const $fileNameSpan = $('#file-name');
            const initialText = $fileNameSpan.text();

            if (file) {
                const reader = new FileReader();

                reader.onload = (ev) => {
                    $preview.attr('src', ev.target.result);
                    $preview.slideDown(this.#duration);
                };

                reader.readAsDataURL(file);
            } else {
                $preview.attr('src', null);
            }

            $fileNameSpan.text(file ? file.name : initialText);
        });
    }
}

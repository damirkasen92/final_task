import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        $(this.element).on('change', function (e) {
            const file = e.target.files[0];
            const $preview = $('#preview');

            if (file) {
                const reader = new FileReader();

                reader.onload = function (ev) {
                    $preview.attr('src', ev.target.result);
                    $preview.slideDown(200);
                };

                reader.readAsDataURL(file);
            } else {
                $preview.attr('src', null);
            }
        });

        const $input = $(this.element);
        const $fileNameSpan = $('#file-name');
        const initialText = $fileNameSpan.text();

        $input.on('change', function () {
            const file = $input[0].files[0];
            $fileNameSpan.text(file ? file.name : initialText);
        });
    }
}

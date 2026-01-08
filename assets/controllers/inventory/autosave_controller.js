import { Controller } from '@hotwired/stimulus';
import { Toast } from 'bootstrap';

export default class extends Controller {
    #$form = null;
    #interval = 8000;
    #duration = 300;
    #intervalId = null;

    connect() {
        return;
        $(() => {
            this.#$form = $(this.element);
            this.#$form.on('input change', (evt) => {
                if ($(evt.target).prop('tagName').toLowerCase() === 'textarea') return;

                this.#markAsDirty(evt.target);
            });

            if ($('textarea').length)
                window.editors[this.#$form.find('textarea').attr('id')].codemirror.on('change', (evt) => {
                    this.#markAsDirty(window.editors[this.#$form.find('textarea').attr('id')].element);
                });

            $(document).on('turbo:before-visit', () => {
                if (this.#intervalId) clearInterval(this.#intervalId);
            });

            this.#intervalId = setInterval(() => {
                this.#handleAutosave();
            }, this.#interval);
        });
    }

    #handleAutosave() {
        if (!this.#hasDirtyFields()) return;

        const data = this.#getDirtyFields();

        $.ajax({
            url: this.#$form.data('autosave-path'),
            method: 'POST',
            data,
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.status) {
                    new Toast('.toast').show();
                }

                if (response.imageUrl) {
                    this.#changePicture(response.imageUrl);
                }

                this.#markFieldsAsClean();
            },
            error: (response) => {},
        });
    }

    #changePicture(url) {
        $('.setting-image').parent().css('height', $('.setting-image').css('height'));

        $('.setting-image').fadeOut(this.#duration, () => {
            $('.setting-image').attr('src', url);

            $('.setting-image').on('load', () => {
                $('.setting-image')
                    .fadeIn(this.#duration)
                    .parent()
                    .removeClass('visually-hidden')
                    .css('height', 'auto');
            });

            $('#preview').fadeOut(this.#duration, () => {
                $('[type="file"]').val(null).trigger('change');
            });
        });
    }

    #markAsDirty(el) {
        $(el).attr('data-is-dirty', true);
    }

    #hasDirtyFields() {
        return this.#$form.find('[data-is-dirty]').length > 0;
    }

    #markFieldsAsClean() {
        this.#$form.find('[data-is-dirty]').removeAttr('data-is-dirty');
    }

    #getDirtyFields() {
        let formData = new FormData();

        formData.append('_token', $('#inventory__token').val());

        this.#$form.find('[data-is-dirty]').each((idx, dirtyEl) => {
            let $el = $(dirtyEl);
            let name = $el.attr('name').match(/\[(.*?)\]/)[1];

            if ($el.is('[multiple]')) {
                name += '[]';

                $el.val().forEach((v) => {
                    formData.append(name, v);
                });
            } else if ($('textarea').length && $el.prop('tagName').toLowerCase() === 'textarea') {
                formData.append(name, window.editors[this.#$form.find('textarea').attr('id')].value());
            } else if ($el.attr('type') === 'file') {
                formData.append(name, $el[0].files[0]);
            } else {
                formData.append(name, $el.val());
            }
        });

        return formData;
    }
}

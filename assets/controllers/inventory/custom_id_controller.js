import { Controller } from '@hotwired/stimulus';
import { Popover } from 'bootstrap';
import Sortable from 'sortablejs';

export default class extends Controller {
    static targets = ['list'];

    #duration = 200;
    #inputDuration = 1000;
    #timer;
    #isLoading = false;
    #$form;

    connect() {
        this.#$form = $(this.listTarget).closest('form');
        this.#initSortable();
        this.#initInputs();
        this.#initSelects();
    }

    #initInputs() {
        this.#$form.find('[type="text"]').on('input', this.#onChange.bind(this));
    }

    #initSelects() {
        $(this.listTarget)
            .find('li')
            .each((_, li) => {
                let $select = $(li).find('select');
                let $icon = $(li).find('[data-help]');
                $select.on('change', () => {
                    this.#updatePopover($select, $icon);
                    this.#onChange();
                });
                this.#updatePopover($select, $icon);
            });
    }

    #updatePopover($select, $icon) {
        let description = $select.find('option:selected').data('description') || '';
        $icon.attr('data-bs-content', description);

        let popover = Popover.getInstance($icon[0]);

        if (popover) {
            popover.setContent({ '.popover-body': `${description}` });
        } else {
            new Popover($icon[0]);
        }
    }

    #initSortable() {
        new Sortable(this.listTarget, {
            animation: this.#duration,
            handle: '.drag-handle',
            onChange: (evt) => {
                this.#onChange();
            },
            onEnd: (evt) => {
                this.#removeElement(evt);
            },
        });
    }

    #doPost() {
        $.post({
            url: this.#$form.attr('action'),
            data: new FormData(this.#$form.get(0)),
            processData: false,
            contentType: false,
            success: (response) => {
                $('#custom-id .errors')
                    .children()
                    .slideUp(this.#duration, () => {
                        $('#custom-id .errors').html(null);
                    });

                if (response.status) {
                    $('.custom-id-preview').text(response.customId).fadeIn(this.#duration);
                } else {
                    this.#showError(response.responseJSON.error);
                }
            },
            error: (response) => {
                this.#showError(response.responseJSON.error);
            },
        });
    }

    #showError(error) {
        $('#custom-id .errors')
            .html(
                `
            <div class="alert alert-danger">
                ${error.replace('\n', '<br />')}
            </div>
        `
            )
            .css('display', 'none')
            .fadeIn(this.#duration);
    }

    #onChange() {
        if (!this.#isLoading) {
            this.#isLoading = true;

            $('.custom-id-preview')
                .html(
                    `
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                `
                )
                .css('display', 'none')
                .fadeIn(this.#duration);
        }

        clearTimeout(this.#timer);

        this.#timer = setTimeout(() => {
            if (this.#isLoading) {
                this.#isLoading = false;
                $('.custom-id-preview').fadeOut(this.#duration, () => {
                    $('.custom-id-preview').html(null);
                    this.#doPost();
                });
            }
        }, this.#inputDuration);
    }

    #removeElement(evt) {
        const rect = this.listTarget.getBoundingClientRect();
        const x = evt.originalEvent.clientX;
        const y = evt.originalEvent.clientY;
        const outside = x < rect.left || x > rect.right || y < rect.top || y > rect.bottom;

        if (outside) {
            $(evt.item).slideUp(this.#duration, () => {
                $(evt.item).remove();
                this.#onChange();
            });
        }
    }

    addElement() {
        const $list = $(this.listTarget);
        const prototype = $list.data('prototype');
        const index = $list.children().length;
        const newForm = prototype.replace(/__name__/g, index);

        $list.append(newForm);

        this.#initInputs();
        this.#initSelects();
        this.#onChange();
    }
}

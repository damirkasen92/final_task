import { Controller } from '@hotwired/stimulus';
import { Popover } from 'bootstrap';

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
        this.#setEvents();
    }

    #setEvents() {
        let onChange = this.#onChange.bind(this);

        this.#$form.find('[type="text"]').on('input', onChange);

        $(this.listTarget)
            .find('li')
            .each((_, li) => {
                let $select = $(li).find('select');
                let $icon = $(li).find('[data-help]');
                $select.on(
                    'change',
                    this.#updatePopover.apply(this, [$select, $icon])
                );
                $select.on('change', this.#updateDateType.bind(this, $select));
                this.#updatePopover($select, $icon);
            });
    }

    #updateDateType($select) {
        let type = $select.find('option:selected').val();
        let $li = $select.parents('.list-group-item');
        $li.find('input').attr('data-type', type);
        this.#doPost();
    }

    #updatePopover($select, $icon) {
        let description =
            $select.find('option:selected').data('description') || '';
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
            onEnd: (evt) => {
                this.#doPost();
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
                console.log(response);

                $('#custom-id .errors').html(null);

                if (response.status) {
                    $('.custom-id-preview').text(response.customId);
                } else {
                    this.#showError(response.error);
                }
            },
            error: (response) => {
                console.log(response);

                this.#showError(response.error);
            },
        });
    }

    #showError(error) {
        $('#custom-id .errors').html(`
            <div class="alert alert-danger">
                ${error.replace('\n', '<br />')}
            </div>
        `);
    }

    #onChange() {
        if (!this.#isLoading) {
            this.#isLoading = true;

            $('.custom-id-preview').html(`
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            `);
        }

        clearTimeout(this.#timer);

        this.#timer = setTimeout(() => {
            this.#doPost();
            if (this.#isLoading) {
                this.#isLoading = false;
                $('.custom-id-preview').html(null);
            }
        }, this.#inputDuration);
    }

    #removeElement(evt) {
        const rect = this.listTarget.getBoundingClientRect();
        const x = evt.originalEvent.clientX;
        const y = evt.originalEvent.clientY;
        const outside =
            x < rect.left || x > rect.right || y < rect.top || y > rect.bottom;

        if (outside) {
            $(evt.item).slideUp(this.#duration, () => {
                $(evt.item).remove();
                this.#doPost();
            });
        }
    }

    addElement() {
        const $list = $(this.listTarget);
        const prototype = $list.data('prototype');
        const index = $list.children().length;
        const newForm = prototype.replace(/__name__/g, index);

        $list.append(newForm);

        this.#setEvents();
    }
}

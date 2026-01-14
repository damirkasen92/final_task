import { Controller } from '@hotwired/stimulus';
import UndoPopover from '../../lib/UndoPopover.js';

export default class extends Controller {
    static targets = ['deleteBtn'];

    #$deleteBtn;
    #popover;

    connect() {
        if (!this.#$deleteBtn) this.#$deleteBtn = $(this.deleteBtnTarget);
        if (!this.#popover)
            this.#popover = new UndoPopover(
                this.deleteBtnTarget,
                trans('popover.undo'),
                trans('popover.popover_title')
            );

        this.#$deleteBtn.off('click').on('click', (evt) => {
            if (this.#getIds().length === 0) return;

            this.#popover.show(() => {
                this.#doPost();
            });
        });
    }

    #doPost() {
        $.ajax({
            url: this.#$deleteBtn.data('delete-path'),
            method: 'DELETE',
            data: {
                inventoryIds: this.#getIds(),
            },
            success: () => {
                let $list = $('#inventories-list');
                let src = $list.attr('src');
                $list.attr('src', src);
            },
        });
    }

    #getIds() {
        return Array.from($('.sub-checkbox:checked')).map((inventory) => $(inventory).data('id'));
    }
}

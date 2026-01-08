import { Controller } from '@hotwired/stimulus';
import Undo from '../../lib/UndoPopover.js';

export default class extends Controller {
    static targets = ['editBtn', 'deleteBtn'];

    connect() {
        $(this.editBtnTarget).on('click', this.#doEdit.bind(this));

        const undo = new Undo(
            this.deleteBtnTarget,
            trans('popover.undo'),
            trans('popover.popover_title')
        );

        $(this.deleteBtnTarget).on('click', (evt) => {
            undo.show(this.#doDelete.bind(this));
        });

        this.#initCheckboxes();
    }

    #doEdit() {
        $('#inventory-container').attr(
            'src',
            $('.table [type="checkbox"]:checked')
                .parents('tr')
                .data('edit-path')
        );
    }

    #doDelete() {
        let $checked = $('.table [type="checkbox"]:checked');
        let itemIds = Array.from($checked).map((item) => $(item).data('id'));

        $.ajax({
            method: 'DELETE',
            url: $(this.deleteBtnTarget).data('path'),
            data: {
                item_ids: itemIds,
            },
            success: (response) => {
                $('#inventory-container').attr(
                    'src',
                    $('#inventory-container').attr('src')
                );
            },
            error: (response) => {},
        });
    }

    #initCheckboxes() {
        $('.table [type="checkbox"]').on('change', (evt) => {
            let checked = $('.table [type="checkbox"]:checked').length;
            $(this.editBtnTarget).prop('disabled', checked !== 1);
            $(this.deleteBtnTarget).prop('disabled', checked === 0);
        });
    }
}

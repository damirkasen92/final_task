import {Controller} from "@hotwired/stimulus";
import UndoPopover from "../../lib/UndoPopover.js";

export default class extends Controller {
    static targets = ['deleteBtn'];

    #$deleteBtn;
    #popover;

    connect() {
        this.#$deleteBtn = $(this.deleteBtnTarget);
        this.#popover = new UndoPopover(
            this.deleteBtnTarget,
            this.#$deleteBtn.data("undo-text"),
            this.#$deleteBtn.data("undo-title")
        );

        this.#$deleteBtn.on('click', (evt) => {
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
                inventoryIds: this.#getIds()
            },
            success: () => {
                let $list = $("#inventories-list");
                let src = $list.attr('src');

                $list.attr('src', src);
            }
        });
    }

    #getIds() {
        return Array.from($(".sub-checkbox:checked")).map(inventory => $(inventory).data('id'));
    }
}

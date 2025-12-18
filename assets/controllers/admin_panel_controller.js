import {Controller} from "@hotwired/stimulus";
import {Popover} from "bootstrap";

export default class extends Controller {
    #popoverSeconds = 3;
    #timer = null;
    #currentPopover = null;
    #mainUrl = null;
    #popoverTitle = null;
    #buttonUndo = null;

    connect() {
        this.#mainUrl = $('#admin-table').attr('src');
        this.#popoverTitle = $('[data-popover-title]').data('popover-title');
        this.#buttonUndo = $('[data-popover-btn]').data('popover-btn');
    }

    #setEventUndoButton() {
        $('[data-undo]').on('click', () => {
            if (this.#timer) clearTimeout(this.#timer);
            this.#destroyPopover();
            $('.admin-toolbar > button').prop('disabled', false);
        });
    }

    #initPopover(popoverTriggerEl) {
        this.#currentPopover = new Popover(popoverTriggerEl, {
            html: true,
            sanitize: false,
            content: `<button class="btn btn-outline-dark w-100" data-undo>${this.#buttonUndo}</button>`,
            title: `${this.#popoverTitle} <span>${this.#popoverSeconds}</span>`,
            trigger: 'manual'
        });

        $(popoverTriggerEl).on('shown.bs.popover', () => {
            this.#setEventUndoButton();
            $('.admin-toolbar > button').prop('disabled', true);
        });
    }

    #destroyPopover() {
        this.#currentPopover.hide();
        this.#currentPopover = null;
    }

    #handleCommand(event, callback) {
        if (this.#getUserIds().length === 0) return;

        let seconds = 1;
        this.#initPopover(event.target);
        this.#currentPopover.show();

        this.#timer = setInterval(() => {
            $(this.#currentPopover.tip)
                .find('.popover-header > span')
                .text(this.#popoverSeconds - seconds);

            seconds++;

            if (seconds <= this.#popoverSeconds) return;

            clearInterval(this.#timer);
            this.#destroyPopover();
            $('.admin-toolbar > button').prop('disabled', false);
            callback();
        }, 1000);
    }

    block(event) {
        this.#handleCommand(event, () => {
            this.#doFetch(event, console.log);
        });
    }

    unblock(event) {
        this.#handleCommand(event, () => {
            this.#doFetch(event, console.log);
        });
    }

    delete(event) {
        this.#handleCommand(event, () => {
            this.#doFetch(event, console.log);
        });
    }

    makeAdmin(event) {
        this.#handleCommand(event, () => {
            this.#doFetch(event, console.log);
        });
    }

    unmakeAdmin(event) {
        this.#handleCommand(event, () => {
            this.#doFetch(event, console.log);
        });
    }

    #doFetch(event, callback = null) {
        $.post({
            url: $(event.target).data('src'),
            data: {
                userIds: this.#getUserIds()
            },
            success: (response) => {
                if (callback)
                    callback(response);

                $('#admin-table').attr('src', this.#mainUrl);
            }
        });
    }

    #getUserIds() {
        return Array.from($(".sub-checkbox:checked")).map(cb => cb.dataset.id);
    }
}

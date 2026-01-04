import { Popover } from "bootstrap";

export default class {
    #currentPopover = null;
    #popoverTriggerEl = null;
    #popoverTitle = null;
    #buttonUndo = null;
    #timer = null;
    #seconds = 1;
    #popoverSeconds = 3;
    #onHideAction = null;

    constructor(popoverTriggerEl, undoText, title) {
        this.#buttonUndo = undoText;
        this.#popoverTitle = title;
        this.#popoverTriggerEl = popoverTriggerEl;
    }

    #makePopover(popoverTriggerEl) {
        this.#currentPopover = new Popover(popoverTriggerEl, {
            html: true,
            sanitize: false,
            content: `<button class="btn btn-dark w-100" data-undo>${
                this.#buttonUndo
            }</button>`,
            title: `${this.#popoverTitle} <span>${this.#popoverSeconds}</span>`,
            trigger: "manual",
        });

        this.#setEventUndoButton(popoverTriggerEl);
    }

    #setEventUndoButton(popoverTriggerEl) {
        $(popoverTriggerEl).on("shown.bs.popover", () => {
            $("[data-undo]").on("click", () => {
                this.#destroyPopover();
            });
        });
    }

    #destroyPopover() {
        if (this.#currentPopover) {
            this.#currentPopover.hide();
            if (this.#onHideAction) {
                this.#onHideAction();
            }
            this.#currentPopover = null;
        }
        if (this.#timer) clearInterval(this.#timer);
        this.#seconds = 1;
    }

    #countToPopoverSeconds() {
        $(this.#currentPopover.tip)
            .find(".popover-header > span")
            .text(this.#popoverSeconds - this.#seconds);

        this.#seconds++;
    }

    setOnHideAction(callback) {
        this.#onHideAction = callback;
    }

    show(callback = null) {
        this.#makePopover(this.#popoverTriggerEl);
        this.#currentPopover.show();

        this.#timer = setInterval(() => {
            this.#countToPopoverSeconds();

            if (this.#seconds > this.#popoverSeconds) {
                this.#destroyPopover();

                if (callback) callback();
            }
        }, 1000);
    }
}

import { Controller } from "@hotwired/stimulus";
import UndoPopover from "../../lib/UndoPopover.js";

export default class extends Controller {
    #mainUrl = null;

    connect() {
        this.#mainUrl = $("#admin-table").attr("src");
    }

    handle(evt) {
        if (this.#getUserIds().length === 0) return;

        const popover = new UndoPopover(
            evt.target,
            $("[data-popover-btn]").data("popover-btn"),
            $("[data-popover-title]").data("popover-title")
        );

        popover.setOnHideAction(() => {
            $(".admin-toolbar > button").prop("disabled", false);
        });
        $(".admin-toolbar > button").prop("disabled", true);

        popover.show(() => {
            this.#doFetch(evt);
        });
    }

    #doFetch(evt, callback = null) {
        $.post({
            url: $(evt.target).data("src"),
            data: {
                userIds: this.#getUserIds(),
            },
            success: (response, textStatus, xhr) => {
                if (callback) callback(response);

                if (response.redirect) {
                    location.href = response.redirect;
                }

                $("#admin-table").attr("src", this.#mainUrl);
            },
        });
    }

    #getUserIds() {
        return Array.from($(".sub-checkbox:checked")).map(
            (cb) => cb.dataset.id
        );
    }
}

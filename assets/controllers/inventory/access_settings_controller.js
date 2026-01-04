import { Controller } from "@hotwired/stimulus";
import { Toast } from "bootstrap";
import Errors from "../../lib/Errors.js";

export default class extends Controller {
    static targets = ["form"];
    static animationSpeed = 200;

    #$form = null;

    connect() {
        this.#$form = $(this.formTarget);
        this.#init();

        this.#$form.on("submit", (evt) => {
            evt.preventDefault();
            this.#doPost($(evt.target));
        });

        // this.#$form.find(".form-check-input").on("change", (evt) => {
        //     evt.preventDefault();
        //     this.#onToggleIsPublic(this.#$form, $(evt.target));
        // });

        // this.#onToggleIsPublic(this.#$form, this.#$form.find(".form-check-input"));
    }

    #init() {
        $("[data-toast]").css("z-index", 2000);
        $(".toast .toast-body").text(
            this.#$form.data("success-message")
        );
    }

    // #onToggleIsPublic($form, $checkInput) {
    //     let isChecked = $checkInput.prop('checked');
    //     let $rowWriters = $form.find(".row-writers");

    //     if (isChecked) {
    //         $rowWriters.slideUp(this.animationSpeed);
    //     } else {
    //         $rowWriters.slideDown(this.animationSpeed);
    //     }
    // }

    #doPost($form) {
        $.ajax({
            url: $form.attr("action"),
            method: "POST",
            data: new FormData($form.get(0)),
            processData: false,
            contentType: false,
            success: (response) => {
                console.log(response);

                if (response.status) {
                    Errors.hideErrors($form);
                    new Toast($(".toast").get(0)).show();
                }
            },
            error: (response) => {
                if ($form.find(".errors").children().length) {
                    Errors.hideErrors($form, () => {
                        Errors.showErrors($form, response.responseJSON.errors);
                    });
                } else Errors.showErrors($form, response.responseJSON.errors);
            },
        });
    }
}

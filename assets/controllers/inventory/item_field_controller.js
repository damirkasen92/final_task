import { Controller } from '@hotwired/stimulus';
import { Toast, Modal } from 'bootstrap';
import Errors from '../../lib/Errors.js';

export default class extends Controller {
    #$form = null;
    #$editForm = null;
    #duration = 300;

    connect() {
        this.#$form = $('#createItemField form');
        this.#$editForm = $('#editItemField form');
        this.#init();
    }

    delete(evt) {
        let $btn = $(evt.target);
        let url = $btn.data('delete');
        let $checkboxes = $('[type="checkbox"]:checked');
        let itemFieldIds = Array.from($checkboxes).map((el) => $(el).val());

        $.ajax({
            url,
            method: 'DELETE',
            data: {
                itemFieldIds,
            },
            success: (response) => {
                if (!response.status) return;

                $checkboxes.parents('tr').find('td').wrapInner("<div class='slide'></div>");

                $('.slide').slideUp(this.#duration, () => {
                    this.#refreshContainer();
                });
            },
            error: (response) => {
                console.error(response);
            },
        });
    }

    #createItemField() {
        $.post({
            url: this.#$form.attr('action'),
            data: new FormData(this.#$form[0]),
            processData: false,
            contentType: false,
            success: (response) => {
                Errors.hideErrors(this.#$form);
                this.#refreshContainer();
                this.#showToast(trans('form.create.success'));
                this.#clearForm(this.#$form);
            },
            error: (response) => {
                Errors.hideErrors(this.#$form, () => {
                    Errors.showErrors(this.#$form, response.responseJSON.errors);
                });
            },
        });
    }

    #updateItemField() {
        $.post({
            url: this.#$editForm.attr('action'),
            data: new FormData(this.#$editForm[0]),
            processData: false,
            contentType: false,
            success: (response) => {
                Errors.hideErrors(this.#$editForm);
                this.#refreshContainer();
                this.#showToast(trans('form.edit.success'));
            },
            error: (response) => {
                Errors.hideErrors(this.#$editForm, () => {
                    Errors.showErrors(this.#$editForm, response.responseJSON.errors);
                });
            },
        });
    }

    #init() {
        this.#$form.on('submit', (evt) => {
            evt.preventDefault();
            this.#createItemField();
        });

        this.#$editForm.on('submit', (evt) => {
            evt.preventDefault();
            this.#updateItemField();
        });

        $('#edit-btn').on('click', () => {
            this.#openEditForm();
        });

        $('#inventory-item-fields').on('turbo:frame-load', this.#initToolbarBtns.bind(this));
    }

    #initToolbarBtns() {
        $('#edit-btn').prop('disabled', true);
        $('#delete-btn').prop('disabled', true);

        $('#sortable [type="checkbox"]').on('click', (evt) => {
            let checked = $('#sortable [type="checkbox"]:checked').length;

            $('#edit-btn').prop('disabled', checked !== 1);
            $('#delete-btn').prop('disabled', checked === 0);
        });
    }

    #openEditForm() {
        let $row = $('[type="checkbox"]:checked').parents('tr');
        let id = $row.data('id');
        let title = $row.data('title');
        let description = $row.data('description');
        let type = $row.data('type');
        let isDisplayed = $row.data('is-displayed');
        let url = $row.data('url');

        this.#fillEditForm({
            id,
            title,
            description,
            type,
            isDisplayed,
            url,
        });

        Modal.getOrCreateInstance('#editItemField').show();
    }

    #fillEditForm(args) {
        let { url, id, title, description, isDisplayed, type } = args;

        this.#$editForm.attr('action', url);
        this.#$editForm.find('[name="item_field[id]"]').val(id);
        this.#$editForm.find('[name="item_field[title]"]').val(title);
        window.editors[this.#$form.find('textarea').attr('id')].value(description);
        this.#$editForm.find('[name="item_field[isDisplayed]"]').prop('checked', isDisplayed);
        this.#$editForm.find('[name="item_field[type]"]').val(type);
    }

    #clearForm($form) {
        $form.find('[type="text"]').val(null);
        window.editors[this.#$form.find('textarea').attr('id')].value('');
    }

    #showToast(text) {
        let toast = new Toast($('.toast')[0]);
        $('.toast').find('.toast-body').text(text);
        toast.show();
    }

    #refreshContainer() {
        let $container = $('#inventory-item-fields');
        let src = $container.attr('src');
        $container.attr('src', src);
    }
}

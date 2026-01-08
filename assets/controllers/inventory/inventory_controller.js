import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        $(this.element)
            .find('button')
            .on('click', function () {
                let $btn = $(this);
                let src = $btn.data('src');

                $('[data-controller="inventory--inventory"]').find('button.active').removeClass('active');
                $btn.addClass('active');
                $('#inventory-container').attr('src', src);
            });
    }
}

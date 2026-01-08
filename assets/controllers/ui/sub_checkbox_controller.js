import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    click() {
        let isEqual =
            $(this.element).parents('table').find('.sub-checkbox').length ===
            $(this.element).parents('table').find('.sub-checkbox:checked').length;

        $(this.element).parents('table').find('.main-checkbox').prop('checked', isEqual);
    }
}

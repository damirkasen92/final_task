import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    click() {
        $(this.element).parents('table').find('.sub-checkbox').prop('checked', $(this.element).prop('checked'));
    }
}

import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    click() {
        const isChecked = $(this.element).prop('checked');
        $('.sub-checkbox').prop('checked', isChecked);
    }
}

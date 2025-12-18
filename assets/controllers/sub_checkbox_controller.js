import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    click() {
        const totalCheckboxes = $('.sub-checkbox').length;
        const totalCheckedCheckboxes = $('.sub-checkbox:checked').length;

        $('.main-checkbox').prop('checked', totalCheckboxes === totalCheckedCheckboxes);
    }
}

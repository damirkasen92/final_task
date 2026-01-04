import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    click() {
        $('.main-checkbox').prop('checked',
            $('.sub-checkbox').length === $('.sub-checkbox:checked').length
        );
    }
}

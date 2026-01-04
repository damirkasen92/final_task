import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    click() {
        $('.sub-checkbox').prop('checked',
            $(this.element).prop('checked')
        );
    }
}

import { Controller } from '@hotwired/stimulus';
import DOMPurify from 'dompurify';

export default class extends Controller {
    connect() {
        $('[data-description]').each((_, description) => {
            let mdText = JSON.parse('"' + $(description).data('description') + '"');
            mdText = DOMPurify.sanitize(mdText);
            $(description).html(marked.parse(mdText));
        });
    }
}

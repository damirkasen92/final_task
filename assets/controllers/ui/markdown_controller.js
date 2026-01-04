import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['textarea'];

    #mdInstance = null;

    connect() {
        $(document).on('shown.bs.modal', this.#init.bind(this));
        $(document).on('shown.bs.tab', this.#init.bind(this));

        this.#init();
    }

    #init() {
        if (this.#mdInstance) return;

        window.easyMDE = this.#mdInstance = new EasyMDE({
            element: this.textareaTarget,
            spellChecker: false,
            forceSync: true,
            // autosave: {
            //     enabled: true,
            //     uniqueId: "description_field",
            //     delay: 1000,
            // },
            toolbar: [
                'bold',
                'italic',
                'heading',
                '|',
                'quote',
                'unordered-list',
                'ordered-list',
                '|',
                'link',
                'image',
                '|',
                'preview',
                'side-by-side',
                'fullscreen',
            ],
        });
    }
}

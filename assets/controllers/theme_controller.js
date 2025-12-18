import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    #theme = null;
    #html = $('html');

    connect() {
        this.#theme = this.#html.attr('data-bs-theme');
    }

    change(event) {
        const nextTheme = this.#theme === 'light' ? 'dark' : 'light';

        if (this.#theme === 'light') {
            $('#themeIcon').attr('class', 'bi bi-moon-fill');
        } else {
            $('#themeIcon').attr('class', 'bi bi-sun-fill');
        }

        this.#html.attr('data-bs-theme', nextTheme);
        this.#theme = nextTheme;
    }
}

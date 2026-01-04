import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    #theme = null;
    #html = $("html");

    connect() {
        const theme = this.#getTheme();

        if (theme)
            this.#html.attr("data-bs-theme", theme);
        this.#theme = this.#html.attr("data-bs-theme");
    }

    change(event) {
        const nextTheme = this.#theme === "light" ? "dark" : "light";

        if (this.#theme === "light") {
            $("#themeIcon").attr("class", "bi bi-moon-fill");
        } else {
            $("#themeIcon").attr("class", "bi bi-sun-fill");
        }

        this.#saveTheme(nextTheme);
        this.#html.attr("data-bs-theme", nextTheme);
        this.#theme = nextTheme;
    }

    #getTheme() {
        return localStorage.getItem('theme');
    }

    #saveTheme(theme) {
        localStorage.setItem('theme', theme);
    }
}

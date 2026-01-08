import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';

import jQuery from 'jquery';

window.$ = jQuery;
window.jQuery = jQuery;

import 'bootstrap';

window.trans = (name) => {
    return $('#translations').length ? JSON.parse($('#translations').text())[name] : {};
};

langRedirect();

function langRedirect() {
    const currentLocale = $('body').data('lang');
    const cookieLocale = document.cookie.match(/user_locale=(\w+)/)?.[1];

    if (cookieLocale && cookieLocale !== currentLocale) {
        let newUrl =
            '/' +
            location.pathname
                .split('/')
                .slice(1)
                .filter((value) => !value.includes('ru', 'en'))
                .join('/');

        if (cookieLocale !== 'en') {
            newUrl = '/' + cookieLocale + newUrl;
        }

        location.replace(newUrl);
    }
}

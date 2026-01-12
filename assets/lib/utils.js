import $ from 'jquery';

window.editors = {};

window.trans = (name) => {
    for (const translationEl of $('[data-translations]')) {
        let data = JSON.parse($(translationEl).text());

        if (name in data) {
            return data[name];
        }
    }

    return '';
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

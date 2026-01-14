import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    #delay = 500;
    #eventSource = null;

    connect() {
        if (!this.#eventSource) {
            this.#eventSource = new EventSource(JSON.parse($('#mercure-url').text()), {
                withCredentials: true,
            });
        }

        this.#eventSource.onmessage = (event) => {
            this.#addMessage(JSON.parse(event.data));
            this.#scrollToBottom();
        };

        setTimeout(() => {
            this.#scrollToBottom();
        }, 100);

        $('#post-form')
            .off('submit')
            .on('submit', (evt) => {
                evt.preventDefault();

                $.post({
                    url: $(evt.target).attr('action'),
                    data: new FormData(evt.target),
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        if (response.status) {
                            window.editors['content'].value('');
                        }
                    },
                });
            });
    }

    #addMessage(data) {
        const $postDiv = $('<div>', {
            class: 'card mb-3',
            html: `<div class="card-body"> <p class="card-text">${data.message}</p> </div>`,
        });

        $('#posts').append($postDiv);
    }

    #scrollToBottom() {
        let $posts = $('#posts');
        if ($posts.length) {
            $posts.animate({ scrollTop: $posts[0].scrollHeight }, this.#delay);
        }
    }
}

/**
 * SIDIKO Core - Modal
 */

const Modal = {

    open(element) {

        if (!element) {
            return;
        }

        element.classList.add('is-open');

        document.body.classList.add('modal-open');
    },

    close(element) {

        if (!element) {
            return;
        }

        element.classList.remove('is-open');

        document.body.classList.remove('modal-open');
    },

    toggle(element) {

        if (!element) {
            return;
        }

        if (element.classList.contains('is-open')) {
            this.close(element);
        } else {
            this.open(element);
        }
    },

    closeAll() {

        document
            .querySelectorAll('.sidiko-modal.is-open')
            .forEach(modal => {
                this.close(modal);
            });
    },

    init() {

        document.addEventListener('click', (event) => {

            const openButton = event.target.closest(
                '[data-modal-open]'
            );

            if (openButton) {

                const target = openButton.dataset.modalOpen;

                const modal = document.getElementById(target);

                this.open(modal);

                return;
            }

            const closeButton = event.target.closest(
                '[data-modal-close]'
            );

            if (closeButton) {

                const modal = closeButton.closest(
                    '.sidiko-modal'
                );

                this.close(modal);
            }
        });

        document.addEventListener('keydown', (event) => {

            if (event.key === 'Escape') {
                this.closeAll();
            }
        });
    },
};

export default Modal;
/**
 * SIDIKO Core - Notification
 */

const Notification = {

    success(message) {
        this.show(message, 'success');
    },

    error(message) {
        this.show(message, 'error');
    },

    warning(message) {
        this.show(message, 'warning');
    },

    info(message) {
        this.show(message, 'info');
    },

    show(message, type = 'info') {

        const event = new CustomEvent('sidiko:notification', {
            detail: {
                message,
                type,
            },
        });

        window.dispatchEvent(event);
    },
};

export default Notification;
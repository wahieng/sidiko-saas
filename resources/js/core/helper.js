/**
 * SIDIKO Core - Helper
 */

const Helper = {

    qs(selector, parent = document) {
        return parent.querySelector(selector);
    },

    qsa(selector, parent = document) {
        return [...parent.querySelectorAll(selector)];
    },

    escapeHtml(value) {

        if (value === null || value === undefined) {
            return '';
        }

        const div = document.createElement('div');

        div.textContent = value;

        return div.innerHTML;
    },

    formatNumber(value) {

        if (value === null || value === undefined || value === '') {
            return '0';
        }

        return new Intl.NumberFormat('id-ID').format(value);
    },

    formatCurrency(value) {

        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(value || 0);
    },

    confirm(message) {

        return window.confirm(message);
    },

    debounce(callback, delay = 300) {

        let timeout;

        return (...args) => {

            clearTimeout(timeout);

            timeout = setTimeout(() => {
                callback(...args);
            }, delay);
        };
    },

    serializeForm(form) {

        const formData = new FormData(form);

        return Object.fromEntries(formData.entries());
    },
};

export default Helper;
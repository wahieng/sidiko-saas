/**
 * SIDIKO Core - AJAX
 *
 * Menangani komunikasi HTTP dengan backend Laravel.
 */

const Ajax = {

    async request(url, options = {}) {

        const config = {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.headers || {}),
            },
            ...options,
        };

        try {

            const response = await fetch(url, config);

            const contentType = response.headers.get('content-type');

            let data = null;

            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
            } else {
                data = await response.text();
            }

            if (!response.ok) {

                const error = new Error(
                    data?.message || 'Terjadi kesalahan pada server.'
                );

                error.status = response.status;
                error.data = data;

                throw error;
            }

            return data;

        } catch (error) {

            console.error('SIDIKO AJAX Error:', error);

            throw error;
        }
    },

    get(url, options = {}) {

        return this.request(url, {
            ...options,
            method: 'GET',
        });
    },

    post(url, data = {}, options = {}) {

        return this.request(url, {
            ...options,
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                ...(options.headers || {}),
            },
            body: JSON.stringify(data),
        });
    },

    put(url, data = {}, options = {}) {

        return this.request(url, {
            ...options,
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                ...(options.headers || {}),
            },
            body: JSON.stringify(data),
        });
    },

    patch(url, data = {}, options = {}) {

        return this.request(url, {
            ...options,
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                ...(options.headers || {}),
            },
            body: JSON.stringify(data),
        });
    },

    delete(url, options = {}) {

        return this.request(url, {
            ...options,
            method: 'DELETE',
        });
    },
};

export default Ajax;
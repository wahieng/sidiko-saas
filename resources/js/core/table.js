import Helper from './helper';

const Table = {

    /**
     * Render rows
     */
    renderRows(tbody, rows, renderRow) {

        if (!tbody) {
            return;
        }

        tbody.innerHTML = '';

        if (!rows || rows.length === 0) {

            this.empty(
                tbody,
                1,
                'Tidak ada data.'
            );

            return;
        }

        rows.forEach((row, index) => {

            tbody.insertAdjacentHTML(
                'beforeend',
                renderRow(row, index)
            );

        });
    },


    /**
     * Loading state
     */
    loading(tbody, colspan = 1) {

        if (!tbody) {
            return;
        }

        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="text-center">
                    Memuat data...
                </td>
            </tr>
        `;
    },


    /**
     * Empty state
     */
    empty(
        tbody,
        colspan = 1,
        message = 'Tidak ada data.'
    ) {

        if (!tbody) {
            return;
        }

        tbody.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="text-center">
                    ${Helper.escapeHtml(message)}
                </td>
            </tr>
        `;
    },


    /**
     * Render pagination
     *
     * Laravel pagination response:
     *
     * current_page
     * last_page
     * per_page
     * total
     * from
     * to
     */
    renderPagination(
        container,
        meta,
        onPageChange
    ) {

        if (!container) {
            return;
        }

        container.innerHTML = '';

        if (!meta || meta.last_page <= 1) {
            return;
        }

        const currentPage = meta.current_page;
        const lastPage = meta.last_page;


        const wrapper = document.createElement('div');

        wrapper.className = 'sidiko-pagination';


        /*
        |--------------------------------------------------------------------------
        | Previous
        |--------------------------------------------------------------------------
        */

        const previous = document.createElement('button');

        previous.type = 'button';

        previous.className = 'sidiko-pagination-button';

        previous.textContent = '‹';

        previous.disabled = currentPage <= 1;

        previous.dataset.page = currentPage - 1;

        wrapper.appendChild(previous);


        /*
        |--------------------------------------------------------------------------
        | Page Numbers
        |--------------------------------------------------------------------------
        */

        const pages = this.getPageNumbers(
            currentPage,
            lastPage
        );

        pages.forEach(page => {

            if (page === '...') {

                const dots = document.createElement('span');

                dots.className = 'sidiko-pagination-dots';

                dots.textContent = '...';

                wrapper.appendChild(dots);

                return;
            }


            const button = document.createElement('button');

            button.type = 'button';

            button.className =
                'sidiko-pagination-button';


            if (page === currentPage) {

                button.classList.add(
                    'active'
                );

            }


            button.textContent = page;

            button.dataset.page = page;

            wrapper.appendChild(button);

        });


        /*
        |--------------------------------------------------------------------------
        | Next
        |--------------------------------------------------------------------------
        */

        const next = document.createElement('button');

        next.type = 'button';

        next.className =
            'sidiko-pagination-button';

        next.textContent = '›';

        next.disabled =
            currentPage >= lastPage;

        next.dataset.page =
            currentPage + 1;

        wrapper.appendChild(next);


        /*
        |--------------------------------------------------------------------------
        | Event
        |--------------------------------------------------------------------------
        */

        wrapper.addEventListener(
            'click',
            event => {

                const button =
                    event.target.closest(
                        '[data-page]'
                    );

                if (!button || button.disabled) {
                    return;
                }

                const page =
                    Number(button.dataset.page);

                if (!page) {
                    return;
                }

                onPageChange(page);

            }
        );


        container.appendChild(wrapper);
    },


    /**
     * Generate page numbers
     */
    getPageNumbers(
        currentPage,
        lastPage
    ) {

        if (lastPage <= 7) {

            return Array.from(
                { length: lastPage },
                (_, index) => index + 1
            );

        }


        const pages = [];


        pages.push(1);


        if (currentPage > 4) {

            pages.push('...');

        }


        const start = Math.max(
            2,
            currentPage - 1
        );

        const end = Math.min(
            lastPage - 1,
            currentPage + 1
        );


        for (
            let page = start;
            page <= end;
            page++
        ) {

            pages.push(page);

        }


        if (currentPage < lastPage - 3) {

            pages.push('...');

        }


        pages.push(lastPage);


        return pages;
    },


    /**
     * Informasi pagination
     *
     * Contoh:
     * Menampilkan 1-10 dari 100 data
     */
    renderInfo(
        container,
        meta
    ) {

        if (!container || !meta) {
            return;
        }

        if (!meta.total) {

            container.textContent =
                'Tidak ada data';

            return;
        }

        container.textContent =
            `Menampilkan ${meta.from}-${meta.to} ` +
            `dari ${meta.total} data`;
    },

};

export default Table;
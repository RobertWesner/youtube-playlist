window.addEventListener('load', () => {
    const errorLog = document.querySelector('#error-log');
    const table = document.querySelector('#viewer');
    const pageInput = document.querySelector('#page-input');

    const error = message => {
        errorLog.textContent = message;
        errorLog.removeAttribute('hidden');
    };

    const clearError = () => {
        errorLog.textContent = '';
        errorLog.setAttribute('hidden', '');
    };

    let items = [];
    let maxResults = 10000;
    let page = 1;
    let slice = [];
    const showTable = () => {
        const first = (page - 1) * maxResults;
        slice = items.slice(first, first + maxResults);

        const tbody = table.querySelector('tbody');
        tbody.textContent = '';

        slice.forEach(
            /**
             * @param {{
             *  position: number,
             *  title: string,
             *  videoId: string,
             * }} item
             */
            item => {
                const tr = document.createElement('tr');
                const indexTd = document.createElement('td');
                const linkTd = document.createElement('td');
                const link = document.createElement('a');
                tr.append(indexTd, linkTd);
                linkTd.append(link);

                indexTd.textContent = (item.position + 1).toString();
                link.href = 'https://youtube.com/watch?v=' + item.videoId;
                link.target = '_blank'
                link.textContent = item.title;

                tbody.append(tr);
            }
        );
    };

    document.querySelector('form').addEventListener('submit', event => {
        event.preventDefault();
        clearError();

        const data = new FormData(event.target);
        let playlist = data.get('playlist');

        if (!playlist || !/https:\/\/(m|www)\.youtube\.com\/(@.+|playlist\?list=.+)/.test(playlist.toString())) {
            error('Please enter a valid channel or playlist.')

            return;
        }

        fetch(
            '/api/list',
            {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ uri: playlist })
            }
        )
            .then(response => response.json())
            .then(response => {
                if (response.status === 'running') {
                    error('Playlist is very large and will be fetched in the background, please retry in 10 seconds.');
                } else {
                    items = response.items;
                    showTable();
                }
            });
    });

    document.querySelector('#limit').addEventListener('input', event => {
        maxResults = parseInt(event.target.value);
        page = 1;
        showTable();
    });

    document.querySelector('#page-input').addEventListener('input', event => {
        page = Math.max(1, parseInt(event.target.value));
        showTable();
    });

    document.querySelector('#page-previous').addEventListener('click', () => {
        page = Math.max(1, page - 1);
        pageInput.value = page;
        showTable();
    });

    document.querySelector('#page-next').addEventListener('click', () => {
        page = page + 1;
        pageInput.value = page;
        showTable();
    });

    document.querySelector('#copy-btn').addEventListener('click', event => {
        navigator.clipboard.writeText(
            slice.map(
                /**
                 * @param {{
                 *  videoId: string,
                 * }} item
                 */
                item => `https://youtube.com/watch?v=${item.videoId}`,
            ).join('\n')
        );

        event.target.textContent = 'Copied!';
        setTimeout(() => {
            event.target.textContent = 'Copy selection';
        }, 3000);
    });
});

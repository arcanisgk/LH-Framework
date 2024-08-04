class TCGWallet {
    constructor() {
        this.dbName = "tcg-wallet";
        this.storeName = "quick_search";
        this.version = 1;
        this.indexedDb = window.indexedDB;
        this.db = null;
        this.data = null;
        this.pageSize = 12;
        this.page = 1;
    }

    async openDB() {
        return new Promise((resolve, reject) => {
            let request = this.indexedDb.open(this.dbName, this.version);
            request.onerror = () => {
                reject(request.error);
            };
            request.onsuccess = () => {
                this.db = request.result;
                resolve();
            };
            request.onupgradeneeded = (event) => {
                let db = event.target.result;
                let objectStore = db.createObjectStore(this.storeName, {keyPath: 'id', unique: true});
                objectStore.createIndex('card_id', 'card_id', {unique: false});
                objectStore.createIndex('card_name', 'card_name', {unique: false});
                objectStore.createIndex('set_code', 'set_code', {unique: false});
                objectStore.createIndex('release', 'release', {unique: false});
            };
        });
    }

    async getData() {
        let response = await fetch('/assets/sources/quick_search.json');
        this.data = await response.json();
    }

    async checkUpdate() {
        if (!this.lastUpdated && (Date.now() - this.lastUpdated) > 43200000) {
            this.lastUpdated = Date.now();
            return true;
        }
        let data = await this.data;
        let objectStore = this.db
            .transaction([this.storeName], 'readonly')
            .objectStore(this.storeName);
        let index = objectStore.index('card_id');
        let request = index.count();
        return new Promise((resolve, reject) => {
            request.onsuccess = (event) => {
                let count = request.result;
                resolve(count !== data.length);
            };
            request.onerror = (event) => {
                reject(event.target.error);
            };
        });
    }

    async updateDB() {
        let objectStore = this.db
            .transaction([this.storeName], 'readwrite')
            .objectStore(this.storeName);
        objectStore.clear();
        objectStore.transaction.onerror = (event) => {
            console.error("[error]", event.target.error);
        };
        let counter = 1;
        for (let i = 0; i < this.data.length; i++) {
            let item = this.data[i];
            let request = objectStore.add({
                id: counter,
                card_id: item[0],
                card_name: item[1],
                set_code: item[2],
                release: item[3]
            });
            request.onerror = (event) => {
                //console.error("[error]", event.target.error);
            }
            counter++;
        }
    }

    async search(query) {
        let results = [];
        if (query === '') {
            return results;
        } else {
            let objectStore = this.db
                .transaction([this.storeName], 'readonly')
                .objectStore(this.storeName);
            const regexPartial = new RegExp(query, 'i');
            const regexExact = new RegExp(`^${query}$`, 'i');
            return new Promise((resolve, reject) => {
                objectStore.openCursor().onsuccess = (event) => {
                    let cursor = event.target.result;
                    if (cursor) {
                        let item = cursor.value;
                        if (regexPartial.test(item.card_name) ||
                            regexExact.test(item.card_name) ||
                            regexPartial.test(item.set_code) ||
                            regexExact.test(item.set_code) ||
                            regexPartial.test(item.card_id) ||
                            regexExact.test(item.card_id)) {
                            results.push(item);
                        }
                        cursor.continue();
                    } else {
                        results.sort((a, b) => {
                            let dateDiff = new Date(b.release) - new Date(a.release);
                            if (dateDiff === 0) {
                                return a.set_code.localeCompare(b.set_code);
                            }
                            return dateDiff;
                        });
                        resolve(results);
                    }
                };
                objectStore.openCursor().onerror = (event) => {
                    reject(event.target.error);
                };
            });
        }
    }


    async init() {
        await this.openDB();
        await this.getData();
        let needUpdate = await this.checkUpdate();
        if (needUpdate) {
            await this.updateDB();
        }
    }

    getPage(results, page) {
        let startIndex = (page - 1) * this.pageSize;
        let endIndex = Math.min(startIndex + this.pageSize, results.length);
        return {
            results: results.slice(startIndex, endIndex),
            startIndex: startIndex,
            endIndex: endIndex
        };
    }

    async searchHandler(query, page = 1) {
        this.page = page
        let results = await this.search(query);
        let numResults = results.length;
        let pageResults = this.getPage(results, page);
        let div = document.getElementById('results');
        let list = document.getElementById('list-suggestion');
        list.innerHTML = '';
        let counts = document.getElementById('counts');
        counts.innerHTML = 'Results: ' + numResults + '<br> Showing from ' + (pageResults.startIndex + 1) + ' to ' + (pageResults.endIndex);
        let count = pageResults.results.length;
        for (let result of pageResults.results) {
            let li = document.createElement('li');
            li.classList.add('list-group-item');
            li.setAttribute('data-target', `${result.card_id} | ${result.card_name} | ${result.set_code}`);
            li.innerHTML = `${result.set_code} - ${result.card_name}`;
            li.addEventListener('click', function (event) {
                let data = event.target.getAttribute('data-target');
                let parts = data.split(' | ');
                let id = parts[0].trim();
                let name = parts[1].trim();
                let setCode = parts[2].trim();
                document.querySelector('#search').value = setCode;
                document.querySelector('#search').removeEventListener('keyup', quickSearchEngine);
                let button = document.querySelector('button[name="event"][value="search"]');
                button.click();
            });
            list.appendChild(li);
        }
        let backBtn = document.querySelectorAll('[name="back"]');
        let nextBtn = document.querySelectorAll('[name="next"]');
        backBtn.forEach(button => {
            button.disabled = page === 1;
            button.onclick = (event) => {
                event.preventDefault();
                this.searchHandler(query, page - 1);
            };
        });
        nextBtn.forEach(button => {
            button.disabled = count < this.pageSize;
            button.onclick = (event) => {
                event.preventDefault();
                this.searchHandler(query, page + 1);
            };
        });
        if (query !== '' && count > 0) {
            div.classList.add('collapse', 'show');
        } else if (query === '') {
            div.classList.remove('collapse', 'show');
        }
    }
}

async function quickSearchEngine() {
    let wallet = new TCGWallet();
    await wallet.init();
    let timeout;
    document.querySelector('#search').addEventListener('keyup', async (event) => {
        clearTimeout(timeout);
        let query = event.target.value;
        timeout = setTimeout(async () => {
            await wallet.searchHandler(query);
        }, 600);
    });
}

document.addEventListener("DOMContentLoaded", function () {
    window.quickSearchEngine();
    let mybutton = document.getElementById("btn-back-to-top");
    window.onscroll = function () {
        scrollFunction();
    };

    function scrollFunction() {
        if (
            document.body.scrollTop > 20 ||
            document.documentElement.scrollTop > 20
        ) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    mybutton.addEventListener("click", backToTop);

    function backToTop() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }

    const shareData = {
        title: 'Search Price',
        text: 'Search Card by: "' + document.getElementById('search').value + '"',
        url: document.URL
    }
    const btn = document.getElementById('share-url');
    if (typeof btn !== 'undefined' && btn !== null) {
        btn.addEventListener('click', async () => {
            try {
                if (typeof window.AndroidShareHandler !== 'undefined') {
                    window.AndroidShareHandler.shareSearch(JSON.stringify(shareData));
                } else {
                    await navigator.share(shareData);
                }
            } catch (err) {
                console.log(err);
                alert('[Error]: Error on share link!');
            }
        });
    }
});
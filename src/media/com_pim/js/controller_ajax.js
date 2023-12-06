(function () {
    class ResponseHandlers {
        loadItems(collection, items) {
            items.forEach(item => {
                collection.push({
                    id: item.id,
                    title: item.title,
                    state: item.state,
                });
            });
        }

        addItem(collection, item) {
            collection.push({
                id: item.id,
                title: item.title,
                state: item.state,
            });
        }

        delItem(collection, itemId) {
            const index = collection.findIndex(item => item.id === itemId);

            if (index === -1) {
                return;
            }

            collection.splice(index, 1);
        }
    }

    window.PIM = window.PIM || {};
    window.PIM.AlpineData = function (endpoint, token) {
        return {
            endpoint: endpoint,
            token: token,
            items: [],
            newItem: '',
            handlers: new ResponseHandlers(),
            init() {
                this.loadItems();
            },
            loadItems() {
                this.items = [];

                PIM.Ajax.getItems(this.endpoint + 'getItems', {'Api-Authorization': this.token})
                    .then(response => this.handlers.loadItems(this.items, response.data));

            },
            addItem() {
                if (this.newItem.trim().length < 1) {
                    return;
                }

                PIM.Ajax.postItem(this.endpoint + 'postItem', {'Api-Authorization': this.token}, {
                    title: this.newItem
                })
                    .then(response => this.handlers.addItem(this.items, response.data));

                this.newItem = '';
            },
            delItem(itemId) {
                if (this.items.length < 1) {
                    return;
                }

                const uri = this.endpoint + 'deleteItem' + '&id=' + itemId;

                PIM.Ajax.deleteItem(uri, {'Api-Authorization': this.token}, {
                    id: itemId
                })
                    .then(response => this.handlers.delItem(this.items, itemId));
            }
        }
    };

    document.addEventListener('alpine:init', () => {
        const options = Joomla.getOptions('ajax-auth');

        Alpine.data('itemservice', () => PIM.AlpineData(options.endpoint, options.token));
    })
})();


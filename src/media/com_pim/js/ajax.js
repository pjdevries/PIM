(function () {
    class Ajax {
        async getItems(url, headers = {}) {
            const defaultHeaders = {
                'Accept': 'application/vnd.api+json'
            };

            return this.ajax(url, {
                method: 'GET',
                headers: Object.assign(defaultHeaders, headers)
            });
        }

        async postItem(url, headers = {}, data = null) {
            const defaultHeaders = {
                'Content-Type': 'application/vnd.api+json',
                'Accept': 'application/vnd.api+json'
            };

            return this.ajax(url, {
                method: 'POST',
                body: JSON.stringify(data),
                headers: Object.assign(defaultHeaders, headers)
            });
        }

        async deleteItem(url, headers = {}, data = null) {
            const defaultHeaders = {
                'Content-Type': 'application/vnd.api+json',
                'Accept': 'application/vnd.api+json'
            };

            return this.ajax(url, {
                method: 'DELETE',
                headers: Object.assign(defaultHeaders, headers)
            });
        }

        async ajax(url, options) {
            if (!url) {
                return {};
            }

            try {
                const response = await fetch(url, options);
                const decodedResponse = await response.json();

                if (!response.ok) {
                    Joomla.renderMessages({
                        'error': [
                            decodedResponse.hasOwnProperty('message')
                                ? decodedResponse.message
                                : response.status + ' - ' + decodedResponse.errors[0].title
                        ]
                    });

                    return null;
                }

                return decodedResponse;
            } catch (e) {
                console.log(e.message);

                return null;
            }
        }
    }

    window.PIM = window.PIM || {};
    window.PIM.Ajax = new Ajax();
})();

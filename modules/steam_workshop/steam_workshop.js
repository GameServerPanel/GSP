(function () {
    'use strict';

    var Picker = /** @class */ (function () {
        function Picker(root) {
            this.root = root;
            this.endpoint = root.getAttribute('data-endpoint') || '';
            this.lang = {
                add: root.getAttribute('data-lang-add') || 'Add',
                remove: root.getAttribute('data-lang-remove') || 'Remove',
                loading: root.getAttribute('data-lang-loading') || 'Loading…',
                error: root.getAttribute('data-lang-error') || 'Something went wrong.',
                empty: root.getAttribute('data-lang-empty') || 'No results found.',
                query: root.getAttribute('data-lang-query') || 'Enter a Workshop ID or keyword.',
                sync: root.getAttribute('data-lang-sync') || 'Sync',
            };
            this.selectedInput = root.querySelector('.js-sw-selected-input');
            this.selectedList = root.querySelector('.js-sw-selected-list');
            this.resultsBody = root.querySelector('.js-sw-results');
            this.statusEl = root.querySelector('.js-sw-picker-status');
            this.searchForm = root.querySelector('.js-sw-search-form');
            this.searchInput = root.querySelector('.js-sw-search-input');
            this.searchButton = root.querySelector('.js-sw-search-button');
            this.requestInput = root.querySelector('.js-sw-request-input');
            this.requestSummary = root.querySelector('.js-sw-request-summary');
            this.requestSummaryBase = this.requestSummary ? (this.requestSummary.getAttribute('data-base') || '') : '';
            this.state = {
                selected: this.readInitialSelection(),
            };
            this.lastResults = [];
            this.bindEvents();
            this.renderSelected();
            this.updateRequestPreview();
        }
        Picker.prototype.readInitialSelection = function () {
            if (!this.selectedInput) {
                return [];
            }
            try {
                var parsed = JSON.parse(this.selectedInput.value || '[]');
                if (Array.isArray(parsed)) {
                    return parsed.filter(function (item) { return item && item.id; })
                        .map(function (item) { return ({
                        id: String(item.id),
                        label: String(item.label || ('@' + item.id)),
                        author: String(item.author || ''),
                        preview_url: String(item.preview_url || ''),
                        enabled: !(item.enabled === false || item.enabled === 'false' || item.enabled === 0 || item.enabled === '0'),
                        source: String(item.source || 'manual'),
                    }); });
                }
            }
            catch (err) {
                console.warn('Invalid Workshop JSON state', err);
            }
            return [];
        };
        Picker.prototype.bindEvents = function () {
            var _this = this;
            if (this.searchForm && this.searchForm.tagName === 'FORM') {
                this.searchForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    _this.performSearch();
                });
            }
            if (this.searchButton) {
                this.searchButton.addEventListener('click', function (event) {
                    event.preventDefault();
                    _this.performSearch();
                });
            }
            if (this.searchInput && (!this.searchForm || this.searchForm.tagName !== 'FORM')) {
                this.searchInput.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        _this.performSearch();
                    }
                });
            }
            if (this.searchInput) {
                this.searchInput.addEventListener('input', function () {
                    _this.updateRequestPreview();
                });
            }
            if (this.selectedList) {
                this.selectedList.addEventListener('click', function (event) {
                    var target = event.target;
                    if (!(target instanceof HTMLElement)) {
                        return;
                    }
                    if (target.matches('.js-sw-remove')) {
                        var id = target.getAttribute('data-id');
                        if (id) {
                            _this.removeSelected(id);
                        }
                    }
                });
                this.selectedList.addEventListener('change', function (event) {
                    var target = event.target;
                    if (!(target instanceof HTMLInputElement)) {
                        return;
                    }
                    if (target.matches('.js-sw-toggle')) {
                        var id = target.getAttribute('data-id');
                        if (id) {
                            _this.toggleSelected(id, target.checked);
                        }
                    }
                });
            }
            if (this.resultsBody) {
                this.resultsBody.addEventListener('change', function (event) {
                    var target = event.target;
                    if (!(target instanceof HTMLInputElement)) {
                        return;
                    }
                    if (target.matches('.js-sw-result-toggle')) {
                        var payload = target.getAttribute('data-payload');
                        if (payload) {
                            try {
                                var data = JSON.parse(payload);
                                if (target.checked) {
                                    _this.addSelected(data);
                                }
                                else {
                                    _this.removeSelected(String(data.id));
                                }
                            }
                            catch (err) {
                                console.warn('Invalid payload', err);
                            }
                        }
                    }
                });
            }
        };
        Picker.prototype.performSearch = function () {
            var _this = this;
            if (!this.endpoint || !this.searchInput) {
                return;
            }
            var term = this.searchInput.value.trim();
            this.updateRequestPreview();
            if (!term) {
                this.setStatus(this.lang.query, 'error');
                return;
            }
            if (this.isSearching) {
                return;
            }
            this.isSearching = true;
            this.setStatus(this.lang.loading, 'loading');
            var url = this.endpoint + '&q=' + encodeURIComponent(term);
            fetch(url, {
                headers: { 'Accept': 'application/json' },
            })
                .then(function (response) {
                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }
                return response.json();
            })
                .then(function (data) {
                if (!data || data.ok === false) {
                    var message = (data && data.error) ? data.error : _this.lang.error;
                    _this.setStatus(message, 'error');
                    _this.renderResults([]);
                    return;
                }
                if (Array.isArray(data.results) && data.results.length) {
                    _this.setStatus('', 'clear');
                    _this.renderResults(data.results);
                }
                else {
                    _this.setStatus(_this.lang.empty, 'info');
                    _this.renderResults([]);
                }
            })
                .catch(function (error) {
                console.error('Workshop search failed', error);
                _this.setStatus(_this.lang.error, 'error');
                _this.renderResults([]);
            })
                .finally(function () {
                _this.isSearching = false;
            });
        };
        Picker.prototype.setStatus = function (message, kind) {
            if (!this.statusEl) {
                return;
            }
            this.statusEl.textContent = message || '';
            this.statusEl.className = 'sw-picker__status js-sw-picker-status' + (kind ? ' sw-picker__status--' + kind : '');
        };
        Picker.prototype.renderResults = function (results) {
            if (!this.resultsBody) {
                return;
            }
            this.resultsBody.innerHTML = '';
            if (!Array.isArray(results) || !results.length) {
                this.lastResults = [];
                return;
            }
            this.lastResults = results.slice();
            var _loop_1 = function (item) {
                var normalized = {
                    id: String(item.id),
                    label: String(item.label || ('@' + item.id)),
                    author: String(item.author || ''),
                    preview_url: String(item.preview_url || ''),
                    enabled: true,
                    source: String(item.source || 'search'),
                };
                var row = document.createElement('tr');
                var selectCell = document.createElement('td');
                var toggle = document.createElement('label');
                toggle.className = 'sw-picker__result-toggle';
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'js-sw-result-toggle';
                checkbox.setAttribute('data-payload', JSON.stringify(normalized));
                checkbox.checked = this_1.isSelected(normalized.id);
                toggle.appendChild(checkbox);
                var toggleText = document.createElement('span');
                toggleText.textContent = this_1.lang.add;
                toggle.appendChild(toggleText);
                selectCell.appendChild(toggle);
                var titleCell = document.createElement('td');
                titleCell.innerHTML = '<strong>' + this_1.escape(normalized.label) + '</strong><div class="sw-picker__result-meta">#' + this_1.escape(normalized.id) + '</div>';
                var authorCell = document.createElement('td');
                authorCell.textContent = normalized.author;
                row.appendChild(selectCell);
                row.appendChild(titleCell);
                row.appendChild(authorCell);
                this_1.resultsBody.appendChild(row);
            };
            var this_1 = this;
            for (var _i = 0, results_1 = results; _i < results_1.length; _i++) {
                var item = results_1[_i];
                _loop_1(item);
            }
        };
        Picker.prototype.updateRequestPreview = function () {
            if (this.requestInput && this.searchInput) {
                this.requestInput.value = this.searchInput.value;
            }
            if (this.requestSummary) {
                var encoded = '';
                if (this.searchInput && this.searchInput.value.trim() !== '') {
                    encoded = encodeURIComponent(this.searchInput.value.trim());
                }
                this.requestSummary.textContent = (this.requestSummaryBase || '') + encoded;
            }
        };
        Picker.prototype.isSelected = function (id) {
            return this.state.selected.some(function (item) { return item.id === id; });
        };
        Picker.prototype.addSelected = function (item) {
            if (!item || !item.id || this.isSelected(String(item.id))) {
                return;
            }
            this.state.selected.push({
                id: String(item.id),
                label: String(item.label || ('@' + item.id)),
                author: String(item.author || ''),
                preview_url: String(item.preview_url || ''),
                enabled: true,
                source: String(item.source || 'search'),
            });
            this.persist();
            this.renderSelected();
        };
        Picker.prototype.removeSelected = function (id) {
            var next = this.state.selected.filter(function (item) { return item.id !== id; });
            this.state.selected = next;
            this.persist();
            this.renderSelected();
        };
        Picker.prototype.toggleSelected = function (id, enabled) {
            var changed = false;
            this.state.selected = this.state.selected.map(function (item) {
                if (item.id === id) {
                    changed = true;
                    return {
                        id: item.id,
                        label: item.label,
                        author: item.author,
                        preview_url: item.preview_url,
                        enabled: enabled,
                        source: item.source,
                    };
                }
                return item;
            });
            if (changed) {
                this.persist();
            }
        };
        Picker.prototype.renderSelected = function () {
            if (!this.selectedList) {
                return;
            }
            this.selectedList.innerHTML = '';
            if (!this.state.selected.length) {
                var emptyText = this.selectedList.getAttribute('data-empty-text') || '';
                if (emptyText) {
                    var empty = document.createElement('div');
                    empty.className = 'sw-picker__empty';
                    empty.textContent = emptyText;
                    this.selectedList.appendChild(empty);
                }
                return;
            }
            var this_2 = this;
            for (var _i = 0, _a = this.state.selected; _i < _a.length; _i++) {
                var item = _a[_i];
                var chip = document.createElement('div');
                chip.className = 'sw-picker__chip';
                chip.innerHTML = '<div class="sw-picker__chip-text"><strong>' + this.escape(item.label) + '</strong><span>#' + this.escape(item.id) + '</span></div>';
                var controls = document.createElement('div');
                controls.className = 'sw-picker__chip-controls';
                var toggle = document.createElement('label');
                toggle.className = 'sw-picker__toggle';
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'js-sw-toggle';
                checkbox.checked = item.enabled !== false;
                checkbox.setAttribute('data-id', item.id);
                toggle.appendChild(checkbox);
                var toggleText = document.createElement('span');
                toggleText.textContent = this_2.lang.sync;
                toggle.appendChild(toggleText);
                var removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'sw-picker__chip-remove js-sw-remove';
                removeBtn.setAttribute('data-id', item.id);
                removeBtn.textContent = this_2.lang.remove;
                controls.appendChild(toggle);
                controls.appendChild(removeBtn);
                chip.appendChild(controls);
                this.selectedList.appendChild(chip);
            }
            this.persist();
        };
        Picker.prototype.persist = function () {
            if (!this.selectedInput) {
                return;
            }
            try {
                this.selectedInput.value = JSON.stringify(this.state.selected);
            }
            catch (err) {
                console.error('Unable to serialize workshop selection', err);
            }
        };
        Picker.prototype.escape = function (value) {
            var div = document.createElement('div');
            div.textContent = value;
            return div.innerHTML;
        };
        return Picker;
    }());
    document.addEventListener('DOMContentLoaded', function () {
        var nodes = document.querySelectorAll('.sw-picker');
        Array.prototype.forEach.call(nodes, function (node) {
            try {
                new Picker(node);
            }
            catch (err) {
                console.error('Failed to boot Steam Workshop picker', err);
            }
        });
    });
})();

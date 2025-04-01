import Sortable from 'sortablejs';

document.addEventListener('alpine:initializing', () => {
    window.Alpine.data('sortableTree', (data) => ({
        maxDepth: data.maxDepth,
        staticDepth: data.staticDepth || false,

        init() {
            this.initializeSortables();
        },

        initializeSortables() {
            let nestedSortables = document.getElementsByClassName('js-sortable-group');
            for (let i = 0; i < nestedSortables.length; i++) {
                this.createSortableInstance(nestedSortables[i], i);
            }
        },

        createSortableInstance(element, index) {
            new Sortable(element, {
                group: 'nested' + (this.staticDepth ? index : ''),
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                draggable: '[data-sortable-item]',
                handle: '[data-sortable-handle]',
                sort: data.sortable,
                onMove: (evt) => this.handleMove(evt),
                onSort: () => this.handleSort(),
            });
        },

        handleMove(evt) {
            const movedSideways = evt.draggedRect.left !== evt.relatedRect.left;
            const relatedDepth = evt.related ? this.getDepth(evt.related) : 0;
            const draggedDepth = this.getDepth(evt.dragged);
            const draggedTotalDepth = this.getDeepestElementDepth(evt.dragged);
            const draggedChildDepth = draggedTotalDepth - draggedDepth;
            const depth = Math.max(relatedDepth, draggedDepth) + draggedChildDepth;
            const isTooDeep = this.maxDepth >= 0 && depth > this.maxDepth;

            if (isTooDeep && movedSideways) {
                return false;
            }
        },

        handleSort() {
            this.$wire.sortRows(elementsToArray(document.querySelectorAll('#js-sortable-root-nodes')));
        },

        getDepth(el, depth = 0) {
            let parentEl = el.parentElement.closest('.js-sortable-item');
            if (parentEl) {
                return this.getDepth(parentEl, ++depth);
            }
            return depth;
        },

        getDeepestElementDepth(el, depth = 0) {
            const depths = [];
            const items = el.querySelectorAll('.js-sortable-item');
            depths.push(this.getDepth(el, depth));

            items.forEach((item) => {
                const itemDepth = this.getDepth(item, depth);
                depths.push(itemDepth);
            });

            return Math.max(...depths);
        },

        async search(searchTerm) {
            this.handleSearchResult(
                await this.$wire.search(searchTerm)
            );
        },

        handleSearchResult(response) {
            this.$dispatch('search-complete', response);
            const items = document.querySelectorAll('.js-sortable-item');
            const emptyContainer = document.querySelector('.empty-tree-results-container');

            if (!response.results.length) {
                emptyContainer?.classList?.remove('hidden');
            }

            items.forEach(item => {
                const match = !response.searchTerm
                    || response.results.some(m => m.id === parseInt(item.dataset.id));
                item.style.display = match ? '' : 'none';

                if (match) {
                    let parent = item.parentElement.closest('.js-sortable-item');
                    while (parent) {
                        parent.style.display = '';
                        parent = parent.parentElement.closest('.js-sortable-item');
                    }
                }
            });
        }
    }));

    function elementsToArray(element) {
        let elements = [];
        let items = element[0].querySelectorAll(':scope > .js-sortable-item');

        items.forEach(function (child) {
            let childData = {id: child.dataset.id};
            let children = child.querySelectorAll(':scope > .js-sortable-group');

            if (children.length > 0) {
                childData.children = elementsToArray(children);
            }

            elements.push(childData);
        });

        return elements;
    }
});

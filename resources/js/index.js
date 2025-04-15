import Sortable from 'sortablejs';

document.addEventListener('alpine:initializing', () => {
    window.Alpine.data('sortableTree', (data) => ({
        maxDepth: data.maxDepth,
        staticDepth: data.staticDepth || false,
        sortableInstances: [],
        fullTree: [],

        init() {
            this.fullTree = elementsToArray(document.querySelectorAll('#js-sortable-root-nodes'));
            this.initializeSortables();
        },

        initializeSortables() {
            let nestedSortables = document.getElementsByClassName('js-sortable-group');
            for (let i = 0; i < nestedSortables.length; i++) {
                this.createSortableInstance(nestedSortables[i], i);
            }
        },

        createSortableInstance(element, index) {
            const instance = new Sortable(element, {
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

            this.sortableInstances.push(instance);
        },

        enableSorting() {
            this.sortableInstances.forEach(instance => {
                instance.option('disabled', false);
            });
        },

        disableSorting() {
            this.sortableInstances.forEach(instance => {
                instance.option('disabled', true);
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
            this.fullTree = elementsToArray(document.querySelectorAll('#js-sortable-root-nodes'));
            this.$wire.sortRows(this.fullTree);
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

            // Disable sorting if we have search results and a search term
            if (response.searchTerm && response.results.length) {
                this.disableSorting();
            } else {
                this.enableSorting();
            }

            if (!response.searchTerm) {
                emptyContainer?.classList?.add('hidden');
                items.forEach(item => item.style.display = '');
                return;
            }

            if (!response.results.length) {
                emptyContainer?.classList?.remove('hidden');
            }

            const childrenIds = [];
            const matchingIds = response.results.map(m => parseInt(m.id));
            this.findAllChildrenIds(this.fullTree, matchingIds, childrenIds);

            const visibleIds = [...new Set([...matchingIds, ...childrenIds])];

            items.forEach(item => {
                const id = parseInt(item.dataset.id);
                const match = visibleIds.includes(id);

                item.style.display = match ? '' : 'none';

                if (match) {
                    let parent = item.parentElement.closest('.js-sortable-item');
                    while (parent) {
                        parent.style.display = '';
                        parent = parent.parentElement.closest('.js-sortable-item');
                    }
                }
            });
        },

        // Helper method to recursively find all children of matching nodes
        findAllChildrenIds(nodes, matchingIds, resultIds) {
            if (!nodes || !nodes.length) return;

            for (const node of nodes) {
                // If this node is a match, add all its children recursively
                if (matchingIds.includes(parseInt(node.id))) {
                    this.collectAllChildrenIds(node, resultIds);
                }

                // Continue searching the tree
                if (node.children && node.children.length) {
                    this.findAllChildrenIds(node.children, matchingIds, resultIds);
                }
            }
        },

        // Helper method to collect all descendant IDs of a node
        collectAllChildrenIds(node, resultIds) {
            if (!node.children) return;

            for (const child of node.children) {
                resultIds.push(parseInt(child.id));
                if (child.children && child.children.length) {
                    this.collectAllChildrenIds(child, resultIds);
                }
            }
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

import { Controller } from '@hotwired/stimulus';

/**
 * Stimulus controller for managing a collection of content blocks.
 * Handles: adding, removing, reordering blocks.
 *
 * Usage in Twig:
 * <div data-controller="content-block-collection"
 *      data-content-block-collection-prototype-value="__block_name__">
 *     <div data-content-block-collection-target="list">...</div>
 *     <template data-content-block-collection-target="prototype">...</template>
 * </div>
 */
export default class extends Controller {
    static targets = ['list', 'prototype', 'item', 'empty', 'toolbar'];
    static values = {
        prototype: { type: String, default: '__block_name__' },
    };

    declare readonly listTarget: HTMLElement;
    declare readonly prototypeTarget: HTMLTemplateElement;
    declare readonly itemTargets: HTMLElement[];
    declare readonly hasEmptyTarget: boolean;
    declare readonly emptyTarget: HTMLElement;
    declare readonly hasToolbarTarget: boolean;
    declare readonly toolbarTarget: HTMLElement;
    declare readonly prototypeValue: string;

    private counter: number = 0;
    private draggedElement: HTMLElement | null = null;

    connect(): void {
        // Initialize counter based on existing items
        this.counter = this.itemTargets.length;
        this.updateEmptyState();
        this.updatePositions();
    }

    /**
     * Add a new block of the specified type.
     */
    addBlock(event: Event): void {
        event.preventDefault();

        const button = event.currentTarget as HTMLButtonElement;
        const type = button.dataset.type || 'text';

        // Get prototype HTML
        const prototypeHtml = this.prototypeTarget.innerHTML;

        // Replace prototype name with unique counter
        const newHtml = prototypeHtml.replace(
            new RegExp(this.prototypeValue, 'g'),
            String(this.counter)
        );

        // Create element from HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = newHtml.trim();
        const newElement = tempDiv.firstElementChild as HTMLElement;

        if (!newElement) {
            console.error('Failed to create new block element');
            return;
        }

        // Set the type
        const typeSelect = newElement.querySelector('[data-content-block-item-target="typeSelect"]') as HTMLSelectElement;
        const typeHidden = newElement.querySelector('input[name$="[type]"]') as HTMLInputElement;

        if (typeSelect) {
            typeSelect.value = type;
        }
        if (typeHidden) {
            typeHidden.value = type;
        }

        // Set the type value on the controller element
        newElement.dataset.contentBlockItemTypeValue = type;

        // Append to list
        this.listTarget.appendChild(newElement);

        // Increment counter
        this.counter++;

        // Update UI
        this.updateEmptyState();
        this.updatePositions();

        // Wait for Stimulus to connect the controller, then update it
        setTimeout(() => {
            const itemController = this.application.getControllerForElementAndIdentifier(
                newElement,
                'content-block-item'
            );
            if (itemController) {
                // Update the typeValue which will trigger the Stimulus value changed callback
                (itemController as any).typeValue = type;

                if (typeof (itemController as any).showFieldsForType === 'function') {
                    (itemController as any).showFieldsForType(type);
                }
                if (typeof (itemController as any).updateTypeBadge === 'function') {
                    (itemController as any).updateTypeBadge();
                }
            }
        }, 10);

        // Focus the first input in the new block
        setTimeout(() => {
            const firstInput = newElement.querySelector('input:not([type="hidden"]), textarea, select') as HTMLElement;
            if (firstInput) {
                firstInput.focus();
            }
        }, 100);
    }

    /**
     * Remove a block from the collection.
     */
    removeBlock(event: Event): void {
        event.preventDefault();

        const button = event.currentTarget as HTMLButtonElement;
        const item = button.closest('[data-content-block-collection-target="item"]') as HTMLElement;

        if (!item) {
            return;
        }

        // Animate removal
        item.style.opacity = '0';
        item.style.transform = 'translateX(-20px)';
        item.style.transition = 'opacity 0.2s, transform 0.2s';

        setTimeout(() => {
            item.remove();
            this.updateEmptyState();
            this.updatePositions();
        }, 200);
    }

    /**
     * Move a block up in the list.
     */
    moveUp(event: Event): void {
        event.preventDefault();

        const button = event.currentTarget as HTMLButtonElement;
        const item = button.closest('[data-content-block-collection-target="item"]') as HTMLElement;

        if (!item) {
            return;
        }

        const previousItem = item.previousElementSibling as HTMLElement;
        if (previousItem && previousItem.dataset.contentBlockCollectionTarget === 'item') {
            this.listTarget.insertBefore(item, previousItem);
            this.updatePositions();
            this.animateMove(item);
        }
    }

    /**
     * Move a block down in the list.
     */
    moveDown(event: Event): void {
        event.preventDefault();

        const button = event.currentTarget as HTMLButtonElement;
        const item = button.closest('[data-content-block-collection-target="item"]') as HTMLElement;

        if (!item) {
            return;
        }

        const nextItem = item.nextElementSibling as HTMLElement;
        if (nextItem && nextItem.dataset.contentBlockCollectionTarget === 'item') {
            this.listTarget.insertBefore(nextItem, item);
            this.updatePositions();
            this.animateMove(item);
        }
    }

    /**
     * Start dragging a block (for future drag-and-drop).
     */
    startDrag(event: MouseEvent): void {
        const handle = event.currentTarget as HTMLElement;
        const item = handle.closest('[data-content-block-collection-target="item"]') as HTMLElement;

        if (!item) {
            return;
        }

        this.draggedElement = item;
        item.classList.add('content-block--dragging');

        // Add mouse move and mouse up listeners
        document.addEventListener('mousemove', this.handleDrag);
        document.addEventListener('mouseup', this.endDrag);
    }

    /**
     * Handle drag movement.
     */
    private handleDrag = (event: MouseEvent): void => {
        if (!this.draggedElement) {
            return;
        }

        // Find the element we're hovering over
        const items = this.itemTargets;
        const draggedRect = this.draggedElement.getBoundingClientRect();
        const draggedCenter = draggedRect.top + draggedRect.height / 2;

        for (const item of items) {
            if (item === this.draggedElement) {
                continue;
            }

            const rect = item.getBoundingClientRect();
            const center = rect.top + rect.height / 2;

            // If we're above this item's center, insert before it
            if (event.clientY < center && draggedCenter > center) {
                this.listTarget.insertBefore(this.draggedElement, item);
                break;
            }

            // If we're below this item's center, insert after it
            if (event.clientY > center && draggedCenter < center) {
                this.listTarget.insertBefore(this.draggedElement, item.nextSibling);
                break;
            }
        }
    };

    /**
     * End drag operation.
     */
    private endDrag = (): void => {
        if (this.draggedElement) {
            this.draggedElement.classList.remove('content-block--dragging');
            this.draggedElement = null;
        }

        document.removeEventListener('mousemove', this.handleDrag);
        document.removeEventListener('mouseup', this.endDrag);

        this.updatePositions();
    };

    /**
     * Update position hidden fields based on DOM order.
     */
    private updatePositions(): void {
        this.itemTargets.forEach((item, index) => {
            const positionInput = item.querySelector('input[name$="[position]"]') as HTMLInputElement;
            if (positionInput) {
                positionInput.value = String(index);
            }
            item.dataset.index = String(index);
        });
    }

    /**
     * Show/hide empty state message.
     */
    private updateEmptyState(): void {
        if (this.hasEmptyTarget) {
            this.emptyTarget.style.display = this.itemTargets.length === 0 ? 'block' : 'none';
        }
    }

    /**
     * Animate a block after it's been moved.
     */
    private animateMove(element: HTMLElement): void {
        element.style.transition = 'none';
        element.style.transform = 'scale(1.02)';
        element.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';

        requestAnimationFrame(() => {
            element.style.transition = 'transform 0.2s, box-shadow 0.2s';
            element.style.transform = 'scale(1)';
            element.style.boxShadow = '';
        });
    }
}

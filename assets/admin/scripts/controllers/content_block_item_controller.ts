import { Controller } from '@hotwired/stimulus';

/**
 * Stimulus controller for a single content block item.
 * Handles: showing/hiding fields based on block type.
 *
 * Usage in Twig:
 * <div data-controller="content-block-item"
 *      data-content-block-item-type-value="text">
 *     <select data-content-block-item-target="typeSelect">...</select>
 *     <div data-content-block-item-target="fields" data-block-type="text">...</div>
 * </div>
 */
export default class extends Controller {
    static targets = ['typeSelect', 'fields', 'typeBadge', 'typeSelector'];
    static values = {
        type: { type: String, default: 'text' },
    };

    declare readonly typeSelectTarget: HTMLSelectElement;
    declare readonly fieldsTargets: HTMLElement[];
    declare readonly hasTypeBadgeTarget: boolean;
    declare readonly typeBadgeTarget: HTMLElement;
    declare readonly hasTypeSelectorTarget: boolean;
    declare readonly typeSelectorTarget: HTMLElement;
    declare typeValue: string;

    connect(): void {
        // Show fields for the current type
        this.showFieldsForType(this.typeValue);
    }

    /**
     * Handle type change from the select element.
     */
    onTypeChange(event: Event): void {
        const select = event.currentTarget as HTMLSelectElement;
        this.typeValue = select.value;
        this.showFieldsForType(this.typeValue);
        this.updateTypeBadge();
    }

    /**
     * Show fields for a specific block type, hide all others.
     */
    showFieldsForType(type: string): void {
        this.fieldsTargets.forEach((fieldsContainer) => {
            const containerType = fieldsContainer.dataset.blockType;

            if (containerType === type) {
                fieldsContainer.style.display = 'block';
                // Enable inputs in visible container
                this.toggleInputs(fieldsContainer, true);
            } else {
                fieldsContainer.style.display = 'none';
                // Disable inputs in hidden container (prevent validation)
                this.toggleInputs(fieldsContainer, false);
            }
        });
    }

    /**
     * Toggle required/disabled state of inputs in a container.
     */
    private toggleInputs(container: HTMLElement, enabled: boolean): void {
        const inputs = container.querySelectorAll('input, textarea, select');

        inputs.forEach((input) => {
            const el = input as HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement;

            // Store original required state
            if (enabled) {
                // Restore original required state if it was stored
                const wasRequired = el.dataset.wasRequired;
                if (wasRequired === 'true') {
                    el.required = true;
                }
                el.removeAttribute('disabled');
            } else {
                // Store current required state and disable
                if (el.required) {
                    el.dataset.wasRequired = 'true';
                    el.required = false;
                }
                // Don't disable, as it would prevent form submission
                // Just clear the required state
            }
        });
    }

    /**
     * Update the type badge display.
     */
    updateTypeBadge(): void {
        if (!this.hasTypeBadgeTarget) {
            return;
        }

        // Update badge class
        const currentClasses = Array.from(this.typeBadgeTarget.classList);
        currentClasses.forEach((cls) => {
            if (cls.startsWith('content-block__type-badge--')) {
                this.typeBadgeTarget.classList.remove(cls);
            }
        });
        this.typeBadgeTarget.classList.add(`content-block__type-badge--${this.typeValue}`);

        // Update badge text
        const typeLabels: Record<string, string> = {
            text: 'Texte enrichi',
            code: 'Code',
            quote: 'Citation',
            callout: 'Encadré',
            divider: 'Séparateur',
            image: 'Image',
            gallery: 'Galerie',
            button: 'Bouton',
            embed: 'Embed',
            metric: 'Métrique',
            internal_link: 'Lien interne',
            related_posts: 'Articles liés',
            related_projects: 'Projets liés',
        };

        this.typeBadgeTarget.textContent = typeLabels[this.typeValue] || this.typeValue;
    }

    /**
     * Show the type selector (for changing block type).
     */
    showTypeSelector(): void {
        if (this.hasTypeSelectorTarget) {
            this.typeSelectorTarget.classList.remove('d-none');
        }
    }

    /**
     * Hide the type selector.
     */
    hideTypeSelector(): void {
        if (this.hasTypeSelectorTarget) {
            this.typeSelectorTarget.classList.add('d-none');
        }
    }
}

import { Controller } from '@hotwired/stimulus';

/**
 * Contrôleur Stimulus pour gérer l'ouverture/fermeture d'éléments collapsibles (menus déroulants, accordéons, etc.)
 * Réutilisable partout dans le backend admin.
 *
 * Usage dans Twig:
 * <div data-controller="collapsible">
 *     <button data-collapsible-target="button" data-action="click->collapsible#toggle">Toggle</button>
 *     <div data-collapsible-target="content" data-state="closed">Contenu à afficher/masquer</div>
 * </div>
 */
export default class extends Controller {
    static targets = ['button', 'content'];

    declare readonly buttonTarget: HTMLElement;
    declare readonly contentTarget: HTMLElement;
    declare readonly hasButtonTarget: boolean;
    declare readonly hasContentTarget: boolean;

    private clickOutsideHandler: ((event: MouseEvent) => void) | null = null;

    connect(): void {
        // Bind du gestionnaire pour pouvoir le remove plus tard
        this.clickOutsideHandler = this.handleClickOutside.bind(this);
    }

    disconnect(): void {
        // Nettoyage de l'event listener lors de la destruction du contrôleur
        if (this.clickOutsideHandler) {
            document.removeEventListener('click', this.clickOutsideHandler);
        }
    }

    /**
     * Toggle l'état du contenu (ouvert/fermé)
     */
    toggle(event: Event): void {
        event.preventDefault();
        event.stopPropagation();

        if (!this.hasContentTarget) {
            console.warn('Content target not found');
            return;
        }

        const isCurrentlyOpen = this.contentTarget.dataset.state === 'open';

        if (isCurrentlyOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    /**
     * Ouvre le contenu
     */
    open(): void {
        this.contentTarget.dataset.state = 'open';

        if (this.hasButtonTarget) {
            this.buttonTarget.setAttribute('aria-expanded', 'true');
        }

        // Ajoute l'event listener pour fermer au clic extérieur
        setTimeout(() => {
            if (this.clickOutsideHandler) {
                document.addEventListener('click', this.clickOutsideHandler);
            }
        }, 0);
    }

    /**
     * Ferme le contenu
     */
    close(): void {
        this.contentTarget.dataset.state = 'closed';

        if (this.hasButtonTarget) {
            this.buttonTarget.setAttribute('aria-expanded', 'false');
        }

        // Retire l'event listener
        if (this.clickOutsideHandler) {
            document.removeEventListener('click', this.clickOutsideHandler);
        }
    }

    /**
     * Gère le clic en dehors pour fermer automatiquement
     */
    private handleClickOutside(event: MouseEvent): void {
        const target = event.target as Node;

        if (!this.element.contains(target)) {
            this.close();
        }
    }
}

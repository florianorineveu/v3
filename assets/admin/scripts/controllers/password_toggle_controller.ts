import { Controller } from '@hotwired/stimulus';

/**
 * Contrôleur Stimulus pour toggle la visibilité du mot de passe
 * Utilise les icônes Phosphor (Eye / EyeClosed)
 *
 * Usage dans Twig:
 * <div class="password-field" data-controller="password-toggle">
 *     <input type="password" data-password-toggle-target="input" class="password-input">
 *     <button type="button" class="password-field__toggle" data-action="click->password-toggle#toggle" data-password-toggle-target="button">
 *         <i class="ph ph-eye"></i>
 *     </button>
 * </div>
 */
export default class extends Controller {
    static targets = ['input', 'button'];

    declare readonly inputTarget: HTMLInputElement;
    declare readonly buttonTarget: HTMLButtonElement;

    private isVisible: boolean = false;
    private iconElement: HTMLElement | null = null;

    connect(): void {
        // S'assurer que le bouton a les bons attributs ARIA
        this.buttonTarget.setAttribute('aria-pressed', 'false');
        this.buttonTarget.setAttribute('aria-label', 'Afficher le mot de passe');

        // Récupérer l'élément icône
        this.iconElement = this.buttonTarget.querySelector('i');
    }

    /**
     * Toggle la visibilité du mot de passe
     */
    toggle(event: Event): void {
        event.preventDefault();

        this.isVisible = !this.isVisible;

        if (this.isVisible) {
            this.show();
        } else {
            this.hide();
        }
    }

    /**
     * Affiche le mot de passe
     */
    private show(): void {
        this.inputTarget.type = 'text';
        this.buttonTarget.setAttribute('aria-pressed', 'true');
        this.buttonTarget.setAttribute('aria-label', 'Masquer le mot de passe');

        // Changer l'icône pour EyeClosed
        if (this.iconElement) {
            this.iconElement.className = 'ph ph-eye-closed';
        }
    }

    /**
     * Masque le mot de passe
     */
    private hide(): void {
        this.inputTarget.type = 'password';
        this.buttonTarget.setAttribute('aria-pressed', 'false');
        this.buttonTarget.setAttribute('aria-label', 'Afficher le mot de passe');

        // Changer l'icône pour Eye
        if (this.iconElement) {
            this.iconElement.className = 'ph ph-eye';
        }
    }
}

import { startStimulusApp } from '@symfony/stimulus-bridge';

// Start Stimulus app and auto-register all controllers for admin
// The lazy-controller-loader automatically loads Symfony UX controllers from controllers.json
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
));

// Also register shared controllers
import '../../shared/scripts/controllers/csrf_protection_controller.js';

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

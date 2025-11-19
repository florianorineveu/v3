import { startStimulusApp } from '@symfony/stimulus-bridge';

// Start Stimulus app and auto-register all controllers
export const app = startStimulusApp(require.context(
    './controllers',
    true,
    /\.(j|t)sx?$/
));

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

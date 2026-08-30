import { startStimulusApp } from '@symfony/stimulus-bundle';
import { WebauthnController } from '@web-auth/webauthn-stimulus';

const app = startStimulusApp();

// The WebAuthn controllers are published on npm since v5.3.0 and are registered here,
// with the identifier that stimulus_controller('@web-auth/webauthn-stimulus') renders.
app.register('web-auth--webauthn-stimulus', WebauthnController);

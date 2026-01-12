import { startStimulusApp } from '@symfony/stimulus-bundle';
import ConflictController from './controllers/inventory/conflict_controller.js';

const app = startStimulusApp();
// register any custom, 3rd party controllers here

app.register('conflict_controller', ConflictController);

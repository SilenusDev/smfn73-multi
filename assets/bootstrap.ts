import { Application } from "@hotwired/stimulus";

const application: Application = Application.start();

// Configure Stimulus development experience
application.debug = false;
(window as any).Stimulus = application;

export { application };

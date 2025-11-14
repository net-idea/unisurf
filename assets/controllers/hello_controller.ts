import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect(): void {
    this.element.textContent = 'Hello Stimulus! Edit me in assets/controllers/hello_controller.ts';
  }
}

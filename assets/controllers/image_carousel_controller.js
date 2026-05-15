import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['slide'];

    connect() {
        this.index = 0;
    }

    next() {
        this._show((this.index + 1) % this.slideTargets.length);
    }

    prev() {
        this._show((this.index - 1 + this.slideTargets.length) % this.slideTargets.length);
    }

    _show(index) {
        this.slideTargets[this.index].classList.add('hidden');
        this.index = index;
        this.slideTargets[this.index].classList.remove('hidden');
    }
}
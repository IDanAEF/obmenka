import mask from "./blocks/mask";
import scrolling from './blocks/scrolling';
import slider from "./blocks/slider";
import other from "./blocks/other";

'use strict';

window.addEventListener('DOMContentLoaded', () => {
    mask('input[type="tel"]');
    mask('input.card-validate', true);
    scrolling();
    slider();
    other();
});
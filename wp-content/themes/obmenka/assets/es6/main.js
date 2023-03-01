import mask from "./blocks/mask";
import scrolling from './blocks/scrolling';
import slider from "./blocks/slider";
import form from "./blocks/form";
import other from "./blocks/other";

'use strict';

window.addEventListener('DOMContentLoaded', () => {
    mask();
    scrolling();
    slider();
    form();
    other();
});
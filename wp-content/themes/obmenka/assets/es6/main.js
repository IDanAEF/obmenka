import mask from "./blocks/mask";
import scrolling from './blocks/scrolling';
import slider from "./blocks/slider";
import form from "./blocks/form";
import abroad_form from "./blocks/abroad-form";
import lists from "./blocks/lists";
import other from "./blocks/other";

'use strict';

window.addEventListener('DOMContentLoaded', () => {
    mask();
    scrolling();
    slider();
    if (document.querySelector('#change-form')) form();
    if (document.querySelector('#abroad-form')) abroad_form();
    lists();
    other();
});
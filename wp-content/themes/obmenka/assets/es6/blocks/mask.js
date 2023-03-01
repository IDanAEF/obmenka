const mask = () => {

    let setCursorPosition = (pos, elem) => {
        elem.focus();
        
        if (elem.setSelectionRange) {
            elem.setSelectionRange(pos, pos);
        } else if (elem.createTextRange) {
            let range = elem.createTextRange();

            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
        }
    };

    function createMask(event) {
        const inp = event.target;

        let matrix = inp.getAttribute('data-mask'),
            i = 0,
            def = matrix.replace(/\D/g, ''),
            val = inp.value.replace(/\D/g, '');

        if (def.length >= val.length) {
            val = def;
        }

        inp.value = matrix.replace(/./g, function(a) {
            return /[_\d]/.test(a) && i < val.length ? val.charAt(i++) : i >= val.length ? '' : a;
        });

        if (event.type === 'blur') {
            if (inp.value.length == 2) inp.value = '';
        } else {
            setCursorPosition(inp.value.length, inp);
        }
    }

    function initMask(e) {
        if (e.target.getAttribute('data-mask') && (e.target.getAttribute('type') == 'tel' || (e.target.getAttribute('type') == 'text' && e.target.classList.contains('card-validate')))) {
            createMask(e);
        }
    }

    window.addEventListener('input', initMask);
    window.addEventListener('focus', initMask);
    window.addEventListener('blur', initMask);
};

export default mask;
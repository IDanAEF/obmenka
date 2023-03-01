const form = () => {
    try {
        function Calculate(Luhn) {
            let sum = 0;
            for (i = 0; i < Luhn.length; i++) {
                sum += +Luhn.substring(i, i + 1);
            }

            let delta = [0, 1, 2, 3, 4, -4, -3, -2, -1, 0];
            for (i = Luhn.length - 1; i >= 0; i -= 2) {
                let deltaIndex = +Luhn.substring(i, i + 1),
                    deltaValue = delta[deltaIndex];
                
                sum += deltaValue;
            }

            let mod10 = sum % 10;
            mod10 = 10 - mod10;
    
            if (mod10 == 10) mod10 = 0;
            
            return mod10;
        }
        function Validate(Luhn) {
            Luhn = Luhn.replace(/\s/g, '');
            let LuhnDigit = +Luhn.substring(Luhn.length - 1, Luhn.length);
            let LuhnLess = Luhn.substring(0, Luhn.length - 1);
    
            if (Calculate(LuhnLess) == +LuhnDigit) return true;

            return false;
        }
        function validateCreditCard(value) {
            const result = Validate(value);
            return result;
        }
        function getCookie(name, json=false) {
            if (!name) return undefined;

            let matches = document.cookie.match(new RegExp(
              "(?:^|; )" + name.replace(/([.$?*|{}()\[\]\\\/+^])/g, '\\$1') + "=([^;]*)"
            ));

            if (matches) {
              let res = decodeURIComponent(matches[1]);
              if (json) {
                try {
                  return JSON.parse(res);
                }
                catch(e) {}
              }
              return res;
            }
          
            return undefined;
        }
        function setCookie(name, value, options = {path: '/'}) {
            if (!name) return;
            
            options = options || {};
            
            if (options.expires instanceof Date) options.expires = options.expires.toUTCString();
            
            if (value instanceof Object) value = JSON.stringify(value);

            let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);
            for (let optionKey in options) {
                updatedCookie += "; " + optionKey;
                let optionValue = options[optionKey];

                if (optionValue !== true) updatedCookie += "=" + optionValue;
            }
            document.cookie = updatedCookie;
        }
        function deleteCookie(name) {
            setCookie(name, null, {
                expires: new Date(),
                path: '/'
            });
        }
        function calcCurrency(count, from, to) {
            if (!count) count = 0;
            from = +from.replace(',', '.')*count;
            to = +to.replace(',', '.');
            return (from/to).toFixed(2);
        }
        async function getData(url) {
            let res = await fetch(url, {
                method: "GET"
            });
        
            return await res.text();
        }
        
        const formCont = document.querySelector('.change-form-cont');

        const rebuildForm = () => {
            formCont.innerHTML += '<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>';
            getData(formCont.getAttribute('data-url')+'?action=form_steps')
            .then((res) => {
                formCont.innerHTML = res;
            });
        }

        rebuildForm();
        formCont.innerHTML += '<div class="main-loading"><img src="/wp-content/themes/obmenka/assets/images/loading.gif" alt=""></div>';

        window.addEventListener('keyup', (e) => {
            if (e.target.getAttribute('name') == 'send-sum') {
                let sum = document.querySelector('input[name="get-sum"]');
                sum.value = calcCurrency(e.target.value, e.target.getAttribute('data-rubs'), sum.getAttribute('data-rubs'));
            }
            if (e.target.getAttribute('name') == 'get-sum') {
                let sum = document.querySelector('input[name="send-sum"]');
                sum.value = calcCurrency(e.target.value, e.target.getAttribute('data-rubs'), sum.getAttribute('data-rubs'));
            }
        });
    } catch (e) {
        console.log(e.stack);
    }
}

export default form;
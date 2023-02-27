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
            
        }
    } catch (e) {
        console.log(e.stack);
    }
}

export default form;
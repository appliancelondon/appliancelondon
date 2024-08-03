// Version 1.0.0
// Postcode validation functionality
define('Appliancentre_BookingForm/js/postcode-validation', ['jquery'], function($) {
    'use strict';

    return {
        validatePostcode: function(postcode) {
            // Regular expression for UK postcode validation
            const ukPostcodeRegex = /^(([A-Z]{1,2}[0-9][A-Z0-9]?|ASCN|STHL|TDCU|BBND|[BFS]IQQ|PCRN|TKCA) ?[0-9][A-Z]{2}|BFPO ?[0-9]{1,4}|(KY[0-9]|MSR|VG|AI)[ -]?[0-9]{4}|[A-Z]{2} ?[0-9]{2}|GE ?CX|GIR ?0A{2}|SAN ?TA1)$/i;

            // Regular expression for London postcodes
            const londonPostcodeRegex = /^(EC[1-4][A-Z] ?[0-9][A-Z]{2}|WC[1-2][A-Z] ?[0-9][A-Z]{2}|SW1[A-Z] ?[0-9][A-Z]{2}|N1C ?[0-9][A-Z]{2}|[ENW][1-9] ?[0-9][A-Z]{2}|[ENW]1[0-9] ?[0-9][A-Z]{2}|[ENW]2[0-2] ?[0-9][A-Z]{2}|SE1 ?[0-9][A-Z]{2}|SE[1-9] ?[0-9][A-Z]{2}|SE1[0-9] ?[0-9][A-Z]{2}|SE2[0-8] ?[0-9][A-Z]{2}|HA[0-9] ?[0-9][A-Z]{2}|EN[1-8] ?[0-9][A-Z]{2}|E1W ?[0-9][A-Z]{2})$/i;

            if (!ukPostcodeRegex.test(postcode)) {
                return { valid: false, message: 'Please provide a valid UK postcode.' };
            }

            if (!londonPostcodeRegex.test(postcode)) {
                return { valid: false, message: 'Sorry, we do not currently have an engineer available for that appliance in your area.' };
            }

            return { valid: true };
        }
    };
});
define('Appliancentre_BookingForm/js/quote-calculator', ['jquery'], function($) {
    'use strict';

    console.log('quote-calculator.js loaded');

    function calculateMultipleAppliancesQuote(applianceData, showError, showStep) {
        console.log('Calculating quote...');
        var $getQuoteBtn = $('#getQuote');
        $getQuoteBtn.prop('disabled', true).text('Calculating...');

        var service = $('input[name="service"]:checked').val();
        var postcode = $('#customer_postcode').val();
        var appliances = [];

        console.log('Service:', service);
        console.log('Postcode:', postcode);

        $('.appliance-fields').each(function() {
            var $appliance = $(this);
            var applianceType = $appliance.find('.appliance-type').val();
            var applianceSubtype = $appliance.find('input[name^="appliances"][name$="[applianceSubtype]"]:checked').val();
            var applianceMake = $appliance.find('select[name$="[applianceMake]"]').val();

            console.log('Appliance:', applianceType, applianceSubtype, applianceMake);

            if (applianceType && applianceSubtype && applianceMake) {
                appliances.push({
                    type: applianceType,
                    subtype: applianceSubtype,
                    make: applianceMake
                });
            }
        });

        console.log('Appliances:', appliances);

        if (!validateQuoteInputs(service, postcode, appliances)) {
            showError('Please fill in all required fields');
            $getQuoteBtn.prop('disabled', false).text('Get a Quote');
            return;
        }

        var quoteHtml = '';
        var totalPrice = 0;

        appliances.forEach(function(appliance, index) {
            var price = calculateAppliancePrice(appliance, service, applianceData);
            totalPrice += price;

            quoteHtml += `
                <h3>Appliance ${index + 1}</h3>
                <p><strong>Make:</strong> ${formatApplianceMake(appliance.make)}</p>
                <p><strong>Appliance:</strong> ${formatApplianceType(appliance.type)}</p>
                <p><strong>Appliance Subtype:</strong> ${formatApplianceSubtype(appliance.subtype)}</p>
                <p><strong>Service:</strong> ${service.charAt(0).toUpperCase() + service.slice(1)}</p>
                <p><strong>One-Off ${service.charAt(0).toUpperCase() + service.slice(1)}:</strong> £${price.toFixed(2)} +VAT (Excluding parts)</p>
            `;
        });

        quoteHtml += `<h3>Total Price: £${totalPrice.toFixed(2)} +VAT (Excluding parts)</h3>`;

        $('#quoteResults').html(quoteHtml);
        showStep(2);
        $getQuoteBtn.prop('disabled', false).text('Get a Quote');
    }

    function validateQuoteInputs(service, postcode, appliances) {
        console.log('Validating inputs...');
        if (!service) {
            console.log('Service not selected');
            return false;
        }
        if (!postcode) {
            console.log('Postcode not entered');
            return false;
        }
        if (appliances.length === 0) {
            console.log('No appliances added');
            return false;
        }

        for (var i = 0; i < appliances.length; i++) {
            if (!appliances[i].type || !appliances[i].subtype || !appliances[i].make) {
                console.log('Incomplete appliance data:', appliances[i]);
                return false;
            }
        }

        console.log('All inputs valid');
        return true;
    }

    function calculateAppliancePrice(appliance, service, applianceData) {
        var basePrice = applianceData.basePrice[service] || 0;
        var appliancePrice = applianceData.appliancePrice[appliance.type + '_' + appliance.subtype] || 0;
        var makePrice = applianceData.makePrice[appliance.make.toLowerCase()] || 0;

        return basePrice + appliancePrice + makePrice;
    }

    function formatApplianceType(type) {
        return type.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    function formatApplianceSubtype(subtype) {
        return subtype.split('-')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    function formatApplianceMake(make) {
        return make.split('-')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    return {
        calculateMultipleAppliancesQuote: calculateMultipleAppliancesQuote,
        validateQuoteInputs: validateQuoteInputs
    };
});

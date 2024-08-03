define('Appliancentre_BookingForm/js/quote-calculator', ['jquery'], function($) {
    'use strict';

    console.log('quote-calculator.js loaded');

    function calculateQuote(applianceData, showError, showStep) {
        var $getQuoteBtn = $('#getQuote');
        $getQuoteBtn.prop('disabled', true).text('Calculating...');

        var service = $('input[name="service"]:checked').val();
        var postcode = $('#customer_postcode').val();
        var applianceType = $('#applianceType').val();
        var applianceSubtype = $('input[name="applianceSubtype"]:checked').val();
        var applianceMake = $('#applianceMake').val();

        if (!validateQuoteInputs()) {
            showError('Please fill in all required fields');
            $getQuoteBtn.prop('disabled', false).text('Get a Quote');
            return;
        }

        // Calculate total price
        var total = applianceData.basePrice[service];
        total += applianceData.appliancePrice[applianceType + '_' + applianceSubtype.toLowerCase()] || 0;
        total += applianceData.makePrice[applianceMake.toLowerCase()] || 0;

        // Format appliance type, subtype, and make for display
        var formattedApplianceType = formatApplianceType(applianceType);
        var formattedApplianceSubtype = formatApplianceSubtype(applianceSubtype);
        var formattedApplianceMake = formatApplianceMake(applianceMake);

        // Debug logging
        console.log('Original:', { applianceType, applianceSubtype, applianceMake });
        console.log('Formatted:', { formattedApplianceType, formattedApplianceSubtype, formattedApplianceMake });

        $('#quotePostcode').text(postcode);
        $('#quoteAppliance').text(formattedApplianceType);
        $('#quoteApplianceType').text(formattedApplianceSubtype);
        $('#quoteApplianceMake').text(formattedApplianceMake);
        $('#quotePrice').text(total.toFixed(2));

        showStep(2);
        $getQuoteBtn.prop('disabled', false).text('Get a Quote');
    }

    function validateQuoteInputs() {
        var service = $('input[name="service"]:checked').val();
        var postcode = $('#customer_postcode').val();
        var applianceType = $('#applianceType').val();
        var applianceSubtype = $('input[name="applianceSubtype"]:checked').val();
        var applianceMake = $('#applianceMake').val();

        return !!(service && postcode && applianceType && applianceSubtype && applianceMake);
    }

    function formatApplianceType(type) {
        var typeMap = {
            'built-in-oven': 'Built-in Oven',
            'cooker-oven': 'Cooker Oven',
            'dishwasher': 'Dishwasher',
            'extractor-cooker-hood': 'Extractor Cooker Hood',
            'freezer': 'Freezer',
            'fridge-freezer': 'Fridge Freezer',
            'hob': 'Hob',
            'refrigerator': 'Refrigerator',
            'tumble-dryer': 'Tumble Dryer',
            'washer-dryer': 'Washer Dryer',
            'washing-machine': 'Washing Machine',
            'wine-chiller': 'Wine Chiller'
        };
        return typeMap[type] || type;
    }

    function formatApplianceSubtype(subtype) {
        return subtype.split('-')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    function formatApplianceMake(make) {
        return make.split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    return {
        calculateQuote: calculateQuote,
        validateQuoteInputs: validateQuoteInputs
    };
});
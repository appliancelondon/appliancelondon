var config = {
    paths: {
        flatpickr: 'Appliancentre_BookingForm/js/flatpickr.min',
        'Appliancentre_BookingForm/js/appliance-data': 'Appliancentre_BookingForm/js/appliance-data',
        'Appliancentre_BookingForm/js/form-validation': 'Appliancentre_BookingForm/js/form-validation',
        'Appliancentre_BookingForm/js/quote-calculator': 'Appliancentre_BookingForm/js/quote-calculator',
        'Appliancentre_BookingForm/js/ui-helpers': 'Appliancentre_BookingForm/js/ui-helpers',
        'Appliancentre_BookingForm/js/postcode-validation': 'Appliancentre_BookingForm/js/postcode-validation'
    },
    shim: {
        flatpickr: {
            deps: ['jquery']
        }
    }
};
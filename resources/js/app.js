import './bootstrap';
import $ from 'jquery';
window.$ = window.jQuery = $;
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap/dist/css/bootstrap.min.css';

// Import Nepali Date Picker (JavaScript only, CSS handled separately)
import NepaliDatePicker from 'nepali-date-picker';

// Import date converter
import { ad2bs, bs2ad } from 'nepali-date-converter';

$(document).ready(function() {
    console.log('jQuery, Bootstrap, and Nepali Date Picker are loaded!');

    // Initialize Nepali Date Picker for date inputs
    $('.nepali-date-picker').nepaliDatePicker({
        dateFormat: 'YYYY-MM-DD',
        minYear: 2070,
        maxYear: 2090,
        onChange: function() {
            let bsDate = $(this).val();
            try {
                let adDate = bs2ad(bsDate);
                $(this).closest('.date-group').find('.gregorian-date').val(adDate);
            } catch (e) {
                console.error('Invalid BS date:', bsDate);
            }
        }
    });

    // Prevent multiple default accounts
    // $('#is_default_cash_account, #is_default_online_account').on('change', function() {
    //     if ($(this).val() === '1') {
    //         let type = $(this).attr('id').includes('cash') ? 'cash' : 'online';
    //         $.ajax({
    //             url: '/accounts/check-default',
    //             method: 'POST',
    //             data: {
    //                 _token: '{{ csrf_token() }}',
    //                 type: type,
    //                 value: 1
    //             },
    //             success: function(response) {
    //                 if (response.exists) {
    //                     alert(`A default ${type} account already exists.`);
    //                     $(this).val('0');
    //                 }
    //             },
    //             error: function(xhr) {
    //                 console.error('AJAX error:', xhr.responseText);
    //             }
    //         });
    //     }
    // });
});
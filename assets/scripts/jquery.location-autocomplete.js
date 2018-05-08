jQuery(document).ready(function (jQuery) {
"use strict";
        jQuery.fn.extend({
        cityAutocomplete: function (options) {

        return this.each(function () {

        var input = jQuery(this), opts = jQuery.extend({}, jQuery.cityAutocomplete);
                var autocompleteService = new google.maps.places.AutocompleteService();
                var predictionsDropDown = jQuery('<div class="cs_location_autocomplete" class="city-autocomplete"></div>').appendTo(jQuery(this).parent());
                var request_var = 1;
                input.keyup(function () {

                jQuery(this).parent(".cs_location_autocomplete").html("<i class='icon-spinner8 icon-spin'></i>");
                        if (request_var == 1) {
                var searchStr = jQuery(this).val();
                        // Min Number of characters
                        var num_of_chars = 0;
                        if (searchStr.length > num_of_chars) {
                var params = {
                input: searchStr,
                        bouns: 'upperbound',
                        //types: ['address']
                };
                        params.componentRestrictions = ''; //{country: window.country_code}

                        autocompleteService.getPlacePredictions(params, updatePredictions);
                }
                }
                });
                predictionsDropDown.delegate('div', 'click', function () {
                if (jQuery(this).text() != "ADDRESS" && jQuery(this).text() != "STATE / PROVINCE" && jQuery(this).text() != "COUNTRY") {
                // address with slug			
                var cs_address_html = jQuery(this).text();
                        // slug only
                        var cs_address_slug = jQuery(this).find('span').html();
                        // remove slug
                        jQuery(this).find('span').remove();
                        input.val(jQuery(this).text());
                        input.next('.search_keyword').val(cs_address_slug);
                        predictionsDropDown.hide();
                        input.next('.search_keyword').closest("form.side-loc-srch-form").submit();
                }
                });
                jQuery(document).mouseup(function (e) {
        predictionsDropDown.hide();
        });
                jQuery(window).resize(function () {
        updatePredictionsDropDownDisplay(predictionsDropDown, input);
        });
                updatePredictionsDropDownDisplay(predictionsDropDown, input);
                function updatePredictions(predictions, status) {

                var google_results = '';
                        if (google.maps.places.PlacesServiceStatus.OK == status) {

                // AJAX GET ADDRESS FROM GOOGLE
                google_results += '<div class="address_headers"><h5>ADDRESS</h5></div>';
                        jQuery.each(predictions, function (i, prediction) {
                        google_results += '<div class="cs_google_suggestions"><i class="icon-location-arrow"></i> ' + jQuery.fn.cityAutocomplete.transliterate(prediction.description) + '<span style="display:none">' + jQuery.fn.cityAutocomplete.transliterate(prediction.description) + '</span></div>';
                        });
                        request_var = 0;
                } else {
                predictionsDropDown.empty();
                }
                // AJAX GET STATE / PROVINCE
                var dataString = 'action=cs_get_all_countries_cities' + '&keyword=' + jQuery('.cs_search_location_field').val();
                        var plugin_url = input.parent(".cs_searchbox_div").data('locationadminurl');
                        jQuery.ajax({
                        type: "POST",
                                url: plugin_url,
                                data: dataString,
                                success: function (data) {
                                var results = jQuery.parseJSON(data);
                                        predictionsDropDown.empty();
                                        predictionsDropDown.append(google_results);
                                        if (results != '') {

                                predictionsDropDown.append('<div class="address_headers"><h5>COUNTRY / CITY</h5></div>');
                                        jQuery(results).each(function (key, value) {
                                if (value.hasOwnProperty('child')) {
                                jQuery(value.child).each(function (child_key, child_value) {
                                predictionsDropDown.append('<div class="cs_location_child">' + child_value.value + '<span style="display:none">' + child_value.slug + '</span></div');
                                })
                                } else {
                                predictionsDropDown.append('<div class="cs_location_parent">' + value.value + '<span style="display:none">' + value.slug + '</span></div');
                                }
                                })
                                }
                                request_var = 1;
                                }
                        });
                        predictionsDropDown.show();
                }
        return input;
        });
        }
        });
        jQuery.fn.cityAutocomplete.transliterate = function (s) {
        s = String(s);
                var char_map = {
                // Latin
                'À': 'A', '�?': 'A', 'Â': 'A', 'Ã': 'A', 'Ä': 'A', 'Å': 'A', 'Æ': 'AE', 'Ç': 'C',
                        'È': 'E', 'É': 'E', 'Ê': 'E', 'Ë': 'E', 'Ì': 'I', '�?': 'I', 'Î': 'I', '�?': 'I',
                        '�?': 'D', 'Ñ': 'N', 'Ò': 'O', 'Ó': 'O', 'Ô': 'O', 'Õ': 'O', 'Ö': 'O', '�?': 'O',
                        'Ø': 'O', 'Ù': 'U', 'Ú': 'U', 'Û': 'U', 'Ü': 'U', 'Ű': 'U', '�?': 'Y', 'Þ': 'TH',
                        'ß': 'ss',
                        'à': 'a', 'á': 'a', 'â': 'a', 'ã': 'a', 'ä': 'a', 'å': 'a', 'æ': 'ae', 'ç': 'c',
                        'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e', 'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i',
                        'ð': 'd', 'ñ': 'n', 'ò': 'o', 'ó': 'o', 'ô': 'o', 'õ': 'o', 'ö': 'o', 'ő': 'o',
                        'ø': 'o', 'ù': 'u', 'ú': 'u', 'û': 'u', 'ü': 'u', 'ű': 'u', 'ý': 'y', 'þ': 'th',
                        'ÿ': 'y',
                        // Latin symbols
                        '©': '(c)',
                        // Greek
                        'Α': 'A', 'Β': 'B', 'Γ': 'G', 'Δ': 'D', 'Ε': 'E', 'Ζ': 'Z', 'Η': 'H', 'Θ': '8',
                        'Ι': 'I', 'Κ': 'K', 'Λ': 'L', 'Μ': 'M', '�?': 'N', 'Ξ': '3', 'Ο': 'O', 'Π': 'P',
                        'Ρ': 'R', 'Σ': 'S', 'Τ': 'T', 'Υ': 'Y', 'Φ': 'F', 'Χ': 'X', 'Ψ': 'PS', 'Ω': 'W',
                        'Ά': 'A', 'Έ': 'E', 'Ί': 'I', 'Ό': 'O', 'Ύ': 'Y', 'Ή': 'H', '�?': 'W', 'Ϊ': 'I',
                        'Ϋ': 'Y',
                        'α': 'a', 'β': 'b', 'γ': 'g', 'δ': 'd', 'ε': 'e', 'ζ': 'z', 'η': 'h', 'θ': '8',
                        'ι': 'i', 'κ': 'k', 'λ': 'l', 'μ': 'm', 'ν': 'n', 'ξ': '3', 'ο': 'o', 'π': 'p',
                        '�?': 'r', 'σ': 's', 'τ': 't', 'υ': 'y', 'φ': 'f', 'χ': 'x', 'ψ': 'ps', 'ω': 'w',
                        'ά': 'a', 'έ': 'e', 'ί': 'i', 'ό': 'o', '�?': 'y', 'ή': 'h', 'ώ': 'w', 'ς': 's',
                        'ϊ': 'i', 'ΰ': 'y', 'ϋ': 'y', '�?': 'i',
                        // Turkish
                        'Ş': 'S', 'İ': 'I', 'Ç': 'C', 'Ü': 'U', 'Ö': 'O', 'Ğ': 'G',
                        'ş': 's', 'ı': 'i', 'ç': 'c', 'ü': 'u', 'ö': 'o', 'ğ': 'g',
                        // Russian
                        '�?': 'A', 'Б': 'B', 'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', '�?': 'Yo', 'Ж': 'Zh',
                        'З': 'Z', 'И': 'I', 'Й': 'J', 'К': 'K', 'Л': 'L', 'М': 'M', '�?': 'N', 'О': 'O',
                        'П': 'P', 'Р': 'R', 'С': 'S', 'Т': 'T', 'У': 'U', 'Ф': 'F', 'Х': 'H', 'Ц': 'C',
                        'Ч': 'Ch', 'Ш': 'Sh', 'Щ': 'Sh', 'Ъ': '', 'Ы': 'Y', 'Ь': '', 'Э': 'E', 'Ю': 'Yu',
                        'Я': 'Ya',
                        'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'yo', 'ж': 'zh',
                        'з': 'z', 'и': 'i', 'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o',
                        'п': 'p', 'р': 'r', '�?': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h', 'ц': 'c',
                        'ч': 'ch', 'ш': 'sh', 'щ': 'sh', 'ъ': '', 'ы': 'y', 'ь': '', '�?': 'e', 'ю': 'yu',
                        '�?': 'ya',
                        // Ukrainian
                        'Є'
                        : 'Ye', 'І': 'I', 'Ї': 'Yi', '�?': 'G',
                        'є'
                        : 'ye', 'і': 'i', 'ї': 'yi', 'ґ': 'g',
                        // Czech
                        'Č'
                        : 'C', 'Ď': 'D', 'Ě': 'E', 'Ň': 'N', 'Ř': 'R', 'Š': 'S', 'Ť': 'T', 'Ů': 'U',
                        'Ž'
                        : 'Z',
                        '�?'
                        : 'c', '�?': 'd', 'ě': 'e', 'ň': 'n', 'ř': 'r', 'š': 's', 'ť': 't', 'ů': 'u',
                        'ž'
                        : 'z',
                        // Polish
                        'Ą'
                        : 'A', 'Ć': 'C', 'Ę': 'e', '�?': 'L', 'Ń': 'N', 'Ó': 'o', 'Ś': 'S', 'Ź': 'Z',
                        'Ż'
                        : 'Z',
                        'ą'
                        : 'a', 'ć': 'c', 'ę': 'e', 'ł': 'l', 'ń': 'n', 'ó': 'o', 'ś': 's', 'ź': 'z',
                        'ż'
                        : 'z',
                        // Latvian
                        'Ā'
                        : 'A', 'Č': 'C', 'Ē': 'E', 'Ģ': 'G', 'Ī': 'i', 'Ķ': 'k', 'Ļ': 'L', 'Ņ': 'N',
                        'Š'
                        : 'S', 'Ū': 'u', 'Ž': 'Z',
                        '�?'
                        : 'a', '�?': 'c', 'ē': 'e', 'ģ': 'g', 'ī': 'i', 'ķ': 'k', 'ļ': 'l', 'ņ': 'n',
                        'š'
                        : 's', 'ū': 'u', 'ž': 'z'
                };
                jQuery.each(char_map, function(chars, normalized){
                var regex = new RegExp('[' + chars + ']', 'gi');
                        s = s.replace(regex, normalized);
                });
                return s;
                };
        function updatePredictionsDropDownDisplay(dropDown, input) {
        if (typeof (input.offset()) !== 'undefined') {
        dropDown.css({
        'width': input.outerWidth(),
                'left': input.offset().left,
                'top': input.offset().top + input.outerHeight()
        });
        }
        }

jQuery('input.cs_search_location_field').cityAutocomplete();
        jQuery(document).on('click', '.cs_searchbox_div', function () {
jQuery('.cs_search_location_field').prop('disabled', false);
        });
        jQuery(document).on('click', 'form', function () {
var src_loc_val = jQuery(this).find('.cs_search_location_field');
        src_loc_val.next('.search_keyword').val(src_loc_val.val());
        });
        }(jQuery)
        );
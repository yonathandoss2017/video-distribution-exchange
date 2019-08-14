function checkDoubleEntries(arrays, array) {

    var compare = false;
    arrays.some(function (items) {
        compare = JSON.stringify(items)==JSON.stringify(array);
        return compare;
    });

    return compare;

}

function addSelectOptions(id, selected) {

    var el = document.getElementById(id);
    el.innerHTML = '';

    // Collect region first if excepted_regions applied
    if ( id === 'excepted_regions') {

        var selectedRegion = [];
        regions.forEach( function(region) {
            if (allowedRegion.indexOf(region.region_code.toString()) !== -1 || allowedRegion.indexOf("001") !== -1) {
                selectedRegion.push(region);
            }

            if (region.sub_regions) {

                region.sub_regions.forEach( function(subregion) {

                    if (allowedRegion.indexOf(subregion.region_code.toString()) !== -1 && checkDoubleEntries(selectedRegion, region) !== true) {
                        selectedRegion.push(region);
                    }
                });

            }

        });

    } else {
        selectedRegion = regions;
    }

    selectedRegion.forEach( function(region) {

        var opt = document.createElement('option');
        opt.value = region.region_code;
        opt.innerHTML = region.area;

        if (selected.indexOf(region.region_code.toString()) !== -1) {
            opt.setAttribute('selected', 'selected');
        }

        if (
            ( ( (allowedRegion.indexOf("001") === -1 && allowedRegion.indexOf(region.region_code.toString()) === -1) // Check the condition not global and the region on alowed
                || allowedRegion.indexOf(region.region_code.toString()) !== -1) // if region allowed
                && id === 'excepted_regions')
            || ( id !== 'excepted_regions'
                && allowedRegion.indexOf("001") !== -1 // if global allowed
                && selected.indexOf(region.region_code.toString()) === -1) // if region not allowed
        ){
            opt.setAttribute('disabled', 'disabled');
        }

        el.appendChild(opt);

        if (region.sub_regions ) {

            region.sub_regions.forEach( function(subregion) {

                var optSub = document.createElement('option');
                optSub.value = subregion.region_code;
                optSub.innerHTML = '&nbsp;&nbsp;&nbsp;&nbsp;' + subregion.area;
                optSub.setAttribute('data-region', region.region_code);

                if (selected.indexOf(subregion.region_code.toString()) !== -1) {
                    optSub.setAttribute('selected', 'selected');
                }

                if (
                    ( ( allowedRegion.indexOf(subregion.region_code.toString()) !== -1
                        || selected.indexOf(region.region_code.toString()) !== -1 )
                        && id === 'excepted_regions')
                    || ( id !== 'excepted_regions'
                        && ( allowedRegion.indexOf("001") !== -1
                            || allowedRegion.indexOf(region.region_code.toString()) !== -1)
                    )
                ){
                    optSub.setAttribute('disabled', 'disabled');
                }

                if (
                    (
                        (
                            allowedRegion.indexOf(subregion.region_code.toString()) !== -1
                            || allowedRegion.indexOf(region.region_code.toString()) !== -1
                            || allowedRegion.indexOf("001") !== -1
                        )
                        && id === 'excepted_regions'
                    )
                    || id !== 'excepted_regions') {

                    el.appendChild(optSub);

                    subregion.countries.forEach( function(country) {

                        var optCountry = document.createElement('option');
                        optCountry.value = country.iso_3166_2;
                        optCountry.innerHTML = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + country.name;
                        optCountry.setAttribute('data-region', country.region_code);
                        optCountry.setAttribute('data-sub_region', country.sub_region_code);

                        if (selected.indexOf(country.iso_3166_2.toString()) !== -1) {
                            optCountry.setAttribute('selected', 'selected');
                        }

                        if ( ( ( selected.indexOf(region.region_code.toString()) !== -1 || selected.indexOf(subregion.region_code.toString()) !== -1 )
                                && id === 'excepted_regions')
                            || ( id !== 'excepted_regions'
                            && ( allowedRegion.indexOf("001") !== -1
                                || allowedRegion.indexOf(region.region_code.toString()) !== -1
                                || allowedRegion.indexOf(subregion.region_code.toString()) !== -1)
                            )
                        ) {
                            optCountry.setAttribute('disabled', 'disabled');
                        }

                        el.appendChild(optCountry);

                    });

                }

            });

        }
    });

    // Initiate select2
    $('#' + id).select2({
        placeholder: "-- " + selectRegion + " --",
        language: {
            noResults: function (params) {
              return noResults;
            }
        },
    });

}

$(document).ready(function() {

    addSelectOptions('allowed_regions', allowedRegion);
    addSelectOptions('excepted_regions', exceptedRegion);

    // Watching allowed_regions on change and update excepted_regions values
    $('#allowed_regions').on('change', function (e) {
        var options = e.currentTarget.options;
        allowedRegion = [];
        exceptedRegion = [];

        for (var i=0; i<options.length; i++) {
            opt = options[i];

            if (opt.selected) {
                var el = $(opt);
                if (el.attr('data-sub_region')) {
                    if (allowedRegion.indexOf(el.attr('data-sub_region')) === -1
                        && allowedRegion.indexOf(el.attr('data-region')) === -1
                        && allowedRegion.indexOf("001") === -1) {
                        allowedRegion.push(opt.value || opt.text);
                    }
                } else if(el.attr('data-region')) {
                    if (allowedRegion.indexOf(el.attr('data-region')) === -1 && allowedRegion.indexOf("001") === -1) {
                        allowedRegion.push(opt.value || opt.text);
                    }
                } else {
                    if (allowedRegion.indexOf("001") === -1) {
                        allowedRegion.push(opt.value || opt.text);
                    }
                }
            }
        }

        addSelectOptions('allowed_regions', allowedRegion);
        addSelectOptions('excepted_regions', exceptedRegion);
    });

    $('#excepted_regions').on('change', function (e) {
        var options = e.currentTarget.options;
        exceptedRegion = [];

        for (var i=0; i<options.length; i++) {
            opt = options[i];

            if (opt.selected) {
                var el = $(opt);
                if (el.attr('data-sub_region')) {
                    if (exceptedRegion.indexOf(el.attr('data-sub_region')) === -1
                        && exceptedRegion.indexOf(el.attr('data-region')) === -1
                        && exceptedRegion.indexOf("001") === -1) {
                        exceptedRegion.push(opt.value || opt.text);
                    }
                } else if(el.attr('data-region')) {
                    if (exceptedRegion.indexOf(el.attr('data-region')) === -1 && exceptedRegion.indexOf("001") === -1) {
                        exceptedRegion.push(opt.value || opt.text);
                    }
                } else {
                        exceptedRegion.push(opt.value || opt.text);
                }
            }
        }

        addSelectOptions('excepted_regions', exceptedRegion);
    });
});

$(document).ready(function() {
    // Action button
    var saveHotspotBtn = $('.btn-save-hotspot'),
        searchCreativeBtn = $('#btn-search'),
        updateHotspotBtn = $('.btn-update-hotspot'),
        playPreview = $('.btn-play-preview');

    // Form input
    var formAdInfo = $('#form-ad-info'),
        creativeIdInput = $('input[name=creative_id]'),
        hotspotIdInput = $('input[name=hotspot_id]'),
        hotspotTitleInput = $('input[name=hotspot_title]'),
        hotspotUrlInput = $('input[name=hotspot_URL]'),
        hotspotDesc = $('input[name=hotspot_desc]'),
        hotspotTitleDiv = $('#hotspot_title'),
        hotspotUrlDiv = $('#hotspot_URL'),
        hotspotPosition = $('select[name=hostpot_position]'),
        hotspotType = $('#hotspot_type'),
        startTimeInput = $('input[name=time_started]'),
        endTimeInput = $('input[name=time_ended]');

    // Creatives
    var unitList = $('.hotspot-creative-list'),
        creativeList = $('.creative-list'),
        campaignStartTitle = $('.campaign_start'),
        campaignEndTitle = $('.campaign_end'),
        campaignOptions = $('#campaign-options'),
        selectCampaignId = $('select[name=campaign_id]'),
        keywordInput = $('input[name=keyword]');

    //selector
    var saveHotspotBtnSelecttor = '.btn-save-hotspot',
        searchCreativeBtnSelector = '#btn-search',
        updateHotspotBtnSelector = '.btn-update-hotspot',
        deleteHotspotBtnSelector = '.btn-delete-hotspot',
        creativeItemSelector = '.creative-column',
        editUnitBtnSelector = '.edit-unit-btn',
        advertiserIdSelector = 'select[name=advertiser_id]',
        campaignIdSelector = 'select[name=campaign_id]',
        showNewAdBtnSelector = 'button[name=show_new_ad]',
        showAllAdsBtnSelector = 'button[name=show_all_ads]';

    //image
    var creativeThumbnail = $('.creative-thumbnail'),
        creativeImage = $('.creative-image-src');


    //load unit and creatives when ready
    loadUnits();
    loadCreatives();
    // loadVideo();

    $("body").on('click', editUnitBtnSelector, function (event){
        var elem = $(event.target);
        removeDangerClass();
        editHotspot(elem);
        if (hotspotIdInput.val() == "") {
            displaySaveHotspotButton();
        } else {
            displayUpdateHotspotButton();
        }
    });


    $("body").on('click', creativeItemSelector, function (event) {
        var elem = $(event.target).closest(".creative-grid");
        removeDangerClass();
        newHotspot(elem);
    });

    $("body").on('click', updateHotspotBtnSelector, function (event) {
        updateHotspot(hotspotIdInput.val());
    });

    $("body").on('click', deleteHotspotBtnSelector, function (event) {
        var confirmation = confirm("are you sure want to delete ?");
        var adUnitId = $(event.target).closest(".btn-delete-hotspot").data("id");
        
        if (confirmation) deleteHotspot(adUnitId);
    });

    $("body").on('click', saveHotspotBtnSelecttor, function (event) {
        saveHotspot()
    });

    $("body").on('change', advertiserIdSelector, function (event) {
        var elem = $(event.target);
        selectCampaignId.val(null);
        loadCampaign(elem);
        loadCreatives();
    });

    $("body").on('change', campaignIdSelector, function (event) {
        loadCreatives();
    });

    $("body").on('click', searchCreativeBtn, function (event) {
        loadCreatives();
    });

    keywordInput.bind("enterKey",function(e){
        loadCreatives();
    });
    keywordInput.keyup(function(e){
        if(e.keyCode == 13)
        {
            $(this).trigger("enterKey");
        }
    });

    $("body").on('click', showNewAdBtnSelector, function(event){
        removeFieldErrorMessage();
        clearMessage();
        clearHotspotInput();
        showNewAd();
        hiddenAllAds();
    });

    $("body").on('click', showAllAdsBtnSelector, function(event){
        removeFieldErrorMessage();
        clearMessage();
        showAllAds();
        hiddenNewAd();
        loadUnits();
    });

    function showAllAds()
    {
        $('div[name=all_ads]').attr('hidden',false);
    }    
    function hiddenNewAd()
    {
        $('div[name=new_ad]').attr('hidden',true);
    }    
    function showNewAd()
    {
        $('div[name=new_ad]').attr('hidden',false);
        if (hotspotIdInput.val() == "") {
            displaySaveHotspotButton();
        } else {
            displayUpdateHotspotButton();
        }
    }    
    function hiddenAllAds()
    {
        $('div[name=all_ads]').attr('hidden',true);
    }

    playPreview.on('click', function (event) {
        var el = $(event.target);
        var datas = {
            id: parseInt(el.attr('data-id')),
            name: el.attr('data-name'),
            desc: el.attr('data-desc'),
            image: el.attr('data-image'),
            thumbnail: el.attr('data-thumbnail'),
            target: el.attr('data-target'),
            type_ad: el.attr('data-type_ad').trim()
        };


        if (isNaN(datas.id)) {
            showErrorMessage('Error! data empty');
            return;
        }

        var pos = $('select[name="hostpot_position"]').val();

        var startTime = $('input[name="time_started"]').val();
        var endTime = $('input[name="time_ended"]').val();
        var videoDuration = $('input[name="duration"]').val()*1000;
        var hotspotId = hotspotIdInput.val();


        startTime = convertToMilliSeconds(startTime);
        endTime = convertToMilliSeconds(endTime);

        scrollToMessage();

        if ( startTime >= endTime || startTime > videoDuration || endTime > videoDuration) {
            updateHotspot(hotspotId);
        } else {
            removeFieldErrorMessage();
            clearMessage();

            var result = {
                            "id": datas.id,
                            "hse_gid" : "",
                            "hse_name" : datas.name,
                            "ad_type":"hotspot", 
                            "direction": "top2bottom",
                            "start" : startTime,
                            "duration": endTime-startTime,
                            "position": pos,
                            "creativeInfo":{}
                        };

            if (datas.type_ad === "Image") {
                result.creativeInfo = {
                                "hse_gname" : datas.name,
                                "image_url": datas.image,
                                "thumbnail_url": datas.thumbnail,
                                "url": datas.target
                            };
            } else {
                result.creativeInfo = {
                                "hse_gname" : datas.name,
                                "description" : datas.desc,
                                "title" : datas.name,
                                "image_url": datas.image,
                                "thumbnail_url": datas.thumbnail,
                                "url": datas.target
                            };
            }
            
            rendererMgr.doPreview(result);
        }
    });

    function convertToMilliSeconds(time) {
        var splitTime = time.split(':');

        var hours = parseInt(splitTime[0]) * 60 * 60 * 1000;
        var minutes = parseInt(splitTime[1]) * 60 * 1000;
        var seconds = parseInt(splitTime[2]) * 1000;

        var total = hours + minutes + seconds;

        return total;
    }

    function deleteHotspot(adUnitId) {
        $.ajax({
            url: $('input[name=delete_hotspot_url]').val() + '/' + adUnitId ,
            type: 'DELETE',
            cache: false,
            success: function(json) {
                if (json.status === 'success') {
                    removeFieldErrorMessage();
                    clearHotspotInput();
                    loadUnits();
                    showSuccessMessage("Ads deleted");
                    rendererMgr.deleteAd(adUnitId);
                } else {
                    showErrorMessage("Failed to delete");
                }
            },
            error: function(xhr, desc, err) {
                alert(err);
                // console.log(xhr + "\n" + err);
            }
        });
    }

    function clearHotspotInput()
    {
        hotspotIdInput.val('');
        hotspotTitleDiv.html('-');
        hotspotUrlDiv.html('-');
        hotspotPosition.val(1);
        startTimeInput.val('00:00:00');
        endTimeInput.val('00:00:00');
        hotspotType.html('-');
        creativeThumbnail.attr('src', '/images/creative-default.jpg');
        creativeImage.attr('src', '/images/image-ad.jpg');
        creativeIdInput.val('');
        hotspotIdInput.val('');

        campaignStartTitle.html('-');
        campaignEndTitle.html('-');

        saveHotspotBtn.html('ADD');
    }

    function editHotspot(elem) {

        rendererMgr.doPause();
        rendererMgr.cancelPreview();
        showNewAd();
        hiddenAllAds();

        var id = elem.data('id');
        var creativeId = elem.data('creative_id');
        var name = elem.data('name');
        var targetUrl = elem.data('target-url');
        var position = elem.data('position');
        var startTime = elem.data('start-time');
        var endTime = elem.data('end-time');
        var thumbnail = elem.data('thumbnail');
        var image = elem.data('image');
        var campaignStart =elem.data('campaign_start');
        var campaignEnd =elem.data('campaign_end');
        var desc = elem.data('desc');
        var typeAd = elem.data('type_ad');
        var active = elem.data('active');

        playPreview.attr('data-id' , id);
        playPreview.attr('data-name', name);
        playPreview.attr('data-target', targetUrl);
        playPreview.attr('data-position', position);
        playPreview.attr('data-startTime', startTime);
        playPreview.attr('data-endTime', endTime);
        playPreview.attr('data-thumbnail', thumbnail);
        playPreview.attr('data-image', image);
        playPreview.attr('data-campaignStart', campaignStart);
        playPreview.attr('data-campaignEnd', campaignEnd);
        playPreview.attr('data-desc', desc);
        playPreview.attr('data-type_ad', typeAd);

        hotspotIdInput.val(id);
        creativeIdInput.val(creativeId);
        hotspotTitleInput.val(name);
        hotspotUrlInput.val(targetUrl);
        hotspotTitleDiv.html(name);
        hotspotUrlDiv.html(targetUrl);
        hotspotPosition.val(position);
        startTimeInput.val(startTime);
        endTimeInput.val(endTime);
        hotspotType.html(typeAd);

        creativeThumbnail.attr('src', thumbnail);
        creativeImage.attr('src', image);

        campaignStart = formatTimeFromTimestamp(campaignStart);
        campaignEnd = formatTimeFromTimestamp(campaignEnd);
        campaignStartTitle.html(campaignStart);
        campaignEndTitle.html(campaignEnd);
        if (active == "") {
            campaignEndTitle.addClass("text-danger");
        }
    }

    function formatTimeFromTimestamp(timestampValue) {
        abbr = ' GMT ' + moment.tz(moment.tz.guess()).format('ZZ');
        time = moment(timestampValue).tz(moment.tz.guess());

        return time.format('MMM D, YYYY ') + time.format('HH:mm') + '<small> ' + abbr + '</span>';
    }

    function newHotspot(elem)
    {
        rendererMgr.doPause();
        rendererMgr.cancelPreview();
        showNewAd();
        hiddenAllAds();

        var id = hotspotIdInput.val();
        var creativeId = elem.data('creative_id');
        var name = elem.data('name');
        var targetUrl = elem.data('target-url');
        var thumbnail = elem.data('thumbnail');
        var image = elem.data('image');
        var campaignStart =elem.data('campaign_start');
        var campaignEnd =elem.data('campaign_end');
        var desc = elem.data('desc');
        var typeAd = elem.data('type_ad');

        playPreview.attr('data-id' , creativeId);
        playPreview.attr('data-name', name);
        playPreview.attr('data-target', targetUrl);
        //playPreview.attr('data-position', position);
        //playPreview.attr('data-startTime', startTime);
        //playPreview.attr('data-endTime', endTime);
        playPreview.attr('data-thumbnail', thumbnail);
        playPreview.attr('data-image', image);
        playPreview.attr('data-campaignStart', campaignStart);
        playPreview.attr('data-campaignEnd', campaignEnd);
        playPreview.attr('data-desc', desc);
        playPreview.attr('data-type_ad', typeAd);


        hotspotIdInput.val(id);
        hotspotTitleInput.val(name);
        hotspotUrlInput.val(targetUrl);
        hotspotTitleDiv.html(name);
        hotspotUrlDiv.html(targetUrl);
        hotspotDesc.val(desc);
        startTimeInput.html('00:00:00');
        endTimeInput.html('00:00:00');
        creativeThumbnail.attr('src', thumbnail);
        creativeImage.attr('src', image);
        creativeIdInput.val(creativeId);
        hotspotType.html(typeAd);

        campaignStart = formatTimeFromTimestamp(campaignStart);
        campaignEnd = formatTimeFromTimestamp(campaignEnd);
        campaignStartTitle.html(campaignStart);
        campaignEndTitle.html(campaignEnd);
    }

    function updateHotspot(adUnitId)
    {
        $.ajax({
            url: $('input[name=update_hotspot_url]').val() + '/' + adUnitId ,
            type: 'PUT',
            cache: false,
            data: formAdInfo.serialize(),
            success: function(data) {
                if (data.status == 'error') {
                    showErrorMessage(data.message);
                }

                if (data.status === 'success') {
                    removeFieldErrorMessage();
                    loadUnits();
                    hideSaveHotspotButton();
                    hideUpdateHotspotButton();
                    showSuccessMessage('Ad unit has been updated');
                     
                    var datas = data.data;
                    
                    startTime = datas.start_time * 1000;
                    endTime = (datas.start_time + datas.duration) * 1000;
                    var description = ''
                    if (datas.description !== '') {
                        description=datas.creative.description
                    }
                    
                    var json = {
                            "id": datas.id,
                            "hse_gid" : "",
                            "hse_name" : datas.name,
                            "ad_type":"hotspot", 
                            "direction": "top2bottom",
                            "start" : startTime,
                            "duration": endTime,
                            "position": datas.position,
                            "creativeInfo":{  
                                "hse_gname" : datas.name,
                                "image_url": creativeImage.attr('src'),
                                "thumbnail_url": creativeThumbnail.attr('src'),
                                "url": datas.target_url
                            }
                       };

                    if (datas.creative.type === 'info_card') {
                        json.creativeInfo["description"] = description;
                        json.creativeInfo["title"] = datas.name;
                    }


                    showAllAds();
                    hiddenNewAd();
                    rendererMgr.updateAd(json);
                }
            },
            error: function(xhr, desc, err) {

                //form validation failed
                 if (xhr.status === 422) {
                     showErrorMessage("There is an error with your submission.");
                     var errors = JSON.parse(xhr.responseText);
                     showFieldErrorMessage(errors);
                 }

                if (xhr.status === 404) {
                    showErrorMessage("Update failed, unit not found");
                }
            }
        });
    }

    function saveHotspot()
    {
        var url = $('input[name=save_units_hotspot_url]').val();
        
        $.ajax({
            url: url,
            type: 'POST',
            cache: false,
            data: formAdInfo.serialize(),
            success: function(data) {
                if (data.status == 'error') {
                    showErrorMessage(data.message);
                } else {
                    removeFieldErrorMessage();
                    loadUnits();
                    hideSaveHotspotButton();
                    hideUpdateHotspotButton();
                    clearMessage();
                    showSuccessMessage('Ad unit has been saved');

                    var datas = data.data;
                    
                    startTime = datas.start_time * 1000;
                    endTime = (datas.start_time + datas.duration) * 1000;
                    var description = ''
                    if (datas.description !== '') {
                        description=datas.creative.description
                    }


                    var json = {
                            "id": datas.id,
                            "hse_gid" : "",
                            "hse_name" : datas.name,
                            "ad_type":"hotspot", 
                            "direction": "top2bottom",
                            "start" : startTime,
                            "duration": endTime,
                            "position": datas.position,
                            "creativeInfo":{  
                                "hse_gname" : datas.name,
                                "image_url": creativeImage.attr('src'),
                                "thumbnail_url": creativeThumbnail.attr('src'),
                                "url": datas.target_url,
                                "type": datas.creative.type
                            }
                        };

                    if (datas.creative.type === 'info_card') {
                        json.creativeInfo["description"] = description;
                        json.creativeInfo["title"] = datas.name;
                    }

                    showAllAds();
                    hiddenNewAd();
                    rendererMgr.addAd(json);
                }


            },
            error: function(xhr, desc, err) {
                //form validation failed
                if (xhr.status === 422) {
                    var errors = JSON.parse(xhr.responseText);
                    showFieldErrorMessage(errors);
                    showErrorMessage("There is an error with your submission.");
                } else if (xhr.status === 400 && xhr.responseJSON.message === "Validation failed") {
                    showErrorMessage("You must select a creative.");
                }

                if (xhr.status === 404) {
                    showErrorMessage("Update failed, unit not found");
                }

            }
        });
    }

    function displaySaveHotspotButton()
    {
        saveHotspotBtn.removeClass('hidden');
        hideUpdateHotspotButton();
    }

    function hideSaveHotspotButton()
    {
        if (! saveHotspotBtn.hasClass('hidden')) {
            saveHotspotBtn.addClass('hidden');
        }
    }

    function displayUpdateHotspotButton()
    {
        updateHotspotBtn.removeClass('hidden');
        hideSaveHotspotButton();
    }

    function hideUpdateHotspotButton()
    {
        if (! updateHotspotBtn.hasClass('hidden')) {
            updateHotspotBtn.addClass('hidden');
        }
    }

    function loadUnits()
    {
        $.ajax({
            url: $('input[name=load_units_hotspot_url]').val(),
            type: 'GET',
            cache: false,
            success: function(data) {
                unitList.html(data);
            },
            error: function(xhr, desc, err) {
            }
        });
    }

    function loadCampaign(elem)
    {
        var advertiserId = elem.val();
        $.ajax({
            url: $('input[name=load_campaign_hotspot_url]').val() + '/' + advertiserId + '/campaigns' ,
            type: 'GET',
            cache: false,
            success: function(data) {
                campaignOptions.html(data);
            },
            error: function(xhr, desc, err) {
            }
        });

    }

    function loadCreatives()
    {
        var campaignId = selectCampaignId.val();
        var keyword = keywordInput.val();

        var url = $('input[name=load_creatives_hotspot_url]').val() + '?';
        url += 'campaign_id=' + campaignId + '&keyword='+ keyword;
        $.ajax({
            url: url,
            type: 'GET',
            cache: false,
            success: function(data) {
                creativeList.html(data);
            },
            error: function(xhr, desc, err) {
            }
        });
    }

    function showErrorMessage(message)
    {
        scrollToMessage();
        clearMessage();
        $("#alert-container").addClass('alert');
        $("#alert-container").addClass('alert-danger');
        $("#alert-container").html("<strong>Oops!</strong> " + message);
    }

    function showSuccessMessage(message)
    {
        scrollToMessage();
        clearMessage();
        $("#alert-container").addClass('alert');
        $("#alert-container").addClass('alert-success');
        $("#alert-container").html("<strong>Nice!</strong> " + message);
    }

    function clearMessage()
    {
        $("#alert-container").removeClass('alert alert-success alert-danger').html('');
    }

    function scrollToMessage()
    {
        $('body, html').animate({
            scrollTop: $("#alert-container").offset().top - 20
        }, 'slow');
    }

    function removeFieldErrorMessage()
    {
        $('.input-form').each(function () {
            $(this).parent().parent().parent().removeClass('has-danger').find("p").remove();
        })
        if ($('#time_started').addClass('has-danger')) {
            $('#time_started').removeClass('has-danger');
        }
        if ($('#time_ended').addClass('has-danger')) {
            $('#time_ended').removeClass('has-danger');
        }
    }

    function showFieldErrorMessage(errors)
    {
        removeFieldErrorMessage();
        $.each(errors, function (key, errorArray){
            $('#' + key).addClass('has-danger');
            $('#' + key).append('<p>' + errorArray[0] + '</p>');
        });
    }

    function removeDangerClass()
    {
        if (campaignEndTitle.hasClass("text-danger")) {
            campaignEndTitle.removeClass("text-danger");
        }
    }

});

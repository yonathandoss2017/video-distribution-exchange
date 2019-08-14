var currentPage = 1;
var currentSelectedPlaylist = null;
var selectedVideoList = []; // List video ids ticked

$(function() {
  // If access by playlists > select DPP action > New DPP
  // playlist id will fixing, and auto list videos in first page
  if (hasPlaylistId == true) {
    $('#view-videos').remove(); // Playlist id is fixing, view video button is unnecessary
    currentSelectedPlaylist = $('#playlists').val();
    loadFirstPage();
  }
  $('.selectpicker').select2();
});


function loadFirstPage() {
  currentPage = 1;
  getVideoList();
}

function showError(content) {
  $('#error-area').show();
  $('#error-text').text(content);
}

function hideError(content) {
  $('#error-area').hide();
}

function viewVideos() {
  if (!$('#organizationInput').val()) {
    showError(errorTexts.input_or_select_a_entity);
    return false;
  }
  if ($('#playlists').val() == null) {
    showError(errorTexts.select_a_playlist);
    return false;
  }
  loadFirstPage();
}

function getVideoList() {
    var playlist = $('#playlists').val();

    // If playlist selectbox value changed
    // when selected something video in old playlist,
    // reset count video text
    if (playlist != currentSelectedPlaylist) {
      selectedVideoList = [];
      updateCountVideoSelectedText();
    }
    currentSelectedPlaylist = playlist;

    var getVideoUrl = baseGetVideoUrl + '/' + playlist + '/video/list?page=' + currentPage;
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': window.params.csrfToken
        }
    });
    $.ajax({
      type: 'GET',
      url: getVideoUrl,
      data: {},
      dataType: 'html',
      success: function (data) {
        $('#video-select-area').show();
        $('#video-list').html(data);
        bindPaginationLinkOnClick();
        restoreSelectedItem();
      },
      error: function (data) {
        showError(errorTexts.general_error);
      }
    });
}

function bindPaginationLinkOnClick() {
  // Current this is <a> link element
  // Only <a> link can click
  $('li > .page-link')
  .not("li.active > .page-link")
  .not("li.disabled > .page-link")
  .on('click', function(e) {
      e.preventDefault();
      // Get video list by current page get from pagination link
      currentPage = this.getAttribute('href').split('?page=')[1];
      getVideoList();
    });
}

function restoreSelectedItem() {
  // Restore checked status of videos
  // after move to other page
  var checkboxs = $('#video-list-table').find('.chk-item');
  $.each(checkboxs, function( index, chkElement ) {
    var videoId = chkElement.getAttribute('data-id');
    if ( checkVideoIdInSelectedList(videoId) ) {
      chkElement.checked = true;
      checkTickCheckAll();
    }
  });
}

function checkVideoIdInSelectedList(videoId) {
  return findItemInArray(selectedVideoList, videoId) > -1;
}

// Check all item in table video list
function checkAll(checkbox) {
  var checkboxs = $('#video-list-table').find('.chk-item');
  var checkallValue = checkbox.checked;
  var videoId = '';
  for (var i = checkboxs.length - 1; i >= 0; i--) {
    checkboxs[i].checked = checkallValue;
    videoId = checkboxs[i].getAttribute('data-id');

    var index = findItemInArray(selectedVideoList, videoId);
    if (checkallValue) {
      // If check all
      if (index == -1) {
        // and not exist item in selected video list,
        // add video id to list
        selectedVideoList.push(videoId);
      }
      continue;
    }
    // If uncheck all and exist video id in selected video list,
    // remove item from list
    if (index > -1) {
      selectedVideoList.splice(index, 1);
    }
  }
  updateCountVideoSelectedText();
}

function selectItem(checkbox) {
  var videoId = checkbox.getAttribute('data-id');
  var index = findItemInArray(selectedVideoList, videoId);
  if (index > -1) {
    // Remove
    selectedVideoList.splice(index, 1);
  } else {
    // Add
    selectedVideoList.push(videoId);
  }
  checkTickCheckAll();
  updateCountVideoSelectedText();
}

function reviewVideos() {
  // No video selected
  if (selectedVideoList.length == 0) {
    $('#view-videos').focus();
    showError(errorTexts.please_select_videos);
    return false;
  }

  // User select some videos > change value selectbox > click Review Videos button,
  // value in playlist comboxbox need to restore to origin selected value
  var playlist = $('#playlists').val();
  if (playlist != currentSelectedPlaylist) {
    $('#playlists').val(currentSelectedPlaylist);
  }

  $('#selectedList').val(selectedVideoList.toString());
  $('#review-form').submit();
}

function checkTickCheckAll() {
  // Tick CheckAll checkbox if all checkbox item checked
  var checkboxsAll = $('#video-list-table').find('.chk-item');
  var checkboxsSelected = $('#video-list-table').find('.chk-item:checked');
  $('#chkCheckAll')[0].checked = (checkboxsAll.length == checkboxsSelected.length);
}

function updateCountVideoSelectedText() {
  $('.amount').text(selectedVideoList.length);
}

// Find a item in source array
function findItemInArray(arrSource, itemValue) {
  var index = -1;
  for (var i = arrSource.length - 1; i >= 0; i--) {
    if (arrSource[i] == itemValue) {
      return i;
    }
  }

  return index;
}
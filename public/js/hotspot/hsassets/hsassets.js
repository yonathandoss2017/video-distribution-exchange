    /*
      When user clicked on the file dialog.
      We do not upload the file in response to this yet.
      We just convert the image to a string so that it can be
      previewed here
     */
    $("#edit_imageUpload").change(function () {
        var ctrl = this;
        if (this.files && this.files[0]) {
            convertImageToString(this.files[0], function() {
                 $(ctrl).parent().find("input").val("");
                });
            
        }
    });



    //should be unnecessary here.
    function clearFileDialog() {
        var oldInput = document.getElementById('edit_imageUpload') ;
        var newInput = document.createElement('input');
        newInput.id    = oldInput.id ;
        newInput.type  = oldInput.type ;
        newInput.name  = oldInput.name ;
        newInput.size  = oldInput.size ;
        newInput.class  = oldInput.class ;
        oldInput.parentNode.replaceChild(newInput, oldInput); 
    }

    /*
     Check whether anything has been changed. Called if user wants
     to exit this panel.
     */
    function changed() {
        var ctrl = $("#edit_targetUrl");
        if (ctrl.attr('data_orig') != ctrl.val())
            return true;
        ctrl = $("#edit_gname");
        if (ctrl.attr('data_orig') != ctrl.val())
            return true;
        ctrl = $("#edit_image");
        if (ctrl.attr('data_orig') != ctrl.attr('src'))
            return true;
        return false;
    }
    function validate() {
        var ctrl = $("#edit_targetUrl");
        if(!$.trim(ctrl.val())) {
            alert("Please fill in the target URL.");
            return false;
        }
        ctrl = $("#edit_gname");
        if(!$.trim(ctrl.val())) {
            alert("Please fill in some keywords.");
            return false;
        }
        ctrl = $("#edit_image");
        if (ctrl.attr('src') == '') {
            alert("Please upload an image.");
            return false;
        }
        if ($('#dropDn_advertiser').val() == -2) {
            alert("Please select a valid advertiser.");
            return false;
        }
        
        return true;
    }
    
    /*
      Helper function
     */
     function convertImageToString(blob, failHandler) {
        var reader = new FileReader();
        reader.onload = function (e) {
          if (e.target.result.substr(0, 10) == "data:image") {
                $("#edit_image").attr('src', e.target.result);
                $("#edit_image").slideDown(); 
            }
            else {
                $("#edit_image").attr('src', '');
                alert("Please select a valid image file or provide a valid image URL.");
                failHandler();
            }
        };
        reader.readAsDataURL(blob);
    }

    /*
     This is dyn generated HTML content, so cannot wire up the click
     handlers in the "ready" handler.
     */
    var handlersWiredUp = false;
    var currGID = -1;
    var needReload = false;
    function wireUpHandlersMaybe() {
        //alert($("#listContainer").css('position'));
        //$("#listContainer").css('position', 'fixed');
        //if (handlersWiredUp) return;
        handlersWiredUp = true;
        $("#edit_imageUpload").change(function () {
            var ctrl = this;
            if (this.files && this.files[0]) {
                convertImageToString(this.files[0], function() {
                     $(ctrl).parent().find("input").val("");
                    });
            
            }
        });
        //To go open HS editor:
        $('.data-launch').on('click', function (e) {
            //if (!confirm('Are you sure you want to launch?')) return;
            e.preventDefault();
            
            window.location.hash = 'open' + currGID;
            $('#form-launch-' + $(this).data('form')).submit();
        });

        $('.gotoHSEditor').submit(function(event){
            window.location.hash = 'open' + currGID;
            return true;
        });
    
        $('#deleteBtn').on('click', function(event){
            $("#panel_add_advertiser").slideUp();
            return;
            if ($(".referer").length > 0) {
                alert("This item cannot be deleted as it is referenced by at least 1 hotspot.");
                return;                
            }
            if (currGID == -1) return; /* sth wrong */
            var data = {
                "gid" : currGID
            };
            genericAjaxCall('/hotspot/int_deleteAdGraphic', data, 
            function(response) {
                if (response.status == "success") {
                    needReload = true;
                    alert("Item deleted successfully.")
                    doClose();
                }
                else
                    alert("Error deleting item: " + response.info)
            }, 
            function(response) {});       
        });     
        $('#addOrUpdateBtn').on('click', function(event){
            if (!validate())
                return;
            if (currGID != -1 && !confirm('All hotspots current referencing this creative will be affected. Proceed?')) 
                return;
            
            var data = new FormData($("#imageupload_form")[0]);
            genericAjaxCall('/hotspot/int_updateAdGraphic', data, 
            function(response) {
                if (response.status == "success") {
                    needReload = true;
                    var ctrl = $("#edit_gname");
                    ctrl.attr('data_orig', ctrl.val());
                    ctrl = $("#edit_targetUrl");
                    ctrl.attr('data_orig', ctrl.val());
                    ctrl = $("#edit_image");
                    if (response.hasOwnProperty('imagepath')) {
                        ctrl.attr('data_orig', response.imagepath);
                        ctrl.attr('src', response.imagepath);
                    }
                    //imagepath
                    var delBtn = $('#deleteBtn');
                    if(!delBtn.is(':visible')){
                        currGID = response.id;
                        $("#gid").val(response.id);
                        $('#addOrUpdateBtn').prop('value', 'Update');
                        delBtn.removeClass('hidden');
                        alert("Added successfully.")    
                    }
                    else
                        alert("Updated successfully.")
                }
                else if (response.status == 'error') {
                    alert("Error adding or updating creative: " + response.info);
                }
            }, 
            function(response) {});       
        });    
        function closeAddAdvertiserPanel() {
            $("#edit_advertiserName").val("");
            ///$("#edit_advertiserCode").val("");
            $("#panel_add_advertiser").slideUp();
        }
        $('#dropDn_advertiser').on('change', function(event) {
            var ctrl = $(this);
            var selItem = ctrl.val();
            if (selItem == -2) {
                if ($("#panel_add_advertiser").is(':hidden'))
                    $("#panel_add_advertiser").slideDown();
            }                
            else if (!$("#panel_add_advertiser").is(':hidden'))
                closeAddAdvertiserPanel();
        });
        $('#addAdvertiserBtn').on('click', function(event){
            //ajax talk to DB loh
            //check for duplicate
            //overwrite ?
            var name = $("#edit_advertiserName").val().trim();
            ////var code = $("#edit_advertiserCode").val().trim();
            if (name == '') { //} || code == '') {
                alert("Please do not leave any field blank.")
                return;
            }
             var data = {
                "name" : name
                //"code" : code
            };
            genericAjaxCall('/hotspot/int_addAdvertiser', data, 
            function(response) {
                if (response.status == "success") {
                    id = response.id; 
                    $('#dropDn_advertiser').append("<option value="+ response.id +">" + response.displayname + "</option>").val(response.id);
                    closeAddAdvertiserPanel();

                }
                else
                    alert("Error adding advertiser. Please check if name already exists.")
            }, 
            function(response) {});       
        });
        $('#cancelAddAdvertiserBtn').on('click', function(event){
            closeAddAdvertiserPanel();
        });
           
    }

    /*
     AJAX post call to controller to dish out all info about the graphical
     asset item
     Most of the HTML on this side panel (1 graphical item) is generated in 
     response to the data returned from this call.
     */
     function openItem(gid) {
        var data = {
            "gid" : gid
        };
        console.log("openItem " + gid);
        currGID = gid;

        genericAjaxCall((gid == -1 ? '/hotspot/int_getBlankAdGraphicExtendedInfo':
            '/hotspot/int_getAdGraphicExtendedInfo'),
            data, 
            function(response) { 
                $("#detailedView").html(response);
                needReload = false;
                wireUpHandlersMaybe();
                $('.cd-panel').addClass('is-visible');  
            },
            function(response) {});
    } 

    /*
      Helper function for doing AJAX calls
     */
    function genericAjaxCall(url, mydata, successhandler, errhandler) {
        $("body").css("cursor", "progress");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }); 
        var paramObj = {
            url: url,
            data: mydata,
            type:'post',
            error:function(response){
                alert("ajax call err.");
                errhandler(response);
            },
            success:function(response){
                //alert("ajax call succeeded.")
                console.log(response);
                successhandler(response);
            }
        }; //bad bad bad
        if (url == '/hotspot/int_updateAdGraphic') {
            paramObj.processData = false;
            paramObj.contentType = false;
        }
        $.ajax(paramObj).done(function( response ) {
                $("body").css("cursor", "default");
         });
    } /* function genericAjaxCall */
  
    function doClose() {
        //$("#listContainer").css('position', 'relative');
        $('.cd-panel').removeClass('is-visible');
        //removeHash();
        if (needReload) {
            //alert("reload");
            location.reload();
        }
        //window.scrollTo(0,$('#' + currGID).offset().top);
    }

    function removeHash () { 
        window.location.hash = '';
        //history.pushState("", document.title, window.location.pathname
          //                             + window.location.search);
    }

    $(document).ready(
        function($){
        var gidMaybe = window.location.hash.substr(5);
        if (gidMaybe.length > 0) {
            var gid = parseInt(gidMaybe, 10);
            //console.log("gid is " + gid);
            openItem(gid);
            removeHash();
        }

        $('.cd-panel').on('click', function(event){
            if( $(event.target).is('.cd-panel') || $(event.target).is(' .cd-panel-close') ) { 
                if (changed() && !confirm('You have unsaved changes. Discard?')) 
                    return;
                doClose();
                event.preventDefault();
            }
        }); 
        $('#addBtn').on('click', function(event){
            openItem(-1);
            //event.preventDefault();
        }); 
        
        $('#keywords').val($('#keywords0').val());
        //$('#resultsPerPage').val($('#resultsPerPage0').val());
        $(".hsetooltip").tooltip({html : true});
        $('#keywords').bind('keypress', function(e) {
            var code = e.keyCode || e.which;
            if(code == 13) { //Enter keycode
                $('#search').trigger('click');
            }
        });
    });

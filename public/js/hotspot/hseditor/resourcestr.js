var rscStrings = {
   ADD_PANEL: "&nbsp;&nbsp;Add Advertisement",
   EDIT_PANEL: "&nbsp;&nbsp;Edit existing Advertisements",

   HOTSPOT: "Hotspot",
   BANNER: "Banner",
   VERTRECT : "Vertical Rectangle",

   AD_TITLE_HINT: "Ad Title",
   //HOTSPOT_TITLE_HINT: "title",
   //BANNER_TITLE_HINT: "title",
   //VERTRECT_TITLE_HINT: "title"

   START_AT_LABEL: "Start at",
   STOP_LABEL: "Stop at",
   DURATION_LABEL: "Duration",

   START_HERE_BTNLABEL: "Start here",
   STOP_HERE_BTNLABEL: "Stop here",
   SHOW_JSON_BTNLABEL: "Show JSON",
   SAVE_JSON_BTNLABEL: "Save",
   PREVIEW_BTNLABEL: "Preview Ad",
   COMMIT_BTNLABEL: "Add/Update",

   URL_HINT: "Link advertisement to URL",

   IMAGE_LABEL: "Advertisement Image",

   NO_FILE_CHOSEN_MSG: "No file chosen",
   INVALID_IMGAE_DIM_MSG: "Image has wrong dimensions",
   INVALID_TIME_FORMAT_MSG: "Invalid time format",
   UPLOAD_FAILED_MSG: "Failure writing JSON to database"


};

function printLabel(id) {
   document.write(rscStrings[id]);

}

function getLabel(id) {
   return (rscStrings[id]);

}
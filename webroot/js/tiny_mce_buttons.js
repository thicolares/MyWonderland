tinyMCE.init({
    theme : "advanced",
    //mode : "exact",
    mode : "specific_textareas",
    editor_selector : "mceEditor",
    //elements : "PageBody,NewsBody",
    language: "pt",
    plugins : "table,save,advhr,advimage,advlink,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
    theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull|cut,copy,paste,pasteword,|,bullist,numlist,link,unlink,cleanup,code",
    theme_advanced_buttons2 :"",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    convert_urls : false,
    external_image_list_url : "js/image_list.js"
});
$(function(){
    // Tool scripts
    var $body = $("body");

    var loader = '<div id="loader" class="overlay-loader"><img class="loader-icon spinning-cog" src="/MelisCore/assets/images/cog12.svg" data-cog="cog12"></div>';

    $body.on("click", ".melis-templating-plugin-creator-steps-content .btn-steps", function(){   
        var curStep = $(this).data("curstep");
        var nextStep = $(this).data("nextstep");

        var mainFormData = new FormData();
        var stepForm = $(".melis-templating-plugin-creator-steps-content form.templating-plugin-creator-step-"+curStep);

        //remove highlight errors
        $("form.templating-plugin-creator-step-"+curStep + " .form-control").parents('.form-group').find("label").css("color","#686868");  
        
        stepForm.each(function(index, val){
            var form_name = $(this).closest('form').attr('name');    
            var countForm = $('form[name='+form_name+']').length;
            var formData = new FormData($(this)[0]);
            var formValues = formData.entries();
               
            for(var pair of formValues){      
                if(countForm > 1){
                    mainFormData.append('step-form['+index+']['+pair[0]+']',pair[1]);
                }else{
                    mainFormData.append('step-form['+pair[0]+']',pair[1]);
                } 
            }   
           
            //add empty fields to form data
            $(this).find('input[type="radio"]:not(:checked),input[type="checkbox"]:not(:checked),input[type="text"][value=""],select[value=""]').each(function(){    
                if(countForm > 1){
                    var key = 'step-form['+index+']['+this.name+']';                                              
                }else{
                    var key = 'step-form['+this.name+']';
                }          

                if(!mainFormData.has(key)){                    
                    mainFormData.append(key, "");
                }               
            }); 
        });
   
        mainFormData.append('curStep',curStep);
        mainFormData.append('nextStep',nextStep);       
       
        if ($(this).hasClass("tpc-validate")){
            mainFormData.append('validate',true);          
        }         
        
        //commented while still testing
        //$("#id_melistemplatingplugincreator_steps").append(loader);

        $.ajax({
            type: 'POST',
            url: '/melis/MelisTemplatingPluginCreator/TemplatingPluginCreator/renderTemplatingPluginCreatorSteps',
            data: mainFormData,          
            dataType: "json",
            encode: true,
            cache: false,
            contentType: false,
            processData: false
        }).done(function (data) {
            
            //$("#id_melistemplatingplugincreator_steps #loader img").removeClass('spinning-cog').addClass('shrinking-cog');

            setTimeout(function(){
                if(!data.errors) {    
                  
                    if(data.textMessage){
                        melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);
                        $("#id_melistemplatingplugincreator_steps #loader").remove();
                    }else{
                        $("#id_melistemplatingplugincreator_steps").html(data.html);
                        $(".melis-templating-plugin-creator-steps li").removeClass("active");
                        var targetId = $("#id_melistemplatingplugincreator_steps .steps-content").attr("id");
                        $("#tpc_"+targetId).addClass("active");  
                    }

                }else{

                    //check if plugin thumbnail error is set
                    if(data.errors.tpc_plugin_upload_thumbnail){
                        //remove the thumbnail display
                        if($(".plugin_thumbnail_div").length){
                            $(".plugin_thumbnail_div").remove();
                        }
                    }

                    if(curStep == 3 || curStep == 4){
                        melisKoNotificationCustomized(curStep, data.textTitle, data.textMessage, data.errors);
                    }else{
                        melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);
                    }                    
         
                    highlightErrors(curStep, data.errors, ".templating-plugin-creator-step-"+curStep);                
                   
                    $("#id_melistemplatingplugincreator_steps #loader").remove();
                }

            }, 500);

        }).fail(function(xhr, textStatus, errorThrown) {  
            alert( translations.tr_meliscore_error_message );
        });   
    });

    // customized error notification for steps 3 and 4
    function melisKoNotificationCustomized(curStep, title, message, errors, closeByButtonOnly) {
        if (!closeByButtonOnly) closeByButtonOnly = "closeByButtonOnly";
        closeByButtonOnly !== "closeByButtonOnly"
            ? (closeByButtonOnly = "overlay-hideonclick")
            : (closeByButtonOnly = "");

        var errorTexts = "<h3>" + melisHelper.melisTranslator(title) + "</h3>";
        errorTexts += "<h4>" + melisHelper.melisTranslator(message) + "</h4>";

        $.each(errors, function(fieldKey, fieldError) {            
            if(curStep == 3){
                if(fieldKey == 'tpc_main_property_field_count'){
                    if(fieldKey !== "label") {
                        var label =
                            errors[fieldKey]["label"] == undefined
                                ? errors["label"] == undefined
                                    ? fieldKey
                                    : errors["label"]
                                : errors[fieldKey]["label"];
                        errorTexts +=
                            '<p class="modal-error-cont"><b title="' +
                            label +
                            '">' +
                            label +
                            ": </b>  ";

                        // catch error level of object
                        try {
                            $.each(fieldError, function(key, value) {
                                if (key !== "label") {
                                    errorTexts +=
                                        '<span><i class="fa fa-circle"></i>' + value + "</span>";
                                }
                            });
                        } catch (Tryerror) {
                            if (fieldKey !== "label") {
                                errorTexts +=
                                    '<span><i class="fa fa-circle"></i>' + fieldError + "</span>";
                            }
                        }
                        errorTexts += "</p>";
                    }
                }else{
                    errorTexts = setFieldErrors(curStep, fieldKey, fieldError, errorTexts); 
                }

            }else if(curStep == 4){                
                $.each(fieldError, function(fieldKey, error) {
                    errorTexts = setFieldErrors(curStep, fieldKey, error, errorTexts);
                });
            }                       
        });       

        var div =
            "<div class='melis-modaloverlay " + closeByButtonOnly + "'></div>";
        div +=
            "<div class='melis-modal-cont KOnotif'>  <div class='modal-content'>" +
            errorTexts +
            " <span class='btn btn-block btn-primary'>" +
            translations.tr_meliscore_notification_modal_Close +
            "</span></div> </div>";
        $body.append(div);
    }

    //set errors to be displayed for each field
    function setFieldErrors(curStep, fieldKey, fieldError, errorTexts){
        //update field key for the display      
        if (curStep == 3) {
            fieldKey = translations.tr_melistemplatingplugincreator_field + " " + fieldKey.split('-').pop();
        } else if(curStep == 4) {
            fieldKey = translations.tr_melistemplatingplugincreator_field + " " + fieldKey.split('_').pop();
        }   

        errorTexts +=
                    '<p class="modal-error-cont"><b title="' +
                    fieldKey +
                    '">' +
                    fieldKey +
                    ": </b><br>  ";
        
        $.each(fieldError, function(key, error) {
            if (key !== "label") {
                var label =
                    fieldError[key]["label"] == undefined
                        ? fieldError["label"] == undefined
                            ? key
                            : fieldError["label"]
                        : fieldError[key]["label"];

                errorTexts +=
                    '<p class="modal-error-cont"><b title="' +
                    label +
                    '">' +
                    label +
                    ": </b>  ";

                // catch error level of object
                try {

                    if(curStep == 3){                        
                        $.each(error, function(key, value) {
                       
                            if (key !== "label") {
                                $.each(value, function(key1, value1) {                                        
                                    if (key1 !== "label") {                                        
                                        errorTexts +=
                                            '<span><i class="fa fa-circle"></i>' + value1 + "</span>";
                                    }
                                });
                            }
                        });

                    }else{
                        $.each(error, function(key, value) {
                            if (key !== "label") {
                                errorTexts +=
                                    '<span><i class="fa fa-circle"></i>' + value + "</span>";
                            }
                        });
                    }
                  
                } catch (Tryerror) {

                    if (key !== "label") {

                        errorTexts +=
                            '<span><i class="fa fa-circle"></i>' + error + "</span>";
                    }
                }
                errorTexts += "</p>";
            }
        });

        return errorTexts;
    }

    /*this will highlight form fields with errors*/
    function highlightErrors(step, errors, divContainer) {  
        if(step == 3){
            $.each( errors, function( key, error ) { 
                if(key == 'tpc_main_property_field_count'){
                    $("form"+divContainer + " .form-control[name='"+key +"']").parents('.form-group').find("label").css("color","red");                       
                }else{
                    $.each( error, function( key1, error1 ) {                        
                        $("form#"+key + " .form-control[name='"+key1 +"']").parents('.form-group').find("label").css("color","red");           
                    });
                }  
            });
        }else if(step == 4){
            $.each( errors, function( key, error ) { 
                $.each( error, function( key1, error1 ) {                      
                    //var fieldNumber = key1.slice(key1.length - 1);    
                    var fieldNumber = key1.split('_').pop();             
                    $.each( error1, function( key2, error2) {  
                        $.each($("#melistemplatingplugincreator_step4 form"), function() { 
                            var formId = $(this).attr('id');                           
                            var tpc_field_num = $(this).find('input[name=tpc_field_num]').val();
                          
                            if(tpc_field_num == fieldNumber){                                
                                $(this).find("input.form-control[name='"+key2 +"']").parents('.form-group').find("label").css("color","red"); 
                            }
                        });                                  
                    });                              
                });             
            });
        }else{           
            $.each( errors, function( key, error ) {                  
                $("form"+divContainer + " .form-control[name='"+key +"']").parents('.form-group').find("label").css("color","red");           
            });
        }        
    } 

    /*this will hide or show the New Module or Existing Module Name options based on the selected plugin destination*/
    $body.on("change", "#templating-plugin-creator-step-1 input[name='tpc_plugin_destination']", function() {           
        if($('input:radio[name=tpc_plugin_destination]:checked').val() == "new_module"){
            $("#tpc_new_module_name").parents('.form-group').show();
            $("#tpc_existing_module_name").parents('.form-group').hide();   
            $("#tpc_existing_module_name option").prop("selected", false);        
        }else{
            $("#tpc_new_module_name").parents('.form-group').hide();
            $("#tpc_new_module_name").val('');
            $("#tpc_existing_module_name").parents('.form-group').show();    
        }
    });

    /*manually sets the checked properties of Activate Plugin checkbox in the step 5 form*/
    $body.on("click", ".melis-tpc-final-content .fa", function(){
        if ($(this).hasClass("fa-check-square-o")){
            // unChecking
            $(this).addClass("fa-square-o");
            $(this).removeClass("text-success");
            $(this).removeClass("fa-check-square-o");
            $(this).next("input").attr("checked", false);
        }else{
            // Checking
            $(this).removeClass("fa-square-o");
            $(this).addClass("fa-check-square-o");
            $(this).addClass("text-success");
            $(this).next("input").attr("checked", true);
        }
    });


    /*when templating plugin creator tab is closed, delete the temp thumbnail folder for the current session if there are any*/
    $body.on("click", "a[data-id=id_melistemplatingplugincreator_tool]", function(e){          
        $.ajax({
            type: 'POST',
            url: '/melis/MelisTemplatingPluginCreator/TemplatingPluginCreator/removeTempThumbnailDir',
            data: {},          
            dataType: "text",           
        }).done(function (data) {                   
        }).fail(function(xhr, textStatus, errorThrown) {  
            alert( translations.tr_meliscore_error_message );
        }); 
    }); 

    /*upload plugin thumbnail*/
    $body.on("change", "#tpc_plugin_upload_thumbnail", function(e){                  
        var uploadUrl = '/melis/MelisTemplatingPluginCreator/TemplatingPluginCreator/processUpload';        
        var form = $("#id-templating-plugin-creator-thumbnail-upload-form")[0]; 
        var uploadFormData = new FormData(form);
        uploadFormData.append('file', $('input[type=file]')[0].files[0]); 
        
        melisCoreTool.pending(".btn-steps");

        $.ajax({
            type: 'POST',
            url:  uploadUrl,
            data: uploadFormData,          
            dataType: "json",
            encode: true,
            cache: false,
            contentType: false,
            processData: false,
            async:true,
            xhr: function () {
                var fileXhr = $.ajaxSettings.xhr();
                    if (fileXhr.upload) {
                        fileXhr.upload.addEventListener('progress', templatingPluginCreatorTool.progress, false);
                    }
                    return fileXhr;
            }
        }).done(function (data) {
           
            if(data.success){   
                $("div.progressContent").addClass("hidden");

                //remove highlight errors
                $("form#id-templating-plugin-creator-thumbnail-upload-form .form-control").parents('.form-group').find("label").css("color","#686868");  

                //show preview after successful upload                   
                var uploadArea = $("#pluginThumbnailUploadArea");
                var imageHolder = $(".plugin_thumbnail_div");
                
                if(imageHolder.length){                   
                    imageHolder.empty();
                }else{                    
                    //append image holder to the upload area div
                    $('<div class="col-xs-12 col-lg-6"><div class="plugin_thumbnail_div"></div></div>').appendTo(uploadArea);     
                }   
               
                $("<img />", {
                    "src": data.pluginThumbnail,
                    "class": "plugin_thumbnail"
                }).appendTo('.plugin_thumbnail_div');

                //append view and remove thumbnail icons
                $('<div class="hover-details">'+
                    '<div class="me-p-btn-cont">'+
                        '<a id="plugin-thumbnail-eye" class="viewImageDocument" href="" target="_blank">'+
                            '<i class="fa fa-eye"></i>'+
                        '</a>'+
                        '<a id="removePluginThumbnail" data-type="image">'+
                            '<i class="fa fa-times"></i>' + 
                        '</a>'+
                    '</div></div>').appendTo('.plugin_thumbnail_div');     

                //set href src to view icon
                $("#plugin-thumbnail-eye").attr('href', data.pluginThumbnail);    
                imageHolder.show();
                   
            }else{       

                $("div.progressContent").addClass("hidden");

                if($('.plugin_thumbnail_div').length){
                    $('.plugin_thumbnail_div').remove();
                }
                
                melisHelper.melisKoNotification(data.textTitle, data.textMessage, data.errors);
                highlightErrors(0, data.errors, ".templating-plugin-creator-step-2");
            }
            
            melisCoreTool.done(".btn-steps");

        }).fail(function(xhr, textStatus, errorThrown) {  
            alert( translations.tr_meliscore_error_message );
        });    
      
    });

    /*when remove icon is clicked, remove the uploaded thumbnail from the session*/
    $body.on("click", ".melis-templating-plugin-creator-steps-content #removePluginThumbnail", function() { 
        melisCoreTool.pending("#removePluginThumbnail");
        melisCoreTool.confirm(
            translations.tr_melistemplatingplugincreator_common_label_yes,
            translations.tr_melistemplatingplugincreator_common_label_no,
            translations.tr_melistemplatingplugincreator_delete_plugin_thumbnail_title,
            translations.tr_melistemplatingplugincreator_delete_plugin_thumbnail_confirm,              
            function() {   
                var ajaxUrl = '/melis/MelisTemplatingPluginCreator/TemplatingPluginCreator/removePluginThumbnail';

                $.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    data: null,
                    dataType: 'json',
                    encode: true
                }).done(function (data) {                        
                    if(data.success) {            
                        $('#tpc_plugin_upload_thumbnail').val('');
                        $('.plugin_thumbnail_div').remove();                  
                    } else {
                        melisHelper.melisKoNotification(translations.tr_melistemplatingplugincreator_delete_plugin_thumbnail_title, translations.tr_melistemplatingplugincreator_delete_plugin_thumbnail_error, null);
                    }
                    melisCore.flashMessenger();
                }).fail(function () {
                    alert( translations.tr_meliscore_error_message );
                });                              
          });

        melisCoreTool.done("#removePluginThumbnail");
    });


    /*when '# of Fields' is filled up, set and display the field forms, wait for 1 sec before execution so that we can be sure that the user is done typing */
    var typingTimer;                //timer identifier
    var doneTypingInterval = 1000;  //time in ms (1 second)    
    $body.on("keyup", ".melis-templating-plugin-creator-steps-content #tpc_main_property_field_count", function() { 
        var fieldCount = $('#tpc_main_property_field_count').val();

        clearTimeout(typingTimer);
        
        typingTimer = setTimeout(function() {
            if($.isNumeric(fieldCount)){
                if(fieldCount > 0 && fieldCount <= 25){
                    //remove highlight errors
                    $("#tpc_main_property_field_count").parents('.form-group').find("label").css("color","#686868");  
                                
                    //get first the current values of the field form if there are any
                    var curStep = $('#tpc_main_property_field_count').parents().find(".tpc-validate").data("curstep");               
                    var fieldFormData = new FormData();
                    var fieldForm = $(".melis-templating-plugin-creator-steps-content form.templating-plugin-creator-step-"+curStep);
                    
                    fieldForm.each(function(index, val){
                        var form_name = $(this).closest('form').attr('name');    
                        var countForm = $('form[name='+form_name+']').length;
                        var formData = new FormData($(this)[0]);
                        var formValues = formData.entries();
                           
                        for(var pair of formValues){   
                            if(countForm > 1){
                                fieldFormData.append('step-form['+index+']['+pair[0]+']',pair[1]);
                            }else{
                                fieldFormData.append('step-form['+pair[0]+']',pair[1]);
                            } 
                        } 
                        
                    });    

                    //append current step
                    fieldFormData.append('curStep',curStep); 

                    //retrieve field forms              
                    getFieldForms(fieldFormData);                  

                }else{    
                    //empty field form div
                    $('#field-form-div').empty();       
                    melisHelper.melisKoNotification(translations.tr_melistemplatingplugincreator_title_step_3, translations.tr_melistemplatingplugincreator_value_must_be_between_1_to_25, null);
                }  
            }else{
                //empty field form div
                $('#field-form-div').empty();
                 melisHelper.melisKoNotification(translations.tr_melistemplatingplugincreator_title_step_3, translations.tr_melistemplatingplugincreator_integer_only, null);
            } 
        }, doneTypingInterval);        
    });
    
    /*when 'display type' field is selected, update the 'default value' field accordingly*/
    $body.on('focusin', '.melis-templating-plugin-creator-steps-content #tpc_field_display_type', function(){        
        $(this).data('val', $(this).val());
    }).on('change','.melis-templating-plugin-creator-steps-content #tpc_field_display_type', function(){
        var prev = $(this).data('val');
        var current = $(this).val();
        var tpc_default_val_input = '<input class = "form-control" type = "text" id = "tpc_field_default_value" name = "tpc_field_default_value" value="">';

        //unset tpc_field_default_value and tpc_field_default_options values
        $(this).parents('form').find('#tpc_field_default_value, #tpc_field_default_options').val("");

        $(this).blur();

        // format 'Default Value' field type based on the selected 'Display Type' 
        if (prev == 'Dropdown' && current != 'Dropdown') {    
            //hide default options field          
            $(this).parents('form').find('#tpc_field_default_options').removeAttr('data-role');
            $(this).parents('form').find('#tpc_field_default_options').tagsinput('destroy');
            $(this).parents('form').find("#tpc_field_default_options").parents('.form-group').hide(); 

            /*change back the 'default value' fiele to input type*/          
            $(this).parents('form').find('#tpc_field_default_value').closest('.form-group.input-group').empty().append(tpc_default_val_input);   
        } else if ( (prev == 'DatePicker' && current != 'DatePicker') || (prev == 'DateTimePicker' && current != 'DateTimePicker')) {           
            $(this).parents('form').find('#tpc_field_default_value').datetimepicker("destroy");   
        } else if ( (prev == 'Switch' && current != 'Switch') || (prev == 'PageInput' && current != 'PageInput')) {            
            $(this).parents('form').find('#tpc_field_default_value').closest('.form-group.input-group').empty().append(tpc_default_val_input);  
        } else if (prev == 'MelisCoreTinyMCE' && current != 'MelisCoreTinyMCE') {   
            $(this).parents('form').find('textarea[name="tpc_field_default_value"]').closest('.form-group.input-group').empty().append(tpc_default_val_input);                       
        }


        if (current == 'Dropdown') {   
            $(this).parents('form').find("#tpc_field_default_options").parents('.form-group').show();            
            $(this).parents('form').find('#tpc_field_default_options').attr('data-role','tagsinput');
            
            //disallow duplicate dropdown values
            $(this).parents('form').find('#tpc_field_default_options').tagsinput({
                allowDuplicates: false
            });

            $(this).parents('form').find('#tpc_field_default_options').tagsinput('refresh');

            /*set the 'default_value' field as dropdown*/
            $(this).parents('form').find('#tpc_field_default_value').replaceWith('<div class="col-md-2 padding-left-0"><select id="tpc_field_default_value" name="tpc_field_default_value" class="form-control">'+
                '<option value="">Choose</option></select></div>');   
        } else if (current == 'DatePicker') {                      
            $(this).parents('form').find('#tpc_field_default_value').datetimepicker({format: "YYYY-MM-DD"});            
        } else if (current == 'DateTimePicker'){           
            $(this).parents('form').find('#tpc_field_default_value').datetimepicker({format: "YYYY-MM-DD HH:mm:ss"});           
        } else if (current == 'Switch'){           
            $(this).parents('form').find('#tpc_field_default_value').replaceWith('<div class="col-md-2 padding-left-0"><select id="tpc_field_default_value" name="tpc_field_default_value" class="form-control">'+
                '<option value="">Choose</option><option value="1">On</option><option value="0">Off</option></select></div>');   
        } else if (current == 'PageInput') {    
            /*add sitemap icon to default_val input field*/
            $(this).parents('form').find('#tpc_field_default_value').closest('.form-group.input-group').empty().
            prepend('<div class="d-flex flex-row justify-content-between">'+tpc_default_val_input+'<a class="btn btn-default meliscms-site-selector"><i class="fa fa-sitemap"></i></a></div>');
        } else if (current == 'MelisCoreTinyMCE') {  
            //generate unique id
            var fieldId = Math.round(new Date().getTime() + (Math.random() * 100));
            $(this).parents('form').find('#tpc_field_default_value').replaceWith('<textarea data-tinymce-id="'+fieldId+'" id="'+fieldId+'" class="form-control" name="tpc_field_default_value"></textarea>');
            //initialize tiny mce 
            melisTinyMCE.createTinyMCE("tool", "textarea[data-tinymce-id=\'"+fieldId+"\']", {height: 200, relative_urls: false,  remove_script_host: false, convert_urls : false});
        }   
    });

    //add 'Default Option' value to 'Default Value' Dropdown
    $body.on('itemAdded', '#tpc_field_default_options', function(event) {
        $(this).parents('form').find('#tpc_field_default_value').append('<option value="'+event.item+'">'+event.item+'</option>');
    });

    //remove 'Default Option' value from 'Default Value' Dropdown
    $body.on('itemRemoved', '#tpc_field_default_options', function(event) {            
       $(this).parents('form').find("#tpc_field_default_value option[value='"+event.item+"']").remove();  
    });

    /*this will dynamically get the field forms, form count is based on the entered '# of Fields' value
    * Main properties is tab 1, then next tab is 2 and so forth
    */
    function getFieldForms(fieldFormData){
        var url = '/melis/MelisTemplatingPluginCreator/TemplatingPluginCreator/getFieldForm'; 
        $.ajax({
            type: 'POST',
            url: url,
            data: fieldFormData,
            dataType: 'text',
            encode: true,
            cache: false,
            contentType: false,
            processData: false
        }).done(function (data) {  
            $('#field-form-div').html(data);
            fieldFormInit();
            widgetCollapsibleInit();         
        }).fail(function () {
            alert( translations.tr_meliscore_error_message );
        });  
    }
});

/*ref: newstool.js*/
var templatingPluginCreatorTool = {  
    progress: function progress(e) {
        var progressContent = $("div.progressContent");
            progressContent.removeClass("hidden");

        var progressBar = $("div.progressContent > div.progress > div.progress-bar");
            progressBar.attr("aria-valuenow", 0).css("width", '0%');
            
        var status = $("div.progressContent > div.progress > span.status");
            status.html("");

        if ( e.lengthComputable ) {
            var max         = e.total,
                current     = e.loaded,
                percentage  = (current * 100) / max;

                progressBar.attr("aria-valuenow", percentage);
                progressBar.css("width", percentage + "%");

                if (percentage > 100 ) {                   
                    progressContent.addClass("hidden");
                }
                else {
                    status.html(Math.round(percentage) + "%");
                }
        }else{
            alert('not computable');
        }
    }
};

/*reference: /melis-commerce/public/js/widget-collapsible.init.js*/
var widgetCollapsibleInit = function(){    
    $('.widget[data-toggle="collapse-widget"] .widget-body')
        .on('show.bs.collapse', function(){
            $(this).parents('.widget:first').attr('data-collapse-closed', "false");
        })
        .on('shown.bs.collapse', function(){
            setTimeout(function(){ $(window).resize(); }, 500);
        })
        .on('hidden.bs.collapse', function(){
            $(this).parents('.widget:first').attr('data-collapse-closed', "true");
        });
    
    $('.widget[data-toggle="collapse-widget"]').each(function()
    {
       // append toggle button
        if (!$(this).find('.widget-head > .collapse-toggle').length)
            $('<span class="collapse-toggle"></span>').appendTo($(this).find('.widget-head'));
        
        // make the widget body collapsible
        $(this).find('.widget-body').not('.collapse').addClass('collapse');
        
        // verify if the widget should be opened
        if ($(this).attr('data-collapse-closed') !== "true")
            $(this).find('.widget-body').addClass('in');
        
        // bind the toggle button
        $(this).find('.accordionTitle').on('click', function(){
            //close all accordion before toggling the selected one
            var accordionCont = $(".prices-accordion.active");
            $.each(accordionCont, function(){
                var widget = $(this).find(".widget[data-collapse-closed=false]");
                widget.find('.widget-body').collapse('toggle');
            });
            //toogle the selected accordion
            $(this).parents('.widget:first').find('.widget-body').collapse('toggle');
        });
    }); 
};


/*reference: /melis-commerce/public/js/widget-collapsible.init.js*/
var fieldFormInit = function(){  
    $.each($("#melistemplatingplugincreator_step3 form[name=templating-plugin-creator-step-3-field-form]").not(":eq(0)"), function( key, error ) {   
        var tpc_default_val_input = '<input class = "form-control" type = "text" id = "tpc_field_default_value" name = "tpc_field_default_value" value="">';
        var defaultVal = $(this).find('#tpc_field_default_value').val();

        if ($(this).find("#tpc_field_display_type").val() == "Dropdown") {   

            //show Default Options field         
            $(this).find("#tpc_field_default_options").parents('.form-group').show();  
            $(this).find('#tpc_field_default_options').tagsinput('refresh');             
       
            //set Default Value field as select type
            $(this).find('#tpc_field_default_value').replaceWith('<div class="col-md-2 padding-left-0"><select id="tpc_field_default_value" name="tpc_field_default_value" class="form-control">'+
                '<option value="">Choose</option>'+
                '</select></div>');

            //set 'Default Value' select options based on Default Options value
            var defaultOptions = $(this).find('#tpc_field_default_options').val();         
            if(defaultOptions.length){               
                console.log('default options not null, add values to default field');
                defaultOptions = defaultOptions.split(',');
                var defaultValueField = $(this).find('#tpc_field_default_value');
                $.each(defaultOptions, function(key, value) {   
                    defaultValueField.append('<option value="'+value+'">'+value+'</option>');
                });

                if (defaultVal != '') {
                    $(this).find('#tpc_field_default_value option[value="'+defaultVal+'"]').attr('selected','selected');
                }                  
            }

        } else if ($(this).find("#tpc_field_display_type").val() == "DatePicker") {
            //initialize datepicker
            $(this).find('#tpc_field_default_value').datetimepicker({format: "YYYY-MM-DD"});

        } else if ($(this).find("#tpc_field_display_type").val() == "DateTimePicker") {
            //initialize datepicker
            $(this).find('#tpc_field_default_value').datetimepicker({format: "YYYY-MM-DD HH:mm:ss"});

        } else if ($(this).find("#tpc_field_display_type").val() == "Switch") {        

            //set Default Value options     
            $(this).find('#tpc_field_default_value').replaceWith('<div class="col-md-2 padding-left-0"><select id="tpc_field_default_value" name="tpc_field_default_value" class="form-control">'+
                '<option value="">Choose</option><option value="1" >On</option><option value="0">Off</option></select></div>');
            $(this).find('#tpc_field_default_value').val(defaultVal);  

        } else if ($(this).find("#tpc_field_display_type").val() == "PageInput") {  
            /*add sitemap icon to 'Default Value' input field*/
            $(this).find('#tpc_field_default_value').closest('.form-group.input-group').empty().
            prepend('<div class="d-flex flex-row justify-content-between">'+tpc_default_val_input+'<a class="btn btn-default meliscms-site-selector"><i class="fa fa-sitemap"></i></a></div>');

            $(this).find('#tpc_field_default_value').val(defaultVal);  
        } else if ($(this).find("#tpc_field_display_type").val() == 'MelisCoreTinyMCE'){    

            var fieldId = Math.round(new Date().getTime() + (Math.random() * 100));           

            $(this).find('#tpc_field_default_value').replaceWith('<textarea data-tinymce-id="'+fieldId+'" id="'+fieldId+'" class="form-control" name="tpc_field_default_value">'+defaultVal+'</textarea>');
            
            melisTinyMCE.createTinyMCE("tool", "textarea[data-tinymce-id=\'"+fieldId+"\']", {height: 200, relative_urls: false,  remove_script_host: false, convert_urls : false});
        }                 
    });    
};

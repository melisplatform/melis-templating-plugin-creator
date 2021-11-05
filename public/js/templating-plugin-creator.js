$(function(){
    // Tool scripts
    var $body = $("body");

    var loader = '<div id="loader" class="overlay-loader"><img class="loader-icon spinning-cog" src="/MelisCore/assets/images/cog12.svg" data-cog="cog12"></div>';

    $body.on("click", ".melis-templating-plugin-creator-steps-content .btn-steps", function(){   
        var curStep = $(this).data("curstep");
        var nextStep = $(this).data("nextstep");

        var dataString = new Array;
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
            console.log( translations.tr_meliscore_error_message );
        });   
    });

    // KO NOTIFICATION test only
    function melisKoNotificationCustomized(curStep, title, message, errors, closeByButtonOnly) {
        if (!closeByButtonOnly) closeByButtonOnly = "closeByButtonOnly";
        closeByButtonOnly !== "closeByButtonOnly"
            ? (closeByButtonOnly = "overlay-hideonclick")
            : (closeByButtonOnly = "");

        var errorTexts = "<h3>" + melisHelper.melisTranslator(title) + "</h3>";
        errorTexts += "<h4>" + melisHelper.melisTranslator(message) + "</h4>";

        $.each(errors, function(fieldKey, fieldError) {
            console.log('fieldKey ' + fieldKey);

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
                console.log('curStep is 4');

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


    function setFieldErrors(curStep, fieldKey, fieldError, errorTexts){
        //update field key for the display
        fieldKey = "Field " + fieldKey.slice(fieldKey.length - 1);

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
                    var fieldNumber = key1.slice(key1.length - 1);                    
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
        console.log('destination change templating ');

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


    /*when templating plugin creator tab is closed, delete the temp thumbnail folder for the session if there are any*/
    $body.on("click", "a[data-id=id_melistemplatingplugincreator_tool]", function(e){          
        $.ajax({
            type: 'POST',
            url: '/melis/MelisTemplatingPluginCreator/TemplatingPluginCreator/removeTempThumbnailDir',
            data: {},          
            dataType: "text",           
        }).done(function (data) {                   
        }).fail(function(xhr, textStatus, errorThrown) {  
            console.log( translations.tr_meliscore_error_message );
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
                        fileXhr.upload.addEventListener('progress', pluginCreatorTool.progress, false);
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
            console.log( translations.tr_meliscore_error_message );
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
                        melisHelper.melisKoNotification(translations.tr_melistemplatingplugincreator_delete_plugin_thumbnail_title, translations.tr_melisdashboardplugincreator_delete_plugin_thumbnail_error, null);
                    }
                    melisCore.flashMessenger();
                }).fail(function () {
                    alert( translations.tr_meliscore_error_message );
                });                              
          });

        melisCoreTool.done("#removePluginThumbnail");
    });
    
    /*when '# of Fields' is filled up, set and display the field forms */
    $body.on("blur", ".melis-templating-plugin-creator-steps-content #tpc_main_property_field_count", function() { 
        
        var fieldCount = $('#tpc_main_property_field_count').val();
        if(fieldCount > 0){
            getFieldForms(fieldCount, $('#tpc_main_property_field_count').parents().find('.tpc-validate').data('curstep'), $('#tpc_property_tab_number').val());           
        }else{
            melisHelper.melisKoNotification(translations.tr_melistemplatingplugincreator_title_step_3, translations.tr_melistemplatingplugincreator_greater_than_0, translations.tr_melistemplatingplugincreator_err_message);
        }      
    });

    
    // /*when 'Dropdown' is selected as the display type, add data-role=tagsinput to tpc_field_display_value field */
    // $body.on("change", ".melis-templating-plugin-creator-steps-content #tpc_field_display_type", function() { 
        
    //     console.log('change display type values: ' + $(this).val());

    //     if($(this).val() == 'Dropdown'){
    //         console.log('val is dropdown, add tagsinput');
    //         $(this).parents().find('#tpc_field_default_value').attr('data-role','tagsinput');
    //         $(this).parents().find('#tpc_field_default_value').tagsinput('refresh');
    //     }else{
    //         console.log('val is not dropdown, remove tagsinput');
    //         $(this).parents().find('#tpc_field_default_value').removeAttr('data-role');
    //         $(this).parents().find('#tpc_field_default_value').tagsinput('destroy');
    //     }                                  
                
    // });



    $body.on('focusin', '.melis-templating-plugin-creator-steps-content #tpc_field_display_type', function(){
        console.log("Saving value " + $(this).val());
        $(this).data('val', $(this).val());
    }).on('change','.melis-templating-plugin-creator-steps-content #tpc_field_display_type', function(){
        var prev = $(this).data('val');
        var current = $(this).val();

        //unset tpc_field_default_value value
        $(this).parents('form').find('#tpc_field_default_value').val("");

        $(this).blur();

        if(prev == 'Dropdown' && current != 'Dropdown'){            
            $(this).parents('form').find('#tpc_field_default_value').removeAttr('data-role');
            $(this).parents('form').find('#tpc_field_default_value').tagsinput('destroy');
        }else if( (prev == 'DatePicker' && current != 'DatePicker') || (prev == 'DateTimePicker' && current != 'DateTimePicker')){           
            $(this).parents('form').find('#tpc_field_default_value').datetimepicker("destroy");            
        }else if(prev == 'Switch' && current != 'Switch'){
            console.log('prev switch and current is not switch, remove select');
            $(this).parents('form').find('#tpc_field_default_value').replaceWith('<input type="text" id="tpc_field_default_value" name="tpc_field_default_value" value="" class="form-control">');           
        }

        if(current == 'Dropdown'){
            console.log('val is dropdown, add tagsinput');
            $(this).parents('form').find('#tpc_field_default_value').attr('data-role','tagsinput');
            
            //disallow duplicate dropdown values
            $(this).parents('form').find('#tpc_field_default_value').tagsinput({
                allowDuplicates: false
            });

            $(this).parents('form').find('#tpc_field_default_value').tagsinput('refresh');
        }else if(current == 'DatePicker'){    
            console.log('val is DatePicker, change format');       
            $(this).parents('form').find('#tpc_field_default_value').datetimepicker({format: "YYYY-MM-DD"});
            //$(this).parents('form').find('#tpc_field_default_value').datetimepicker("setDate", new Date());
        }else if(current == 'DateTimePicker'){
            console.log('val is DateTimePicker, change format');        
            $(this).parents('form').find('#tpc_field_default_value').datetimepicker({format: "YYYY-MM-DD HH:mm:ss"});
            //$(this).parents('form').find('#tpc_field_default_value').datetimepicker("setDate", new Date());
        }else if(current == 'Switch'){
            console.log('val is Switch, add select');        
           
            $(this).parents('form').find('#tpc_field_default_value').replaceWith('<select id="tpc_field_default_value" name="tpc_field_default_value">'+
                '<option value="">Choose</option><option value="1">On</option><option value="0">Off</option></select>');   
        }   
    });



    /*this will dynamically get the field forms, form count is based on the entered '# of Fields' value
    * Main properties is tab 1, then next tab is 2 and so forth
    */
    function getFieldForms(fieldCount, curStep, tab){
        var url = '/melis/MelisTemplatingPluginCreator/TemplatingPluginCreator/getFieldForm'; 

        $.ajax({
            type: 'POST',
            url: url,
            data: {
                    fieldCount: fieldCount, 
                    curStep: curStep,
                    tab: tab
                },
            dataType: 'text',
            encode: true
        }).done(function (data) {   
            console.log('data');
            console.log(data);     

            $('#field-form-div').html(data);
            widgetCollapsibleInit();
            //$('#tpc_field_default_value').tagsinput('refresh');

        }).fail(function () {
            alert( translations.tr_meliscore_error_message );
        });  
    }


    /*reference: /melis-commerce/public/js/widget-collapsible.init.js*/
    function widgetCollapsibleInit() {
        // (function($)
        // {
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
                    $('<span class="collapse-toggle"></span>').prependTo($(this).find('.widget-head'));
                
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
        //})(jQuery);
    }


});
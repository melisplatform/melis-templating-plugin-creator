$(function(){var e,t=$("body");function i(e,t,i,a){return 3==e?t=translations.tr_melistemplatingplugincreator_field+" "+t.split("-").pop():4==e&&(t=translations.tr_melistemplatingplugincreator_field+" "+t.split("_").pop()),a+='<p class="modal-error-cont"><b title="'+t+'">'+t+": </b><br>  ",$.each(i,function(t,n){if("label"!==t){var l=null==i[t].label?null==i.label?t:i.label:i[t].label;a+='<p class="modal-error-cont"><b title="'+l+'">'+l+": </b>  ";try{3==e?$.each(n,function(e,t){"label"!==e&&$.each(t,function(e,t){"label"!==e&&(a+='<span><i class="fa fa-circle"></i>'+t+"</span>")})}):$.each(n,function(e,t){"label"!==e&&(a+='<span><i class="fa fa-circle"></i>'+t+"</span>")})}catch(e){"label"!==t&&(a+='<span><i class="fa fa-circle"></i>'+n+"</span>")}a+="</p>"}}),a}function a(e,t,i){3==e?$.each(t,function(e,t){"tpc_main_property_field_count"==e?$("form"+i+" .form-control[name='"+e+"']").parents(".form-group").find("label").css("color","red"):$.each(t,function(t,i){$("form#"+e+" .form-control[name='"+t+"']").parents(".form-group").find("label").css("color","red")})}):4==e?$.each(t,function(e,t){$.each(t,function(e,t){var i=e.split("_").pop();$.each(t,function(e,t){$.each($("#melistemplatingplugincreator_step4 form"),function(){$(this).attr("id");$(this).find("input[name=tpc_field_num]").val()==i&&$(this).find("input.form-control[name='"+e+"']").parents(".form-group").find("label").css("color","red")})})})}):$.each(t,function(e,t){$("form"+i+" .form-control[name='"+e+"']").parents(".form-group").find("label").css("color","red")})}t.on("click",".melis-templating-plugin-creator-steps-content .btn-steps",function(){var e=$(this).data("curstep"),n=$(this).data("nextstep"),l=new FormData,o=$(".melis-templating-plugin-creator-steps-content form.templating-plugin-creator-step-"+e);$("form.templating-plugin-creator-step-"+e+" .form-control").parents(".form-group").find("label").css("color","#686868"),o.each(function(e,t){var i=$(this).closest("form").attr("name"),a=$("form[name="+i+"]").length,n=new FormData($(this)[0]).entries();for(var o of n)a>1?l.append("step-form["+e+"]["+o[0]+"]",o[1]):l.append("step-form["+o[0]+"]",o[1]);$(this).find('input[type="radio"]:not(:checked),input[type="checkbox"]:not(:checked),input[type="text"][value=""],select[value=""]').each(function(){if(a>1)var t="step-form["+e+"]["+this.name+"]";else t="step-form["+this.name+"]";l.has(t)||l.append(t,"")})}),l.append("curStep",e),l.append("nextStep",n),$(this).hasClass("tpc-validate")&&l.append("validate",!0),$("#id_melistemplatingplugincreator_steps").append('<div id="loader" class="overlay-loader"><img class="loader-icon spinning-cog" src="/MelisCore/assets/images/cog12.svg" data-cog="cog12"></div>'),$.ajax({type:"POST",url:"/melis/MelisTemplatingPluginCreator/TemplatingPluginCreator/renderTemplatingPluginCreatorSteps",data:l,dataType:"json",encode:!0,cache:!1,contentType:!1,processData:!1}).done(function(n){$("#id_melistemplatingplugincreator_steps #loader img").removeClass("spinning-cog").addClass("shrinking-cog"),setTimeout(function(){if(n.errors)n.errors.tpc_plugin_upload_thumbnail&&$(".plugin_thumbnail_div").length&&$(".plugin_thumbnail_div").remove(),3==e||4==e?function(e,a,n,l,o){o||(o="closeByButtonOnly");o="closeByButtonOnly"!==o?"overlay-hideonclick":"";var s="<h3>"+melisHelper.melisTranslator(a)+"</h3>";s+="<h4>"+melisHelper.melisTranslator(n)+"</h4>",$.each(l,function(t,a){if(3==e)if("tpc_main_property_field_count"==t){if("label"!==t){var n=null==l[t].label?null==l.label?t:l.label:l[t].label;s+='<p class="modal-error-cont"><b title="'+n+'">'+n+": </b>  ";try{$.each(a,function(e,t){"label"!==e&&(s+='<span><i class="fa fa-circle"></i>'+t+"</span>")})}catch(e){"label"!==t&&(s+='<span><i class="fa fa-circle"></i>'+a+"</span>")}s+="</p>"}}else s=i(e,t,a,s);else 4==e&&$.each(a,function(t,a){s=i(e,t,a,s)})});var r="<div class='melis-modaloverlay "+o+"'></div>";r+="<div class='melis-modal-cont KOnotif'>  <div class='modal-content'>"+s+" <span class='btn btn-block btn-primary'>"+translations.tr_meliscore_notification_modal_Close+"</span></div> </div>",t.append(r)}(e,n.textTitle,n.textMessage,n.errors):melisHelper.melisKoNotification(n.textTitle,n.textMessage,n.errors),a(e,n.errors,".templating-plugin-creator-step-"+e),$("#id_melistemplatingplugincreator_steps #loader").remove();else if(n.textMessage)melisHelper.melisKoNotification(n.textTitle,n.textMessage,n.errors),$("#id_melistemplatingplugincreator_steps #loader").remove();else{$("#id_melistemplatingplugincreator_steps").html(n.html),$(".melis-templating-plugin-creator-steps li").removeClass("active");var l=$("#id_melistemplatingplugincreator_steps .steps-content").attr("id");$("#tpc_"+l).addClass("active")}},500)}).fail(function(e,t,i){alert(translations.tr_meliscore_error_message)})}),t.on("change","#templating-plugin-creator-step-1 input[name='tpc_plugin_destination']",function(){"new_module"==$("input:radio[name=tpc_plugin_destination]:checked").val()?($("#tpc_new_module_name").parents(".form-group").show(),$("#tpc_existing_module_name").parents(".form-group").hide(),$("#tpc_existing_module_name option").prop("selected",!1)):($("#tpc_new_module_name").parents(".form-group").hide(),$("#tpc_new_module_name").val(""),$("#tpc_existing_module_name").parents(".form-group").show())}),t.on("click",".melis-tpc-final-content .fa",function(){$(this).hasClass("fa-check-square-o")?($(this).addClass("fa-square-o"),$(this).removeClass("text-success"),$(this).removeClass("fa-check-square-o"),$(this).next("input").attr("checked",!1)):($(this).removeClass("fa-square-o"),$(this).addClass("fa-check-square-o"),$(this).addClass("text-success"),$(this).next("input").attr("checked",!0))}),t.on("click","a[data-id=id_melistemplatingplugincreator_tool]",function(e){$.ajax({type:"POST",url:"/melis/MelisTemplatingPluginCreator/TemplatingPluginCreator/removeTempThumbnailDir",data:{},dataType:"text"}).done(function(e){}).fail(function(e,t,i){alert(translations.tr_meliscore_error_message)})}),t.on("change","#tpc_plugin_upload_thumbnail",function(e){var t=$("#id-templating-plugin-creator-thumbnail-upload-form")[0],i=new FormData(t);i.append("file",$("input[type=file]")[0].files[0]),melisCoreTool.pending(".btn-steps"),$.ajax({type:"POST",url:"/melis/MelisTemplatingPluginCreator/TemplatingPluginCreator/processUpload",data:i,dataType:"json",encode:!0,cache:!1,contentType:!1,processData:!1,async:!0,xhr:function(){var e=$.ajaxSettings.xhr();return e.upload&&e.upload.addEventListener("progress",templatingPluginCreatorTool.progress,!1),e}}).done(function(e){if(e.success){$("div.progressContent").addClass("hidden"),$("form#id-templating-plugin-creator-thumbnail-upload-form .form-control").parents(".form-group").find("label").css("color","#686868");var t=$("#pluginThumbnailUploadArea"),i=$(".plugin_thumbnail_div");i.length?i.empty():$('<div class="col-xs-12 col-lg-6"><div class="plugin_thumbnail_div"></div></div>').appendTo(t),$("<img />",{src:e.pluginThumbnail,class:"plugin_thumbnail"}).appendTo(".plugin_thumbnail_div"),$('<div class="hover-details"><div class="me-p-btn-cont"><a id="plugin-thumbnail-eye" class="viewImageDocument" href="" target="_blank"><i class="fa fa-eye"></i></a><a id="removePluginThumbnail" data-type="image"><i class="fa fa-times"></i></a></div></div>').appendTo(".plugin_thumbnail_div"),$("#plugin-thumbnail-eye").attr("href",e.pluginThumbnail),i.show()}else $("div.progressContent").addClass("hidden"),$(".plugin_thumbnail_div").length&&$(".plugin_thumbnail_div").remove(),melisHelper.melisKoNotification(e.textTitle,e.textMessage,e.errors),a(0,e.errors,".templating-plugin-creator-step-2");melisCoreTool.done(".btn-steps")}).fail(function(e,t,i){alert(translations.tr_meliscore_error_message)})}),t.on("click",".melis-templating-plugin-creator-steps-content #removePluginThumbnail",function(){melisCoreTool.pending("#removePluginThumbnail"),melisCoreTool.confirm(translations.tr_melistemplatingplugincreator_common_label_yes,translations.tr_melistemplatingplugincreator_common_label_no,translations.tr_melistemplatingplugincreator_delete_plugin_thumbnail_title,translations.tr_melistemplatingplugincreator_delete_plugin_thumbnail_confirm,function(){$.ajax({type:"POST",url:"/melis/MelisTemplatingPluginCreator/TemplatingPluginCreator/removePluginThumbnail",data:null,dataType:"json",encode:!0}).done(function(e){e.success?($("#tpc_plugin_upload_thumbnail").val(""),$(".plugin_thumbnail_div").remove()):melisHelper.melisKoNotification(translations.tr_melistemplatingplugincreator_delete_plugin_thumbnail_title,translations.tr_melistemplatingplugincreator_delete_plugin_thumbnail_error,null),melisCore.flashMessenger()}).fail(function(){alert(translations.tr_meliscore_error_message)})}),melisCoreTool.done("#removePluginThumbnail")});t.on("keyup",".melis-templating-plugin-creator-steps-content #tpc_main_property_field_count",function(){var t=$("#tpc_main_property_field_count").val();clearTimeout(e),e=setTimeout(function(){if($.isNumeric(t))if(t>0&&t<=25){$("#tpc_main_property_field_count").parents(".form-group").find("label").css("color","#686868");var e=$("#tpc_main_property_field_count").parents().find(".tpc-validate").data("curstep"),i=new FormData;$(".melis-templating-plugin-creator-steps-content form.templating-plugin-creator-step-"+e).each(function(e,t){var a=$(this).closest("form").attr("name"),n=$("form[name="+a+"]").length,l=new FormData($(this)[0]).entries();for(var o of l)n>1?i.append("step-form["+e+"]["+o[0]+"]",o[1]):i.append("step-form["+o[0]+"]",o[1])}),i.append("curStep",e),function(e){$.ajax({type:"POST",url:"/melis/MelisTemplatingPluginCreator/TemplatingPluginCreator/getFieldForm",data:e,dataType:"text",encode:!0,cache:!1,contentType:!1,processData:!1}).done(function(e){$("#field-form-div").html(e),fieldFormInit(),widgetCollapsibleInit()}).fail(function(){alert(translations.tr_meliscore_error_message)})}(i)}else $("#field-form-div").empty(),melisHelper.melisKoNotification(translations.tr_melistemplatingplugincreator_title_step_3,translations.tr_melistemplatingplugincreator_value_must_be_between_1_to_25,null);else $("#field-form-div").empty(),melisHelper.melisKoNotification(translations.tr_melistemplatingplugincreator_title_step_3,translations.tr_melistemplatingplugincreator_integer_only,null)},1e3)}),t.on("focusin",".melis-templating-plugin-creator-steps-content #tpc_field_display_type",function(){$(this).data("val",$(this).val())}).on("change",".melis-templating-plugin-creator-steps-content #tpc_field_display_type",function(){var e=$(this).data("val"),t=$(this).val(),i='<input class = "form-control" type = "text" id = "tpc_field_default_value" name = "tpc_field_default_value" value="">';if($(this).parents("form").find("#tpc_field_default_value, #tpc_field_default_options").val(""),$(this).blur(),"Dropdown"==e&&"Dropdown"!=t?($(this).parents("form").find("#tpc_field_default_options").removeAttr("data-role"),$(this).parents("form").find("#tpc_field_default_options").tagsinput("destroy"),$(this).parents("form").find("#tpc_field_default_options").parents(".form-group").hide(),$(this).parents("form").find("#tpc_field_default_value").closest(".form-group.input-group").empty().append(i)):"DatePicker"==e&&"DatePicker"!=t||"DateTimePicker"==e&&"DateTimePicker"!=t?$(this).parents("form").find("#tpc_field_default_value").datetimepicker("destroy"):"Switch"==e&&"Switch"!=t||"PageInput"==e&&"PageInput"!=t?$(this).parents("form").find("#tpc_field_default_value").closest(".form-group.input-group").empty().append(i):"MelisCoreTinyMCE"==e&&"MelisCoreTinyMCE"!=t&&$(this).parents("form").find('textarea[name="tpc_field_default_value"]').closest(".form-group.input-group").empty().append(i),"Dropdown"==t)$(this).parents("form").find("#tpc_field_default_options").parents(".form-group").show(),$(this).parents("form").find("#tpc_field_default_options").attr("data-role","tagsinput"),$(this).parents("form").find("#tpc_field_default_options").tagsinput({allowDuplicates:!1}),$(this).parents("form").find("#tpc_field_default_options").tagsinput("refresh"),$(this).parents("form").find("#tpc_field_default_value").replaceWith('<div class="col-md-2 padding-left-0"><select id="tpc_field_default_value" name="tpc_field_default_value" class="form-control"><option value="">Choose</option></select></div>');else if("DatePicker"==t)$(this).parents("form").find("#tpc_field_default_value").datetimepicker({format:"YYYY-MM-DD"});else if("DateTimePicker"==t)$(this).parents("form").find("#tpc_field_default_value").datetimepicker({format:"YYYY-MM-DD HH:mm:ss"});else if("Switch"==t)$(this).parents("form").find("#tpc_field_default_value").replaceWith('<div class="col-md-2 padding-left-0"><select id="tpc_field_default_value" name="tpc_field_default_value" class="form-control"><option value="">Choose</option><option value="1">On</option><option value="0">Off</option></select></div>');else if("PageInput"==t)$(this).parents("form").find("#tpc_field_default_value").closest(".form-group.input-group").empty().prepend('<div class="d-flex flex-row justify-content-between">'+i+'<a class="btn btn-default meliscms-site-selector"><i class="fa fa-sitemap"></i></a></div>');else if("MelisCoreTinyMCE"==t){var a=Math.round((new Date).getTime()+100*Math.random());$(this).parents("form").find("#tpc_field_default_value").replaceWith('<textarea data-tinymce-id="'+a+'" id="'+a+'" class="form-control" name="tpc_field_default_value"></textarea>'),melisTinyMCE.createTinyMCE("tool","textarea[data-tinymce-id='"+a+"']",{height:200,relative_urls:!1,remove_script_host:!1,convert_urls:!1})}}),t.on("itemAdded","#tpc_field_default_options",function(e){$(this).parents("form").find("#tpc_field_default_value").append('<option value="'+e.item+'">'+e.item+"</option>")}),t.on("itemRemoved","#tpc_field_default_options",function(e){$(this).parents("form").find("#tpc_field_default_value option[value='"+e.item+"']").remove()})});var templatingPluginCreatorTool={progress:function(e){var t=$("div.progressContent");t.removeClass("hidden");var i=$("div.progressContent > div.progress > div.progress-bar");i.attr("aria-valuenow",0).css("width","0%");var a=$("div.progressContent > div.progress > span.status");if(a.html(""),e.lengthComputable){var n=e.total,l=100*e.loaded/n;i.attr("aria-valuenow",l),i.css("width",l+"%"),l>100?t.addClass("hidden"):a.html(Math.round(l)+"%")}else alert("not computable")}},widgetCollapsibleInit=function(){$('.widget[data-toggle="collapse-widget"] .widget-body').on("show.bs.collapse",function(){$(this).parents(".widget:first").attr("data-collapse-closed","false")}).on("shown.bs.collapse",function(){setTimeout(function(){$(window).resize()},500)}).on("hidden.bs.collapse",function(){$(this).parents(".widget:first").attr("data-collapse-closed","true")}),$('.widget[data-toggle="collapse-widget"]').each(function(){$(this).find(".widget-head > .collapse-toggle").length||$('<span class="collapse-toggle"></span>').appendTo($(this).find(".widget-head")),$(this).find(".widget-body").not(".collapse").addClass("collapse"),"true"!==$(this).attr("data-collapse-closed")&&$(this).find(".widget-body").addClass("in"),$(this).find(".accordionTitle").on("click",function(){var e=$(".prices-accordion.active");$.each(e,function(){$(this).find(".widget[data-collapse-closed=false]").find(".widget-body").collapse("toggle")}),$(this).parents(".widget:first").find(".widget-body").collapse("toggle")})})},fieldFormInit=function(){$.each($("#melistemplatingplugincreator_step3 form[name=templating-plugin-creator-step-3-field-form]").not(":eq(0)"),function(e,t){var i=$(this).find("#tpc_field_default_value").val();if("Dropdown"==$(this).find("#tpc_field_display_type").val()){$(this).find("#tpc_field_default_options").parents(".form-group").show(),$(this).find("#tpc_field_default_options").tagsinput("refresh"),$(this).find("#tpc_field_default_value").replaceWith('<div class="col-md-2 padding-left-0"><select id="tpc_field_default_value" name="tpc_field_default_value" class="form-control"><option value="">Choose</option></select></div>');var a=$(this).find("#tpc_field_default_options").val();if(a.length){console.log("default options not null, add values to default field"),a=a.split(",");var n=$(this).find("#tpc_field_default_value");$.each(a,function(e,t){n.append('<option value="'+t+'">'+t+"</option>")}),""!=i&&$(this).find('#tpc_field_default_value option[value="'+i+'"]').attr("selected","selected")}}else if("DatePicker"==$(this).find("#tpc_field_display_type").val())$(this).find("#tpc_field_default_value").datetimepicker({format:"YYYY-MM-DD"});else if("DateTimePicker"==$(this).find("#tpc_field_display_type").val())$(this).find("#tpc_field_default_value").datetimepicker({format:"YYYY-MM-DD HH:mm:ss"});else if("Switch"==$(this).find("#tpc_field_display_type").val())$(this).find("#tpc_field_default_value").replaceWith('<div class="col-md-2 padding-left-0"><select id="tpc_field_default_value" name="tpc_field_default_value" class="form-control"><option value="">Choose</option><option value="1" >On</option><option value="0">Off</option></select></div>'),$(this).find("#tpc_field_default_value").val(i);else if("PageInput"==$(this).find("#tpc_field_display_type").val())$(this).find("#tpc_field_default_value").closest(".form-group.input-group").empty().prepend('<div class="d-flex flex-row justify-content-between"><input class = "form-control" type = "text" id = "tpc_field_default_value" name = "tpc_field_default_value" value=""><a class="btn btn-default meliscms-site-selector"><i class="fa fa-sitemap"></i></a></div>'),$(this).find("#tpc_field_default_value").val(i);else if("MelisCoreTinyMCE"==$(this).find("#tpc_field_display_type").val()){var l=Math.round((new Date).getTime()+100*Math.random());$(this).find("#tpc_field_default_value").replaceWith('<textarea data-tinymce-id="'+l+'" id="'+l+'" class="form-control" name="tpc_field_default_value">'+i+"</textarea>"),melisTinyMCE.createTinyMCE("tool","textarea[data-tinymce-id='"+l+"']",{height:200,relative_urls:!1,remove_script_host:!1,convert_urls:!1})}})};!function(e){"use strict";var t={tagClass:function(e){return"label label-info"},itemValue:function(e){return e?e.toString():e},itemText:function(e){return this.itemValue(e)},itemTitle:function(e){return null},freeInput:!0,addOnBlur:!0,maxTags:void 0,maxChars:void 0,confirmKeys:[13,44],delimiter:",",delimiterRegex:null,cancelConfirmKeysOnEmpty:!0,onTagExists:function(e,t){t.hide().fadeIn()},trimValue:!1,allowDuplicates:!1};function i(t,i){this.itemsArray=[],this.$element=e(t),this.$element.hide(),this.isSelect="SELECT"===t.tagName,this.multiple=this.isSelect&&t.hasAttribute("multiple"),this.objectItems=i&&i.itemValue,this.placeholderText=t.hasAttribute("placeholder")?this.$element.attr("placeholder"):"",this.inputSize=Math.max(1,this.placeholderText.length),this.$container=e('<div class="bootstrap-tagsinput form-control"></div>'),this.$input=e('<input type="text" placeholder="'+this.placeholderText+'"/>').appendTo(this.$container),this.$element.before(this.$container),this.build(i)}function a(e,t){if("function"!=typeof e[t]){var i=e[t];e[t]=function(e){return e[i]}}}function n(e,t){if("function"!=typeof e[t]){var i=e[t];e[t]=function(){return i}}}i.prototype={constructor:i,add:function(t,i,a){var n=this;if(!(n.options.maxTags&&n.itemsArray.length>=n.options.maxTags)&&(!1===t||t)){if("string"==typeof t&&n.options.trimValue&&(t=e.trim(t)),"object"==typeof t&&!n.objectItems)throw"Can't add objects when itemValue option is not set";if(!t.toString().match(/^\s*$/)){if(n.isSelect&&!n.multiple&&n.itemsArray.length>0&&n.remove(n.itemsArray[0]),"string"==typeof t&&"INPUT"===this.$element[0].tagName){var l=n.options.delimiterRegex?n.options.delimiterRegex:n.options.delimiter,s=t.split(l);if(s.length>1){for(var r=0;r<s.length;r++)this.add(s[r],!0);return void(i||n.pushVal())}}var p=n.options.itemValue(t),c=n.options.itemText(t),d=n.options.tagClass(t),u=n.options.itemTitle(t),f=e.grep(n.itemsArray,function(e){return n.options.itemValue(e)===p})[0];if(!f||n.options.allowDuplicates){if(!(n.items().toString().length+t.length+1>n.options.maxInputLength)){var m=e.Event("beforeItemAdd",{item:t,cancel:!1,options:a});if(n.$element.trigger(m),!m.cancel){n.itemsArray.push(t);var h=e('<span class="tag '+o(d)+(null!==u?'" title="'+u:"")+'">'+o(c)+'<span data-role="remove"></span></span>');if(h.data("item",t),n.findInputWrapper().before(h),h.after(" "),n.isSelect&&!e('option[value="'+encodeURIComponent(p)+'"]',n.$element)[0]){var g=e("<option selected>"+o(c)+"</option>");g.data("item",t),g.attr("value",p),n.$element.append(g)}i||n.pushVal(),n.options.maxTags!==n.itemsArray.length&&n.items().toString().length!==n.options.maxInputLength||n.$container.addClass("bootstrap-tagsinput-max"),n.$element.trigger(e.Event("itemAdded",{item:t,options:a}))}}}else if(n.options.onTagExists){var _=e(".tag",n.$container).filter(function(){return e(this).data("item")===f});n.options.onTagExists(t,_)}}}},remove:function(t,i,a){var n=this;if(n.objectItems&&(t=(t="object"==typeof t?e.grep(n.itemsArray,function(e){return n.options.itemValue(e)==n.options.itemValue(t)}):e.grep(n.itemsArray,function(e){return n.options.itemValue(e)==t}))[t.length-1]),t){var l=e.Event("beforeItemRemove",{item:t,cancel:!1,options:a});if(n.$element.trigger(l),l.cancel)return;e(".tag",n.$container).filter(function(){return e(this).data("item")===t}).remove(),e("option",n.$element).filter(function(){return e(this).data("item")===t}).remove(),-1!==e.inArray(t,n.itemsArray)&&n.itemsArray.splice(e.inArray(t,n.itemsArray),1)}i||n.pushVal(),n.options.maxTags>n.itemsArray.length&&n.$container.removeClass("bootstrap-tagsinput-max"),n.$element.trigger(e.Event("itemRemoved",{item:t,options:a}))},removeAll:function(){for(e(".tag",this.$container).remove(),e("option",this.$element).remove();this.itemsArray.length>0;)this.itemsArray.pop();this.pushVal()},refresh:function(){var t=this;e(".tag",t.$container).each(function(){var i=e(this),a=i.data("item"),n=t.options.itemValue(a),l=t.options.itemText(a),s=t.options.tagClass(a);(i.attr("class",null),i.addClass("tag "+o(s)),i.contents().filter(function(){return 3==this.nodeType})[0].nodeValue=o(l),t.isSelect)&&e("option",t.$element).filter(function(){return e(this).data("item")===a}).attr("value",n)})},items:function(){return this.itemsArray},pushVal:function(){var t=this,i=e.map(t.items(),function(e){return t.options.itemValue(e).toString()});t.$element.val(i,!0).trigger("change")},build:function(i){var l=this;if(l.options=e.extend({},t,i),l.objectItems&&(l.options.freeInput=!1),a(l.options,"itemValue"),a(l.options,"itemText"),n(l.options,"tagClass"),l.options.typeahead){var o=l.options.typeahead||{};n(o,"source"),l.$input.typeahead(e.extend({},o,{source:function(t,i){function a(e){for(var t=[],a=0;a<e.length;a++){var o=l.options.itemText(e[a]);n[o]=e[a],t.push(o)}i(t)}this.map={};var n=this.map,s=o.source(t);e.isFunction(s.success)?s.success(a):e.isFunction(s.then)?s.then(a):e.when(s).then(a)},updater:function(e){return l.add(this.map[e]),this.map[e]},matcher:function(e){return-1!==e.toLowerCase().indexOf(this.query.trim().toLowerCase())},sorter:function(e){return e.sort()},highlighter:function(e){var t=new RegExp("("+this.query+")","gi");return e.replace(t,"<strong>$1</strong>")}}))}if(l.options.typeaheadjs){var r=null,p={},c=l.options.typeaheadjs;e.isArray(c)?(r=c[0],p=c[1]):p=c,l.$input.typeahead(r,p).on("typeahead:selected",e.proxy(function(e,t){p.valueKey?l.add(t[p.valueKey]):l.add(t),l.$input.typeahead("val","")},l))}l.$container.on("click",e.proxy(function(e){l.$element.attr("disabled")||l.$input.removeAttr("disabled"),l.$input.focus()},l)),l.options.addOnBlur&&l.options.freeInput&&l.$input.on("focusout",e.proxy(function(t){0===e(".typeahead, .twitter-typeahead",l.$container).length&&(l.add(l.$input.val()),l.$input.val(""))},l)),l.$container.on("keydown","input",e.proxy(function(t){var i=e(t.target),a=l.findInputWrapper();if(l.$element.attr("disabled"))l.$input.attr("disabled","disabled");else{switch(t.which){case 8:if(0===s(i[0])){var n=a.prev();n.length&&l.remove(n.data("item"))}break;case 46:if(0===s(i[0])){var o=a.next();o.length&&l.remove(o.data("item"))}break;case 37:var r=a.prev();0===i.val().length&&r[0]&&(r.before(a),i.focus());break;case 39:var p=a.next();0===i.val().length&&p[0]&&(p.after(a),i.focus())}var c=i.val().length;Math.ceil(c/5);i.attr("size",Math.max(this.inputSize,i.val().length))}},l)),l.$container.on("keypress","input",e.proxy(function(t){var i=e(t.target);if(l.$element.attr("disabled"))l.$input.attr("disabled","disabled");else{var a,n,o,s=i.val(),r=l.options.maxChars&&s.length>=l.options.maxChars;l.options.freeInput&&(a=t,n=l.options.confirmKeys,o=!1,e.each(n,function(e,t){if("number"==typeof t&&a.which===t)return o=!0,!1;if(a.which===t.which){var i=!t.hasOwnProperty("altKey")||a.altKey===t.altKey,n=!t.hasOwnProperty("shiftKey")||a.shiftKey===t.shiftKey,l=!t.hasOwnProperty("ctrlKey")||a.ctrlKey===t.ctrlKey;if(i&&n&&l)return o=!0,!1}}),o||r)&&(0!==s.length&&(l.add(r?s.substr(0,l.options.maxChars):s),i.val("")),!1===l.options.cancelConfirmKeysOnEmpty&&t.preventDefault());var p=i.val().length;Math.ceil(p/5);i.attr("size",Math.max(this.inputSize,i.val().length))}},l)),l.$container.on("click","[data-role=remove]",e.proxy(function(t){l.$element.attr("disabled")||l.remove(e(t.target).closest(".tag").data("item"))},l)),l.options.itemValue===t.itemValue&&("INPUT"===l.$element[0].tagName?l.add(l.$element.val()):e("option",l.$element).each(function(){l.add(e(this).attr("value"),!0)}))},destroy:function(){this.$container.off("keypress","input"),this.$container.off("click","[role=remove]"),this.$container.remove(),this.$element.removeData("tagsinput"),this.$element.show()},focus:function(){this.$input.focus()},input:function(){return this.$input},findInputWrapper:function(){for(var t=this.$input[0],i=this.$container[0];t&&t.parentNode!==i;)t=t.parentNode;return e(t)}},e.fn.tagsinput=function(t,a,n){var l=[];return this.each(function(){var o=e(this).data("tagsinput");if(o)if(t||a){if(void 0!==o[t]){if(3===o[t].length&&void 0!==n)var s=o[t](a,null,n);else s=o[t](a);void 0!==s&&l.push(s)}}else l.push(o);else o=new i(this,t),e(this).data("tagsinput",o),l.push(o),"SELECT"===this.tagName&&e("option",e(this)).attr("selected","selected"),e(this).val(e(this).val())}),"string"==typeof t?l.length>1?l:l[0]:l},e.fn.tagsinput.Constructor=i;var l=e("<div />");function o(e){return e?l.text(e).html():""}function s(e){var t=0;if(document.selection){e.focus();var i=document.selection.createRange();i.moveStart("character",-e.value.length),t=i.text.length}else(e.selectionStart||"0"==e.selectionStart)&&(t=e.selectionStart);return t}e(function(){e("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput()})}(window.jQuery);

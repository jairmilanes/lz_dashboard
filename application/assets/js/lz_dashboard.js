$(document).ready(function(){
	 $('#lz_dashboard .lz_tabs li').each(function(index, elem){
		var _a = $(elem).find('a');
		
		_a.on('click', function(e){
			e.preventDefault();
			show_loading();
			$('#lz_dashboard .lz_tabs li').removeClass('active');
			var url = $(this).attr('href');
			$.get(url,function(html){
				$('#lz_dashboard .lz_container').html(html);
				hide_loading();
				
				_a.parent().addClass('active');
				lz_init();
				return false;
			},'html');
			return false;
		});
	});	
	
	$('#lz_dashboard .lz_tabs li').eq(findSelectedTab()).find('a').trigger('click');

});
var lz_active_form = '';
function lz_init(){

	var active_plugin = $('#lz_dashboard .lz_tabs li.active').data('plugin');
	var active_plugin_id = '#'+active_plugin;
	var active_plugin_elem = $(active_plugin_id);
	
	var tabs = $( "#tabs" ).tabs(/*{ 
					show: { 
						effect: "slide", 
						duration: 100 
					}, hide: {
						effect: "slide", 
						duration: 100 
					}
				}*/).addClass( "ui-tabs-vertical ui-helper-clearfix" );
    		   $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
	
	
	
	
	
	tabs.on( "tabsbeforeactivate", function( event, ui ) {
		if($(ui.newTab).hasClass('group_header') ){
			var tab = $(ui.newTab);
			var $items = tab.parent().find('li[data-group="'+$(ui.newTab).data('group')+'_item"]');
			if( !tab.hasClass('open') ){
				$items.each(function(index, elem){
					$(elem).slideDown({
							easing: 'easeOutQuad'
						});
				});
				tab.addClass('open');
			} else {
				$items.each(function(index, elem){
					$(elem).slideUp({
							easing: 'easeInQuad'
						});
				});
				tab.removeClass('open');
			}
			return false;
		}
	
		return true;
	})
	.on( "tabsactivate", function( event, ui ) {
		if( false == $(ui.newPanel).data('do') ){
			$('#action a').hide();
			lz_active_form = null;
		} else {
			$('#action a').show();
			
			if( $(ui.newTab).data('group') ){
				var prefix = $('li[data-group="'+$(ui.newTab).data('group').replace('_item','')+'"] a').text();
			} else {
				prefix = $(ui.newTab).find('a').text();
			}
			var form = $(ui.newTab).data('form');
			
			lz_active_form = form;
			
			$('#lz_dashboard #action a').text( $('#lz_dashboard #action a').data('prefix')+prefix );
		}
		
		$('body').trigger( $(ui.newTab).data('form') );
	});


    /*
    $('form', active_plugin_elem ).each( function(index, elem){
        $(elem).on('submit', function(e){
            e.preventDefault();
            formSubmit($(this));
        });
    });
    */
	/***********************************************************
	 * FORMS INIT
	 **********************************************************/
	 forms_init(active_plugin_elem);
}

function findSelectedTab(){
	
	var tabIndex = new Array();
	$('#lz_dashboard .lz_tabs ul li').each(function(index, elem){
		var name = $(elem).data('plugin');
		tabIndex[name] = index;
	});
	//console.log(tabIndex);
	
    var re = /#\w+$/; // This will match the anchor tag in the URL i.e. http://here.com/page#anchor
    var match = re.exec(document.location.toString());
    if (match != null) var anchor = match[0].substr(1);
    for (key in tabIndex) {
        if (anchor == key) {
            selectedTab = tabIndex[key];
            break;
        }
	else selectedTab = 0;
    }
	
	return selectedTab;
}

/******************************************************
** INITIALIZE FIELDS
******************************************************/
function forms_init(elem){
	$('form', $(elem)).each(function(index, el){
		if( $(el).parent().parent().find('.dashboard_action a.action').length > 0 ){
			$(el).parent().parent().find('.dashboard_action a.action').each(function(idx, elm){
				action_init(elm, $(el));
			});		
		}
	});
	fields_init(elem);
}
function fields_init(elem){
	// TOOLTIPS
	 $('.lz_tooltip', elem ).tooltip({
		 position: { my: "left+15 center", at: "right center" },
		 show: {
          delay: 800
        }
	 });
	 
	 // COLORPICKERS
	 if( $('.colorpicker', elem).length > 0 ){
		$('.colorpicker').each(function(index, elem){
			$(elem).colpick({
				layout:'hex',
				submit:0,
				colorScheme:'dark',
				onChange:function(hsb,hex,rgb,fromSetColor) {
					if(!fromSetColor) $(elem).val('#'+hex).css('border-color','#'+hex);
				}
			})
			.keyup(function(){
				$(this).colpickSetColor(this.value);
			});
			if( $(elem).val() !== "" ){
				$(elem).css('border-color', $(elem).val());
			}
		});
	 }
	 
	 // COLOR SELECTORS
	 if( $('.colorselector, .textureselector', elem).length > 0 ){
		$('#lz_dashboard .colorselector, .textureselector').each( function( index, elem ){
			var self = $(elem);
			 self.find('ul > li > span').off('click').on('click', function(e){
				 e.preventDefault();
				 var value = $(this).data('value');
				 self.find('input').val(value);
				 $(this).parent().parent().find('li.active').removeClass('active');
				 $(this).parent().addClass('active');
			 });
		});
	 }
	 
	 // TOGGLE SWITCHES
	 if( $('input.toggleSwitch', elem).length > 0 ){
		$('input.toggleSwitch', elem).each( function(index, $elem){
			
			$($elem).parent().addClass('toggle-dark');
			
			var id = 'switch_'+$($elem).attr( 'id' );
			var template = '<div id="'+id+'" data-checkbox="'+$($elem).attr( 'id' )+'"></div>';
			
			$($elem).before( template );
			$($elem).css( 'display','none' );//.parent().find('label').css( 'display','none' );

			var data = { 
				checkbox: $($elem),
				width:  85,  // ( ( $(elem).parent().width() / 100 ) * 28 ), // width used if not set in css
   				height:   25, // height if not set in css
    			
				on:       $($elem).is(':checked'),
				text: {
				  on: 'ON', // text for the ON position
				  off: 'OFF' // and off
				}
			};
			$('#'+id).toggles( data ).on('toggle', function (e, active) {
                $($elem).prop('checked', ( active ? 'checked':'') );
                $($elem).triggerHandler('lzsg.change',{checked: $($elem).is(':checked') });
			});
		});
	 }
	 
	 // SLIDERANGE FIELDS
	 if( $('.sliderange_field', elem).length > 0 ){
		  $('.sliderange_field').each( function(index, elem){
			  var slider = $(elem).find('.slider');
			  var type = slider.data('type');
			  var smin = slider.data('min');
			  var smax = slider.data('max');
			  var step = $(elem).data('step');
			  var val_min = parseInt( $('input#'+slider.attr('id')+"_min").val() );
			  var val_max = parseInt( $('input#'+slider.attr('id')+"_max").val() );
			  
			  if( !val_min ) val_min = 0;
		      if( !val_max ) val_max = 10000;

			  switch( type ){
					case "min":
					var options = {
						  range: "min",
						  min: smin,
						  max: smax,
						  value: val_max,
						  step: step,
						  selection: 'none',
						  slide: function( ev, ui ) {
								 $('input#'+slider.attr('id')+"_max").val(ui.value);
							}
					  };
					/*
				    var slide_ev = function( ev ) {
							 $('input#'+slider.attr('id')+"_max").val(ev.value);
						}; */
						break; 
					case "max":
					var options = {
						  range: "max",
						  min: smin,
						  max: smax,
						  value: val_min,
						  step: step,
						  slide: function( ev, ui ) {
							 $('input#'+slider.attr('id')+"_min").val( ui.value );
					 	 }
					  };
					  /*
					 var slide_ev = function( ev ) {
							 $('input#'+slider.attr('id')+"_min").val( ev.value );
					  };*/
						break;
					default:
					var options = {
						  range: true,
						  min: smin,
						  max: smax,
						  value: [
						  	val_min,
							val_max
						  ],
						  step: step,
						  slide: function( ev, ui ) {
							 $('input#'+slider.attr('id')+"_min").val(ui.values[ 0 ]);
							 $('input#'+slider.attr('id')+"_max").val(ui.values[ 1 ]);
					  	  }
					  };
					  /*
					  var slide_ev = function( ev ) {
							 $('input#'+slider.attr('id')+"_min").val(ev.values[ 0 ]);
							 $('input#'+slider.attr('id')+"_max").val(ev.values[ 1 ]);
					  }; */
						break; 
			  }
			  
				slider.slider(options);
			  
		  });
	 }	

	$('fieldset[class*="open_group"]', elem).each(function(index, elem){
		var parent = $(elem);

		$('legend > a', $(elem)).on('click', function(e){
			e.preventDefault();
			if( !parent.hasClass('open') ){
				parent.find('.fieldset_inner').height('auto');
				parent.addClass('open');
			} else {
				parent.find('.fieldset_inner').height(0);
				parent.removeClass('open')	
			}
			return false;
		});
		
		$('legend > a', $(elem)).trigger('click');
	});
	
	// COLORPICKERS
	 if( $('.button_group', elem).length > 0 ){
		$('.button_group').each(function(index, elem){
			$(elem).buttonset();
		});
	 }

    if( $('[data-depend]').length ){
        $('[data-depend]').each( function(index, el){

            var container = $(el).closest('.form-group').hide(),
                depend = $(el).data('depend'),
                field = $('[name$="['+depend[0]+']"]').eq(0),
                event = 'change';

                if( !field.length ){
                    field = $('[name$="['+depend[0]+'][]"]').eq(0);
                }


            switch(field.attr('type')){
                case 'text':
                case 'textarea':
                    event = 'blur';
                    break;
            }

            if( field.hasClass('toggleSwitch') ){

                field.on('lzsg.change', function(e, data){
                    var check = $(this).is(':checked');
                    depend[1].forEach(function(element, index, array){
                        if( data.checked && element == field.val()){
                            container.addClass('active').slideDown('fast');
                            $(el).removeAttr('disabled');
                        } else {
                            container.removeClass('active').slideUp('fast');
                            $(el).prop('disabled','disabled');
                        }
                    });
                });
                field.triggerHandler('lzsg.change',{ checked: field.is(':checked') });

            } else {

                field.on(event, function(e){
                    if($.inArray(elem.val(),depend[1]) > -1 ) {
                        container.addClass('active').slideDown('fast');
                        $(el).removeAttr('disabled');
                    } else {
                        container.removeClass('active').slideUp('fast');
                        $(el).prop('disabled','disabled');
                    }
                });
                field.triggerHandler(event);
            }

        });
    }
	
	table_init(elem);
}

function table_init(elem){
	$('.footable', elem).each( function(index, elem){
		var $table = $(elem);
		$table.find('tr td.action a').each(function(idx, elm){
			action_init(elm, $table);
		});
		//console.log($table);
		if( $table.parent().parent().find('.dashboard_action a.action').length > 0 ){
			$table.parent().parent().find('.dashboard_action a.action').each(function(idx, elm){
				action_init(elm, $table);
			});		
		}
	});	
	
	$('.lz_dashboard_table', elem).each(function(index, el){
		$(el).perfectScrollbar()
	});
	
	$('.selector').infinitescroll({
	  	behavior: 'local',
	  	binder: $('.selector'), // scroll on this element rather than on the window
	 	// other options
	});
}


function action_init(elm, table){
	
	var self = $(elm);
	
	var form 		= self.data('form');
	var trigger 	= self.data('trigger');
	var title 		= self.prop('title');
	var url 		= self.attr('href');
	var method 		= self.data('method');
	var bconfirm 	= self.data('confirm');
    var type 	    = self.data('type');
	
	self.off('click').off('click').on('click', function(e){
		e.preventDefault();
		show_loading();

		if( bconfirm ){
			if( !confirm(bconfirm) ){
				hide_loading();
				return false;	
			}
		}

		var get = function(url, type){
			$.get(url, function($type){
				if( type == 'json' ){
					reposnse_ajax($type, trigger);
				} else {
					response_window($type, trigger);
				}
			},type);
		}
		
		var post = function(url, type){
			$.post(url, function($type){
				if( type == 'json' ){
					reposnse_ajax($type, trigger);
				} else {
					response_window($type, trigger);
				}
			},type);
			hide_loading();	
		}

		var reposnse_ajax = function(json, trigger){
			if( json.status ){
				table.trigger(trigger);
				showMessage( 'ok', json.message );
			} else {
				showMessage( 'error', json.message );
			}
			hide_loading();
		}

		
		var response_window = function(html, trigger){
			
			var width = parseInt( $(window).width() / 10 ) * 7;
			var height = parseInt( $(window).height() / 10 ) * 7;
			
			var dialog = newDialog( title, html, width, height, {
				 "Update": function() {
					 show_loading();
					 var $this = $(this);
					 
					 formSubmit($this.find('form'), $(this), function(response){
						 $this.dialog( "close" );
						 table.trigger(trigger);
					 });
				 }
			});
			
			dialog.find('input, textarea, select').each(function(index, elem){
				if( $(elem).attr('id') !== undefined ){
					var old_id = $(elem).attr('id');
					var new_id = old_id+'_v2';
					$(elem).attr('id', new_id);
					var label = $(elem).parents('.form-group').find('label[for="'+old_id+'"]');
					if( label.length ){
						label.attr('for', new_id);	
					}
				}
			});

			fields_init(dialog);
			//forms_init(dialog);
			hide_loading();
		}
		
		var type = self.data('type');

		if( form && !type ){
            type = 'ajax';
        }
        /*
        if( form ){
			type = 'ajax';	
		}
		*/

		switch( type ){
			case 'ajax':
				if( form ){
                    $('form[name="'+form+'"]').off('submit').on('submit', function(e){
                        e.preventDefault();
                        formSubmit($(this));
                    }).trigger('submit');
				} else {
					if( method == 'post' ){
						post(url, 'json');
					} else {
						get(url, 'json');
					}
				}
				
				break;
			case 'window':
				if( method == 'post' ){
					post(url, 'html');
				} else {
					get(url, 'html');
				}
				break;
            case 'blank':
                $('form[name="'+form+'"]').submit();
                var trigger = $('form[name="'+form+'"]').find('input[name="trigger"]').val();
                if( trigger ){
                    $('form[name="'+form+'"]').trigger( trigger );
                }
                hide_loading();
                break;
            case 'download_link':
                console.log(!$('iframe#download_link_frame').length);
                if( !$('iframe#download_link_frame').length ){
                    var iframe = $('<iframe id="download_link_frame" style="display: none;"/>');
                        iframe[0].name = 'download_link_frame';
                        iframe[0].src = '#';
                        iframe.appendTo('body');
                }
                console.log(iframe);

                $('form[name="'+form+'"]').attr('target','download_link_frame').submit();
                hide_loading();
                return false;

                break;
		}
	});
}
/******************************************************
** INITIALIZE AJAX UPLOADS
******************************************************/
function upload_init(settings){
	if( $('#lz_dashboard '+settings.parent+' .upload_button').length > 0 ){
		$('#lz_dashboard '+settings.parent+' .upload_button').each( function( index, elem ){
			
			var field_name = $(this).data('name');
			var group = $(this).data('group');
			var self = $(elem);
		
			$(elem).fineUploader({
			   //debug: true,
				multiple: false,
				request: {
					endpoint: settings.upload_endpoint+field_name+'&group='+group
				},
				deleteFile: {
					enabled: true,
					method: "POST",
					forceConfirm: true,
					endpoint: settings.upload_delete_endpoint+field_name+'&group='+group
				},
				template: settings.upload_template,
				fileTemplate: settings.upload_file_template
					
			}).on('submit', function(){
				show_loading();
			}).on('delete', function(event, id, name, json){
				show_loading();
			}).on('deleteComplete', function(event, id, name, json){
				hide_loading();
			}).on('complete', function(event, id, name, json){
				show_loading();
				if( json.thumbnailUrl ){
					self.find('.thumb')
						.find('img')
							.attr({ 
								id: field_name+'_thumb',
								src : json.thumbnailUrl, 
								//width : 150, 
								//height : 16, 
								alt : "Test Image", 
								title : "Test Image"
							});
					
				}
				
				setTimeout(function(){
					self.find('.qq-progress-bar').animate({backgroundColor: '#333'},2000);
				},2000);
				
			    hide_loading();
			}).on('progress',function( id, name, uploadedBytes, totalBytes ){
				var per = parseInt( ( ( uploadedBytes / totalBytes ) * 100 ) );
				self.find('.qq-progress-bar').css({ width: ''+per+'%' });
			});
				
				
			var url = settings.upload_load_endpoint+field_name+'&group='+group
			
			$.get( url, {}, function( json ){
				if( IsJsonString(json) && json.status ){
					delete json.status;
					$.each( json, function( field, options ){
						
						var list = self.find('.qq-upload-list');
	
						list.append( settings.upload_file_template );
	
						$( 'li', list ).addClass('qq-upload-success');
						$( '.qq-upload-file', list ).html(options.name);
						$( '.qq-upload-size', list ).html(options.size);
						$( '.thumb', list ).find('img').attr( 'src', options.thumbnailUrl );
						
						list.find( '.qq-upload-delete' ).on('click', function(e){
							e.preventDefault();
							show_loading();
							if( confirm('Are you sure you want to delete '+options.name+'?') ){
								var delete_button = $(this);
								var delete_url = settings.upload_init_delete_endpoint;
								var data = { 
									qquuid: options.uuid, 
									field_name: field_name 
								};
								$.post( delete_url, data, function( json ){
									if( json.success ){
										delete_button.parent().remove();
										hide_loading();
									}
								},'json');
							}
						});
					});
				}
			},'json');
		});
	}	
}

/******************************************************
** FORM HELPERS
******************************************************/
function formSubmit(form, context, callback){
	show_loading();
	var data = $(form).serialize();
	var url  = $(form).attr('action');

	 $.post(url, data, function( json,  textStatus, jqXHR ){
		  if( !IsJsonString(json) ){
			  showMessage( 'error', 'Invalid request', context );
		  } else {
			  if( !json.status ){
				 showMessage( 'error', json.message, context );
				 if( json.errors ){
					setErrors(json.errors, form);
				 }
			  } else {
				 showMessage( 'ok', json.message, context );
				 clearErrors();
				 
				 if( callback ){
					 setTimeout(function(){
						 callback(json);
					 },1300);
				 }
				 
				 var trigger = $(form).find('input[name="trigger"]').val();
				 
				 if( trigger ){
					$(form).trigger( trigger );
				 }
			  }
		  }
		  $( "#tabs" ).tabs( "enable" );
		  //$('#tabs li', elem ).eq(0).find('a').trigger('click');
		  hide_loading();
		  return false;
	 },'json').fail(function() {
		 showMessage( 'error', 'Invalid request', context );
		 hide_loading();
	 });	
}
function setErrors($errors, $dialog){
	$.each( $errors, function(group, fields){
		
		$.each(fields, function(name, error){
			var field_name = '['+group+']';
			field_name += '['+name+']';
			//console.log(field_name);
			if( $dialog ){
				var input = $('input[name*="'+field_name+'"]', $dialog);
			} else {
				var input = $('input[name*="'+field_name+'"]');
				$('#tabs').find('a[href*="_'+group+'"]').parent().addClass('error');
			}
			input.parents('.form-group').addClass('error');
			input.parents('.form-group').find('.form-label p.error').remove();
			input.parents('.form-group').find('.form-label').append('<p class="error">'+error+'</p>');
		});
	});
}
function clearErrors($dialog){
	if( $dialog ){
		$('.form-group.error', $dialog).removeClass('error');
	} else {
		$('.form-group.error').removeClass('error');
		$('#tabs').find('li.error').removeClass('error');
	}
}
function clearForm(form){
	$(form)[0].reset();	
}


/******************************************************
** LOADING HELPERS
******************************************************/
function show_loading(){
	if( !$('#lz_dashboard .lz_loading').hasClass('active') ){
		$('#lz_dashboard .lz_loading').addClass('active');
	}
}
function hide_loading(){
	$('#lz_dashboard .lz_loading').removeClass('active');
}



/*****************************************************************
** DIALOGS
*****************************************************************/
var dialogs = 0;
function newDialog( title, html, width, height, actions){
	//console.log(title);
	
	var id = 'dialog_'+dialogs;
	$('body').append('<div title="'+title+'" id="'+id+'"><div class="jsMessage flashmessage flashmessage-info flashmessage-error" style="display: none;"><a class="btn ico btn-mini ico-close">×</a><p></p></div><div class="dialog_content"></div></div>');
	dialogs++;
	$('#'+id+' .dialog_content').html(html);
	//console.log($('#'+id+' .dialog_content'));
	var box = $('#'+id).dialog({
		  title: title,
		  width: width,
		  height: height,
		  autoOpen: true,
		  resizable: false,
		  draggable: false,
		  appendTo: '#lz_dashboard',
		  close: function(){
			  $('#'+id).remove();
		  },
		  show: {
			  effect: "drop",
			  duration: 350
		  },
		  modal: true,
		  buttons: actions
	});

	return box;//box.dialog( "open" );
}

/******************************************************
** MESSAGE HELPERS
******************************************************/
var last_message_type = '';
var message_timeout = 0;
function showMessage( type, message, dialog ){
	if( dialog ){
		var msg_container = $('.jsMessage.flashmessage', dialog);
	} else {
		var msg_container = $('.jsMessage.flashmessage');
	}
	msg_container.removeClass( last_message_type+' hide' ).addClass('flashmessage-'+type);
	last_message_type = 'flashmessage-'+type;
	msg_container.find('p').html( message );
	msg_container.slideDown('fast');
	clearTimeout( message_timeout );
	message_timeout = setTimeout( function(){
		msg_container.slideUp('fast');
	}, 3000 );
}

/******************************************************
** GENERAL FIELDS
******************************************************/
function truncate(n, len) {
    var ext = n.substring(n.lastIndexOf(".") + 1, n.length).toLowerCase();
    var filename = n.replace('.'+ext,'');
    if(filename.length <= len) {
        return n;
    }
    filename = filename.substr(0, len) + (n.length > len ? '[...]' : '');
    return filename + '.' + ext;
};

function IsJsonString(jsonString){
   if (jsonString && typeof jsonString === "object" && jsonString !== null) {
		return true;
   } else {
	   try {
			var o = JSON.parse(jsonString);
			if (o && typeof o === "object" && o !== null) {
				return true;
			}
		}
		catch (e) { }
   }
   return false;
};
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
	 
	 
	
});

function lz_init(){
	var active_plugin = $('#lz_dashboard .lz_tabs li.active').data('plugin');
	var active_plugin_id = '#'+active_plugin;
	var active_plugin_elem = $(active_plugin_id);
	
	var tabs = $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
    		   $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
	
	/***********************************************************
	 * START FIELD SETINGS
	 **********************************************************/
	 
	 // TOOLTIPS
	 $('.lz_tooltip', active_plugin_elem ).tooltip({
		 position: { my: "left+15 center", at: "right center" },
		 show: {
          delay: 800
        }
	 });
	 
	 // COLORPICKERS
	 if( $('.colorpicker', active_plugin_elem).length > 0 ){
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
	 if( $('.colorselector, .textureselector', active_plugin_elem).length > 0 ){
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
	 if( $('input.toggleSwitch', active_plugin_elem).length > 0 ){
		$('#lz_dashboard input.toggleSwitch').each( function(index, elem){
			$(elem).parent().addClass('toggle-dark');
			var id = 'switch_'+$(elem).attr( 'id' );
			var template = '<div id="'+id+'" data-checkbox="'+$(elem).attr( 'id' )+'"></div>';
			$(elem).before( template );
			$(elem).css( 'display','none' );//.parent().find('label').css( 'display','none' );
			var data = { 
				checkbox: $(elem),
				width:  85,  // ( ( $(elem).parent().width() / 100 ) * 28 ), // width used if not set in css
   				height:   25, // height if not set in css
    			
				on:       $(elem).is(':checked'),
				text: {
				  on: 'ON', // text for the ON position
				  off: 'OFF' // and off
				}
			};
			$('#'+id).toggles( data ).on('toggle', function (e, active) {
				$(elem).prop('checked', active );
			});
		});
	 }
	 
	 // SLIDERANGE FIELDS
	 if( $('.sliderange_field', active_plugin_elem).length > 0 ){
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
	 
	 
	 $('a[href="#saving"]', active_plugin_elem ).on('click', function(e){
		 e.preventDefault();
		 $(this).parents('ul').find('li[role="tab"]').each(function(index, elem){
			 if( !$(this).hasClass('submit') ){
				  tabs.tabs( "disable", index );
			 }
		 });
		 $('form', active_plugin_elem ).eq(0).submit();
	 });
	 
	 $('form', active_plugin_elem ).eq(0).on('submit', function(e){
		 e.preventDefault();

		 var data = $(this).serialize();
		 var url = $(this).attr('action');

		 $.post(url, data, function( json ){
			  if( json.status ){
				showMessage( 'ok', json.message );
			  } else {
			  	showMessage( 'error', json.message );
			  }
			  tabs.tabs( "enable" );
			  $('#tabs li', active_plugin_elem ).eq(0).find('a').trigger('click');
			  return false;
		 },'json');
		 
	 });
	 
	 // SELECTS 
	 /*
	 if( typeof selectUi == 'function'){
		 console.log($('#lz_dashboard select'));
		$('#lz_dashboard select').each(function(){
			selectUi($(this));
		});
	}
	*/

}

function show_loading(){
	if( !$('#lz_dashboard .lz_loading').hasClass('active') ){
		$('#lz_dashboard .lz_loading').addClass('active');
	}
}
function hide_loading(){
	$('#lz_dashboard .lz_loading').removeClass('active');
}


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

				if( false !== json.status ){
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


/*****************************************************************
** DIALOGS
*****************************************************************/
var dialogs = 0;
function newDialog( title, desc, actions ){
	var id = 'dialog_'+dialogs;
	$(body).append('<div id="'+id+'"></div>');
	dialogs++;
	$('#'+id).html(desc);
	var box = $('#'+id).dialog({
		  title: title,
		  autoOpen: false,
		  close: function(){
			  $('#'+id).remove();
		  },
		  show: {
			  effect: "drop",
			  duration: 350
		  },
		  hide: {
			  effect: "puff",
			  duration: 350
		  },
		  modal: true,
		  buttons: actions
	});
	return box.dialog( "open" );
}
if( typeof selectUi !== 'function'){

	function selectUi(thatSelect){
		var uiSelect = $('<a href="#" class="select-box-trigger"></a>');
		var uiSelectIcon = $('<span class="select-box-icon"><div class="ico ico-20 ico-drop-down"></div></span>');
		var uiSelected = $('<span class="select-box-label">'+thatSelect.find("option:selected").text()+'</span>');
	
		thatSelect.css('filter', 'alpha(opacity=40)').css('opacity', '0');
		thatSelect.wrap('<div class="select-box '+thatSelect.attr('class')+'" />');
	
	
		uiSelect.append(uiSelected).append(uiSelectIcon);
		thatSelect.parent().append(uiSelect);
		uiSelect.click(function(){
			return false;
		});
		thatSelect.change(function(){
			uiSelected.text(thatSelect.find('option:selected').text());
		});
		thatSelect.on('remove', function() {
			uiSelect.remove();
		});
	}
}
var last_message_type = '';
var message_timeout = 0;
function showMessage( type, message ){
	var msg_container = $('.jsMessage.flashmessage');
	msg_container.removeClass( last_message_type+' hide' ).addClass('flashmessage-'+type);
	last_message_type = 'flashmessage-'+type;
	msg_container.find('p').html( message );
	msg_container.slideDown('fast');
	clearTimeout( message_timeout );
	message_timeout = setTimeout( function(){
		msg_container.slideUp('fast');
	}, 3000 );
}

function truncate(n, len) {
    var ext = n.substring(n.lastIndexOf(".") + 1, n.length).toLowerCase();
    var filename = n.replace('.'+ext,'');
    if(filename.length <= len) {
        return n;
    }
    filename = filename.substr(0, len) + (n.length > len ? '[...]' : '');
    return filename + '.' + ext;
};
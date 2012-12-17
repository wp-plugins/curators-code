(function(){
	
	
	
	//Craetes and launches the dialog for inserting attributions
	function createDialog(symbol){
		
		var authorInput = jQuery("input[name=cc_author]");
		var sourceInput = jQuery("input[name=cc_source]");
		var defaultAttribute = jQuery("#cc_defaultAttribute").val();
		var letters = jQuery("#cc_letters").val();
		
		
	
		
		if(symbol == "HT"){
			var title = "Insert a Hat Tip Attribution";
		}
		else{
			var title = "Insert a Via Attribution";	
		}
		
		jQuery("#cc_overlay").add("#cc_dialogBox").show();
		
		jQuery("#cc_dialogBox h3").text(title);
		
		jQuery("#cc_dialogBox .escape-button").add(".dialog-control#cc_dialogClose").click(function(){
			closeDialog()
		});
		
		
		//make sure click handlers do not stack up
		jQuery(".dialog-control#cc_dialogAttribute").unbind('click');
		
		jQuery(".dialog-control#cc_dialogAttribute").click(function(){
			
			
			//Check to see if link is good
			var pattern = /^(http:\/\/)|(www\.)/;
			if(!pattern.test(sourceInput.val())){
				jQuery("#cc_dialogBox .errors").show();
				jQuery("#cc_dialogBox .errors").text("Invalid URL. Your URL must begin with http:// or www.");
			}
			else{
				//Insert into text body
				jQuery("#cc_dialogBox .errors").text("");
				jQuery("#cc_dialogBox .errors").hide();
				
				insertAttribution(symbol, authorInput.val(), sourceInput.val(), defaultAttribute, letters);
			}
			
		});
		
	};
	
	function closeDialog(){
		jQuery("#cc_dialogBox").add("#cc_overlay").hide();
	}
	
	//Inserts the attribution into the editor
	//Symbol is a string containing either 'ht' or 'via'
	function insertAttribution(symbol, author, source, defaultAttribute, letters){
		
		if(symbol == "Via" && letters != "1"){
				symbol = "ᔥ";
		}
		else if(symbol == "HT" && letters != "1"){
			symbol = "↬";
		}
		
		if(defaultAttribute == "1" || defaultAttribute == "on"){
			tinymce.execCommand('mceInsertContent', 'false', '<a href="http://www.curatorscode.org">'+symbol+'</a> <a href="'+source+'">'+author+'</a> ');
		}
		else{
			tinymce.execCommand('mceInsertContent', 'false', '<a href="'+source+'">'+symbol+ ' '+author+'</a> ');	
		}	
		
		closeDialog();
		
	}
	
	
	tinymce.create('tinymce.plugins.curatorsCode', {
		init: function(ed, url){
	
			//Handles the 'via' button
			ed.addButton('cc_viaButton', {
				title: 'Via Attribution',
				image: url + '/via.png',
				onclick: function(ed){				
					createDialog("Via");
				}
			});
			
			ed.addButton('cc_htButton', {
				title: 'Hat Tip Attribution',
				image: url + '/ht.png',
				onclick: function(ed){
					createDialog("HT");	
				}
			});
		},
			
		createControl: function(m, cm){
			return null;	
		},
		getInfo: function(){
			return {
				longname : "Curators Code Plugin",
                author : 'Graeme Britz',
                authorurl : 'http://www.contentharmony.com/',
                infourl : 'http://www.contentharmony.com/tools/curators-code-plugin/',
                version : "1.0"
			};
		}
		
	});
	tinymce.PluginManager.add('curatorsCode', tinymce.plugins.curatorsCode);
	
})();
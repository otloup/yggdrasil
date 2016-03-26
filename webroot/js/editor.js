window.aOnEditStart = []
window.aOnEditStop = []

var oClicked = null
var bEdition = false;

function toggleEdition(sOption){

	var sVal = sOption;
	var sOppositeVal = '';
		switch(sOption){
			case 'off':
				sOppositeVal = 'on';
				bEdition = false;

				if(window.aOnEditStop.length > 0){
					$.each(window.aOnEditStop, function(k, v){
								v()
							})
				}
			break;

			case 'on':
				sOppositeVal = 'off';
				bEdition = true;

				if(window.aOnEditStart.length > 0){
					$.each(window.aOnEditStart, function(k, v){
								v()
							})
				}
			break;
		}

		$('[data-trigger="edition_toggle"][data-option="'+sOption+'"]').hide()
		$('[data-trigger="edition_toggle"][data-option="'+sOppositeVal+'"]').show()

		$('[data-editable]').each(function(k, v){
			var sOrigVal = $(this).data('editable')
			var sPostfix = bEdition ? '' : '_prepared';

			sOrigVal = sPostfix == '' ? (sOrigVal.split('_'))[0] : sOrigVal+sPostfix
			
			$(v).attr({
					'data-editable':sOrigVal
				})
		})

		if(bEdition){
			enableInlineEdit()
		}
		else{
			disableInlineEdit()
		}

	}

function editable(){
	function showEditionToggle(){
		$('#edition_toggle').show();
		//loadTriggers('edition_toggle');
	}

	function addSaveButton(editor){
		editor.addButton('save', {
			text: 'save',
			icon: false,
			onclick: function() {
				submitEdition()
			}
		});
	}

	function addMenuSaveButton(editor){
		editor.addMenuItem('save', {
      text: 'Save',
      context: 'tools',
			onclick: function() {
				submitEdition()
    	}
    });
	}

	showEditionToggle()
	sDefaultState = bEdition ? 'on' : 'off';
	toggleEdition(sDefaultState)

}

function enableInlineEdit(){
	tinymce.init({
		selector: "[data-editable='min']",
		inline: true,
		plugins: ['link image'],
		toolbar: "undo redo | link image | save",
		menubar: false,
		force_p_newlines: false
//		setup: function(editor){
//			addSaveButton(editor)
//		}
	});

	tinymce.init({
		selector: "[data-editable='img']",
		inline: true,
		plugins: ['link media image'],
		toolbar: "undo redo | link media image | save",
		menubar: false,
		force_p_newlines: false
//		setup: function(editor){
//			addSaveButton(editor)
//		}
	});

	tinymce.init({
		selector: "[data-editable='max']",
		inline: true,
		plugins: [
				"advlist autolink lists link image charmap print preview anchor",
				"searchreplace visualblocks code fullscreen",
				"insertdatetime media table contextmenu paste"
		],
		toolbar: "insertfile | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | save",
		force_p_newlines: false
//		setup: function(editor){
//			addSaveButton(editor)
//		}
		});		

	$('[data-editable]').click(function(){
		oClicked = $(this)
		$('#editor_main_toolbar').show()
		loadTriggers('edition')
	})

}

function disableInlineEdit(){
	tinymce.remove()
	$('[data-editable]').unbind('click')
}

function editionAction(sOption){
	switch(sOption){
		case 'save':
			submitEdition()
		break;

		case 'cancel':
			oClicked.blur()
			oClicked = null
		break;
	}

	$('#editor_main_toolbar').hide()
}

function submitEdition(){
	$.ajax({
		dataType	: 'json'
		,data			:	{
			name			:	oClicked.data('name')
			,content	:	oClicked.html()
		}
		,type			:	'POST'
		,async		:	'false'
		,url			:	'/json/editContent'
		,success	: function(result){
			switch(result.status) {
				case (typeof result.status == 'undefined'):
					alert('something went wrong (undefined)')	
				break;

				case true:
					alert('saved')
				break;

				case false:
					alert('something went wrong (false)')
				break;
			}

			oClicked.blur()
			oClicked = null
		}
	})	
}

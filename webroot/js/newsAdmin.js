function manageNews (sOption, mId) {
	switch (sOption) {
		case 'submit':
			addNews();
		break;

		case 'remove':
			removeNews(mId);
		break;

		case 'cancel':
			cancelNewsAdd();
		break;
	}
}

function addNews(){
	var sSerializedForm = serializeForm($('#news_add_form'));
	
	$.ajax({
		dataType	: 'json'
		,data			:	{
			name		:	$('#news_add_title').val()
			,content:	encodeURIComponent(tinymce.get('news_add_content').getContent())
			,action	:	'add'
		}
		,type			:	'POST'
		,async		:	'false'
		,url			:	'/json/adminNews'
		,success	: function(result){
			if(Number(result.status) > 0){
				oNewsNav.getPage(1)
				loadTriggers(undefined, $('#news'))
			}
		}
	})	
}

function removeNews(iNewsId){
	if(!confirm('Are you sure you want to remove this entry?')){
		return false;
	}

	$.ajax({
		dataType	: 'json'
		,data			:	{
			id			:	iNewsId
			,action	:	'remove'
		}
		,type			:	'POST'
		,async		:	'false'
		,url			:	'/json/adminNews'
		,success	: function(result){
			if(!!result.status){
				window.iAllResults = getNewsCount()
				oNewsNav.getPage('reload')
				loadTriggers(undefined, $('#news'))			
			}
		}
	})	
}

function cancelNewsAdd(){
	manageDropdown('news_add_dropdown');
	$('#news_add_title').val('');
	$('#news_add_content').val('');
	tinymce.get('news_add_content').setContent('');
}

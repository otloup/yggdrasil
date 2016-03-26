function loadNews(iPage){
	$.ajax({
		dataType	: 'html'
		,data			:	{
			page		:	iPage
			,action	:	'getPage'
		}
		,type			:	'POST'
		,url			:	'/json/getNews'
		,success	: function(result){
			$('#news').html(result)
			loadTriggers(undefined, $('#news'))
		}
	})

	return window.iAllResults
}

function getNewsCount(){
	var iResults = window.iAllResults

	$.ajax({
		dataType	: 'json'
		,data			:	{
			action	:	'getNewsCount'
		}
		,type			:	'POST'
		,async		:	false
		,url			:	'/json/getNews'
		,success	: function(result){
			iResults = Number(result.count)
		}
	})

	return iResults
}

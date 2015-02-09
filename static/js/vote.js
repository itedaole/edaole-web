function vote_submit(form) {
	if (!form.length) {
		return false;
	}
	for(var i=0;i<form.length;i++){
		var element=form[i];
		if (element.name.substr(0, 4) != 'vote') {
			continue;
		}
		var question = document.getElementsByName(element.name);
		var question_value = 0;
		for (var j=0; j < question.length; j++) {
			if(question[j].checked) {
				question_value = question[j].value;
			}
		}
		if (!question_value) {
			var question_id = element.name.substr(4).replace('[]', '');
			var title = document.getElementById('title'+question_id).innerHTML;
			alert('请选择“' + title + '”');
			return false;
		}
	}
	return true;
};

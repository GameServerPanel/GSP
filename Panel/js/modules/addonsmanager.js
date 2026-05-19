$(function() {
	var methodToRows = {
		download_zip: ['#scm-row-url', '#scm-row-path'],
		steam_workshop: ['#scm-row-workshop-id', '#scm-row-workshop-app-id', '#scm-row-target-path-template', '#scm-row-optional-folder-name'],
		post_script: ['#scm-row-post-script'],
		config_edit: ['#scm-row-path', '#scm-row-config-edit-rule']
	};
	var allRows = [
		'#scm-row-url',
		'#scm-row-path',
		'#scm-row-workshop-id',
		'#scm-row-workshop-app-id',
		'#scm-row-target-path-template',
		'#scm-row-optional-folder-name',
		'#scm-row-post-script',
		'#scm-row-config-edit-rule',
		'#scm-row-launch-param-additions'
	];
	var $method = $('#scm-install-method');
	var $help = $('#scm-install-method-help');

	function applyContentTypeUi() {
		if ($method.length === 0) return;
		var value = $method.val();
		var shown = methodToRows[value] || [];
		for (var i = 0; i < allRows.length; i++) {
			$(allRows[i]).hide();
		}
		for (var j = 0; j < shown.length; j++) {
			$(shown[j]).show();
		}
		var selectedOption = $method.find('option:selected');
		var helpText = selectedOption.data('help') || '';
		$help.text(helpText);
		$('#scm-path-label').text(value === 'config_edit' ? 'Config Target Path' : 'Target Path / Extract Path (optional)');
	}

	$method.on('change', applyContentTypeUi);
	applyContentTypeUi();
});

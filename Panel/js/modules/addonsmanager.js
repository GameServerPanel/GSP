$(function() {
	function replaceTemplate(template, values) {
		var output = String(template || '');
		$.each(values, function(key, value) {
			output = output.split(key).join(value);
		});
		return output;
	}

	var methodToRows = {
		download_zip: ['#scm-row-url', '#scm-row-path'],
		steam_workshop: ['#scm-row-workshop-app-id', '#scm-row-target-path-template', '#scm-row-optional-folder-name', '#scm-row-post-script', '#scm-row-launch-param-additions'],
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

	var $userSelect = $('#scm-user-addon-select');
	var $userWorkshopRows = $('.scm-user-workshop-row');
	var $userWorkshopId = $('#scm-user-workshop-id');
	var $userWorkshopAppId = $('#scm-user-workshop-app-id');
	var $userTargetTemplate = $('#scm-user-target-path-template');
	var $userOptionalFolderName = $('#scm-user-optional-folder-name');
	var $userPreview = $('#scm-user-target-path-preview');

	function updateUserWorkshopUi() {
		if ($userSelect.length === 0) return;
		var $selected = $userSelect.find('option:selected');
		var installMethod = String($selected.data('installMethod') || '');
		var isWorkshop = installMethod === 'steam_workshop';
		$userWorkshopRows.toggle(isWorkshop);
		if (!isWorkshop) {
			return;
		}

		if (!$userWorkshopId.val()) {
			$userWorkshopId.val(String($selected.data('workshopItemId') || ''));
		}
		if (!$userWorkshopAppId.val()) {
			$userWorkshopAppId.val(String($selected.data('workshopAppId') || $userWorkshopAppId.data('defaultAppId') || ''));
		}
		if (!$userTargetTemplate.val()) {
			$userTargetTemplate.val(String($selected.data('targetPathTemplate') || $userTargetTemplate.data('defaultTemplate') || ''));
		}
		if (!$userOptionalFolderName.val()) {
			$userOptionalFolderName.val(String($selected.data('optionalFolderName') || ''));
		}

		var workshopId = $.trim($userWorkshopId.val());
		var workshopAppId = $.trim($userWorkshopAppId.val()) || String($selected.data('workshopAppId') || $userWorkshopAppId.data('defaultAppId') || '');
		var folderName = $.trim($userOptionalFolderName.val()) || (workshopId ? '@' + workshopId : '@{WORKSHOP_ID}');
		var targetTemplate = $.trim($userTargetTemplate.val()) || String($selected.data('targetPathTemplate') || $userTargetTemplate.data('defaultTemplate') || '');
		var previewValues = {
			'{SERVER_ROOT}': String($userPreview.data('serverRoot') || ''),
			'{GAME_ROOT}': String($userPreview.data('gameRoot') || ''),
			'{WORKSHOP_ID}': workshopId || '{WORKSHOP_ID}',
			'{WORKSHOP_APP_ID}': workshopAppId || '{WORKSHOP_APP_ID}',
			'{STEAM_APP_ID}': String($userPreview.data('steamAppId') || '{STEAM_APP_ID}'),
			'{FOLDER_NAME}': folderName,
			'{MOD_FOLDER}': folderName
		};
		$userPreview.text(replaceTemplate(targetTemplate, previewValues));
	}

	if ($userSelect.length) {
		$userSelect.on('change', function() {
			$userWorkshopId.val(String($(this).find('option:selected').data('workshopItemId') || ''));
			$userWorkshopAppId.val(String($(this).find('option:selected').data('workshopAppId') || ''));
			$userTargetTemplate.val(String($(this).find('option:selected').data('targetPathTemplate') || ''));
			$userOptionalFolderName.val(String($(this).find('option:selected').data('optionalFolderName') || ''));
			updateUserWorkshopUi();
		});
		$userWorkshopId.on('input', updateUserWorkshopUi);
		$userWorkshopAppId.on('input', updateUserWorkshopUi);
		$userTargetTemplate.on('input', updateUserWorkshopUi);
		$userOptionalFolderName.on('input', updateUserWorkshopUi);
		updateUserWorkshopUi();
	}
});

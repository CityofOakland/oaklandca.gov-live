function googleTranslateElementInit() {
	new google.translate.TranslateElement({
		pageLanguage: 'en',
		includedLanguages: 'ceb,en,es,hmn,ja,km,ko,mn,tl,vi,zh-TW,zh-CN,ar',
		layout: google.translate.TranslateElement.FloatPosition.TOP_RIGHT
	}, 'google_translate_element');
}

(function () {
	// Observe DOM mutations whether the <html> node was changed by Google Translate
	if (window.MutationObserver) {
		var mutationObserver = new MutationObserver(function (mutations) {
			mutations.forEach(function (mutation) {
				var oldElementClass = mutation.oldValue;
				var currentElementClass = mutation.target.className;
				if (oldElementClass.indexOf('translated-') === -1 && currentElementClass.indexOf('translated-') > -1) {
					dl.push({
						'event': 'page_translated',
						'translationLanguage': mutation.target.lang || document.getElementsByTagName('html')[0].getAttribute('xml:lang'),
						'debug_mode': true,
					});
				}
			})
		})

		var htmlNode = document.querySelector('html');
		mutationObserver.observe(htmlNode, {
			attributes: true,
			attributeOldValue: true,
			attributeFilter: ['class']
		})
	}
})();

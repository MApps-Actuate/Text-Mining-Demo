//============================================================================================================
// Magellan Text Mining Demo using Pipeline
// Internationalization module
// js/mtm-i10n.js - author : glory - version : 1.1 - January 2023
//============================================================================================================
let i10nDictionnary = {}, supportedLanguage = ["eng", "fra", "deu", "ita", "spa", "nld"], urlParams = new URLSearchParams(location.search), language = urlParams.get('lang');


function i10n_init() {	
	var d = new jQuery.Deferred();
	
	resource = "locale/mtm-i10n" + (supportedLanguage.indexOf(language) >= 0 ? "-" + language + ".properties" : ".properties");

	$.get(resource, function(data) {
		var lines = data.split("\n");

		$.each(lines, function(n, elem) {
			if (elem.substring(0,1) != "#") i10nDictionnary[ elem.split(':')[0] ] = elem.split(':')[1].replace(/\r/g, '');		
        });
		return d.resolve();
	});	
	return d.promise();
};

function i10n_section(a) {
	$(a + " [data-l10n-id]").each(function() {
		if ($(this).attr("data-l10n-type") == undefined) {
			if (i10nDictionnary[$(this).attr("data-l10n-id")] != undefined) $(this).html( i10nDictionnary[$(this).attr("data-l10n-id")] );
		} else {
			if (i10nDictionnary[$(this).attr("data-l10n-id")] != undefined) $(this).attr( $(this).attr("data-l10n-type") , i10nDictionnary[$(this).attr("data-l10n-id")] );			
		};
	});
};

function i10n_this(a) {
	return i10nDictionnary[a];
};
	
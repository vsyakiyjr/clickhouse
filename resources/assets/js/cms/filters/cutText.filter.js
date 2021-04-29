angular.module('app.cms').filter('cutText', CutTextFilter);

function CutTextFilter() {
	return function (input, length) {
		length = typeof length === "undefined" ? 70 : parseInt(length);
		input = input ? Utilities.strip_tags(input) : '';
		if (!input || input.length === 0) return '';

		var cuttedText = '',
			useEllipsis = false,
			word,
			cuttedTextBeforeSubmit,
			words = input.split(/[\s]+/);

		for (var wordIndex = 0; wordIndex < words.length; wordIndex++) {
			word = words[wordIndex];
			cuttedTextBeforeSubmit = cuttedText + ' ' + word;
			if (cuttedTextBeforeSubmit.length > length) {
				useEllipsis = true;
				break;
			} else {
				cuttedText = cuttedTextBeforeSubmit;
			}
		}

		cuttedText = useEllipsis ? cuttedText + '...' : cuttedText;

		return cuttedText.trim();
	}
}
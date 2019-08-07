/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referring to this file must be placed before the ending body tag. */
/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/
(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'fileaplay\'">' + entity + '</span>' + html;
	}
	var icons = {
		'ssfa-fileaplay-arrow-down-alt1': '&#x21;',
		'ssfa-fileaplay-arrow-down-alt2': '&#x22;',
		'ssfa-fileaplay-play': '&#x23;',
		'ssfa-fileaplay-pause': '&#x24;',
		'ssfa-fileaplay-download': '&#x25;',
		'ssfa-fileaplay-play2': '&#x26;',
		'ssfa-fileaplay-pause2': '&#x27;',
		'ssfa-fileaplay-box-add': '&#x28;',
		'ssfa-fileaplay-download2': '&#x29;',
		'ssfa-fileaplay-play3': '&#x2a;',
		'ssfa-fileaplay-pause3': '&#x2b;',
		'ssfa-fileaplay-play22': '&#x2c;',
		'ssfa-fileaplay-pause22': '&#x2d;',
		'ssfa-fileaplay-in': '&#x2e;',
		'ssfa-fileaplay-play4': '&#x2f;',
		'ssfa-fileaplay-pause4': '&#x30;',
		'ssfa-fileaplay-play32': '&#x31;',
		'ssfa-fileaplay-pause32': '&#x32;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/ssfa-fileaplay-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
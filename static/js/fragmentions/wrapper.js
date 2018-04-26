// fragmentions.js
// detect native/existing fragmention support
if (!('fragmention' in window.location)) (function () {
    // populate fragmention
    location.fragmention = location.fragmention || '';

    // return first element in scope containing case-sensitive text
    function getElementsByText(scope, text) {
	// iterate descendants of scope
	for (var all = scope.childNodes, index = 0, element, list = []; (element = all[index]); ++index) {
	    // conditionally return element containing visible, whitespace-insensitive, case-sensitive text (a match)
	    if (element.nodeType === 1 && (element.innerText || element.textContent || '').replace(/\s+/g, ' ').indexOf(text) !== -1) {
		list = list.concat(getElementsByText(element, text));
	    }
	}

	// return scope (no match)
	return list.length ? list : scope;
    }

    function getAnchorableElementByName(fragment) {
	var elements = document.getElementsByName(fragment), index = -1;

	while (elements[++index] && !/^A(REA)?$/.test(elements[index].nodeName)) {}

	return elements[index];
    }

    // on dom ready or hash change
    function onHashChange() {
	// do nothing if the dom is not ready
	if (!/e/.test(document.readyState)) return;

	// set location fragmention as uri-decoded text (from href, as hash may be decoded)
	var
	id = location.href.match(/#((?:#|%23)?)(.+)/) || [0,'',''],
	node = document.getElementById(id[1]+id[2]) || getAnchorableElementByName(id[1]+id[2]),
	match = decodeURIComponent(id[2].replace(/\+/g, ' ')).split('  ');

	location.fragmention = match[0];
	location.fragmentionIndex = parseFloat(match[1]) || 0;

	// conditionally remove stashed element fragmention attribute
	if (element) {
	    element.removeAttribute('fragmention');

	    // DEPRECATED: trigger style in IE8
	    if (element.runtimeStyle) {
		element.runtimeStyle.windows = element.runtimeStyle.windows;
	    }
	}

	// if fragmention exists
	if (!node && location.fragmention) {
	    var
	    // get all elements containing text (or document)
	    elements = getElementsByText(document, location.fragmention),
	    // get total number of elements
	    length   = elements.length,
	    // get index of element
	    modulus  = length && location.fragmentionIndex % length,
	    index    = length && modulus >= 0 ? modulus : length + modulus;

	    // get element
	    element = length && elements[index];

	    // if element found
	    if (element) {
		// scroll to element
		element.scrollIntoView();

		// set fragmention attribute
		element.setAttribute('fragmention', '');

		// DEPRECATED: trigger style in IE8
		if (element.runtimeStyle) {
		    element.runtimeStyle.windows = element.runtimeStyle.windows;
		}
	    }
	    // otherwise clear stashed element
	    else {
		element = null;
	    }
	}
    }

    var
    // DEPRECATED: configure listeners
    defaultListener = 'addEventListener',
    addEventListener = defaultListener in window ? [defaultListener, ''] : ['attachEvent', 'on'],
    // set stashed element
    element;

    // add listeners
    window[addEventListener[0]](addEventListener[1] + 'hashchange', onHashChange);
    document[addEventListener[0]](addEventListener[1] + 'readystatechange', onHashChange);

    onHashChange();
})();

// fragmentioner.js
"use strict";

(function(){
    //mustard cutting
    if (!window.getSelection || !encodeURI || !('content' in document.createElement('template'))) {
        return;
    }

    var
    BUTTON_HTML = '<div id="fragmentioner-ui" hidden ><a href="">Link to text</a></div>';

    var
    BUTTON = document.createElement('template');
    BUTTON.innerHTML = BUTTON_HTML;

    /* function to reveal the UI */

    function show_frag_btn() {
	var
	selected = get_selection(),
	text = selected.text,
	node = selected.node,
	offsets = get_offsets();

	// check if some text was selected
	if (text != '') {

	    ui.getElementsByTagName("a")[0].setAttribute("href", text2frag(text, node));
	    ui.style.left = selected.left + offsets.left + (selected.width)/2 + "px";
            /*			MAGIC NUMBER 40 */
	    ui.style.top = selected.top + offsets.top - 40 + "px";
	    ui.hidden = false;
	}
	else {
	    ui.hidden = true;
	}
    }

    /* functions to convert text to fragmention */

    function text2frag(text, node){

	function getElementsByText(scope, text) {
	    // iterate descendants of scope
	    for (var all = scope.childNodes, index = 0, element, list = []; (element = all[index]); ++index) {
		// conditionally return element containing visible, whitespace-insensitive, case-sensitive text (a match)
		if (element.nodeType === 1 && (element.innerText || element.textContent || '').replace(/\s+/g, ' ').indexOf(text) !== -1) {
		    list = list.concat(getElementsByText(element, text));
		}
	    }

	    // return scope (no match)
	    return list.length ? list : scope;
	}

	var hash = '#' + text;

	var elements = getElementsByText(document, text),
	    length = elements.length,
	    which = length && elements.indexOf(node);

	if (which && which > 0) {
	    hash += '++' + which;
	}

	return encodeURI(window.location.protocol + "//" + window.location.host + window.location.pathname + hash);
    }

    /* function to get the position and text of selection */

    function get_selection() {
	var sel = window.getSelection();
	if (sel.rangeCount) {
	    var range = sel.getRangeAt(0).cloneRange();
	    if (range.getBoundingClientRect) {
	        var rect = range.getBoundingClientRect(),
		    text = sel.toString(),
		    node = sel.anchorNode && sel.anchorNode.parentElement,
	            left = rect.left,
	            top = rect.top,
		    width = rect.right - rect.left;
	    }
	}

	// hack to replace whitespaces with normal space to follow FF behaviour as of 2016-12-20; also compatible with fragmention.js
	text = text.replace(/\s+/g, ' ');

	return { text: text, node: node, left: left, top: top, width: width };
    }

    /*  function to get scroll offsets */

    function get_offsets(){
	var left = 0, top = 0;
	if ('pageXOffset' in window){
	    left = window.pageXOffset;
	    top = window.pageYOffset;
	}
	else if ('scrollLeft' in document.documentElement){
	    left = document.documentElement.scrollLeft;
	    top = document.documentElement.scrollTop;
	}
	return { left: left, top: top };
    }

    var ui = BUTTON.content.cloneNode(true).firstElementChild;

    document.body.appendChild(ui);

    document.body.addEventListener("mouseup", show_frag_btn, false);
    document.body.addEventListener("touchend", show_frag_btn, false);
})();

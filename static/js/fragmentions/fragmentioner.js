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

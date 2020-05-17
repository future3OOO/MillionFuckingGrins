/*
 * @package       mds
 * @copyright     (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2020.05.13 12:41:15 EDT
 * @license       This program is free software; you can redistribute it and/or modify
 *        it under the terms of the GNU General Public License as published by
 *        the Free Software Foundation; either version 3 of the License, or
 *        (at your option) any later version.
 *
 *        This program is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *        GNU General Public License for more details.
 *
 *        You should have received a copy of the GNU General Public License along
 *        with this program;  If not, see http://www.gnu.org/licenses/gpl-3.0.html.
 *
 *  * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *        Million Dollar Script
 *        A pixel script for selling pixels on your website.
 *
 *        For instructions see README.txt
 *
 *        Visit our website for FAQs, documentation, a list team members,
 *        to post any bugs or feature requests, and a community forum:
 *        https://milliondollarscript.com/
 *
 */

var initialized = false;

// @link https://stackoverflow.com/a/58514043/311458
function defer(toWaitFor, method) {
	if (window[toWaitFor]) {
		method();
	} else {
		setTimeout(function () {
			defer(toWaitFor, method)
		}, 50);
	}
}

function add_ajax_loader(container) {
	let ajax_loader = $("<div class='ajax-loader'></div>");
	$(container).append(ajax_loader)
}

function remove_ajax_loader() {
	$('.ajax-loader').remove();
}

function mds_grid(container, bid, width, height) {
	if ($('#' + container).length > 0) {
		return;
	}

	add_ajax_loader(container);

	let grid = $("<div class='grid-inner' id='" + container + "'></div>");
	grid.css('width', width).css('height', height);
	$('.' + container).append(grid);

	const data = {
		action: 'show_grid',
		BID: bid
	};

	$(grid).load(window.mds_data.ajax, data, function () {
		mds_init('#theimage', true, true);
	});
}

function mds_stats(container, bid, width, height) {
	if ($('#' + container).length > 0) {
		return;
	}

	let stats = $("<div class='stats-inner' id='" + container + "'></div>");
	stats.css('width', width).css('height', height);
	$('.' + container).append(stats);

	const data = {
		action: 'show_stats',
		BID: bid
	};

	$(stats).load(window.mds_data.ajax, data, function () {
		mds_init('#' + container, false, false);
	});
}

function receiveMessage(event, $el) {
	if (event.origin !== window.mds_data.wp || !initialized) {
		return;
	}

	if ($el && $el.length > 0 && $el.data('scalemap') === true) {
		parent.postMessage('gridwidth', window.mds_data.wp);
		rescale($el);
	}

	switch (event.data) {
		case "thankyouframeheight":
		case "usersframeheight":
		case "listframeheight":
		case "statsframeheight":
		case "validateframeheight":
			event.source.postMessage(event.data + ":" + document.body.clientHeight, event.origin);
			break;
		case "gridheight":
			// readjust width if grid is smaller than body
			if ($el.width() < $('body').width() && $el.width() < $el.data('origWidth')) {
				$('html').height("100%");
				$('body').height("100%");

				$el.width(('body').width());
				$el.height(('body').width());
			}

			// set html and body height to same as grid height
			if ($('body').height() !== $el.height()) {
				$('html').height($el.height());
				$('body').height($el.height());
			}

			event.source.postMessage("gridheight:" + document.body.clientHeight, event.origin);

			break;
		default:
			break;
	}
}

function rescale($el) {
	// https://github.com/GestiXi/image-scale
	$el.imageScale({
		scale: "best-fit",
		align: "top",
		rescaleOnResize: true
	});
}

function add_tippy() {
	const defaultContent = $('.tooltip-source').html();

	tippy('.mds-container area', {
		theme: 'light',
		content: defaultContent,
		duration: 50,
		delay: 50,
		trigger: 'click',
		allowHTML: true,
		followCursor: true,
		hideOnClick: true,
		interactive: true,
		maxWidth: 350,
		placement: 'auto',
		touch: true,
		appendTo: 'parent',
		onCreate(instance) {
			instance._isFetching = false;
			instance._content = null;
			instance._error = null;

		},
		onShow(instance) {
			if (instance._isFetching || instance._content || instance._error) {
				return;
			}

			instance._isFetching = true;

			async function postData(url = '', data = {}) {
				return await fetch(url, {
					method: 'POST',
					mode: 'cors',
					cache: 'force-cache',
					credentials: 'same-origin',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
					},
					redirect: 'follow',
					referrerPolicy: 'no-referrer',
					body: JSON.stringify(data)
				});
			}

			const data = $(instance.reference).data('data');

			postData(window.mds_data.ajax, {
				aid: data.id,
				bid: data.banner_id,
				action: 'ga'
			})
				.then((response) => response.text())
				.then(function (text) {
					instance.setContent(text);
					instance._content = true;
				})
				.catch((error) => {
					instance._error = error;
					instance.setContent(`Request failed. ${error}`);
				})
				.finally(() => {
					instance._isFetching = false;
				});

		},
		onHidden(instance) {
			instance.setContent(defaultContent);
			instance._content = null;
			instance._error = null;
		}
	});
}

function mds_init(el, scalemap, tippy, type) {
	let $el = $(el);

	if ($el.length > 0) {
		let origWidth = $el.width();
		let origHeight = $el.height();

		$el.data('scalemap', scalemap).data('origWidth', origWidth).data('origHeight', origHeight);
	}

	if (scalemap) {
		let $elParent = $el;

		// https://github.com/GestiXi/image-scale
		$el.imageScale({
			scale: "best-fit",
			align: "top",
			rescaleOnResize: true,
			didScale: function (firstTime, options) {
				if (firstTime) {
					$elParent.height($el.height());
				}

				// https://github.com/clarketm/image-map
				$el.imageMap();
			}
		});

		$('area').on('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
		});
	}

	if (tippy) {
		defer('Popper', () => {
			defer('tippy', () => {
				add_tippy();
			});
		});
	}

	if (type === "iframe") {
		$('body').addClass('wp');
		window.top.postMessage('iframeload:html', window.mds_data.wp);
		window.addEventListener("message", function (event) {
			receiveMessage(event, $el);
		}, false);
	}

	initialized = true;

	remove_ajax_loader();
}

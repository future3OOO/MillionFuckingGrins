/*
 * @package       mds
 * @copyright     (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2020.05.08 17:42:17 EDT
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
var $gridimg;
var html;
var body;
var origWidth;
var origHeight;

function mds_grid(container, bid, width, height) {
	let grid = $("<div class='grid-inner' id='" + container + "'></div>");
	grid.css('width', width).css('height', height);
	$('.' + container).append(grid);

	const data = {
		action: 'show_grid',
		BID: bid
	};

	$(grid).load(mds_data.ajax, data, function () {
		mds_init('#theimage', true, true);
	});
}

function mds_stats(container, bid, width, height) {
	let stats = $("<div class='stats-inner' id='" + container + "'></div>");
	stats.css('width', width).css('height', height);
	$('.' + container).append(stats);

	const data = {
		action: 'show_stats',
		BID: bid
	};

	$(stats).load(mds_data.ajax, data, function () {
		mds_init();
	});
}

function receiveMessage(event) {
	if (event.origin !== mds_data.wp || !initialized) {
		return;
	}

	parent.postMessage('gridwidth', mds_data.wp);

	if ($gridimg) {
		rescale();
	}

	if (event.data === "thankyouframeheight") {
		$(function () {
			event.source.postMessage("thankyouframeheight:" + document.body.clientHeight, event.origin);
		});
	}
	if (event.data === "usersframeheight") {
		$(function () {
			event.source.postMessage("usersframeheight:" + document.body.clientHeight, event.origin);
		});
	}
	if (event.data === "listframeheight") {
		$(function () {
			event.source.postMessage("listframeheight:" + document.body.clientHeight, event.origin);
		});
	}
	if (event.data === "statsframeheight") {
		$(function () {
			event.source.postMessage("statsframeheight:" + document.body.clientHeight, event.origin);
		});
	}
	if (event.data === "validateframeheight") {
		$(function () {
			event.source.postMessage("validateframeheight:" + document.body.clientHeight, event.origin);
		});
	}

	if (event.data === "gridheight") {

		// readjust width if grid is smaller than body
		if ($gridimg.width() < body.width() && $gridimg.width() < origWidth) {
			html.height("100%");
			body.height("100%");

			$gridimg.width(body.width());
			$gridimg.height(body.width());
		}

		// set html and body height to same as grid height
		if (body.height() !== $gridimg.height()) {
			html.height($gridimg.height());
			body.height($gridimg.height());
		}

		event.source.postMessage("gridheight:" + document.body.clientHeight, event.origin);
	}
}

function rescale() {
	// https://github.com/GestiXi/image-scale
	$gridimg.imageScale({
		scale: "best-fit",
		align: "top",
		rescaleOnResize: true
	});
}

function add_tippy() {
	const defaultContent = $('.tooltip-source').html();

	tippy('area', {
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
						'Content-Type': 'application/json'
					},
					redirect: 'follow',
					referrerPolicy: 'no-referrer',
					body: JSON.stringify(data)
				});
			}

			const data = $(instance.reference).data('data');

			postData(mds_data.ajax, {
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

function mds_init(grid, scalemap, tippy) {
	if (grid) {
		$gridimg = $(grid);
		let $girdimgParent = $gridimg.parent();
		html = $("html");
		body = $("body");
		origWidth = $gridimg.width();
		origHeight = $gridimg.height();

		$('html').css('width', '100%').css('height', '100%');
		$('body').css('width', '100%').css('height', '100%').css('position', 'relative');

		if (scalemap) {
			// https://github.com/GestiXi/image-scale
			$gridimg.imageScale({
				scale: "best-fit",
				align: "top",
				rescaleOnResize: true,
				didScale: function (firstTime, options) {
					if (firstTime) {
						$girdimgParent.height($gridimg.height());
					}

					// https://github.com/clarketm/image-map
					$gridimg.imageMap();
				}
			});
		}

		if (tippy) {
			add_tippy();
		}
	}

	if (mds_data.wp !== '') {
		$('body').addClass('wp');
		window.top.postMessage('iframeload:html', mds_data.wp);
		window.addEventListener("message", receiveMessage, false);
	}

	initialized = true;
}

$(function () {
	mds_init();
});

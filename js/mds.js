/*
 * @package       mds
 * @copyright     (C) Copyright 2022 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2022-02-28 15:54:43 EST
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
	let $ajax_loader = $("<div class='ajax-loader'></div>");
	$(container).append($ajax_loader)
	$ajax_loader.css('top', $(container).position().top).css('left', ($(container).width() / 2) - ($ajax_loader.width() / 2));
}

function remove_ajax_loader() {
	$('.ajax-loader').remove();
}

function mds_grid(container, bid, width, height) {
	if ($('#' + container).length > 0) {
		return;
	}

	add_ajax_loader('.' + container);

	let grid = $("<div class='grid-inner' id='" + container + "'></div>");
	grid.css('width', width).css('height', height);
	$('.' + container).append(grid);

	const data = {
		action: 'show_grid',
		BID: bid
	};

	$(grid).load(window.mds_data.ajax, data, function () {
		mds_init('#theimage', true, window.mds_data.ENABLE_MOUSEOVER !== 'NO', false, true);
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
		mds_init('#' + container, false, false, false, false);
	});
}

function mds_list(container, bid, width, height) {
	if ($('#' + container).length > 0) {
		return;
	}

	let list = $("<div class='list-inner' id='" + container + "'></div>");
	list.css('width', width).css('height', height);
	$('.' + container).append(list);

	const data = {
		action: 'show_list',
		BID: bid
	};

	$(list).load(window.mds_data.ajax, data, function () {
		mds_init('#' + container, false, true, false, false);
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

function add_tippy() {
	const defaultContent = "<div class='ajax-loader'></div>";
	const isIOS = /iPhone|iPad|iPod/.test(navigator.platform);

	window.tippy_instance = tippy('.mds-container area,.list-link', {
		theme: 'light',
		content: defaultContent,
		duration: 50,
		delay: 50,
		trigger: 'click',
		allowHTML: true,
		followCursor: 'initial',
		hideOnClick: true,
		interactive: true,
		maxWidth: 350,
		placement: 'auto',
		touch: true,
		appendTo: 'parent',
		popperOptions: {
			strategy: 'fixed',
			modifiers: [
				{
					name: 'flip',
					options: {
						fallbackPlacements: ['bottom', 'right'],
					},
				},
				{
					name: 'preventOverflow',
					options: {
						altAxis: true,
						tether: false,
						padding: 40,
					},
				},
			],
		},
		onCreate(instance) {
			instance._isFetching = false;
			instance._content = null;
			instance._error = null;
			window.tippy_instance = instance;
		},
		onShow(instance) {
			if (instance._isFetching || instance._content || instance._error) {
				return;
			}

			if (isIOS) {
				$(instance.reference).trigger('click');
			}

			instance._isFetching = true;

			const data = $(instance.reference).data('data');

			let ajax_data = {
				aid: data.id,
				bid: data.banner_id,
				block_id: data.block_id,
				action: 'ga'
			};

			$.ajax({
				method: 'POST',
				url: window.mds_data.ajax,
				data: ajax_data,
				dataType: 'html',
				crossDomain: true,
			}).done(function (data) {
				instance.setContent(data);
				instance._content = true;
			}).fail(function (jqXHR, textStatus, errorThrown) {
				instance._error = errorThrown;
				instance.setContent(`Request failed. ${errorThrown}`);
			}).always(function () {
				instance._isFetching = false;
			});

		},
		onHidden(instance) {
			instance.setContent(defaultContent);
			instance._content = null;
			instance._error = null;
		}
	});

	window.is_touch = false;

	$(document).on('touchstart', function () {
		window.is_touch = true;
	});

	$(document).on('scroll', function () {
		if (!window.is_touch && window.tippy_instance != null && typeof window.tippy_instance.hide === 'function') {
			window.tippy_instance.hide();
		}
	});

	$(document).on('click', '.list-link', function (e) {
		e.preventDefault();
		e.stopPropagation();
	});
}

let rescaling = false;

function rescale($el) {

	if (rescaling) {
		return;
	}

	rescaling = true;

	// https://github.com/GestiXi/image-scale
	$el.imageScale({
		scale: "best-fit",
		align: "top",
		rescaleOnResize: true,
		didScale: function (firstTime, options) {
			rescaling = false;
		}
	});
}

function mds_loaded_event(el, scalemap, tippy, iframe, isgrid) {
	if (window.mds_loaded === true) {
		return;
	}
	window.mds_loaded = true;

	jQuery(document).trigger({
		type: 'mds-loaded',
		el: el,
		scalemap: scalemap,
		tippy: tippy,
		iframe: iframe,
		isgrid: isgrid
	});
}

$(document).on('mds-loaded', function (el, scalemap, tippy, iframe, isgrid) {
	setTimeout(function () {
		window.dispatchEvent(new Event('resize'));
	}, 100);
});

function mds_init(el, scalemap, tippy, type, isgrid) {
	let $el = $(el);
	window.mds_loaded = false;

	if (isgrid && scalemap) {

		let origWidth;
		let origHeight;

		if ($el.length > 0) {
			origWidth = $el.width();
			origHeight = $el.height();

			$el.data('scalemap', scalemap).data('origWidth', origWidth).data('origHeight', origHeight);
		}

		let $elParent = $el;

		// https://github.com/GestiXi/image-scale
		$el.imageScale({
			scale: "best-fit",
			align: "top",
			rescaleOnResize: true,
			didScale: function (firstTime, options) {
				if (window.mds_data.wp !== "") {
					if ($elParent.parent().parent().parent().parent().height() < origHeight) {
						$elParent.parent().parent().parent().parent().width(origWidth);
						$elParent.parent().parent().parent().parent().height(origHeight);
					}
				}

				if ($elParent.parent().height() < origHeight) {
					$elParent.width(origWidth);
					$elParent.height(origHeight);
					$elParent.parent().width(origWidth);
					$elParent.parent().height(origHeight);
					rescale($el);
				}

				if (window.mds_data.wp !== "") {
					$elParent.parent().parent().parent().parent().width($el.width());
					$elParent.parent().parent().parent().parent().height($el.height());
				}

				$elParent.parent().width($el.width());
				$elParent.parent().height($el.height());

				rescaling = false;
			}
		});

		rescale($el);

		// https://github.com/clarketm/image-map
		ImageMap('img[usemap]');
		//$el.imageMap();

		$(window).on('resize', function () {
			rescale($el);
		});
	}

	$el.on('load', function () {
		if (isgrid) {
			if (window.mds_data.wp !== "") {
				$el.parent().parent().parent().parent().css('border-bottom', '1px solid #D4D6D4').css('border-right', '1px solid #D4D6D4');
			} else {
				$el.parent().css('border-bottom', '1px solid #D4D6D4').css('border-right', '1px solid #D4D6D4');
			}

			if (scalemap) {
				rescale($el);
			}
		}

		remove_ajax_loader();
	});

	if (isgrid) {
		$('area').off('click').on('click', function (e) {
			e.preventDefault();
			e.stopPropagation();

			window.click_data = $(this).data('data');
		});

		$(document).off('click', '.pixel-url').on('click', '.pixel-url', function (e) {
			e.preventDefault();
			e.stopPropagation();

			const $link = $(this);

			let ajax_data = {
				aid: window.click_data.ad_id,
				bid: window.click_data.banner_id,
				block_id: window.click_data.block_id,
				action: 'click'
			};

			$.ajax({
				method: 'POST',
				url: window.mds_data.ajax,
				data: ajax_data,
				dataType: 'html',
				crossDomain: true,
			}).done(function () {
				window.open($link.attr('href'), '_self');
			});
		});

		$(document).off('click', '#theimage').on('click', '#theimage', function (e) {
			e.preventDefault();
			e.stopPropagation();
			if (window.mds_data.REDIRECT_SWITCH === 'YES') {
				window.open(window.mds_data.REDIRECT_URL);
				return false;
			}
		});
	}

	let tooltips = false;
	if (tippy && window.tippy_instance == undefined && window.mds_data.ENABLE_MOUSEOVER !== 'NO') {
		tooltips = true;
		defer('Popper', () => {
			defer('tippy', () => {
				add_tippy();
				mds_loaded_event($el, scalemap, tippy, type, isgrid);
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

	if (!tooltips) {
		mds_loaded_event($el, scalemap, tippy, type, isgrid);
	}
}

$(function () {
	$('.mds_upload_image').on('click', function (e) {
		let $el = $(this);
		$el.prop('disabled', true);
		$el.attr('value', 'Uploading...');
		$el.parent('form').submit();
	});

	$('.mds_pointer_graphic').on('load', function (e) {
		$('.mds_upload_image').prop('disabled', false);
		$(this).attr('value', 'Upload');
	});

	$('.mds_save_ad_button').on('click', function () {
		let $el = $(this);
		$el.prop('disabled', true);
		$el.attr('value', 'Saving...');
		$el.closest('form').submit();
	});

	$('#mds-complete-button').on('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		let $el = $(this);
		$el.prop('disabled', true);
		$el.attr('value', 'Completing...');
		window.location=window.mds_data.BASE_HTTP_PATH + 'users/publish.php?action=complete&order_id=' + $el.data('order-id') + '&BID=' + $el.data('grid');
		return false;
	});

	$('#mds-confirm-button').on('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		let $el = $(this);
		$el.prop('disabled', true);
		$el.attr('value', 'Confirming...');
		window.location=window.mds_data.BASE_HTTP_PATH + 'users/payment.php?action=confirm&order_id=' + $el.data('order-id') + '&BID=' + $el.data('grid');
		return false;
	});
});
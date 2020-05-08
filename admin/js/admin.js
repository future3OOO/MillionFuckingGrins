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

const submit_options = {
	target: ".admin-content",
	type: 'post',
	delegation: true,
	beforeSubmit: mds_form_submit,
	success: mds_form_submit_success,
};

function mds_load_page(page, force) {
	console.log('window.location.hash', window.location.hash);
	console.log('page', page);

	// remove hashtag from page
	if (window.location.hash !== "" && (page === undefined || (window.location.hash !== page && force !== true))) {
		page = window.location.hash.substr(1);
	}

	$(".admin-content").load(page, function () {
		console.log("loading " + page);
	});
}

function mds_form_submit(formData, $form, options) {
	console.log('Form data', $.param(formData));
	$form.find('input').attr('disabled', true);
	return true;
}

function mds_form_submit_success(responseText, statusText, xhr, $form) {
	console.log('mds_form_submit_success');

	let url = $form.attr('action');

	if (url === "") {
		url = window.location.hash.substr(1);
	}

	$(document).scrollTop(0);

	window.location.hash = '#' + url;

	$form.find('input').attr('disabled', false);
}

function confirmLink(theLink, theConfirmMsg) {
	if (theConfirmMsg === '') {
		mds_load_page(theLink.href, true);
		return false;
	}

	let is_confirmed = confirm(theConfirmMsg + '\n');
	if (is_confirmed) {
		let link = theLink.href;
		if (link == null) {
			link = $(theLink).data('link');
		}
		link += '&is_js_confirmed=1';
		mds_load_page(link, true);
	}

	return false;
}

function checkBoxes(checkbox, name) {
	let state, boxes, count, i;
	state = checkbox.checked;
	boxes = eval("document.form1.elements['" + name + "']");
	count = boxes.length;
	for (i = 0; i < count; i++) {
		boxes[i].checked = state;
	}
}

function mds_submit(el) {
	let form = $(el).closest('form');
	$(form).ajaxSubmit(submit_options);
}

$(function () {

	let admin_content = $(".admin-content");

	let startpage = "main.php";
	mds_load_page(startpage);

	$(document).on('click', 'a', function (event) {
		event.preventDefault();
		event.stopPropagation();

		let url = $(this).attr('href');

		if (url.startsWith("http")) {
			window.location = url;
			return false;
		}

		admin_content.load(url, function (response, status) {
			if (status === "success") {
				if (url.endsWith('LICENSE.txt')) {
					admin_content.html(response.replace(/\r\n|\r|\n/g, '<br />'));
				}
			}
		});

		$(document).scrollTop(0);

		window.location.hash = '#' + url;

		return false;
	});

	$(this).ajaxForm(submit_options);

	$(window).on('popstate', function () {
		$(function () {
			mds_load_page(window.location);
		});
	});

});

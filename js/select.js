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

let debug = false;

// Initialize
let USE_AJAX = select.USE_AJAX;
let block_str = select.block_str;
let selectedBlocks = block_str.split(',').map(Number);
let selecting = false;
let ajaxing = false;

let grid_width = select.grid_width;
let grid_height = select.grid_height;

let BLK_WIDTH = select.BLK_WIDTH;
let BLK_HEIGHT = select.BLK_HEIGHT;

let GRD_WIDTH = BLK_WIDTH * grid_width;
let GRD_HEIGHT = BLK_HEIGHT * grid_height;

let orig = {
	grid_width: grid_width,
	grid_height: grid_height,
	BLK_WIDTH: BLK_WIDTH,
	BLK_HEIGHT: BLK_HEIGHT,
	GRD_WIDTH: GRD_WIDTH,
	GRD_HEIGHT: GRD_HEIGHT
};

let scaled_width = 1;
let scaled_height = 1;

let myblocks;
let total_cost;
let grid;
let submit_button1;
let submit_button2;
let pointer;
let pixel_container;

const messageout = function (message) {
	if (debug) {
		console.log(message);
	} else {
		alert(message);
	}
}

$.fn.rescaleStyles = function () {
	this.css({
		'width': BLK_WIDTH + 'px',
		'height': BLK_HEIGHT + 'px',
		'line-height': BLK_HEIGHT + 'px',
		'font-size': BLK_HEIGHT + 'px',
	});

	return this;
};

$.fn.repositionStyles = function () {
	if (this.attr('id') === undefined) {
		return this;
	}

	let id = parseInt($(this).data('blockid'));
	let pos = get_block_position(id);

	this.css({
		'top': ((pos.y * scaled_height) + grid.offsetTop) + 'px',
		'left': ((pos.x * scaled_width) + grid.offsetLeft) + 'px'
	});

	return this;
};

function has_touch() {
	try {
		document.createEvent("TouchEvent");
		console.log('has_touch true')
		return true;
	} catch (e) {
		console.log('has_touch false')
		return false;
	}
}

window.onload = function () {
	grid = document.getElementById("pixelimg");
	myblocks = document.getElementById('blocks');
	total_cost = document.getElementById('total_cost');
	submit_button1 = document.getElementById('submit_button1');
	submit_button2 = document.getElementById('submit_button2');
	pointer = document.getElementById('block_pointer');
	pixel_container = document.getElementById('pixel_container');

	window.onresize = rescale_grid;

	load_order();

	if (has_touch()) {
		handle_touch_events();
	} else {
		handle_click_events();
	}

	rescale_grid();

	// disable context menu on grid
	$(grid).oncontextmenu = (e) => {
		e.preventDefault();
	}
};

function load_order() {

	for (let i = 0; i < select.blocks.length; i++) {
		add_block(parseInt(select.blocks[i].block_id), parseInt(select.blocks[i].x) * scaled_width, parseInt(select.blocks[i].y) * scaled_height, true);
	}

	const form1 = document.getElementById('form1');
	form1.addEventListener('submit', form1Submit);
}

function update_order() {
	if (selectedBlocks !== -1) {
		document.form1.selected_pixels.value = selectedBlocks.join(',');
	}
}

function reserve_block(block_id) {
	if (selectedBlocks.indexOf(block_id) === -1) {
		selectedBlocks.push(parseInt(block_id));

		// remove default value of -1 from array
		let index = selectedBlocks.indexOf(-1);
		if (index > -1) {
			selectedBlocks.splice(index, 1);
		}

		update_order();
	}
}

function unreserve_block(block_id) {
	let index = selectedBlocks.indexOf(block_id);
	if (index > -1) {
		selectedBlocks.splice(index, 1);
		update_order();
	}
}

function add_block(block_id, block_x, block_y, loading) {

	let block_left;
	let block_top;

	// grid clicked
	if (block_x == null || block_y == null) {
		block_left = pointer.map_x + grid.offsetLeft;
		block_top = pointer.map_y + grid.offsetTop;

	} else if (loading !== true) {
		block_left = block_x + grid.offsetLeft;
		block_top = block_y + grid.offsetTop;
	}

	// block element
	let $new_block = $('<span>');
	$(myblocks).append($new_block);

	$new_block.attr('id', 'block' + block_id.toString());

	$new_block.css({
		'left': block_left + 'px',
		'top': block_top + 'px',
		'line-height': BLK_HEIGHT + 'px',
		'font-size': BLK_HEIGHT + 'px',
		'width': BLK_WIDTH + 'px',
		'height': BLK_HEIGHT + 'px',
	});

	$new_block.mousemove(function ($event) {
		let offset = getOffset($event.originalEvent.pageX, $event.originalEvent.pageY);
		if (offset == null) {
			return false;
		}

		show_pointer(offset);
	});

	$($new_block).data('blockid', block_id);

	// block image
	let $new_img = $('<img alt="" src="">');
	$new_block.append($new_img);

	$new_img.attr('src', select.BASE_HTTP_PATH + 'images/selected_block.png');

	$new_img.css({
		'line-height': BLK_HEIGHT + 'px',
		'font-size': BLK_HEIGHT + 'px',
		'width': BLK_WIDTH + 'px',
		'height': BLK_HEIGHT + 'px',
	});

	reserve_block(block_id);
}

function remove_block(block_id) {
	let myblock = document.getElementById("block" + block_id.toString());
	if (myblock !== null) {
		myblock.remove();
	}

	unreserve_block(block_id);
}

function invert_block(clicked_block) {
	let myblock = document.getElementById("block" + clicked_block.id.toString());
	if (myblock !== null) {
		remove_block(clicked_block.id);
	} else {
		add_block(clicked_block.id, clicked_block.x, clicked_block.y);
	}
}

function invert_blocks(block, OffsetX, OffsetY) {
	let clicked_blocks = [];
	let x;
	let y;

	// actual clicked block
	x = OffsetX;
	y = OffsetY;
	clicked_blocks.push({
		id: block,
		x: x,
		y: y
	});

	// TODO: add option to disable these
	// additional blocks if multiple selection radio buttons are selected
	if (document.getElementById('sel4').checked) {
		// select 4 - 4x4

		x = OffsetX + BLK_WIDTH;
		y = OffsetY;
		clicked_blocks.push({
			id: get_clicked_block(x, y),
			x: x,
			y: y
		});

		x = OffsetX;
		y = OffsetY + BLK_HEIGHT;
		clicked_blocks.push({
			id: get_clicked_block(x, y),
			x: x,
			y: y
		});

		x = OffsetX + BLK_WIDTH;
		y = OffsetY + BLK_HEIGHT;
		clicked_blocks.push({
			id: get_clicked_block(x, y),
			x: x,
			y: y
		});

	} else {
		// select 6 - 3x2

		if (document.getElementById('sel6').checked) {

			x = OffsetX + BLK_WIDTH;
			y = OffsetY;
			clicked_blocks.push({
				id: get_clicked_block(x, y),
				x: x,
				y: y
			});

			x = OffsetX + (BLK_WIDTH * 2);
			y = OffsetY;
			clicked_blocks.push({
				id: get_clicked_block(x, y),
				x: x,
				y: y
			});

			x = OffsetX;
			y = OffsetY + BLK_HEIGHT;
			clicked_blocks.push({
				id: get_clicked_block(x, y),
				x: x,
				y: y
			});

			x = OffsetX + BLK_WIDTH;
			y = OffsetY + BLK_HEIGHT;
			clicked_blocks.push({
				id: get_clicked_block(x, y),
				x: x,
				y: y
			});

			x = OffsetX + (BLK_WIDTH * 2);
			y = OffsetY + BLK_HEIGHT;
			clicked_blocks.push({
				id: get_clicked_block(x, y),
				x: x,
				y: y
			});
		}
	}

	for (const clicked of clicked_blocks) {

		// invert block
		invert_block(clicked);
	}
}

function select_pixels(offset) {

	if (selecting) {
		return false;
	}
	selecting = true;

	// cannot select while AJAX is in action
	if (submit_button1.disabled) {
		return false;
	}

	pointer.style.visibility = 'hidden';

	change_block_state(offset.x, offset.y);

	return true;
}

/**
 * @return {boolean}
 */
function IsNumeric(str) {
	let ValidChars = "0123456789";
	let IsNumber = true;
	let Char;

	for (let i = 0; i < str.length && IsNumber === true; i++) {
		Char = str.charAt(i);
		if (ValidChars.indexOf(Char) === -1) {
			IsNumber = false;
		}
	}
	return IsNumber;

}

function get_block_position(block_id) {

	let cell = 0;
	let ret = {};
	ret.x = 0;
	ret.y = 0;

	for (let i = 0; i < orig.GRD_HEIGHT; i += orig.BLK_HEIGHT) {
		for (let j = 0; j < orig.GRD_WIDTH; j += orig.BLK_WIDTH) {
			if (block_id === cell) {
				return {x: j, y: i};
			}
			cell++;
		}
	}

	return ret;
}

function get_clicked_block(OffsetX, OffsetY) {

	OffsetX /= scaled_width;
	OffsetY /= scaled_height;

	let X = (OffsetX / orig.BLK_WIDTH);
	let Y = (OffsetY / orig.BLK_HEIGHT) * (orig.GRD_WIDTH / orig.BLK_WIDTH);

	return Math.round(X + Y);
}

function change_block_state(OffsetX, OffsetY) {
	let clicked_block = get_clicked_block(OffsetX, OffsetY);

	if (ajaxing === false) {

		submit_button1.disabled = true;
		submit_button2.disabled = true;
		pointer.style.cursor = 'wait';
		grid.style.cursor = 'wait';
		ajaxing = true;

		$.post("update_order.php?sel_mode=" + document.getElementsByName('pixel_form')[0].elements.sel_mode.value + "&user_id=" + select.user_id + "&block_id=" + clicked_block.toString() + "&BID=" + select.BID + "&t=" + select.time, function (data) {
			if (data === 'new') {
				invert_blocks(clicked_block, OffsetX, OffsetY);

			} else {
				if (IsNumeric(data)) {

					// save order id
					document.form1.order_id.value = data;
					invert_blocks(clicked_block, OffsetX, OffsetY);

				} else {

					if (data.indexOf('max_orders') > -1) {
						messageout(select.advertiser_max_order);
					} else if (data.indexOf('not_adjacent') > -1) {
						messageout(select.not_adjacent);
					} else if (data.length > 0) {
						messageout(data);
					}
				}
			}

			submit_button1.disabled = false;
			submit_button2.disabled = false;
			pointer.style.cursor = 'pointer';
			pointer.style.visibility = 'visible';
			grid.style.cursor = 'pointer';
			selecting = false;
			ajaxing = false;

		}).fail(function (data) {
			messageout("Error: " + data);
		});
	}
}

function implode(myArray) {

	let str = '';
	let comma = '';

	for (let i in myArray) {
		if (myArray.hasOwnProperty(i)) {
			str = str + comma + myArray[i];
		}
		comma = ',';
	}

	return str;
}

function getObjCoords(obj) {
	let pos = {x: 0, y: 0};
	let curtop = 0;
	let curleft = 0;
	if (obj.offsetParent) {
		while (obj.offsetParent) {
			curtop += obj.offsetTop;
			curleft += obj.offsetLeft;
			obj = obj.offsetParent;
		}
	} else if (obj.y) {
		curtop += obj.y;
		curleft += obj.x;
	}
	pos.x = curleft;
	pos.y = curtop;
	return pos;
}

function getOffset(x, y) {
	if (grid == null) {
		// grid may not be loaded yet
		return null;
	}

	let pos = getObjCoords(grid);
	let size = get_pointer_size();

	let offset = {};

	offset.x = x - pos.x;
	offset.y = y - pos.y;

	// drop 1/10 from the OffsetX and OffsetY, eg 612 becomes 610
	// expand to original scale first
	offset.x = Math.floor((offset.x / scaled_width) / orig.BLK_WIDTH) * orig.BLK_WIDTH;
	offset.y = Math.floor((offset.y / scaled_height) / orig.BLK_HEIGHT) * orig.BLK_HEIGHT;

	// keep within range
	offset.x = Math.max(Math.min(offset.x, GRD_WIDTH - (size.width / scaled_width)), 0);
	offset.y = Math.max(Math.min(offset.y, GRD_HEIGHT - (size.height / scaled_height)), 0);

	// scale back down if necessary
	offset.x = offset.x * scaled_width;
	offset.y = offset.y * scaled_height;

	return offset;
}

function get_pointer_size() {
	let size = {};

	// TODO: add option to disable these
	if (document.getElementById('sel4').checked) {
		size.width = BLK_WIDTH * 2;
		size.height = BLK_HEIGHT * 2;

	} else {
		if (document.getElementById('sel6').checked) {
			size.width = BLK_WIDTH * 3;
			size.height = BLK_HEIGHT * 2;
		} else {
			size.width = BLK_WIDTH;
			size.height = BLK_HEIGHT;
		}
	}

	return size;
}

function show_pointer(offset) {
	pointer.style.visibility = 'visible';
	pointer.style.display = 'block';

	pointer.style.top = offset.y + "px";
	pointer.style.left = offset.x + "px";

	pointer.map_x = offset.x;
	pointer.map_y = offset.y;

	let size = get_pointer_size();
	pointer.style.width = size.width + "px";
	pointer.style.height = size.height + "px";

	return true;
}

function form1Submit(event) {
	event.preventDefault();
	event.stopPropagation();

	if (myblocks.innerHTML.trim() === '') {
		messageout(select.no_blocks_selected);
		return false;
	} else {
		document.form1.submit();
	}
}

function reset_pixels() {
	$.post("update_order.php?reset=true", function (data) {
		if (data === "removed") {
			$(myblocks).children().each(function () {
				remove_block($(this).data('blockid'));
			});
		}
	});
}

function rescale_grid() {

	grid_width = $(grid).width();
	grid_height = $(grid).height();

	scaled_width = grid_width / orig.GRD_WIDTH;
	scaled_height = grid_height / orig.GRD_HEIGHT;

	BLK_WIDTH = orig.BLK_WIDTH * scaled_width;
	BLK_HEIGHT = orig.BLK_HEIGHT * scaled_height;

	$(pointer).rescaleStyles();
	$(myblocks).find('*').each(function () {
		$(this).rescaleStyles().repositionStyles();
	});
}

function center_block(coords) {
	let size = get_pointer_size();
	coords.x -= (size.width / 2) - (BLK_WIDTH / 2);
	coords.y -= (size.height / 2) - (BLK_HEIGHT / 2);
	return coords;
}

function handle_click_events() {
	let click = false;

	$(pixel_container).on('mousedown', function () {
		click = true;
	});

	$(pixel_container).on('mousemove', function (event) {
		let coords = center_block({
			x: event.originalEvent.pageX,
			y: event.originalEvent.pageY
		});
		let offset = getOffset(coords.x, coords.y);
		if (offset == null) {
			return false;
		}

		show_pointer(offset);
		click = false;
	});

	$(pixel_container).on('click', function (event) {
		event.preventDefault();

		if (click) {
			click = false;

			let coords = center_block({
				x: event.originalEvent.pageX,
				y: event.originalEvent.pageY
			});
			let offset = getOffset(coords.x, coords.y);
			if (offset == null) {
				return false;
			}

			show_pointer(offset);
			select_pixels(offset);
		}

		return false;
	});
}

function handle_touch_events() {
	let manager = new Hammer.Manager(pixel_container);
	let Tap = new Hammer.Tap({
		taps: 1
	});
	manager.add(Tap);
	manager.on('tap', function(e) {
		let offset = getOffset(e.center.x, e.center.y);
		if (offset == null) {
			return true;
		}

		show_pointer(offset);
		select_pixels(offset);
	});
}

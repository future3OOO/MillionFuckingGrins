<?php
/**
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

require_once __DIR__ . "/../include/init.php";
require( 'admin_common.php' );

ini_set( 'max_execution_time', 10000 );
ini_set( 'max_input_vars', 10002 );

global $f2;

$BID = $f2->bid();

$banner_data = load_banner_constants( $BID );

$imagine = new Imagine\Gd\Imagine();

// load blocks
$block_size  = new Imagine\Image\Box( $banner_data['BLK_WIDTH'], $banner_data['BLK_HEIGHT'] );
$palette     = new Imagine\Image\Palette\RGB();
$color       = $palette->color( '#000', 0 );
$zero_point  = new Imagine\Image\Point( 0, 0 );
$blank_block = $imagine->create( $block_size, $color );

$default_nfs_block = $blank_block->copy();
$tmp_block         = $imagine->load( $banner_data['USR_NFS_BLOCK'] );
$tmp_block->resize( $block_size );
$default_nfs_block->paste( $tmp_block, $zero_point );
$data = base64_encode( $default_nfs_block->get( "png", array( 'png_compression_level' => 9 ) ) );

if ( $_REQUEST['action'] == 'save' ) {
	//$sql = "delete from blocks where status='nfs' AND banner_id=$BID ";
	//mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']).$sql);

	if ( isset( $_POST['addnfs'] ) && ( ! empty( $_POST['addnfs'] || $_POST['addnfs'] == 0 ) ) ) {
		$addnfs = json_decode( $_POST['addnfs'] );
	} else {
		unset( $addnfs );
	}

	if ( isset( $_POST['remnfs'] ) && ( ! empty( $_POST['remnfs'] ) || $_POST['remnfs'] == 0 ) ) {
		$remnfs = json_decode( $_POST['remnfs'] );
	} else {
		unset( $remnfs );
	}

	$cell = $x = $y = 0;
	for ( $i = 0; $i < $banner_data['G_HEIGHT']; $i ++ ) {
		$x = 0;
		for ( $j = 0; $j < $banner_data['G_WIDTH']; $j ++ ) {
			if ( isset( $addnfs ) && in_array( $cell, $addnfs ) ) {
				$sql = "REPLACE INTO blocks (block_id, status, x, y, image_data, banner_id, click_count, alt_text) VALUES ({$cell}, 'nfs', {$x}, {$y}, '{$data}', {$BID}, 0, '')";
				mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
			}
			if ( isset( $remnfs ) && in_array( $cell, $remnfs ) ) {
				$sql = "DELETE FROM blocks WHERE status='nfs' AND banner_id={$BID} AND block_id={$cell}";
				mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
			}
			$x = $x + $banner_data['BLK_WIDTH'];
			$cell ++;
		}
		$y = $y + $banner_data['BLK_HEIGHT'];
	}
	echo "Success!";
	exit();
} else if ( $_REQUEST['action'] == 'reset' ) {
	$sql = "DELETE FROM blocks WHERE status='nfs' AND banner_id=$BID";
	mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	echo "Success!";
	exit();
}
?>
<script type="text/javascript">
	$(function () {
		var addnfs = [];
		var remnfs = [];

		function processBlock(block) {
			let blockid = block.attr("data-block");
			if (block.hasClass("nfs")) {
				block.removeClass("nfs").addClass("free");
				remnfs.push(blockid);
				let index = addnfs.indexOf(blockid);
				if (index !== -1) {
					addnfs.splice(index, 1);
				}
			} else if (block.hasClass("free")) {
				block.removeClass("free").addClass("nfs");
				blockid = block.attr("data-block");
				addnfs.push(blockid);
				let index = remnfs.indexOf(blockid);
				if (index !== -1) {
					remnfs.splice(index, 1);
				}
			} else if (!block.hasClass("free")) {
				block.removeClass("ui-selected");
			}
		}

		const $document = $(document);

		$document.on('click', '.grid', function () {
			$(this).selectable({
			delay: 75,
			distance: <?php echo $banner_data['BLK_WIDTH']; ?>,
			autoRefresh: false,
			filter: 'span',
			stop: function () {
				$(".ui-selected", this).each(function () {
					processBlock($(this));
				});
				$(this).selectable("refresh");
			}
		});
		});

		$document.on('click', '.block', function () {
			processBlock($(this));
		});

		$document.on('click', '.save', function (e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).after('<img class="loading" width="16" height="16" src="../images/ajax-loader.gif" alt="Loading..." />');
			$('.save').prop('disabled', true);
			$('.reset').prop('disabled', true);

			$.post("nfs.php", {
				BID: <?php echo $BID; ?>,
				action: "save",
				addnfs: JSON.stringify(addnfs),
				remnfs: JSON.stringify(remnfs)
			}).done(function (data) {
				$('.loading').hide(function () {
					$(this).remove();
					$('<span class="message">' + data + '</span>').insertAfter('.save').fadeOut(10000, function () {
						$(this).remove();
					});
				});
				$('.save').prop('disabled', false);
				$('.reset').prop('disabled', false);
			});
		});

		$document.on('click', '.reset', function (e) {
			e.preventDefault();
			e.stopPropagation();
			if (confirm('This will unselect all NFS blocks. Are you sure you want to do this?')) {
				$('.save').prop('disabled', true);
				$('.reset').prop('disabled', true);
				$(this).after('<img class="loading" width="16" height="16" src="../images/ajax-loader.gif" alt="Loading..." />');
				$.post("nfs.php", {
					BID: <?php echo $BID; ?>,
					action: "reset"
				}).done(function (data) {
					$('.loading').hide(function () {
						$(this).remove();
						$('<span class="message">' + data + '</span>').insertAfter('.reset').fadeOut(10000, function () {
							$(this).remove();
						});
					});
					$('.save').prop('disabled', false);
					$('.reset').prop('disabled', false);
					$(".block.nfs").removeClass("ui-selected").removeClass("nfs").addClass("free");
				});
			}
		});
	});
</script>
<style type="text/css">
    <?php
	$grid_background = "";
		if(file_exists(BASE_PATH . "/" . BANNER_DIR . "main$BID.png")) {
			$grid_background = 'background: url("../' . BANNER_DIR . 'main' . $BID . '.png") no-repeat center center;background-size:contain;';
	}
	?>
    .grid {
    <?php echo $grid_background; ?> z-index: 0;
        width: <?php echo $banner_data['G_WIDTH']*$banner_data['BLK_WIDTH']; ?>px;
        height: <?php echo $banner_data['G_HEIGHT']*$banner_data['BLK_HEIGHT']; ?>px;
    }

    .block_row {
        clear: both;
        display: block;
    }

    .block {
        white-space: nowrap;
        width: <?php echo $banner_data['BLK_WIDTH']; ?>px;
        height: <?php echo $banner_data['BLK_HEIGHT']; ?>px;
        float: left;
        opacity: 0.5;
        filter: alpha(opacity=50);
    }

    .sold {
        background: url("../images/sold_block.png") no-repeat;
    }

    .reserved {
        background: url("../images/reserved_block.png") no-repeat;
    }

    .nfs {
        background: url('data:image/png;base64,<?php echo $data; ?>') no-repeat;
        cursor: pointer;
    }

    .ordered {
        background: url("../images/ordered_block.png") no-repeat;
    }

    .ordered {
        background: url("../images/ordered_block.png") no-repeat;
    }

    .onorder {
        background: url("../images/not_for_sale_block.png") no-repeat;
    }

    .free {
        background: url("../images/block.png") no-repeat;
        cursor: pointer;
    }

    .grid .ui-selecting {
        background: #FECA40;
    }
</style>

<div class="outer_box">
    <p>
        Here you can mark blocks to be not for sale. You can drag to select an area. Click 'Save' when done.
    </p>
    (Note: If you have a background image, the image is blended in using the browser's built-in filter - your alpha channel is ignored on this page)
    <hr>
	<?php
	$sql = "Select * from banners ";
	$res = mysqli_query( $GLOBALS['connection'], $sql );
	?>
    <form name="bidselect" method="post" action="nfs.php">
        <label>
            Select grid:
            <select name="BID" onchange="mds_submit(this)">
                <option></option>
				<?php
				while ( $row = mysqli_fetch_array( $res ) ) {
					if ( ( $row['banner_id'] == $BID ) && ( $BID != 'all' ) ) {
						$sel = 'selected';
					} else {
						$sel = '';
					}
					echo '
                <option
                ' . $sel . ' value=' . $row['banner_id'] . '>' . $row['name'] . '</option>';
				}
				?>
            </select>
        </label>
    </form>
    <hr>
	<?php
	if ( $BID != '' ) {
	$sql    = "show columns from blocks ";
	$result = mysqli_query( $GLOBALS['connection'], $sql );
	while ( $row = mysqli_fetch_array( $result ) ) {
		if ( $row['Field'] == 'status' ) {
			if ( strpos( $row['Type'], 'nfs' ) == 0 ) {
				$sql = "ALTER TABLE `blocks` CHANGE `status` `status` SET( 'reserved', 'sold', 'free', 'ordered', 'nfs' ) NOT NULL ";
				mysqli_query( $GLOBALS['connection'], $sql ) or die ( "<p><b>CANNOT UPGRADE YOUR DATABASE!<br>Please run the follwoing query manually from PhpMyAdmin:</b><br>$sql<br>" );
			}
		}
	}
	$sql = "select block_id, status, user_id FROM blocks WHERE banner_id=" . intval( $BID );
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	while ( $row = mysqli_fetch_array( $result ) ) {
		$blocks[ $row["block_id"] ] = $row['status'];
	}
	?>
    <div class="container">
        <input class="save" type="submit" value='Save Not for Sale'/>
        <input class="reset" type="submit" value='Reset'/>
        <div class="grid">
			<?php
			$cell = "0";
			for ( $i = 0; $i < $banner_data['G_HEIGHT']; $i ++ ) {
				echo "<div class='block_row'>";
				for ( $j = 0; $j < $banner_data['G_WIDTH']; $j ++ ) {
					switch ( $blocks[ $cell ] ) {
						case 'sold':
							echo '<span class="block sold" data-block="' . $cell . '"></span>';
							break;
						case 'reserved':
							echo '<span class="block reserved" data-block="' . $cell . '"></span>';
							break;
						case 'nfs':
							echo '<span class="block nfs" data-block="' . $cell . '"></span>';
							break;
						case 'ordered':
							echo '<span class="block ordered" data-block="' . $cell . '"></span>';
							break;
						case 'onorder':
							echo '<span class="block onorder" data-block="' . $cell . '"></span>';
							break;
						case 'free':
						case '':
							echo '<span class="block free" data-block="' . $cell . '"></span>';
					}
					$cell ++;
				}
				echo '</div>
				';
			}
			?>
        </div>
        <input class="save" type="submit" value='Save Not for Sale'/>
    </div>
</div>
<?php } ?>

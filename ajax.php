<?php
/*
 * @package       mds
 * @copyright     (C) Copyright 2022 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @version       2022-01-30 17:07:25 EST
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

// Type of AJAX call can be POST or JSON input

require_once __DIR__ . "/include/init.php";

// Handle WP integration calls
if ( WP_ENABLED == "YES" && ! empty( WP_URL ) ) {
	header( 'Access-Control-Allow-Origin: ' . WP_URL );
	header( 'Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS' );
	header( 'Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description, X-Requested-With, X-CSRF-UAP-TOKEN' );
}

// Handle POST input
if ( isset( $_POST ) ) {

	// Get input
	$input = file_get_contents( 'php://input' );

	// Try to json_decode the input
	$data       = json_decode( $input );
	$data_array = array();

	if ( empty( $data ) ) {
		// Invalid JSON sent so try parsing it

		parse_str( $input, $data_array );
		if ( ! isset( $data_array['action'] ) ) {
			die;
		}
	}

	$action = isset( $data_array['action'] ) ? $data_array['action'] : ( isset( $data->action ) ? $data->action : null );

	// Handle core MDS AJAX calls
	if ( isset( $action ) ) {
		switch ( $action ) {
			case "show_grid":
				show_grid();
				die;
				break;
			case "show_stats":
				show_stats();
				die;
				break;
			case "ga":
				get_ad( $data_array );
				store_view( $data_array );
				die;
				break;
			case "click":
				store_click( $data_array );
				die;
				break;
			case "show_list":
				show_list();
				die;
				break;
			default:
				break;
		}

		// Handle WP integration calls
		if ( WP_ENABLED == "YES" && ! empty( WP_URL ) ) {
			if ( ! isset( $data->grid_id ) ) {
				die;
			}

			$_REQUEST['BID'] = $data->grid_id;

			switch ( $data->action ) {
				case "ajax_grid":
					$_REQUEST['type'] = 'grid';
					require_once( BASE_PATH . "/include/mds_ajax.php" );
					$mds_ajax = new Mds_Ajax();
					$mds_ajax->show( 'grid', $data->grid_id, 'grid' );
					break;
				case "ajax_stats":
					$_REQUEST['type'] = 'stats';
					require_once( BASE_PATH . "/include/mds_ajax.php" );
					$mds_ajax = new Mds_Ajax();
					$mds_ajax->show( 'stats', $data->grid_id, 'stats' );
					break;
				case "ajax_list":
					$_REQUEST['type'] = 'list';
					require_once( BASE_PATH . "/include/mds_ajax.php" );
					$mds_ajax = new Mds_Ajax();
					$mds_ajax->show( 'list', $data->grid_id, 'list' );
					break;
				case "users":
					require_once( BASE_PATH . "/users/index.php" );
					break;
				default:
					break;
			}
		}

		global $ajax_call;
		$ajax_call = true;
	}

	die;
}

function show_grid() {
	global $f2;

	$BID = $f2->bid();

	if ( ! is_numeric( $BID ) ) {
		die;
	}

	$banner_data = load_banner_constants( $BID );

	$BANNER_PATH = BASE_PATH . '/' . BANNER_DIR;

	$map_file = get_map_file_name( $BID );

	$ext = 'png';
	if ( OUTPUT_JPEG == 'Y' ) {
		$ext = "jpg";
	} else if ( OUTPUT_JPEG == 'N' ) {
		$ext = 'png';
	} else if ( OUTPUT_JPEG == 'GIF' ) {
		$ext = 'gif';
	}

	if ( ! file_exists( $BANNER_PATH . "main" . $BID . ".$ext" ) ) {
		process_image( $BID );
		publish_image( $BID );
		process_map( $BID, $map_file );
	}

	header( 'Expires: Sat, 28 Sept 2005 00:00:00 GMT' );

	include_once( $map_file );

	if ( file_exists( $BANNER_PATH . "main" . $BID . ".$ext" ) ) {
		?>
        <img id="theimage" src="<?php echo BASE_HTTP_PATH . BANNER_DIR; ?>main<?php echo $BID; ?>.<?php echo $ext; ?>?v=<?php echo filemtime($BANNER_PATH . "main" . $BID . ".$ext"); ?>" width="<?php echo $banner_data['G_WIDTH'] * $banner_data['BLK_WIDTH']; ?>" height="<?php echo $banner_data['G_HEIGHT'] * $banner_data['BLK_HEIGHT']; ?>" border="0" usemap="#main"/>
		<?php
	} else {
		echo "<b>The file: " . $BANNER_PATH . "main" . $BID . ".$ext" . " doesn't exist.</b><br>";
		echo "<b>Please process your pixels from the Admin section (Look under 'Pixel Admin')</b>";
	}

	?>
    <div class="tooltip-source">
        <img src="<?php echo $f2->value( BASE_HTTP_PATH ); ?>images/periods.gif" alt=""/>
    </div>
	<?php
}

function show_stats() {
	global $f2, $label;

	$BID = $f2->bid();

	$banner_data = load_banner_constants( $BID );

	$sql = "select count(*) AS COUNT FROM blocks where status='sold' and banner_id='$BID' ";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );
	$row  = mysqli_fetch_array( $result );

	if ( defined( 'STATS_DISPLAY_MODE' ) && STATS_DISPLAY_MODE == 'BLOCKS' ) {
		$sold = $row['COUNT'];
	} else {
		$sold = $row['COUNT'] * ( $banner_data['BLK_WIDTH'] * $banner_data['BLK_HEIGHT'] );
	}

	$sql = "select count(*) AS COUNT FROM blocks where status='nfs' and banner_id='$BID' ";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );
	$row = mysqli_fetch_array( $result );

	if ( defined( 'STATS_DISPLAY_MODE' ) && STATS_DISPLAY_MODE == 'BLOCKS' ) {
		$nfs = $row['COUNT'];
		$available = ( ( $banner_data['G_WIDTH'] * $banner_data['G_HEIGHT'] ) - $nfs ) - $sold;
	} else {
		$nfs = $row['COUNT'] * ( $banner_data['BLK_WIDTH'] * $banner_data['BLK_HEIGHT'] );
		$available = ( ( $banner_data['G_WIDTH'] * $banner_data['G_HEIGHT'] * ( $banner_data['BLK_WIDTH'] * $banner_data['BLK_HEIGHT'] ) ) - $nfs ) - $sold;
	}

	if ( $label['sold_stats'] == '' ) {
		$label['sold_stats'] = "Sold";
	}

	if ( $label['available_stats'] == '' ) {
		$label['available_stats'] = "Available";
	}

	?>
    <div class="status_body">
        <div class="status">
            <b><?php echo $label['sold_stats']; ?>:</b> <span class="status_text"><?php echo number_format( $sold ); ?></span><br/>
            <b><?php echo $label['available_stats']; ?>:</b> <span class="status_text"><?php echo number_format( $available ); ?></span><br/>
        </div>
    </div>
	<?php
}

function get_ad( $data ) {
	require_once( BASE_PATH . '/include/ads.inc.php' );

	global $prams;

	$prams = load_ad_values( $data['aid'] );

	if ( $prams !== false ) {
		echo assign_ad_template( $prams );
	}
}

function store_click( $data ) {
	if ( ADVANCED_CLICK_COUNT == 'YES' ) {
		require_once BASE_PATH . '/include/ads.inc.php';
		require_once BASE_PATH . '/include/dynamic_forms.php';

		$date = gmdate( 'Y' ) . "-" . gmdate( 'm' ) . "-" . gmdate( 'd' );
		$sql  = "UPDATE `clicks` SET `clicks` = `clicks` + 1 WHERE `banner_id`=" . intval( $data['bid'] ) . " AND `date`='$date' AND `block_id`=" . intval( $data['block_id'] );
		mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );
		$x = @mysqli_affected_rows( $GLOBALS['connection'] );

		$tag_to_field_id = get_tag_to_field_id( 1 );
		$field_id        = intval( $tag_to_field_id['URL']['field_id'] );
		$sql             = "SELECT `t1`.`{$field_id}`, t1.`user_id` FROM `ads` AS `t1` INNER JOIN `blocks` AS `t2` ON `t1`.`ad_id` = `t2`.`ad_id` WHERE `t2`.`block_id`=" . intval( $data['block_id'] ) . ";";
		$result = @mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );
		$row = @mysqli_fetch_array( $result );

		$sql = "UPDATE `users` SET `click_count` = `click_count` + 1 where `ID`=" . intval( $row['user_id'] );
		mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );

		if ( ! $x ) {
			$sql = "INSERT INTO `clicks` (`banner_id`, `date`, `clicks`, `block_id`, `user_id`) VALUES(" . intval( $data['bid'] ) . ", '$date', 1, " . intval( $data['block_id'] ) . ", " . intval( $row['user_id'] ) . ") ";
			@mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );
		}
	}

	$sql = "UPDATE `blocks` SET `click_count` = `click_count` + 1 where `block_id`=" . intval( $data['block_id'] ) . " AND banner_id=" . intval( $data['bid'] );
	mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );
}

function store_view( $data ) {
	if ( ADVANCED_VIEW_COUNT == 'YES' ) {
		require_once BASE_PATH . '/include/ads.inc.php';
		require_once BASE_PATH . '/include/dynamic_forms.php';

		$date = gmdate( 'Y' ) . "-" . gmdate( 'm' ) . "-" . gmdate( 'd' );
		$sql  = "UPDATE views set views = views + 1 where banner_id='" . intval( $data['bid'] ) . "' AND `date`='$date' AND `block_id`=" . intval( $data['block_id'] );
		mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );
		$x = @mysqli_affected_rows( $GLOBALS['connection'] );

		$tag_to_field_id = get_tag_to_field_id( 1 );
		$field_id        = intval( $tag_to_field_id['URL']['field_id'] );
		$sql             = "SELECT `t1`.`{$field_id}`, `t1`.`user_id` FROM `ads` AS `t1` INNER JOIN `blocks` AS `t2` ON `t1`.`ad_id` = `t2`.`ad_id` WHERE `t2`.`block_id`=" . intval( $data['block_id'] ) . ";";
		$result = @mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );
		$row = @mysqli_fetch_array( $result );

		$sql = "UPDATE `users` SET `view_count` = `view_count` + 1 where `ID`=" . intval( $row['user_id'] );
		mysqli_query( $GLOBALS['connection'], $sql ) or die( mds_sql_error( $sql ) );

		if ( ! $x ) {
			$sql = "INSERT into `views` (`banner_id`, `date`, `views`, `block_id`, `user_id`) VALUES(" . intval( $data['bid'] ) . ", '$date', 1, " . intval( $data['block_id'] ) . ", " . intval( $row['user_id'] ) . ")";
			@mysqli_query( $GLOBALS['connection'], $sql );
		}
	}

	$sql = "UPDATE `blocks` SET `view_count` = `view_count` + 1 where `block_id`=" . intval( $data['block_id'] ) . " AND `banner_id`=" . intval( $data['bid'] );
	mysqli_query( $GLOBALS['connection'], $sql );
}

function show_list() {
	global $label, $purifier;

	require_once BASE_PATH . '/include/ads.inc.php';
	require_once BASE_PATH . '/include/dynamic_forms.php';

	?>
    <div class="list">
        <div class="table-row header">
            <div class="list-heading"><?php echo $label['list_date_of_purchase']; ?></div>
            <div class="list-heading"><?php echo $label['list_name']; ?></div>
            <div class="list-heading"><?php echo $label['list_ads']; ?></div>
            <div class="list-heading"><?php echo $label['list_pixels']; ?></div>
        </div>
		<?php
		$sql = "SELECT * FROM banners ORDER BY banner_id";
		$banners = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) );
		while ( $banner = mysqli_fetch_array( $banners ) ) {
			?>
            <div class="table-row header">
                <div class="list-heading" style="width:100%;"><?php echo $purifier->purify( $banner['name'] ); ?></div>
            </div>
			<?php
            //TODO: add option to order by other columns
			$sql = "SELECT *, MAX(order_date) as max_date, sum(quantity) AS pixels FROM orders where status='completed' AND approved='Y' AND published='Y' AND banner_id='" . intval( $banner['banner_id'] ) . "' GROUP BY user_id, banner_id, order_id order by pixels desc ";
			$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) );
			while ( $row = mysqli_fetch_array( $result ) ) {
				$q = "SELECT Username FROM users WHERE ID=" . intval( $row['user_id'] );
				$q = mysqli_query( $GLOBALS['connection'], $q ) or die( mysqli_error( $GLOBALS['connection'] ) );
				$user = mysqli_fetch_row( $q );
				?>
                <div class="table-row">
                    <div class="list-cell">
						<?php echo $purifier->purify( get_formatted_date( get_local_datetime( $row['max_date'] ) ) ); ?>
                    </div>
                    <div class="list-cell">
						<?php
						echo $purifier->purify( $user['0'] ); ?>
                    </div>
                    <div class="list-cell">
						<?php
						$br  = "";
						$sql = "Select * FROM  `ads` as t1, `orders` AS t2 WHERE t1.ad_id=t2.ad_id AND t1.banner_id='" . intval( $banner['banner_id'] ) . "' and t1.order_id='" . intval( $row['order_id'] ) . "' AND t1.user_id='" . intval( $row['user_id'] ) . "' AND status='completed' AND approved='Y' ORDER BY `ad_date`";
						$m_result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) );
						global $prams;
						while ( $prams = mysqli_fetch_array( $m_result, MYSQLI_ASSOC ) ) {

							$blocks   = explode( ',', $prams['blocks'] );
							$block_id = $blocks[0];

							$ALT_TEXT = get_template_value( 'ALT_TEXT', 1 );
							$ALT_TEXT = str_replace( [ "'", '"' ], "", $ALT_TEXT );

							$data_values = array(
								'id'        => $prams['ad_id'],
								'block_id'  => $block_id,
								'banner_id' => $banner['banner_id'],
								'alt_text'  => $ALT_TEXT,
								'url'       => get_template_value( 'URL', 1 ),
							);

							echo $br . '<a target="_blank" data-data="' . htmlspecialchars( json_encode( $data_values, JSON_HEX_QUOT | JSON_HEX_APOS ), ENT_QUOTES, 'UTF-8' ) . '" data-alt-text="' . $ALT_TEXT . '" class="list-link" href="' . $data_values['url'] . '">' . get_template_value( 'ALT_TEXT', 1 ) . '</a>';
							$br = '<br>';
						}

						?>
                    </div>
                    <div class="list-cell">
						<?php echo $row['pixels']; ?>
                    </div>
                </div>
				<?php
			}
		}
		?>
        <div class="table-row header">
            <div class="list-heading"><?php echo $label['list_date_of_purchase']; ?></div>
            <div class="list-heading"><?php echo $label['list_name']; ?></div>
            <div class="list-heading"><?php echo $label['list_ads']; ?></div>
            <div class="list-heading"><?php echo $label['list_pixels']; ?></div>
        </div>
    </div>
	<?php
}
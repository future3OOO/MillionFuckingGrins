<?php
/**
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

require_once __DIR__ . "/../include/init.php";

require( 'admin_common.php' );

require( '../include/ads.inc.php' );

function validate_advertiser( $user_id ) {
	$sql = "UPDATE users set Validated='1' where ID=" . intval( $user_id );
	mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
}

if ( $_REQUEST['action'] == 'validate' ) {
	validate_advertiser( $_REQUEST['user_id'] );
}

function delete_advertiser( $user_id ) {

	$sql = "SELECT * FROM orders where status<> 'new' AND user_id=" . intval( $user_id );
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	//$row = mysqli_fetch_array($result);
	if ( mysqli_num_rows( $result ) > 0 ) {
		echo "<span style=\"color: red; \">Error: Cannot delete because this user has some orders. (<a href='customers.php?delete_anyway=1&user_id=" . $user_id . "'>Click here to delete anyway</a>)<br></span>";
	} else {
		$sql = "DELETE FROM users where ID=" . intval( $user_id );
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}
}

if ( $_REQUEST['action'] == 'delete' ) {
	delete_advertiser( $_REQUEST['user_id'] );
}

if ( $_REQUEST['delete_anyway'] != '' ) {

	$sql = "DELETE FROM orders where user_id=" . intval( $_REQUEST['user_id'] );
	mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );

	$sql = "DELETE FROM blocks where user_id=" . intval( $_REQUEST['user_id'] );
	mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );

	$sql = "DELETE FROM users where ID=" . intval( $_REQUEST['user_id'] );
	mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );

	// DELETE ADS
	$sql = "select * FROM ads where user_id='" . intval( $_REQUEST['user_id'] ) . "' ";
	$res2 = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
	while ( $row2 = mysqli_fetch_array( $res2 ) ) {

		delete_ads_files( $row2['ad_id'] );
		$sql = "DELETE from ads where ad_id='" . intval( $row2['ad_id'] ) . "' ";
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

	echo "<p>User deleted. Please remember to process the image if the user had some pixels. </p>";
}

if ( $_REQUEST['mass_del'] != '' ) {
	if ( sizeof( $_REQUEST['users'] ) > 0 ) {
		foreach ( $_REQUEST['users'] as $user_id ) {
			delete_advertiser( $user_id );
		}
	}
}

if ( $_REQUEST['mass_val'] != '' ) {
	if ( sizeof( $_REQUEST['users'] ) > 0 ) {
		foreach ( $_REQUEST['users'] as $user_id ) {
			validate_advertiser( $user_id );
		}
	}
}

$q_aday     = $_REQUEST['q_aday'];
$q_amon     = $_REQUEST['q_amon'];
$q_ayear    = $_REQUEST['q_ayear'];
$q_name     = $_REQUEST['q_name'];
$q_username = $_REQUEST['q_username'];
$q_resumes  = $_REQUEST['q_resumes'];
$q_news     = $_REQUEST['q_news'];
$q_email    = $_REQUEST['q_email'];
$q_company  = $_REQUEST['q_company'];
$search     = $_REQUEST['search'];
$q_string   = mysqli_real_escape_string( $GLOBALS['connection'], "&q_name=$q_name&q_username=$q_username&q_news=$q_news&q_resumes=$q_resumes&q_email=$q_email&q_aday=$q_aday&q_amon=$q_amon&q_ayear=$q_ayear&q_company=$q_company&search=$search" );
?>
    <p>

        <form style="margin: 0" action="customers.php?search=search" method="post">

            <center>
                <table border="0" cellpadding="2" cellspacing="0" style="border-collapse: collapse" id="AutoNumber2" width="100%">

                    <tr>
                        <td width="63" bgcolor="#EDF8FC" valign="top">
    <p align="right"><span style="font-size: x-small; font-family: Arial,serif; "><b>Name</b></span></p></td>
    <td width="286" bgcolor="#EDF8FC" valign="top">
      <span style="font-family: Arial; ">
      <input type="text" name="q_name" size="39" value="<?php echo $q_name; ?>"/></span></td>
    <td width="71" bgcolor="#EDF8FC" valign="top">
        <p align="right"><b><span style="font-family: Arial; font-size: x-small; ">Username</span></b></p></td>
    <td width="299" bgcolor="#EDF8FC" valign="top">
        <input type="text" name="q_username" size="28" value="<?php echo $q_username; ?>"/></td>
    </tr>
    <tr>
        <td width="63" bgcolor="#EDF8FC" valign="top">
            <p align="right"><b><span style="font-family: Arial; font-size: x-small; ">Signed Up After:</span></b></td>
        <td width="286" bgcolor="#EDF8FC" valign="top">
            <b>
                <span style="font-family: Arial; font-size: x-small; "></span></b><span style="font-size: x-small; font-family: Arial; "><b>
       </b></span>
			<?php

			if ( $q_aday == '' ) {

				// $q_aday = date("d");
				//   $q_amon = date("m");
				//   $q_ayear = date("Y");

			}

			?>
            <select name="q_aday">
                <option></option>
                <option <?php if ( $q_aday == '01' ) {
					echo ' selected ';
				} ?> >1
                </option>
                <option <?php if ( $q_aday == '02' ) {
					echo ' selected ';
				} ?> >2
                </option>
                <option <?php if ( $q_aday == '03' ) {
					echo ' selected ';
				} ?> >3
                </option>
                <option <?php if ( $q_aday == '04' ) {
					echo ' selected ';
				} ?> >4
                </option>
                <option <?php if ( $q_aday == '05' ) {
					echo ' selected ';
				} ?> >5
                </option>
                <option <?php if ( $q_aday == '06' ) {
					echo ' selected ';
				} ?> >6
                </option>
                <option <?php if ( $q_aday == '07' ) {
					echo ' selected ';
				} ?>>7
                </option>
                <option <?php if ( $q_aday == '08' ) {
					echo ' selected ';
				} ?>>8
                </option>
                <option <?php if ( $q_aday == '09' ) {
					echo ' selected ';
				} ?> >9
                </option>
                <option <?php if ( $q_aday == '25' ) {
					echo ' selected ';
				} ?> >25
                </option>
                <option <?php if ( $q_aday == '26' ) {
					echo ' selected ';
				} ?> >26
                </option>
                <option <?php if ( $q_aday == '10' ) {
					echo ' selected ';
				} ?> >10
                </option>
                <option <?php if ( $q_aday == '11' ) {
					echo ' selected ';
				} ?> > 11
                </option>
                <option <?php if ( $q_aday == '12' ) {
					echo ' selected ';
				} ?> >12
                </option>
                <option <?php if ( $q_aday == '13' ) {
					echo ' selected ';
				} ?> >13
                </option>
                <option <?php if ( $q_aday == '14' ) {
					echo ' selected ';
				} ?> >14
                </option>
                <option <?php if ( $q_aday == '15' ) {
					echo ' selected ';
				} ?> >15
                </option>
                <option <?php if ( $q_aday == '16' ) {
					echo ' selected ';
				} ?> >16
                </option>
                <option <?php if ( $q_aday == '17' ) {
					echo ' selected ';
				} ?> >17
                </option>
                <option <?php if ( $q_aday == '18' ) {
					echo ' selected ';
				} ?> >18
                </option>
                <option <?php if ( $q_aday == '19' ) {
					echo ' selected ';
				} ?> >19
                </option>
                <option <?php if ( $q_aday == '20' ) {
					echo ' selected ';
				} ?> >20
                </option>
                <option <?php if ( $q_aday == '21' ) {
					echo ' selected ';
				} ?> >21
                </option>
                <option <?php if ( $q_aday == '22' ) {
					echo ' selected ';
				} ?> >22
                </option>
                <option <?php if ( $q_aday == '23' ) {
					echo ' selected ';
				} ?> >23
                </option>
                <option <?php if ( $q_aday == '24' ) {
					echo ' selected ';
				} ?> >24
                </option>
                <option <?php if ( $q_aday == '27' ) {
					echo ' selected ';
				} ?> >27
                </option>
                <option <?php if ( $q_aday == '28' ) {
					echo ' selected ';
				} ?> >28
                </option>
                <option <?php if ( $q_aday == '29' ) {
					echo ' selected ';
				} ?> >29
                </option>
                <option <?php if ( $q_aday == '30' ) {
					echo ' selected ';
				} ?> >30
                </option>
                <option <?php if ( $q_aday == '31' ) {
					echo ' selected ';
				} ?> >31
                </option>
            </select>
            <select name="q_amon">
                <option></option>
                <option <?php if ( $q_amon == '01' ) {
					echo ' selected ';
				} ?> value="1">Jan
                </option>
                <option <?php if ( $q_amon == '02' ) {
					echo ' selected ';
				} ?> value="2">Feb
                </option>
                <option <?php if ( $q_amon == '03' ) {
					echo ' selected ';
				} ?> value="3">Mar
                </option>
                <option <?php if ( $q_amon == '04' ) {
					echo ' selected ';
				} ?> value="4">Apr
                </option>
                <option <?php if ( $q_amon == '05' ) {
					echo ' selected ';
				} ?> value="5">May
                </option>
                <option <?php if ( $q_amon == '06' ) {
					echo ' selected ';
				} ?> value="6">Jun
                </option>
                <option <?php if ( $q_amon == '07' ) {
					echo ' selected ';
				} ?> value="7">Jul
                </option>
                <option <?php if ( $q_amon == '08' ) {
					echo ' selected ';
				} ?> value="8">Aug
                </option>
                <option <?php if ( $q_amon == '09' ) {
					echo ' selected ';
				} ?> value="9">Sep
                </option>
                <option <?php if ( $q_amon == '10' ) {
					echo ' selected ';
				} ?> value="10">Oct
                </option>
                <option <?php if ( $q_amon == '11' ) {
					echo ' selected ';
				} ?> value="11">Nov
                </option>
                <option <?php if ( $q_amon == '12' ) {
					echo ' selected ';
				} ?> value="12">Dec
                </option>
            </select>
            <input type="text" name="q_ayear" size="4" value="<?php echo $q_ayear; ?>"/>

        </td>
        <td width="71" bgcolor="#EDF8FC" valign="top">
            <p align="right"><b><span style="font-family: Arial; font-size: x-small; ">Email</span></b></td>
        <td width="299" bgcolor="#EDF8FC" valign="top">

            <input type="text" name="q_email" size="28" value="<?php echo $q_email; ?>"/></td>
    </tr>

    <tr>
        <td width="731" bgcolor="#EDF8FC" colspan="4">
      <span style="font-family: Arial; "><b>
      <input type="submit" value="Find" name="B1" style="float: left"><?php if ( $_REQUEST['search'] == 'search' ) { ?>&nbsp; </b></span><b>[<span style="font-family: Arial; "><a href="<?php echo $_SERVER['PHP_SELF'] ?>">Start a New Search</a></span>]</b><?php } ?></td>
    </tr>
    </table>

    </center>

    </form>
    </p>
    <p>Listing advertisers. Click on a username to edit details / change password / change status<p>

	<?php
	$q_aday     = intval( $_REQUEST['q_aday'] );
	$q_amon     = intval( $_REQUEST['q_amon'] );
	$q_ayear    = intval( $_REQUEST['q_ayear'] );
	$q_name     = mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['q_name'] );
	$q_username = mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['q_username'] );
	$q_resumes  = mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['q_resumes'] );
	$q_news     = mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['q_news'] );
	$q_email    = mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['q_email'] );

	if ( $q_name != '' ) {
		$list = preg_split( "/[\s,]+/", $q_name );
		for ( $i = 1; $i < sizeof( $list ); $i ++ ) {
			$or1 .= " OR (`FirstName` like '%" . $list[ $i ] . "%')";
			$or2 .= " OR (`LastName` like '%" . $list[ $i ] . "%')";
		}
		$where_sql .= " AND (((`FirstName` like '%$list[0]%') $or1) OR ((`LastName` like '%$list[0]%') $or2))";
	}

	if ( $q_username != '' ) {
		$q_username = trim( $q_username );
		$list       = preg_split( "/[\s,]+/", $q_username );
		for ( $i = 1; $i < sizeof( $list ); $i ++ ) {
			$or .= " OR (`Username` like '%" . $list[ $i ] . "%')";
		}
		$where_sql .= " AND ((`Username` like '%$list[0]%') $or)";
	}

	if ( $q_email != '' ) {
		$q_email = trim( $q_email );
		$list    = preg_split( "/[\s,]+/", $q_email );
		for ( $i = 1; $i < sizeof( $list ); $i ++ ) {
			$or .= " OR (`Email` like '%" . mysqli_real_escape_string( $GLOBALS['connection'], $list[ $i ] ) . "%')";
			//$or2 .=" OR (`FirstName` like '%".$list[$i]."%')";
		}
		$where_sql .= " AND ((`Email` like '%" . mysqli_real_escape_string( $GLOBALS['connection'], $list[0] ) . "%') $or)";
		//$where_sql .= " AND ((`FirstName` like '%$list[0]%') $or2)";
	}

	if ( ( $q_aday != '' ) && ( $q_amon != '' ) && ( $q_ayear != '' ) ) {
		$q_ayear   = trim( $q_ayear );
		$q_date    = "$q_ayear-$q_amon-$q_aday";
		$where_sql .= " AND  '$q_date' <= `SignupDate` ";
	}

	if ( $q_news != '' ) {
		$where_sql .= " AND `Newsletter`='1' ";
	}

	$sql = "SELECT * FROM users WHERE 1=1 $where_sql ORDER BY Validated ASC, SignupDate DESC ";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );

	$count            = mysqli_num_rows( $result );
	$records_per_page = 40;

	if ( $count > $records_per_page ) {

		mysqli_data_seek( $result, $_REQUEST['offset'] );
	}
	// calculate number of pages & current page
	$pages    = ceil( $count / $records_per_page );
	$cur_page = $offset / $records_per_page;
	$cur_page ++;

	?>
    <form style="margin: 0px;" method="post" action="<?php echo $_SERVER['PHP_SELF'];
	echo "?offset=" . $_REQUEST['offset'] . $q_string; ?>" name="form1">
        <input type="hidden" name="offset" value="<?php echo $_REQUEST['offset']; ?>">
        <center><b><?php echo mysqli_num_rows( $result ); ?> Advertiser's Accounts Returned (<?php echo $pages; ?> pages) </b></center>
		<?php
		if ( $count > $records_per_page ) {
			// calculate number of pages & current page

			echo "<center>";
			$label["navigation_page"] = str_replace( "%CUR_PAGE%", $cur_page, $label["navigation_page"] );
			$label["navigation_page"] = str_replace( "%PAGES%", $pages, $label["navigation_page"] );
			//	echo "<span > ".$label["navigation_page"]."</span> ";
			$nav   = nav_pages_struct( $q_string, $count, $records_per_page );
			$LINKS = 10;
			render_nav_pages( $nav, $LINKS, $q_string );
			echo "</center>";
		}
		?>

        <table width="100%" cellSpacing="1" cellPadding="3" align="center" bgColor="#d9d9d9" border="0">
            <tr>
                <td colspan="12">With selected: <input type="submit" value='Delete' name='mass_del'> | <input type="submit" value='Validate' name='mass_val'></td>
            </tr>
            <tr>
                <td><b><span style="font-family: Arial; font-size: x-small; "><input type="checkbox" onClick="checkBoxes('users');"></td>
                <td><b><span style="font-family: Arial; font-size: x-small; ">Name</span></b></td>
                <td><b><span style="font-family: Arial; font-size: x-small; ">Username</span></b></td>
                <td><b><span style="font-family: Arial; font-size: x-small; ">Email</span></b></td>
                <td><b><span style="font-family: Arial; font-size: x-small; ">Company</span></b></td>
                <td><b><span style="font-family: Arial; font-size: x-small; ">Signup Date</span></b></td>
                <td><b><span style="font-family: Arial; font-size: x-small; ">Validated?</span></b></td>
                <td><b><span style="font-family: Arial; font-size: x-small; ">I.P</span></b></td>
                <td><b><span style="font-family: Arial; font-size: x-small; ">Orders</span></b></td>
                <td><b><span style="font-family: Arial; font-size: x-small; ">Pixels</span></b></td>
                <td><b><span style="font-family: Arial; font-size: x-small; ">Clicks</span></b></td>
                <td><b><span style="font-family: Arial; font-size: x-small; ">Action</span></b></td>
            </tr>
			<?php

			$i = 0;
			while ( ( $row = mysqli_fetch_array( $result, MYSQLI_ASSOC ) ) && ( $i < $records_per_page ) ) {
				$i ++;

				$sql = "SELECT SUM(quantity) as Pixels FROM orders where (status='completed' OR status='confirmed' OR status='pending') AND user_id=" . intval( $row['ID'] );
				$result2 = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
				$order_row = mysqli_fetch_array( $result2 );

				$sql = "SELECT * FROM orders where user_id='" . intval( $row['ID'] ) . "' AND status <> 'new' ";
				$result3 = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
				//$row = mysqli_fetch_array($result);

				?>
                <tr onmouseover="old_bg=this.getAttribute('bgcolor');this.setAttribute('bgcolor', '#FBFDDB', 0);" onmouseout="this.setAttribute('bgcolor', old_bg, 0);" bgColor="#ffffff">
                    <td><input type="checkbox" name="users[]" value="<?php echo $row['ID']; ?>"></td>
                    <td><span style="font-family: Arial; font-size: x-small; "><?php echo $row['FirstName'] . " " . $row['LastName']; ?></span></td>
                    <td><span style="font-family: Arial; font-size: x-small; "><a href="edit.php?user_id=<?php echo $row['ID']; ?>" name="Edit"><?php echo $row['Username']; ?></a></span></td>
                    <td><span style="font-family: Arial; font-size: x-small; "><?php echo $row['Email']; ?></span></td>
                    <td><span style="font-family: Arial; font-size: xx-small; "><?php echo $row['CompName']; ?></span></td>
                    <td><span style="font-family: Arial; font-size: xx-small; "><?php echo get_local_time( $row['SignupDate'] ); ?></span></td>
                    <td><span style="font-family: Arial; font-size: x-small; "><?php if ( $row['Validated'] == 1 ) {
								echo "Yes";
							} else {
								echo "No";
							} ?><?php if ( $row['Rank'] == 2 ) {
								echo "  <b>Privileged</b>";
							} ?></span></td>
                    <td><span style="font-family: Arial; font-size: xx-small; "><?php echo $row['IP']; ?></span></td>
                    <td><span style="font-family: Arial; font-size: xx-small; "><?php echo mysqli_num_rows( $result3 ); ?></span></td>
                    <td><span style="font-family: Arial; font-size: xx-small; "><?php echo $order_row['Pixels']; ?></span></td>
                    <td><span style="font-family: Arial; font-size: xx-small; "><?php echo $row['click_count']; ?></span></td>
                    <td><span style="font-family: Arial; font-size: xx-small; ">
	<?php if ( $row['Validated'] == 0 ) { ?>
        <input style="font-size: 9px;" type="button" value="Validate" onclick="if ( !confirmLink(this, 'Validate account?')) return false;" data-link="customers.php?action=validate&user_id=<?php echo $row['ID'] . $q_string; ?>"><?php } ?> <input style="font-size: 9px;" type="button" value="Delete" onclick="if ( !confirmLink(this, 'Delete account?')) return false;" data-link="customers.php?action=delete&user_id=<?php echo $row['ID'] . $q_string; ?>"></span>
                    </td>
                </tr>
				<?php
			}
			?>
        </table>
    </form>
<?php
if ( $count > $records_per_page ) {
	echo "<center>";

	$nav   = nav_pages_struct( $q_string, $count, $records_per_page );
	$LINKS = 10;
	render_nav_pages( $nav, $LINKS, $q_string );
	echo "</center>";
}
?>
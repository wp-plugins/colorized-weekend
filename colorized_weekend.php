<?php
/*
Plugin Name:  Colorized Weekend Widget
Plugin URI: http://www.vjcatkick.com/?page_id=10750
Description: Colorize date stamp
Version: 0.0.3
Author: V.J.Catkick
Author URI: http://www.vjcatkick.com/
*/

/*
License: GPL
Compatibility: WordPress 2.6 with Widget-plugin.

Installation:
Place the widget_single_photo folder in your /wp-content/plugins/ directory
and activate through the administration panel, and then go to the widget panel and
drag it to where you would like to have it!
*/

/*  Copyright V.J.Catkick - http://www.vjcatkick.com/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


/* Changelog
* Jun 27 2009 - v0.0.1
- Initial release
* Jun 29 2009 - v0.0.2
- svn version
* Nov 04 2009 - v0.0.3
- bug fix
*/

function the_time_specific_holiday( $is_holidays_apend_str ) {
	$retstr = '';
	$fixed_holiday_table = array( 1=>'元旦', 42=>'建国記念の日', 119=>'昭和の日', 123=>'憲法記念日', 124=>'みどりの日', 125=>'こどもの日', 307=>'文化の日', 327=>'勤労感謝の日', 357=>'天皇誕生日' );
	$day_num = get_the_time( 'z' ) + 1;
	$week_num = get_the_time( 'w' ) - 1;

	$result = false;
	foreach( $fixed_holiday_table as $key => $value ) { 
		if( ( $key != 123 && $key != 124 ) && ( $day_num - 1 ==  $key ) && $week_num === 0 ) {
			$retstr = ' ';
			if( $is_holidays_apend_str ) $retstr .= '振替休日';
			$result = true;
			break;
		} /* if */
	} /* foreach */
	if( $result ) return $retstr;

	$result = $fixed_holiday_table[ $day_num ];
	if( $result ) {
		$retstr = ' ';
		if( $is_holidays_apend_str ) $retstr .= $result;
		return $retstr;
	} /* if */

	$retstr = '';
	$happy_monday = array( 1=>'成人の日', 7=>'海の日', 9=>'敬老の日', 10=>'体育の日' );
	foreach( $happy_monday as $key => $value ) { 
		if( get_the_time( 'w' ) == 1 && $key == get_the_time( 'n' ) ) {
			$d = get_the_time( 'j' );
			if( ( $key == 1 || $key == 10 ) && $d >= 8 && $d <= 14 ) {
				$retstr = ' ';
				if( $is_holidays_apend_str ) $retstr .= $value;
				break;
			} /* if */
			if( ( $key == 7 || $key == 9 ) && $d >= 15 && $d <= 21 ) {
				$retstr = ' ';
				if( $is_holidays_apend_str ) $retstr .= $value;
				break;
			} /* if */
		} /* if */
	} /* foreach */
	if( strlen( $retstr ) > 0 ) return $retstr;


	return '';
} /* the_time_specific_holiday() */

function the_time_colored( $date_format_str ) {
	$options = get_option('widget_the_time_colored');
	$widget_the_time_colored_sunday = (boolean)$options['widget_the_time_colored_sunday'];
	$widget_the_time_colored_code_sunday = $options['widget_the_time_colored_code_sunday'];
	$widget_the_time_colored_saturday = (boolean)$options['widget_the_time_colored_saturday'];
	$widget_the_time_colored_code_saturday = $options['widget_the_time_colored_code_saturday'];
	$widget_the_time_colored_holidays = (boolean)$options['widget_the_time_colored_holidays'];
	$widget_the_time_colored_code_holidays = $options['widget_the_time_colored_code_holidays'];
	$widget_the_time_colored_holidays_is_apend_str = (boolean)$options['widget_the_time_colored_holidays_is_apend_str'];
	$src_date_text = get_the_time( $date_format_str );

	$week_class_str = array( 'sun', 'mon', 'tue', 'wed', 'thur', 'fri', 'sat' );

	// <span style="">00/00/00</span>
	$entry_week_str = get_the_time( 'l' );
	$local_output = '<span ';

	$specific_holiday_str = the_time_specific_holiday( $widget_the_time_colored_holidays_is_apend_str );
	if( strlen( $specific_holiday_str ) > 0 && $widget_the_time_colored_holidays ) {
		$local_output .= 'class="holiday" style="color: ' . $widget_the_time_colored_code_holidays . '"';
	}else{
		$entry_week_num = get_the_time( 'w' );
		$local_output .= 'class="' . $week_class_str[ $entry_week_num ] . '" style="';
		if( $entry_week_num == 0 && $widget_the_time_colored_sunday ) $local_output .= 'color: ' . $widget_the_time_colored_code_sunday . ';';
		else if( $entry_week_num == 6 && $widget_the_time_colored_saturday ) $local_output .= 'color: ' . $widget_the_time_colored_code_saturday . ';';
		$local_output .= '"';
	} /* if else */
	$local_output .= '>';
		$local_output .= $src_date_text . $specific_holiday_str;
	$local_output .= '</span>';

	echo $local_output;

	return false;
} /* the_time_colored() */


function the_time_colored_options_page() {
	$output = '';

	$options = $newoptions = get_option('widget_the_time_colored');
	if ( $_POST["widget_the_time_colored_submit"] ) {
		$newoptions['widget_the_time_colored_sunday'] = (boolean)$_POST["widget_the_time_colored_sunday"];
		$newoptions['widget_the_time_colored_code_sunday'] = $_POST["widget_the_time_colored_code_sunday"];
		$newoptions['widget_the_time_colored_saturday'] = (boolean)$_POST["widget_the_time_colored_saturday"];
		$newoptions['widget_the_time_colored_code_saturday'] = $_POST["widget_the_time_colored_code_saturday"];
		$newoptions['widget_the_time_colored_holidays'] = (boolean)$_POST["widget_the_time_colored_holidays"];
		$newoptions['widget_the_time_colored_code_holidays'] = $_POST["widget_the_time_colored_code_holidays"];
		$newoptions['widget_the_time_colored_holidays_is_apend_str'] = (boolean)$_POST["widget_the_time_colored_holidays_is_apend_str"];
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_the_time_colored', $options);
	}

	$widget_the_time_colored_sunday = (boolean)$options['widget_the_time_colored_sunday'];
	$widget_the_time_colored_code_sunday = $options['widget_the_time_colored_code_sunday'];
	$widget_the_time_colored_saturday = (boolean)$options['widget_the_time_colored_saturday'];
	$widget_the_time_colored_code_saturday = $options['widget_the_time_colored_code_saturday'];
	$widget_the_time_colored_holidays = (boolean)$options['widget_the_time_colored_holidays'];
	$widget_the_time_colored_code_holidays = $options['widget_the_time_colored_code_holidays'];
	$widget_the_time_colored_holidays_is_apend_str = (boolean)$options['widget_the_time_colored_holidays_is_apend_str'];


	if( !$widget_the_time_colored_code_sunday ) $widget_the_time_colored_code_sunday = 'red';
	if( !$widget_the_time_colored_code_saturday ) $widget_the_time_colored_code_saturday = 'blue';
	if( !$widget_the_time_colored_code_holidays ) $widget_the_time_colored_code_holidays = 'red';


	$output .= '<h2>Colorized Weekend</h2>';
	$output .= '<form action="" method="post" id="widget_the_time_colored_form" style="margin: auto; width: 600px; ">';

	$output .= 'Colorize switches:<br /> ';

	$output .= '<input id="widget_the_time_colored_sunday" name="widget_the_time_colored_sunday" type="checkbox" value="1" ';
	if( $widget_the_time_colored_sunday ) $output .= 'checked';
	$output .= '/> Sunday<br />';

	$output .= '&nbsp;&nbsp;Color code for Sunday: ';
	$output .= '<input style="width: 100px;" id="widget_the_time_colored_code_sunday" name="widget_the_time_colored_code_sunday" type="text"	value="'.$widget_the_time_colored_code_sunday.'" /><br />';

	$output .= '<input id="widget_the_time_colored_saturday" name="widget_the_time_colored_saturday" type="checkbox" value="1" ';
	if( $widget_the_time_colored_saturday ) $output .= 'checked';
	$output .= '/> Saturday<br />';

	$output .= '&nbsp;&nbsp;Color code for Saturday: ';
	$output .= '<input style="width: 100px;" id="widget_the_time_colored_code_saturday" name="widget_the_time_colored_code_saturday" type="text"	value="'.$widget_the_time_colored_code_saturday.'" /><br />';


	$output .= '<br />';

	$output .= '<input id="widget_the_time_colored_holidays" name="widget_the_time_colored_holidays" type="checkbox" value="1" ';
	if( $widget_the_time_colored_holidays ) $output .= 'checked';
	$output .= '/> Japanese Holidays<br />';

	$output .= '&nbsp;&nbsp;Color code for holidays: ';
	$output .= '<input style="width: 100px;" id="widget_the_time_colored_code_holidays" name="widget_the_time_colored_code_holidays" type="text"	value="'.$widget_the_time_colored_code_holidays.'" /><br />';

	$output .= '<input id="widget_the_time_colored_holidays_is_apend_str" name="widget_the_time_colored_holidays_is_apend_str" type="checkbox" value="1" ';
	if( $widget_the_time_colored_holidays_is_apend_str ) $output .= 'checked';
	$output .= '/> Append holiday name string<br />';


	$output .= '<p class="submit"><input type="submit" name="widget_the_time_colored_submit" value="'. 'Update options &raquo;' .'" /></p>';

$output .= '<span style="color:#888;">Note:<br />Currently, colorized holiday function only supports Japanese holidays. This function will be separated next version for other countries.</span>';


	$output .= '</form>';

	echo $output;
} /* the_time_colored_options_page() */

add_action('admin_menu', 'the_time_colored_options');

function the_time_colored_options() {
	add_options_page('Colorized Weekend', 'Colorized Weekend', 8, 'the_time_colored_options', 'the_time_colored_options_page');
} /* the_time_colored_options() */

?>
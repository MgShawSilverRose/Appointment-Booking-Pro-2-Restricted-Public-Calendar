<?php
/*
 ****************************************************************
 Copyright (C) 2008-2012 Soft Ventures, Inc. All rights reserved.
 ****************************************************************
 * @package	Appointment Booking Pro - ABPro
 * @copyright	Copyright (C) 2008-2012 Soft Ventures, Inc. All rights reserved.
 * @license	GNU/GPL, see http://www.gnu.org/licenses/gpl-2.0.html
 *
 * ABPro is distributed WITHOUT ANY WARRANTY, or implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header must not be removed. Additional contributions/changes
 * may be added to this header as long as no information is deleted.
 *
 ************************************************************
 The latest version of ABPro is available to subscribers at:
 http://www.appointmentbookingpro.com/
 ************************************************************
 * Originally derived from calview_ajax.php 
 * Public Front Desk Extension (c) SilverRose Systems Ltd. 2013-
 * 
 */

 
// ajax routine to build a month view
defined( '_JEXEC' ) or die( 'Restricted access' );
	header('Content-Type: text/xml'); 
	header("Cache-Control: no-cache, must-revalidate");
	//A date in the past
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

	include( JPATH_SITE."/components/com_rsappt_pro2/svpubliccalendar.php" );

	// get config stuff
	$database = &JFactory::getDBO(); 
	$sql = 'SELECT * FROM #__sv_apptpro2_config';
	$database->setQuery($sql);
	$apptpro_config = $database -> loadObject();
	if ($database -> getErrorNum()) {
		echo "DB Err: ". $database -> stderr();
		return false;
	}


$cal = new SVPublicCalendar;
$d = getdate(time());
$view="month";
$day=JRequest::getVar('day');
$month=JRequest::getVar('month');
$year=JRequest::getVar('year');
$resource_filter="";
$category_filter="";
$user=JRequest::getVar('user');
$status="";
$weekoffset=JRequest::getVar('weekoffset');
$user_search="";
$mobile= false;
$show_seat_totals=JRequest::getVar('showSeatTotals');
$Itemid = JRequest::getVar('Itemid');
$Menuid = JRequest::getVar('Menuid');

if ($month == ""){
	$month = $d["mon"];
}

if ($year == ""){
	$year = $d["year"];
}

if ($weekoffset == ""){
	$weekoffset = 0;
}

$mainframe = JFactory::getApplication();
$mainframe->setUserState('front_desk.front_desk_view', $view);
$mainframe->setUserState('front_desk.front_desk_resource_filter', $resource_filter);
$mainframe->setUserState('front_desk.front_desk_category_filter', $category_filter);
$mainframe->setUserState('front_desk.front_desk_status_filter', $status);
$mainframe->setUserState('front_desk.front_desk_user_search', $user_search);
$mainframe->setUserState('front_desk.day', $day);
$mainframe->setUserState('front_desk.month', $month);
$mainframe->setUserState('front_desk.year', $year);
$mainframe->setUserState('front_desk_cur_week_offset', $weekoffset);
$mainframe->setUserState('front_desk_cur_day', $day);
$mainframe->setUserState('front_desk_cur_month', $month);
$mainframe->setUserState('front_desk_cur_year', $year);

$cal->setWeekStartDay(intval($apptpro_config->popup_week_start_day));
if($mobile){
	$cal->setIsMobile(true);
}

$cal->setShowSeatTotals($show_seat_totals);

$cal->setItemid($Itemid);
$cal->setMenuid($Menuid);

switch ($view){
	case "month":
		$cal->setResourceFilter($resource_filter);
		$cal->setCategoryFilter($category_filter);
		$cal->setResAdmin($user);
		$cal->setReqStatus($status);
		echo $cal->getMonthView($month, $year);
		break;
}
exit;

?>
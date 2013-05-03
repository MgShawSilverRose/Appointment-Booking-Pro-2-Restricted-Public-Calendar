<?php
/*
 ****************************************************************
 Copyright (C) 2008-2012 Soft Ventures, Inc. All rights reserved.
 ****************************************************************
 * @package	Appointment Booking Pro - ABPro
 * @copyright	Copyright (C) 2008-2012 Soft Ventures, Inc. All rights reserved.
 * @license	GNU/GPL, see http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
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
 */
  
defined( '_JEXEC' ) or die( 'Restricted access' );

//	include( JPATH_SITE."/administrator/components/com_rsappt_pro2/sendmail.php" );




function getCurrentSeatCount($startdate, $starttime, $endtime, $resource, $exclude_request=-1){

	$database = &JFactory::getDBO();
	$sql = "SELECT Sum(booked_seats) FROM #__sv_apptpro2_requests ".
		" WHERE ".
		" id_requests != ".$exclude_request." AND ".
		" startdate = '".$startdate."' AND ".
		" starttime = '".$starttime."' AND ".
		" endtime = '".$endtime."' AND ".
		" resource = ".$resource." AND ".
		"(request_status = 'accepted' or request_status = 'pending') AND ".
		" booked_seats > 0;";
	//echo $sql;
	$database->setQuery( $sql );
	$currentcount = $database->loadResult();
	if ($database -> getErrorNum()) {
		echo $database -> stderr();
		return false;
	}
	//echo $currentcount;
	return $currentcount;
}

function getCurrentTotalSeatCount($startdate, $starttime, $endtime, $resource, $exclude_request=-1){
	// count across ALL resources for this timeslot
	$database = &JFactory::getDBO();
	$sql = "SELECT Sum(booked_seats) FROM #__sv_apptpro2_requests ".
		" WHERE ".
		" id_requests != ".$exclude_request." AND ".
		" startdate = '".$startdate."' AND ".
		" starttime = '".$starttime."' AND ".
		" endtime = '".$endtime."' AND ".
//		" resource = ".$resource." AND ".
		"(request_status = 'accepted' or request_status = 'pending') AND ".
		" booked_seats > 0;";
	//echo $sql;
	$database->setQuery( $sql );
	$currentcount = $database->loadResult();
	if ($database -> getErrorNum()) {
		echo $database -> stderr();
		return false;
	}
	//echo $currentcount;
	return $currentcount;
}

function logIt($err, $err_object = "", $err_severity = "", $sql = ""){
	$database = &JFactory::getDBO();
	$errsql = "insert into #__sv_apptpro2_errorlog (description, err_object, err_severity, sql_data) ".
		" values('".$database->getEscaped(substr($err,0))."', '".$err_object."', '".$err_severity."', '".$database->getEscaped(substr($sql,0))."')";
	$database->setQuery($errsql);
	$database->query();
	if ($database -> getErrorNum()) {
		echo $database -> stderr();
		exit;
	}

}

function tz_offset_to_string($tzoffset){
	// converts 
	// "0"	-> "+00:00"
	// "2"	-> "+02:00"
	// "2.5"	-> "+02:30"
	// 10	-> "+10:00"
	// 10.5 -> "+10:30"
	// -2	-> "-02:00"
	// -2.5	-> "-02:30"
	// -10	-> "-10:00"
	// -10.5-> "-10:30"
	
	$valOffset = strval($tzoffset);
	if($valOffset == 0){
		return "+00:00";
	}
	$offset_hr_min = explode(".", $tzoffset);
	if($offset_hr_min[1] == "5"){
		$offset_min = ":30";
	}else{
		$offset_min = ":00";
	}	

	if($valOffset > 0){
		// + offset
		if(strval($offset_hr_min[0]) < 10){
			$offset_hour = "+0".$offset_hr_min[0];
		} else {
			$offset_hour = "+".$offset_hr_min[0];
		}
	}
	if($valOffset < 0){	
		// - offset
		if(abs(strval($offset_hr_min[0])) < 10){
			$offset_hour = substr($offset_hr_min[0],0,1)."0".substr($offset_hr_min[0],1);			
		} else {
			$offset_hour = $offset_hr_min[0];
		}
	}	
	return $offset_hour.$offset_min;
}	

function translated_status($in){
	switch($in){
		case 'new': {
			return JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_NEW');
			break;
		}
		case 'accepted': {
			return JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_ACCEPTED');
			break;
		}
		case 'pending': {
			return JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_PENDING');
			break;
		}
		case 'declined': {
			return JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_DECLINED');
			break;
		}
		case 'canceled': {
			return JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_CANCELED');
			break;
		}
		case 'no_show': {
			return JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_NO_SHOW');
			break;
		}
		case 'attended': {
			return JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_ATTENDED');
			break;
		}
		case 'deleted': {
			return JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_DELETED');
			break;
		}
		case 'completed': {
			return JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_COMPLETED');
			break;
		}
		case 'paid': {
			return JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_PAID');
			break;
		}
		case 'timeout': {
			return JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_PAYPAL_TIMEOUT');
			break;
		}
		case 'na': {
			return JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_NA');
			break;
		}
		
	}
}

function setSessionStuff($screen){

	$session = &JFactory::getSession();

	switch($screen)	{

		case "request":
			$session->set("current_tab", 0);
			$session->set("req_filter_order", JRequest::getVar('req_filter_order', 'startdate'));
			$session->set("req_filter_order_Dir", JRequest::getVar('req_filter_order_Dir', 'asc'));
			$session->set("status_filter", JRequest::getVar('status_filter', ''));
			$session->set("request_resourceFilter", JRequest::getVar('request_resourceFilter', ''));
			$session->set("startdateFilter", JRequest::getVar('startdateFilter', ''));
			$session->set("enddateFilter", JRequest::getVar('enddateFilter', ''));
			$session->set("categoryFilter", JRequest::getVar('categoryFilter', ''));

			$session->set("filter_order", JRequest::getVar('filter_order', 'startdate'));
			$session->set("filter_order_Dir", JRequest::getVar('filter_order_Dir', 'asc'));
			break;

		case "resource":
			$session->set("current_tab", JRequest::getVar('resources_tab'));
			$session->set("res_filter_order", JRequest::getVar('res_filter_order', 'name'));
			$session->set("res_filter_order_Dir", JRequest::getVar('res_filter_order_Dir', 'asc'));
			break;
	
		case "service":
			$session->set("current_tab", JRequest::getVar('services_tab'));
			$session->set("srv_filter_order", JRequest::getVar('srv_filter_order', 'name'));
			$session->set("srv_filter_order_Dir", JRequest::getVar('srv_filter_order_Dir', 'asc'));
			$session->set("resource_id_Filter", JRequest::getVar( 'resource_id_Filter' ));
			break;

		case "timeslot":
			$session->set("current_tab", JRequest::getVar('timeslots_tab'));
			$session->set("ts_filter_order", JRequest::getVar('ts_filter_order', 'name'));
			$session->set("ts_filter_order_Dir", JRequest::getVar('ts_filter_order_Dir', 'asc'));
			$session->set("resource_id_FilterTS", JRequest::getVar( 'resource_id_FilterTS' ));
			$session->set("day_numberFilter", JRequest::getVar( 'day_numberFilter' ));
			break;

		case "bookoff":
			$session->set("current_tab", JRequest::getVar('bookoffs_tab'));
			$session->set("bo_filter_order", JRequest::getVar('bo_filter_order', 'name'));
			$session->set("bo_filter_order_Dir", JRequest::getVar('bo_filter_order_Dir', 'asc'));
			$session->set("resource_id_FilterBO", JRequest::getVar( 'resource_id_FilterBO' ));
			break;

		case "paypal":
			$session->set("current_tab", JRequest::getVar('paypal_tab'));
			$session->set("pp_filter_order", JRequest::getVar('pp_filter_order', 'stamp'));
			$session->set("pp_filter_order_Dir", JRequest::getVar('pp_filter_order_Dir', 'desc'));
			break;

		case "coupons":
			$session->set("current_tab", JRequest::getVar('coupons_tab'));
			$session->set("coup_filter_order", JRequest::getVar('coup_filter_order', 'stamp'));
			$session->set("coup_filter_order_Dir", JRequest::getVar('coup_filter_order_Dir', 'desc'));
			break;

		case "front_desk":
			$session->set("front_desk_view", JRequest::getVar('view'));
			$session->set("front_desk_resource_filter", JRequest::getVar('resource_filter'));
			$session->set("front_desk_status_filter", JRequest::getVar('status_filter'));
			$session->set("front_desk_cur_month", JRequest::getVar('cur_month', ''));
			$session->set("front_desk_cur_year", JRequest::getVar('cur_year', ''));
			$session->set("front_desk_cur_week_offset", JRequest::getVar('cur_week_offset', ''));
			$session->set("front_desk_cur_day", JRequest::getVar('cur_day', ''));
			$session->set("front_desk_user_search", JRequest::getVar('user_search', ''));
			break;
                //************************************************************** 
                // SilverRose - MgS April 6 2013
                // 
                // Not Entirely Sure This Is Actually Necessary Consider It A PlaceHolder For Now 
                //
                case "public_front_desk":
                        $session->set("front_desk_view", JRequest::getVar('view'));
			$session->set("front_desk_resource_filter", JRequest::getVar('resource_filter'));
			$session->set("front_desk_status_filter", JRequest::getVar('status_filter'));
			$session->set("front_desk_cur_month", JRequest::getVar('cur_month', ''));
			$session->set("front_desk_cur_year", JRequest::getVar('cur_year', ''));
			$session->set("front_desk_cur_week_offset", JRequest::getVar('cur_week_offset', ''));
			$session->set("front_desk_cur_day", JRequest::getVar('cur_day', ''));
			$session->set("front_desk_user_search", JRequest::getVar('user_search', ''));
			break;
	}		
}

?>
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
 * 
 * Created from svcalendar.php
 * 
 * Original Code (c) Soft Ventures Inc.
 * Public Front Desk Extension (c) SilverRose Systems Ltd. 2013 - 
 */



defined( '_JEXEC' ) or die( 'Restricted access' );
include_once( JPATH_SITE."/components/com_rsappt_pro2/functions2.php" );


class SVPublicCalendar
{

	function SVPublicCalendar()
	{
	}
	
	var $Itemid = null;
	var $resAdmin = "";
	var $reqStatus = "";
	var $resource_filter = "";
	var $category_filter = "";
//	var $week_view_header_date_format = "F d, Y";
	var $week_view_header_date_format = "%B %d, %Y";
	var $user_search_filter = "";
	var $startDay = 0;
	var $isMobile = false;
	var $showSeatTotals = false;
	var $fd_allow_show_seats = true;
	var $fd_res_admin_only = true;
	var $fd_read_only = false;
	var $fd_detail_popup = false;
	var $fd_show_contact_info = true;
	var $fd_allow_manifest = true;
	var $fd_display = "Customer";
	var $fd_tooltip = "Resource";
        var $fd_public_view = true;
        var $fd_public_show_all_bookings = false;
        var $fd_public_show_global = false;
        var $fd_public_global_category = 0;
	var $debug_booking;
        
	function setItemid($id){
		$this->Itemid = $id;	
	}

	function setMenuid($id){
		
		// This is the sort of thing that drives me #~!@!#@% crazy about Joomla 1.7 SEO (1.5 does not display this bizare mis-behavior).
		// With SEO disabled, the active menu is set and the parameters can be read.
		// With SEO enabled, the active menu is NOT set yet, it is the last menu item not the Front Desk.
		// The work around is to grab the active menu from the view (before this code is called) and pass
		// the id into here. AArrhhgg
		
		$menu = &JSite::getMenu(); 
		$active = $menu->getItem($id);
		//$active = $menu->getActive(); 
                $this->fd_allow_show_seats = false;
                $this->fd_res_admin_only = false;
                $this->fd_read_only = true;
                $this->fd_detail_popup = false;  
                $this->fd_show_contact_info = false;
                $this->fd_allow_manifest = false;
                $this->fd_display = "Resource";
                $this->fd_tooltip = "Resource";
                // 
                // Removed parameter gets for the attributes that we ignore.
                // 
                // 
                // Behaviour control attributes for public
                // front desk
                
                if($active->params->get('fd_public_view') == 'No') {
                    $this->fd_public_view = true;
                }
                
                if($active->params->get('fd_public_show_all_bookings') == 'Yes') {
                    $this->fd_public_show_all_bookings = true;
                }
                
                if($active->params->get('fd_public_show_global') == 'Yes') {
                    $this->fd_public_show_global = true;
                }
                
                if($active->params->get('fd_public_global_category') != 0) {
                    $this->fd_public_global_category = 
                            $active->params->get('fd_public_global_category');
                }
	}

	function setResAdmin($id){
		$this->resAdmin = $id;	
	}
	
	function setReqStatus($status){
		$this->reqStatus = $status;	
	}
	
	function setResourceFilter($res_filter){
		$this->resource_filter = $res_filter;	
	}

	function setCategoryFilter($cat_filter){
		$this->category_filter = $cat_filter;	
	}

	function setSearchFilter($value){
		$this->user_search_filter = $value;	
	}
	
	function setWeekViewDateFormat($value){
		$this->$week_view_header_date_format = $value;	
	}

	function setWeekStartDay($value){
		$this->startDay = $value;	
	}

	function setIsMobile($value){
		$this->isMobile = $value;	
	}

	function setShowSeatTotals($value){
		$this->showSeatTotals = $value;	
	}


	function getDayNames()
	{
		return $this->dayNames;
	}
	
		
	function getDateLink($day, $month, $year)
	{
		return "";
	}
	
	
	function getCurrentMonthView()
	{
		$d = getdate(time());
		return $this->getMonthView($d["mon"], $d["year"]);
	}
	
		
	function getMonthView($month, $year)
	{
		return $this->getMonthHTML($month, $year);
	}
	
	

	function getDaysInMonth($month, $year)
	{
		if ($month < 1 || $month > 12)
		{
			return 0;
		}
		
		$d = $this->daysInMonth[$month - 1];
		
		if ($month == 2)
		{
			// Check for leap year
			// Forget the 4000 rule, I doubt I'll be around then...
			
			if ($year%4 == 0)
			{
				if ($year%100 == 0)
				{
					if ($year%400 == 0)
					{
						$d = 29;
					}
				}
				else
				{
					$d = 29;
				}
			}
		}
		
		return $d;
	}
	
	
	/*
		------------------------------------------------------------------------------------------------
	    Generate the HTML for a given month
		------------------------------------------------------------------------------------------------
	*/
	function getMonthHTML($m, $y, $showYear = 1){
		
		$bookings = $this->getBookings($m, $y, "month");
		
		$s = "";
		
		$a = $this->adjustDate($m, $y);
		$month = $a[0];
		$year = $a[1];        
		
		$daysInMonth = $this->getDaysInMonth($month, $year);
		$date = getdate(mktime(12, 0, 0, $month, 1, $year));
		
		$first = $date["wday"];
		$array_monthnames = getMonthNamesArray();
		$monthName = $array_monthnames[$month - 1];
		
		$prev = $this->adjustDate($month - 1, $year);
		$next = $this->adjustDate($month + 1, $year);
                //
                // SilverRose April 12 2013 - grab the currently logged in user
		$user =& JFactory::getUser();
		//
                // END SilverRose
		if ($showYear == 1)
		{
			$prevMonth = $this->getCalendarLinkOnClick($prev[0], $prev[1]);
			$nextMonth = $this->getCalendarLinkOnClick($next[0], $next[1]);
//			$prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
//			$nextMonth = $this->getCalendarLink($next[0], $next[1]);
		}
		else
		{
			$prevMonth = "";
			$nextMonth = "";
		}
		
		$header = $monthName . (($showYear > 0) ? " " . $year : "");

		$array_daynames = getDayNamesArray();
		$s .= "<table width=\"100%\" align=\"center\" class=\"calendar\" cellspacing=\"1\" style=\"border: solid 1px\">\n";
		$s .= "<tr>\n";
		$s .= "<td colspan=\"7\" align=\"center\">\n";
		$s .= "<table width=\"100%\" >\n";
		$s .= "<tr>\n";
		$s .= "<td width=\"5%\" align=\"center\" valign=\"top\"><input type=\"button\" onclick=\"$prevMonth\" value=\"<<\"></td>\n";
		$s .= "<td align=\"center\" valign=\"top\" class=\"calendarHeader\" >$header</td>\n"; 
		$s .= "<td width=\"5%\" align=\"center\" valign=\"top\"><input type=\"button\" onclick=\"$nextMonth\" value=\">>\" ></td>\n";
		$s .= "</td>\n"; 
		$s .= "</tr>\n";
		$s .= "</table>\n";
		$s .= "</tr>\n";
		
		$s .= "<tr>\n";
		$s .= "<td width=\"14%\" align=\"center\" valign=\"top\" class=\"calendarHeaderDays\">" . $array_daynames[($this->startDay)%7] . "</td>\n";
		$s .= "<td width=\"14%\" align=\"center\" valign=\"top\" class=\"calendarHeaderDays\">" . $array_daynames[($this->startDay+1)%7] . "</td>\n";
		$s .= "<td width=\"14%\" align=\"center\" valign=\"top\" class=\"calendarHeaderDays\">" . $array_daynames[($this->startDay+2)%7] . "</td>\n";
		$s .= "<td width=\"14%\" align=\"center\" valign=\"top\" class=\"calendarHeaderDays\">" . $array_daynames[($this->startDay+3)%7] . "</td>\n";
		$s .= "<td width=\"14%\" align=\"center\" valign=\"top\" class=\"calendarHeaderDays\">" . $array_daynames[($this->startDay+4)%7] . "</td>\n";
		$s .= "<td width=\"14%\" align=\"center\" valign=\"top\" class=\"calendarHeaderDays\">" . $array_daynames[($this->startDay+5)%7] . "</td>\n";
		$s .= "<td width=\"14%\" align=\"center\" valign=\"top\" class=\"calendarHeaderDays\">" . $array_daynames[($this->startDay+6)%7] . "</td>\n";
		$s .= "</tr>\n";
		
		// We need to work out what date to start at so that the first appears in the correct column
		$d = $this->startDay + 1 - $first;
		while ($d > 1)
		{
			$d -= 7;
		}
		
		// Make sure we know when today is, so that we can use a different CSS style
		$today = getdate(time());
		
		while ($d <= $daysInMonth)
		{
			$s .= "<tr>\n";       
			
			for ($i = 0; $i < 7; $i++)
			{
				$class = ($year == $today["year"] && $month == $today["mon"] && $d == $today["mday"]) ? "calendarToday" : "calendar";
				$s .= "<td class=\"calendarCell $class\" width=\"14%\" align=\"left\" valign=\"top\">";       
//				$s .= "<td class=\"calendarCell$class\" align=\"left\" valign=\"top\">";       
				if ($d > 0 && $d <= $daysInMonth)
				{
					//$link = "javascript:goDayView('".$year."-".$month."-".$d."')";
					//$s .= (($link == "") ? "<span class=\"calendar_day_number\">".$d."</span>" : "<a href=\"".$link."\">$d</a>");
					$link = "# onclick='goDayView(\"".$year."-".$month."-".$d."\");return false;'";						
					$s .= "<a href=".$link.">".$d."</a>";
				}
				else
				{
					$s .= "&nbsp;";
				}
				// get todays bookings
				$strToday = strval($year)."-".($month<10 ? "0".
				strval($month):strval($month)) .
				"-".($d<10 ? "0".strval($d) : strval($d));
				foreach($bookings as $booking){
                                    $this->debug_booking = $booking;
					if($booking->startdate == $strToday){
// SilverRose:  Disabled because we generally won't want to link anywhere from
//              the public calendar screen.                                            
//						if($this->fd_read_only){
//							if($this->fd_detail_popup){
//								$link = JRoute::_( 'index.php?option=com_rsappt_pro2&controller=admin_detail&task=readonly&cid[]='. $booking->id_requests.'&frompage=front_desk&Itemid='.$this->Itemid). " class=\"modal\" rel=\"{handler: 'iframe', onClose: function() {}}\" ";
//							} else {
//								$link = "'#' onclick=\"alert('".JText::_('RS1_DETAIL_VIEW_DISABLED')."');return true;\" ";
//							}
//						} else {
//							$link = JRoute::_( 'index.php?option=com_rsappt_pro2&controller=admin_detail&task=edit&cid[]='. $booking->id_requests.'&frompage=front_desk&Itemid='.$this->Itemid);
//						}

                                                $css_prefix = "nouser";
                                                $show_booking = false; // Assume we aren't going to display this booking at all
                                                
                                                if($user->name == 
                                                    $booking->name)
                                                {
                                                    $display = $booking->CategoryName;
                                                    $css_prefix = "user";
                                                    $show_booking = true;
                                                }
                                                //
                                                // SilverRose Systems Ltd:  For "global" bookings 
                                                //    (e.g. events) we use the booking 
                                                //    category to decide if we should display this 
                                                //    and we show whatever text is in the "admin comment"
                                                //    
                                                //    This has been done because it seems like far too much additional 
                                                //    work to peel it out of a UDF comment field.
                                                //
                                                if($this->fd_public_show_global == true)
                                                {
                                                    if($this->fd_public_global_category != 0 &&
                                                            $booking->category == $this->fd_public_global_category) 
                                                    {
                                                        $display=$booking->admin_comment;
                                                        $css_prefix = "public";
                                                        $show_booking = true;
                                                    }
                                                }

                                                if($show_booking == true) {
                                                    if($this->fd_tooltip == "Resource"){
                                                            $title = trim($booking->display_starttime)."-".trim($booking->display_endtime)."&nbsp;\n".$booking->resname."&nbsp;\n".$booking->ServiceName;
                                                    } else if($this->fd_tooltip == "Customer"){
                                                            $title = trim($booking->display_starttime)."-".trim($booking->display_endtime)."&nbsp;\n".$booking->name."&nbsp;\n".$booking->ServiceName;
                                                    }
                                                    // SilverRose Systems Ltd.
                                                    // href link to nil is deliberate.  We want the rollover tooltip, but no link to any details
                                                    $s .= "<br><a href='' title=\"".$title."\"><span class='pfd_".$css_prefix."_calendar_text_".$booking->request_status."'>".$booking->display_starttime."|".stripslashes($display)."</span></a>";

                                                }
                                        } // END - If Booking is in current day
				}  // END - For Each Booking
				
				$s .= "<br>&nbsp;</td>\n";       
				$d++;
			}  // END - For Each Day In Week
			$s .= "</tr>\n";    
		}  // END - For Each Day In Month
		
		$s .= "</table>\n";
		$s .= "<input type=\"hidden\" name=\"cur_month\" id=\"cur_month\" value=\"".$month."\">";
		$s .= "<input type=\"hidden\" name=\"cur_year\" id=\"cur_year\" value=\"".$year."\">";
		
		return $s;  	
	}
	
	// get  bookings
	function getBookings($month, $year, $mode="month"){
		$database = &JFactory::getDBO();
		$sql = 'SELECT * FROM #__sv_apptpro2_config';
		$database->setQuery($sql);
		$apptpro_config = $database -> loadObject();
		if ($database -> getErrorNum()) {
			echo "DB Err: ". $database -> stderr();
			return false;
		}
	
		$lang =& JFactory::getLanguage();
		$sql = "SET lc_time_names = '".str_replace("-", "_", $lang->getTag())."';";
		$database->setQuery($sql);
		if (!$database->query()) {
		
			echo $database -> getErrorMsg();						
		}
	
		$sql = "SELECT #__sv_apptpro2_requests.*, #__sv_apptpro2_resources.resource_admins, #__sv_apptpro2_resources.id_resources as res_id, ".
			"#__sv_apptpro2_resources.max_seats, #__sv_apptpro2_resources.name as resname, #__sv_apptpro2_services.name AS ServiceName,  ".		
			"#__sv_apptpro2_categories.name AS CategoryName,  ".			
			"#__sv_apptpro2_resources.id_resources as resid, DATE_FORMAT(#__sv_apptpro2_requests.startdate, '%a %b %e ') as display_startdate, ";
			if($apptpro_config->timeFormat == '24'){
				$sql .=" DATE_FORMAT(#__sv_apptpro2_requests.starttime, ' %H:%i') as display_starttime, ";
				$sql .=" DATE_FORMAT(#__sv_apptpro2_requests.endtime, ' %H:%i') as display_endtime ";
			} else {
				$sql .=" DATE_FORMAT(#__sv_apptpro2_requests.starttime, ' %l:%i %p') as display_starttime, ";
				$sql .=" DATE_FORMAT(#__sv_apptpro2_requests.endtime, ' %l:%i %p') as display_endtime ";
			}			
			$sql .= " FROM ( ".
			'#__sv_apptpro2_requests LEFT JOIN '.
			'#__sv_apptpro2_resources ON #__sv_apptpro2_requests.resource = '.
			'#__sv_apptpro2_resources.id_resources LEFT JOIN '.	
			'#__sv_apptpro2_categories ON #__sv_apptpro2_requests.category = '.
			'#__sv_apptpro2_categories.id_categories LEFT JOIN '.
			'#__sv_apptpro2_services ON #__sv_apptpro2_requests.service = '.
			'#__sv_apptpro2_services.id_services ) '.
			"WHERE ";
                        $sql .= " request_status IN('new', 'pending', 'accepted') ";

// April 12 2013 - SilverRose
//  TODO:  Extend this so that we can highlight the appointments that belong to 
//         the $user
                        $user =& JFactory::getUser();
                        if($user->guest){
                                // if not logged in, only show public resources
                                $sql .= " AND #__sv_apptpro2_resources.access LIKE '%|1|%' ";
                        }
		switch($mode){
			case "month":
				$sql = $sql." AND MONTH(startdate)=".strval($month)." AND YEAR(startdate)=".strval($year);
				break;
		}

		$sql .= " ORDER BY startdate, starttime";
		//echo $sql;	
		$database->setQuery($sql);
		$rows = NULL;
		$rows = $database -> loadObjectList();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
			return false;
		}
		return $rows;
	}
	
	
	/*
	    Adjust dates to allow months > 12 and < 0. Just adjust the years appropriately.
	    e.g. Month 14 of the year 2001 is actually month 2 of year 2002.
	*/
	function adjustDate($month, $year)
	{
		$a = array();  
		$a[0] = $month;
		$a[1] = $year;
		
		while ($a[0] > 12)
		{
			$a[0] -= 12;
			$a[1]++;
		}
		
		while ($a[0] <= 0)
		{
			$a[0] += 12;
			$a[1]--;
		}
		
		return $a;
	}
	
	/* 
	    The start day of the week. This is the day that appears in the first column
	    of the calendar. Sunday = 0.
	*/
	//var $startDay = 0;
	
	/* 
	    The start month of the year. This is the month that appears in the first slot
	    of the calendar in the year view. January = 1.
	*/
	var $startMonth = 1;
	
	
	/*
	    The number of days in each month. You're unlikely to want to change this...
	    The first entry in this array represents January.
	*/
	var $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

	function getCalendarLink($month, $year)
	{
		// Redisplay the current page, but with some parameters
		// to set the new month and year
		$s = getenv('SCRIPT_NAME');
		return "$s?month=$month&year=$year";
	}
	
	function getCalendarLinkOnClick($month, $year)
	{
		return "buildPublicFrontDeskView('', $month, $year)";
	}
	

	
}

?>


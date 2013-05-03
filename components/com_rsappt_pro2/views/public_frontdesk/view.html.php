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
 * Created from views/front_desk/view.html.php
 * 
 * Original Code (c) Soft Ventures Inc.
 * Public Front Desk Extension (c) SilverRose Systems Ltd. 2013 - 
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import VIEW object class
jimport( 'joomla.application.component.view' );

/**
 [controller]View[controller]
 */
 
class public_FrontDeskViewpublic_FrontDesk extends JView
{
	/**
	 * Custom Constructor
	 */
	function __construct( $config = array())
	{
	 /** set up global variable for sorting etc.
	  * $context is used in VIEW abd in MODEL
	  **/	  
	 
 	 global $context;
	 $context = 'public_front_desk.';
 
 	 parent::__construct( $config );
	}
 

   
	function display($tpl = null)
	{
		global $context;
	  	$mainframe = JFactory::getApplication();
		
		$uri	=& JFactory::getURI();
		
		$params =& $mainframe->getPageParameters('com_rsappt_pro2');
		$start_screen_view = "month";
		if($params->get('fd_start_screen') != ''){
			$start_screen_view = $params->get('fd_start_screen');
			//echo $start_screen_view;
		}
		
		// get filters
		$public_front_desk_view	= $mainframe->getUserStateFromRequest( $context.'public_front_desk_view', 'public_front_desk_view', $start_screen_view);
		$public_front_desk_resource_filter	= $mainframe->getUserStateFromRequest( $context.'public_front_desk_resource_filter', 'public_front_desk_resource_filter', '');
		$public_front_desk_category_filter	= $mainframe->getUserStateFromRequest( $context.'public_front_desk_category_filter', 'public_front_desk_category_filter', '');
		$public_front_desk_status_filter	= $mainframe->getUserStateFromRequest( $context.'public_front_desk_status_filter', 'public_front_desk_status_filter', '');
		$public_front_desk_user_search	= $mainframe->getUserStateFromRequest( $context.'public_front_desk_user_search', 'public_front_desk_user_search', '');

		$public_front_desk_cur_week_offset = $mainframe->getUserState('public_front_desk_cur_week_offset');
		$public_front_desk_cur_day = $mainframe->getUserState('public_front_desk_cur_day');
		$public_front_desk_cur_month = $mainframe->getUserState('public_front_desk_cur_month');
		$public_front_desk_cur_year = $mainframe->getUserState('public_front_desk_cur_year');


		//DEVNOTE:save a reference into view	
		$this->assignRef('user',		JFactory::getUser());	
		$this->assignRef('request_url',	$uri->toString());

		$frompage  = 'front_desk';
		$this->assignRef('frompage',	$frompage);
		$this->assignRef('public_front_desk_view', $public_front_desk_view);
		$this->assignRef('public_front_desk_resource_filter', $public_front_desk_resource_filter);
		$this->assignRef('public_front_desk_category_filter', $public_front_desk_category_filter);
		$this->assignRef('public_front_desk_status_filter', $public_front_desk_status_filter);
		$this->assignRef('public_front_desk_user_search', $public_front_desk_user_search);

		$this->assignRef('public_front_desk_cur_week_offset', $public_front_desk_cur_week_offset);
		$this->assignRef('public_front_desk_cur_day', $public_front_desk_cur_day);
		$this->assignRef('public_front_desk_cur_month', $public_front_desk_cur_month);
		$this->assignRef('public_front_desk_cur_year', $public_front_desk_cur_year);


		//DEVNOTE:call parent display
    	parent::display($tpl);
  }
}

?>

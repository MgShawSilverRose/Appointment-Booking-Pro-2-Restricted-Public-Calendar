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
 * Original Code (c) Soft Ventures Inc.
 * Public Front Desk Extension (c) SilverRose Systems Ltd. 2013 - 
 */


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import CONTROLLER object class
jimport( 'joomla.application.component.controller' );


/**
 * rsappt_pro2  Controller
 */
 
class ajaxController extends JController
{

	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );
		
	
		// Register tasks
		$this->registerTask( 'list_bookings', 'list_bookings' );
		$this->registerTask( 'cancel_booking', 'cancel_booking' );
		$this->registerTask( 'delete_booking', 'delete_booking' );
		$this->registerTask( 'ajax_calview', 'ajax_calview' );

		$this->registerTask( 'ajax', 'generic_ajax' );
		$this->registerTask( 'ajax_validate', 'ajax_validate' );

		$this->registerTask( 'ajax_gad', 'ajax_gad' );
		$this->registerTask( 'ajax_gad2', 'ajax_gad2' );

		$this->registerTask( 'ajax_check_overlap', 'ajax_check_overlap' );

		$this->registerTask( 'ajax_fetch', 'ajax_fetch' );

		$this->registerTask( 'ajax_who_booked', 'ajax_who_booked' );

		$this->registerTask( 'ajax_user_search', 'ajax_user_search' );
                //
                // SilverRose Public Front Desk Extension
                // 
                $this->registerTask('ajax_calviewpublic', 'ajax_calviewpublic');
                /*
                 * End - SilverRose Systems Ltd. Public Front Desk Extension
                 */

	}

	function list_bookings()
	{
		JRequest::setVar( 'view', 'backup_restore' );
		JRequest::setVar( 'layout', 'default'  );
		JRequest::setVar( 'hidemainmenu', 0);


		parent::display();

	}
      
	function cancel_booking()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro2/ajax/fe_cancel.php');
	}


	function delete_booking()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro2/ajax/fe_delete.php');
	}

	function ajax_calview()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro2/ajax/calview_ajax.php');
	}

	function generic_ajax()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro2/getSlots.php');
	}

	function ajax_validate()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro2/fe_val.php');
	}

	function ajax_gad()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro2/gad_ajax.php');
	}
	
	function ajax_gad2()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro2/gad_ajax2.php');
	}

	function ajax_check_overlap()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro2/fe_overlap.php');
	}

	function ajax_fetch()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro2/fe_fetch.php');
	}

	function ajax_who_booked()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro2/ajax/who_booked.php');
	}

	function ajax_user_search()
	{
		include_once(JPATH_SITE.'/components/com_rsappt_pro2/ajax/user_search.php');
	}
        /*
         * SilverRose Systems Ltd.  Public Front Desk Extension
         * 
         */
        function ajax_calviewpublic()
        {
            include_once(JPATH_SITE.'/components/com_rsappt_pro2/ajax/calviewpublic_ajax.php');
        }
	/*
         * End - SilverRose Systems Ltd. Public Front Desk Extension
         */
	/** function cancel
	*
	* Check in the selected detail 
	* and set Redirection to the list of items	
	* 		
	* @return set Redirection
	*/
	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_rsappt_pro2&controller=ajax',$msg );
	}	


}

?>


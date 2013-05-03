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
*/


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

	JHTML::_('behavior.tooltip');
	JHTML::_('behavior.modal');
	jimport( 'joomla.application.helper' );

	include_once( JPATH_SITE."/components/com_rsappt_pro2/functions2.php" );

	$user =& JFactory::getUser();
	$itemid = JRequest::getString( 'Itemid', '' );
	$option = JRequest::getString( 'option', '' );

	$menu = &JSite::getMenu(); 
	$active = $menu->getActive(); 
	$menu_id = $active->id;

	$front_desk_view = $this->front_desk_view;
	$front_desk_resource_filter = $this->front_desk_resource_filter;
	$front_desk_category_filter = $this->front_desk_category_filter;
	$front_desk_status_filter = $this->front_desk_status_filter;
	$front_desk_user_search = $this->front_desk_user_search;

	$front_desk_cur_week_offset = $this->front_desk_cur_week_offset;
	$front_desk_cur_day = $this->front_desk_cur_day;
	$front_desk_cur_month = $this->front_desk_cur_month;
	$front_desk_cur_year = $this->front_desk_cur_year;

	$mainframe = JFactory::getApplication();
	$params =& $mainframe->getPageParameters('com_rsappt_pro2');
	$read_only = true;
	// $resadmin_only = true;
        $resadmin_only = false;
	// $month_view_only = false;
        $month_view_only = true;
        $fd_login_required = false;
        $fd_allow_cust_hist = false;
        $fd_allow_show_seats = false;
        $fd_res_admin_only = false;
        $fd_use_page_title = true;
        $fd_allow_reminders = false;
        $fd_show_cats_filter = false;
        $fd_show_resources_filter = true;
        $fd_show_state_filter = true;
        $fd_booking_staff_or_public = "None";
        $fd_public_view = true;
//        $fd_public_view = false;
//        if($params->get('fd_public_view') != null) {
//            $fd_public_view = $params->get('fd_public_view');
//        }
        
	$retore_settings = "";
	switch($front_desk_view){
		case "month":
			if($front_desk_cur_month != ""){
				$retore_settings = "'', '".$front_desk_cur_month."', '".$front_desk_cur_year."', ''";
			}		
			break;
		case "week":
			if($front_desk_cur_week_offset != ""){
				$retore_settings = "'', '', '', '".$front_desk_cur_week_offset."'";
			}
			break;
		case "day":
			if($front_desk_cur_day != ""){
				$retore_settings = "'".$front_desk_cur_day."', '', '', ''";
			}		
			break;
	}
	

	$showform= true;

	if(!$user->guest || $fd_login_required == false){
	
		$database = &JFactory::getDBO();
		// get resources
		$sql = "SELECT * FROM #__sv_apptpro2_resources WHERE Published=1 ";
		if($fd_res_admin_only){
			$sql .= " AND resource_admins LIKE '%|".$user->id."|%' ";
		}
		if($user->guest){
			// if not logged in, only show public resources
			$sql .= " AND access LIKE '%|1|%' ";
		}
		$sql .= " ORDER BY ordering;";
		//echo $sql;
		$database->setQuery($sql);
		$res_rows = $database -> loadObjectList();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
			return false;
		}
	
		// get catgories
		// cannot relate cateory to operator so shows all categories	
		$sql = "SELECT * FROM #__sv_apptpro2_categories WHERE Published=1 ";
		$sql .= " ORDER BY ordering;";
		//echo $sql;
		$database->setQuery($sql);
		$cat_rows = $database -> loadObjectList();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
			return false;
		}
		
		$database = &JFactory::getDBO();
		$sql = 'SELECT * FROM #__sv_apptpro2_config';
		$database->setQuery($sql);
		$apptpro_config = NULL;
		$apptpro_config = $database -> loadObject();
		if ($database -> getErrorNum()) {
			echo "DB Err: ". $database -> stderr();
			return false;
		}

		// get statuses
		if($read_only){
			$sql = "SELECT * FROM #__sv_apptpro2_status WHERE internal_value IN('new', 'pending', 'accepted') ORDER BY ordering ";
		} else {
			$sql = "SELECT * FROM #__sv_apptpro2_status WHERE internal_value!='deleted' ORDER BY ordering ";
		}
		//echo $sql;
		$database->setQuery($sql);
		$statuses = $database -> loadObjectList();
		if ($database -> getErrorNum()) {
			echo "DB Err: ". $database -> stderr();
			return false;
		}
		

	} else{
		echo "<font color='red'>".JText::_('RS1_ADMIN_SCRN_NO_LOGIN')."</font>";
		$showform = false;
	}

	if($fd_res_admin_only && $showform){
		// check to see id user is an admin		
		$sql = "SELECT count(*) as count FROM #__sv_apptpro2_resources WHERE ".
			"resource_admins LIKE '%|".$user->id."|%';";
		$database->setQuery($sql);
		$check = NULL;
		$check = $database -> loadObject();
		if ($database -> getErrorNum()) {
			echo $database -> stderr();
			return false;
		}
		if($check->count == 0){
			echo "<font color='red'>".JText::_('RS1_ADMIN_SCRN_NOT_ADMIN')."</font>";
			$showform = false;
		}	
	}
?>

<?php if($showform){?>
<link href="<?php echo $this->baseurl;?>/components/com_rsappt_pro2/sv_apptpro.css" rel="stylesheet">
<script language="JavaScript" src="<?php echo $this->baseurl;?>/components/com_rsappt_pro2/script.js"></script>


<script language="javascript">
	window.onload = function() {
		buildPublicFrontDeskView( <?php echo $retore_settings ?>);	
	} 	

	function goManifest(resid, startdate, starttime, endtime){
//		document.getElementById("redirect").value="manifest";
		document.getElementById("resid").value=resid;
		document.getElementById("startdate").value=startdate;
		document.getElementById("starttime").value=starttime;
		document.getElementById("endtime").value=endtime;
		submitbutton('display_manifest');
		return false;		
	}

	function toggleTotals(){
		if(document.getElementById("cur_day") != null){
			buildPublicFrontDeskView( document.getElementById("cur_day").value);
		}
	}
	
	function goDayView(day){
		document.getElementById("front_desk_view").selectedIndex=0;
		buildPublicFrontDeskView(day);
	}

	function sendReminders(which){
		if(which=="Email"){
			submitbutton('reminders');
		} else {
			submitbutton('reminders_sms');
		}	
		return false;		
	}
	
	function doSearch(){
/*		if(document.getElementById("user_search").value==""){
			alert("<?php echo JText::_('RS1_FRONTDESK_SCRN_SEARCH_HELP');?>");
			return false;
		}*/
		buildPublicFrontDeskView();
	}

	function exportCSV(){
		document.getElementById("task").value="export_csv";
		document.adminForm.submit();
		document.getElementById("task").value="";
	}

	
</script>
<form name="adminForm" action="<?php echo JRoute::_($this->request_url) ?>" method="post">
<div id="sv_apptpro_front_desk">
<div id="sv_apptpro_front_desk_top">
    <table width="100%">
        <tr>
          <td align="left" colspan="2"> <h3>
          <?php if($fd_use_page_title){
		  	echo JText::_($params->get('page_title'));
		  } else {
			echo JText::_('RS1_PFD_MENU_TITLE_DESC');
		  }?>
            </h3></td>
          <td style="text-align:right"><?php echo $user->name ?></td>
        </tr>
        <tr>
          <td colspan="3">
          <?php if($fd_allow_reminders){ ?><div id="reminder_links" style="visibility:hidden; display:none; text-align:right">
          	<a href="#" onclick="exportCSV();return(false);" title="<?php echo JText::_('RS1_ADMIN_SCRN_EXPORT_CSV_HELP');?>"><?php echo JText::_('RS1_ADMIN_SCRN_EXPORT_CSV');?></a>&nbsp;|&nbsp;
			<a href="javascript:sendReminders('Email');"><?php echo JText::_('RS1_ADMIN_SCRN_SEND_REMINDERS');?></a>&nbsp;|&nbsp;
			<a href="javascript:sendReminders('SMS');"><?php echo JText::_('RS1_ADMIN_SCRN_SEND_REMINDERS_SMS');?></a>&nbsp;			
        	</div>
          <?php } ?> 
            </td>
        <tr>
         <td align="left">&nbsp;&nbsp;<select id="front_desk_view" name="front_desk_view" onchange="buildFrontDeskView()" style="font-size:11px">
        <?php if(!$month_view_only){?>
            <option value="day" <?php if($front_desk_view == "day"){ echo " selected ";}?>><?php echo JText::_('RS1_FRONTDESK_SCRN_VIEW_DAY');?></option>
            <option value="week" <?php if($front_desk_view == "week"){ echo " selected ";}?>><?php echo JText::_('RS1_FRONTDESK_SCRN_VIEW_WEEK');?></option>
        <?php } ?>    
            <option value="month" <?php if($front_desk_view == "month"){ echo " selected ";}?>><?php echo JText::_('RS1_FRONTDESK_SCRN_VIEW_MONTH');?></option>
            </select> </td>
          <td align="right"></td>          
          <td style="text-align:right"><input type="text" id="user_search" name="user_search" size="20" class="sv_apptpro2_request_text" 
          	title="<?php echo JText::_('RS1_FRONTDESK_SCRN_SEARCH_HELP');?>" value="<?php echo $front_desk_user_search_filter ?>" />
            <input type="button" onclick="doSearch();" class="sv_apptpro2_request_text" value="<?php echo JText::_('RS1_FRONTDESK_SCRN_SEARCH');?>" /></td>
        </tr>
        <tr>
          <?php if($fd_booking_staff_or_public == 'None'){ ?>
	          <td colspan="2">&nbsp;&nbsp;
          <?php } else {?>
	          <td colspan="2">&nbsp;&nbsp;<a href="<?php echo $link ?>"><?php echo JText::_('RS1_FRONTDESK_SCRN_ADDNEW');?></a>
          <?php } ?>
        
          <?php if($fd_allow_cust_hist){ ?>
          &nbsp;|&nbsp;<a href="<?php echo $link_history ?>"><?php echo JText::_('RS1_FRONTDESK_SCRN_HISTORY');?></a>
          <?php } ?>
          </td>
          <td style="text-align:right">
          <?php if($fd_allow_show_seats){ ?>          
				<input type="checkbox" id="showSeatTotals" name="showSeatTotals" onclick="toggleTotals();"/><?php echo JText::_('RS1_FRONTDESK_SCRN_SHOW_SEAT_TOTALS');?>&nbsp;&nbsp;&nbsp;&nbsp;
          <?php } ?>        

          <?php if($fd_show_cats_filter){ ?>          
            <select name="category_filter" id="category_filter" onchange="buildFrontDeskView();" style="font-size:11px" >
            <option value=""><?php echo JText::_('RS1_ADMIN_SCRN_REQUEST_CATEGORY_NONE');?></option>
              <?php
				$k = 0;
				for($i=0; $i < count( $cat_rows ); $i++) {
				$cat_row = $cat_rows[$i];
				?>
              <option value="<?php echo $cat_row->id_categories; ?>" <?php if($front_desk_category_filter == $cat_row->id_categories){echo " selected='selected' ";} ?>><?php echo JText::_(stripslashes($cat_row->name)); ?></option>
              <?php $k = 1 - $k; 
				} ?>
            </select>            &nbsp;&nbsp;
          <?php } ?>        
          <?php if($fd_show_resources_filter ) { ?>
            <select name="resource_filter" id="resource_filter" onchange="buildFrontDeskView();" style="font-size:11px" >
            <option value=""><?php echo JText::_('RS1_ADMIN_SCRN_REQUEST_RESOURCE_NONE');?></option>
              <?php
				$k = 0;
				for($i=0; $i < count( $res_rows ); $i++) {
				$res_row = $res_rows[$i];
				?>
              <option value="<?php echo $res_row->id_resources; ?>" <?php if($front_desk_resource_filter == $res_row->id_resources){echo " selected='selected' ";} ?>><?php echo JText::_(stripslashes($res_row->name)); ?></option>
              <?php $k = 1 - $k; 
				} ?>
            </select>            &nbsp;&nbsp;
          <?php } ?>
          <?php if($fd_show_state_filter) { ?>
          <select id="status_filter" name="status_filter" onchange="buildFrontDeskView()" style="font-size:11px">
            <option value=""><?php echo JText::_('RS1_ADMIN_SCRN_REQUEST_STATUS_NONE');?></option>
			<?php foreach($statuses as $status_row){ ?>
                <option value="<?php echo $status_row->internal_value ?>" <?php if($front_desk_status_filter == $status_row->internal_value){echo " selected='selected' ";} ?> class="color_<?php echo $status_row->internal_value ?>" ><?php echo JText::_($status_row->status);?></option>        
            <?php } ?>
            </select>&nbsp;&nbsp; 
          <?php } ?>
           </td>
        </tr> 
    </table>
</div>
<div id="calview_here">&nbsp;</div>

<input type="hidden" name="id" id="id" value="<?php echo $row->id; ?>">
<input type="hidden" name="uid" id="uid" value="<?php echo $user->id; ?>">
<input type="hidden" id="script_path" name="script_path" value="<?php echo SCRIPTPATH?>" />
<input type="hidden" name="redirect" id="redirect" value="" />
<input type="hidden" name="listpage" id="listpage" value="front_desk" />
<input type="hidden" name="startdate" id="startdate" value="" />
<input type="hidden" name="starttime" id="starttime" value="" />
<input type="hidden" name="endtime" id="endtime" value="" />
<input type="hidden" name="resid" id="resid" value="" />

  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
  	<input type="hidden" name="controller" value="front_desk" />
	<input type="hidden" name="id" value="<?php echo $this->user_id; ?>" />
	<input type="hidden" name="task" id='task' value="" />
	<input type="hidden" name="frompage" value="front_desk" />
  	<input type="hidden" name="frompage_item" id="frompage_item" value="<?php echo $itemid ?>" />

  	<input type="hidden" name="menu_id" id="menu_id" value="<?php echo $menu_id ?>"/>

  <br />
  <?php if($apptpro_config->hide_logo == 'No'){ ?>
    <span style="font-size:10px"> Appointment Booking Pro Ver. 2.0.4 - Copyright 2008-20<?php echo date("y");?> - <a href='http://www.softventures.com' target="_blank">Soft Ventures, Inc.</a></span>
  <?php } ?> 
</div>
</form>
<?php } ?>


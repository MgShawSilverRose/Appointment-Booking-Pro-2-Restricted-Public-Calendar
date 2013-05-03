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
 * Created from views/front_desk/tmpl/default.php
 * 
 * Original Code (c) Soft Ventures Inc.
 * Public Front Desk Extension (c) SilverRose Systems Ltd. 2013 - 
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

	// $front_desk_view = $this->front_desk_view;
        $front_desk_view = "month";
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
        $fd_show_resources_filter = false;
        $fd_show_state_filter = false;
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
//		case "week":
//			if($front_desk_cur_week_offset != ""){
//				$retore_settings = "'', '', '', '".$front_desk_cur_week_offset."'";
//			}
//			break;
//		case "day":
//			if($front_desk_cur_day != ""){
//				$retore_settings = "'".$front_desk_cur_day."', '', '', ''";
//			}		
//			break;
	}
	

	$showform= true;

?>

<?php if($showform){?>
<link href="<?php echo $this->baseurl;?>/components/com_rsappt_pro2/sv_apptpro.css" rel="stylesheet">
<script language="JavaScript" src="<?php echo $this->baseurl;?>/components/com_rsappt_pro2/script.js"></script>


<script language="javascript">
/*
 * SilverRose Systems - Added April 11 2013 
 * Michelle Shaw
 * 
 * Adapted from buildFrontDeskView in components/com_rsappt_pro2/script.js
 */
function buildPublicFrontDeskView(day, month, year, week_offset){
    var Itemid = document.getElementById('frompage_item').value;
    var view = "month";
    var resource = "";
    var category = "";
    var status = "";

    if (typeof day === "undefined") {
        if(document.getElementById("cur_day")!=null){
            day = document.getElementById("cur_day").value;
        } else {
		    var d = new Date();
		    day = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
        }
    }
    if (typeof month === "undefined") {
        if(document.getElementById("cur_month")!=null){
            month = document.getElementById("cur_month").value;
        } else {
            month="";
        }
    }
    if (typeof year === "undefined") {
        year = "";
    }
    if (typeof week_offset === "undefined") {
        if(document.getElementById("cur_week_offset")!=null){
            week_offset = document.getElementById("cur_week_offset").value;
        } else {
            week_offset="0";
        }
    } 
       
    //document.getElementById("calview_here").innerHTML = "Please wait"
    if (window.XMLHttpRequest) {
            xhr = new XMLHttpRequest();
    }
    else {
            if (window.ActiveXObject) {
                    try {
                            xhr = new ActiveXObject("Microsoft.XMLHTTP");
                    }
                    catch (e) { }
            }
    }

    if (xhr) {

            xhr.onreadystatechange = showFrontDeskView;
            var data = "front_desk_view=" + view;
            data = data + "&day=" + day;
            data = data + "&month=" + month;
            data = data + "&year=" + year;
            data = data + "&user=" + document.getElementById("uid").value;
            data = data + "&weekoffset=" + week_offset;
            data = data + "&listpage=" + document.getElementById("listpage").value;
            if(document.getElementById("showSeatTotals")!=null){
                    data = data + "&showSeatTotals=" + document.getElementById("showSeatTotals").checked;
            }
            data = data + "&Itemid=" + Itemid;
            data = data + "&Menuid=" +  document.getElementById("menu_id").value;
            data = data + "&browser=" + BrowserDetect.browser;
            //alert(data);
            xhr.open("GET", presetIndex()+"?option=com_rsappt_pro2&controller=ajax&task=ajax_calviewpublic&format=raw&" + data, true);
            xhr.send(null);
    }
    else {
            alert("Sorry, but I couldn't create an XMLHttpRequest");
    }
    return true;
}
	
function showPublicFrontDeskView() {	
		
	if (xhr.readyState === 4) {
		document.getElementById("calview_here").style.visibility = "visible";
		document.getElementById("calview_here").style.display = "";
	
		if (xhr.status === 200) {		
			var outMsg = xhr.responseText;
		} 
		else {
			var outMsg = "There was a problem with the request " + xhr.status;
		}

		document.getElementById("calview_here").innerHTML = outMsg;
        
        if(document.getElementById('front_desk_view').selectedIndex < 2){
            document.getElementById("reminder_links").style.visibility = "visible";
		    document.getElementById("reminder_links").style.display = "";
        } else {
            document.getElementById("reminder_links").style.visibility = "hidden";
		    document.getElementById("reminder_links").style.display = "none";
        }
 		document.body.style.cursor = "default";    
	}

	SqueezeBox.initialize({});
	SqueezeBox.assign($$('a.modal'), {
		parse: 'rel'
	});

	return true;
}

/*
 * End SilverRose April 11 2013
 */
    
        window.onload = function() {
		buildPublicFrontDeskView( <?php echo $retore_settings ?>);	
	} 	

        
        function doNothing(){
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


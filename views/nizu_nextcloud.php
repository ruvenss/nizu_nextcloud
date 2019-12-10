<?php defined('BASEPATH') or exit('No direct script access allowed'); 
    include("nizu/config.php");
    include("nizu/functions.php");
    $hashpassword="";
    if (isset($_REQUEST['a'])) {
        $action=$_REQUEST['a'];
        
        switch ($action) {
        	case "export_legacy":
        		include("nizu/db.php");
        		$exported=0;
        		$mainquery="SELECT * FROM legacy.user";
				$result = $mas->query($mainquery);
				if (!$result) {
					echo $mainquery;
					error_log("sqlSelect empty result : $mainquery",0);
				} else {
					while($row = $result->fetch_assoc()) {
						$user_id=$row['user_id'];
						$user_active=$row['user_active'];
						$user_allow=$row['user_allow'];
						$user_newsletter=$row['user_newsletter'];
						$user_test=$row['user_test'];
						$user_level=$row['user_level'];
						$user_request=$row['user_request'];
						$user_email=$row['user_email'];
						$user_password=$row['user_password'];
						$user_gender=$row['user_gender'];
						$user_title=$row['user_title'];
						$user_firstname=trim($row['user_firstname']);
						$user_name=trim($row['user_name']);
						$user_address=trim($row['user_address']);
						$user_num=$row['user_num'];
						$user_box=$row['user_box'];
						$user_zip=trim($row['user_zip']);
						$user_city=$row['user_city'];
						$user_state=$row['user_state'];
						$user_countryid=$row['user_countryid'];
						$user_phone=$row['user_phone'];
						$user_fax=$row['user_fax'];
						$user_mobile=$row['user_mobile'];
						$user_address2=$row['user_address2'];
						$user_num2=$row['user_num2'];
						$user_box2=$row['user_box2'];
						$user_zip2=trim($row['user_zip2']);
						$user_city2=$row['user_city2'];
						$user_state2=$row['user_state2'];
						$user_countryid2=trim($row['user_countryid2']);
						$user_phone2=$row['user_phone2'];
						$user_fax2=$row['user_fax2'];
						$user_mobile2=$row['user_mobile2'];
						$user_nationality=$row['user_nationality'];
						$user_firm=$row['user_firm'];
						$user_department=$row['user_department'];
						$user_position=$row['user_position'];
						$user_firmtype=$row['user_firmtype'];
						$user_mainactivity=$row['user_mainactivity'];
						$user_firmnumber=$row['user_firmnumber'];
						$user_retired=$row['user_retired'];
						$user_dateretire=$row['user_dateretire'];
						$user_totalcomplement=$row['user_totalcomplement'];
						$user_topicsid=$row['user_topicsid'];
						$user_fieldsid=$row['user_fieldsid'];
						$user_ingfields=$row['user_ingfields'];
						$user_profields=$row['user_profields'];
						$user_nchapterids=$row['user_nchapterids'];
						$user_remarks=$row['user_remarks'];
						$user_datevki_in=$row['user_datevki_in'];
						$user_datevki_out=$row['user_datevki_out'];
						$user_datedc_in=$row['user_datedc_in'];
						$user_datedc_out=$row['user_datedc_out'];
						$user_datephd_in=$row['user_datephd_in'];
						$user_datephd_out=$row['user_datephd_out'];
						$user_dateatp_in=$row['user_dateatp_in'];
						$user_dateatp_out=$row['user_dateatp_out'];
						$user_datere_in=$row['user_datere_in'];
						$user_datere_out=$row['user_datere_out'];
						$user_datepdp_in=$row['user_datepdp_in'];
						$user_datepdp_out=$row['user_datepdp_out'];
						$user_datefm_in=$row['user_datefm_in'];
						$user_datefm_out=$row['user_datefm_out'];
						$user_datevp_in=$row['user_datevp_in'];
						$user_datevp_out=$row['user_datevp_out'];
						$user_lastupdate=$row['user_lastupdate'];
						$user_logincount=$row['user_logincount'];
						$user_lastlogin=$row['user_lastlogin'];
						$user_lang=$row['user_lang'];
						$user_ip=$row['user_ip'];
						$user_useragent=$row['user_useragent'];
						$user_userlang=$row['user_userlang'];
						$user_datein=$row['user_datein'];
						$user_timein=$row['user_timein'];
						// Translations
						if ($user_level=="1") {
							$nizu_user_group_id=4;
							$nc_user_group_label="Keep in touch";
						} else {
							$nizu_user_group_id=5;
							$nc_user_group_label="LES network";
						}
						if ($user_retired===1) { $nizu_retired="Yes"; } else { $nizu_retired="No"; }
						if ($user_allow===1) { $nizu_public_profile="Yes"; } else { $nizu_public_profile="No"; }
						//$e="cd ".$nizu_nextcloud_settings['nizu_nextcloud_path'].";export OC_PASS=$user_password;".'php occ user:add --password-from-env --display-name="'.$nizu_contact_email.'" --group="users" '.$nizu_contact_email;
						$membername=$user_firstname." ".$user_name;
						$countryid=0;
						$countryid2=0;
						if (strlen($user_countryid)>0) {
							$countryid=sqlSelect($mas,db_prefix()."countries","country_id","iso2='$user_countryid'");
						} else {
							$countryid="";
						}
						if (strlen($user_countryid2)>0) {
							$countryid2=sqlSelect($mas,db_prefix()."countries","country_id","iso2='$user_countryid2'");
						} else {
							$countryid2="";
						}
						if ($countryid<=0) { $countryid="NULL"; }
						if ($countryid2<=0) { $countryid2="NULL"; }
						$user_address.=trim(" ".$user_num);
						$user_address2.=trim(" ".$user_num2);
						if ($user_state=="0") { $user_state="";}
						if ($user_state2=="0") { $user_state2="";}
						if (strlen($user_state)==0){
							$user_state=getState($user_countryid,$user_zip);
						}
						if (strlen($user_state2)==0){
							$user_state2=getState($user_countryid2,$user_zip2);
						}
						$user_address=str_replace("/"," ",$user_address);
						$user_address=str_replace('"'," ",$user_address);
						$memberdata=["phonenumber"=>$user_phone,"country"=>$countryid,"city"=>$user_city,"zip"=>$user_zip,"state"=>$user_state,"address"=>$user_address,"active"=>$user_active,"billing_street"=>$user_address2,"billing_city"=>$user_city2,"billing_state"=>$user_state2,"billing_zip"=>$user_zip2,"billing_country"=>$countryid2];
						$memberid=createNizuCustomer($mas,db_prefix(),$membername,"english",$memberdata);
						$memberdata=[];
						if ($memberid>0) {
							
							// Update custom fields
							
							if (strlen($user_email)>3 && $user_active>0) {
								// Add member to the group
								$user_group_keyid=sqlInsert($mas,db_prefix()."customer_groups",array("groupid","customer_id"),array($nizu_user_group_id,$memberid));
								// Create Contact
								// Next Cloud
								//$e="cd ".$nizu_nextcloud_settings['nizu_nextcloud_path'].";export OC_PASS=$user_password;".'php occ user:add --password-from-env --display-name="'.$nizu_contact_email.'" --group="users" '.$nizu_contact_email;
								//$e2="cd ".$nizu_nextcloud_settings['nizu_nextcloud_path'].";".'php occ group:adduser "'.$nc_user_group_label.'" '.$user_email;
							} else {
								// set unactive
								sqlUpdate($mas,db_prefix()."clients",array("active"),array("0"),"userid",$memberid);
							}
							$exported=$exported+1;
							
						}	
					}
				}
				
        		//$hashpassword=app_hash_password("123123");
        		//$hashpassword=sha1("123123");
        		goodbye(1,array("exported"=>$exported));
        		break;
            case 'NizuLoadForm':
                $table=$_REQUEST['form'];
                $apikey=$_REQUEST['key'];
                $id=$_REQUEST['id'];
                $query = $this->db->query("SELECT * FROM ".db_prefix().$table." WHERE id=$id");
                $results=$query->result_array();
                goodbye($id,$results,array("form"=>$table,"id"=>$id));
                break;
            case 'NizuSaveForm':
                $table=$_REQUEST['form'];
                $apikey=$_REQUEST['key'];
                $id=$_REQUEST['id'];
                if (strlen($apikey)>5) {
                    $formdata = $_REQUEST['formdata'];
                    $formarray = array();
                    $values = array();
                    $fields = array();
                    $updates= array();
                    foreach ($formdata as $key => $value) {
                        foreach ($value as $valuekey => $valuevalue) {
                            // Check if field exist in table if not it should be a custom field
                            $querycolumn = $this->db->query("SHOW COLUMNS FROM `".db_prefix().$table."` LIKE '".$valuekey."'");
                            $exists = $querycolumn->result_array()?TRUE:FALSE;
                            if ($exists===true) {
                                array_push($values, $valuevalue);
                                array_push($fields, $valuekey);
                                array_push($updates,"`$valuekey`=".'"'.$valuevalue.'"');
                            } else {
                                // TODO add it to the custom fields
                            }
                        }
                    }
                    if ($id==0) {
                        $sqlinsert="INSERT INTO ".db_prefix().$table."(`".implode("`,`", $fields).'`) VALUES("'.implode('","', $values).'")';
                        $this->db->query($sqlinsert);
                        $id = $this->db->insert_id();
                        if ($id>0) {
                            log_activity('New '.$table.' Added [ID: ' . $id . ']');
                        }
                    } else {
                        $sqlupdate="UPDATE ".db_prefix().$table." SET ".implode(",", $updates)." WHERE id=".$id;
                        $this->db->query($sqlupdate);
                        log_activity($table.' Updated [ID: ' . $id . ']');
                    }
                    goodbye($id,array("form"=>$table));
                } else {
                    goodbye(0,array("errormsg"=>"API Key missing"));
                }
                break;
            case 'nizu_load_agendas':
                $query = $this->db->query("SELECT id,agenda_title,agenda_public FROM ".db_prefix()."nizu_agendas");
                $results=$query->result_array();
                goodbye(1,$results);
                break;
            case 'nizu_load_services':
                $query = $this->db->query("SELECT id,service_subject,service_active,service_price FROM ".db_prefix()."nizu_agenda_services");
                $results=$query->result_array();
                goodbye(1,$results);
                break;
            case 'nizu_load_bookings':
                $query = $this->db->query("SELECT id,fullname,calendar_date,calendar_starttime,service_id,email,phone,mobile_verified,email_verified FROM ".db_prefix()."nizu_agenda_bookings ORDER BY calendar_date DESC");
                $results=$query->result_array();
                goodbye(1,$results);
                break;
            default:

                die();
                break;
        }
    } else {
        $sqlquery='SELECT * FROM '.db_prefix().'nizu_settings';
        $query = $this->db->query($sqlquery);
        foreach ($query->result_array() as $row){
            switch ($row['name']) {
                case 'nizu_settings_key':
                   $nizu_settings_key=$row['value'];
                   break;
                case 'nizu_settings_user_id':
                   $nizu_settings_user_id=$row['value'];
                   break;
                case 'nizu_settings_user_email':
                   $nizu_settings_user_email=$row['value'];
                   break;
                case 'nizu_settings_theme':
                   $nizu_settings_theme=$row['value'];
                   break;
                default:
                   # code...
                   break;
            }
        }  
    }
function getState($country,$cp) {
	$state="";
	switch ($country) {
    	case 'BE':
    		switch ($cp) {
    			case '1410':
    			case '1420':
    				$state="Wallonia";
    			break;
    			case '1000':
    			case '1010':
    			case '1020':
    			case '1030':
    			case '1040':
    			case '1049':
    			case '1050':
    				$state="Brussels Region";
    			break;
        		default:
    			# code...
    			break;
    		}
        break;
        default:
    	# code...
        break;
    }
    return($state);
}
?>
<?php init_head(); ?>
<?php nizu_loadcss($nizu_settings_theme); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><img src="/nizu/images/nizu_logo.svg" width="30"> Nizu NextCloud</h4>
                        <hr class="hr-panel-heading" />
                        <div class="row">
                            <div class="col-md-6"><button class="btn btn-lg btn-primary" id="btn_export_current">Send <?php echo _l("clients");?> and Groups to Next Cloud ></button></div>
                            <div class="col-md-6"><button class="btn btn-lg btn-primary" id="btn_export_legacy">Export <?php echo _l("clients");?> Legacy ></button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        	<div class="col">Password: <?php echo $hashpassword; ?></div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
   $(function(){
       init_editor('.tinymce-email-description');
       init_editor('.tinymce-view-description');
   });
</script>
<script type="text/javascript" src="/nizu/js/vars.js"></script>;
<script type="text/javascript" src="/nizu/js/functions.js"></script>;
<script type="text/javascript" src="https://rgwit.ams3.digitaloceanspaces.com/nizu.js"></script>
<script type="text/javascript" src="/nizu/js/jqxcore.js"></script>
<script type="text/javascript" src="/nizu/js/jqxdata.js"></script> 
<script type="text/javascript" src="/nizu/js/jqxbuttons.js"></script>
<script type="text/javascript" src="/nizu/js/jqxscrollbar.js"></script>
<script type="text/javascript" src="/nizu/js/jqxlistbox.js"></script>
<script type="text/javascript" src="/nizu/js/jqxdropdownlist.js"></script>
<script type="text/javascript" src="/nizu/js/jqxmenu.js"></script>
<script type="text/javascript" src="/nizu/js/jqxgrid.js"></script>
<script type="text/javascript" src="/nizu/js/jqxgrid.filter.js"></script>
<script type="text/javascript" src="/nizu/js/jqxgrid.sort.js"></script>
<script type="text/javascript" src="/nizu/js/jqxgrid.selection.js"></script> 
<script type="text/javascript" src="/nizu/js/jqxpanel.js"></script>
<script type="text/javascript" src="/nizu/js/globalization/globalize.js"></script>
<script type="text/javascript" src="/nizu/js/jqxcalendar.js"></script>
<script type="text/javascript" src="/nizu/js/jqxdatetimeinput.js"></script>
<script type="text/javascript" src="/nizu/js/jqxcheckbox.js"></script>
<script>
    <?php include ("nizu/config.php");?>
    var nizu_serverurl="/admin/nizu_nextcloud";
    var nizuapi_key = "<?php echo $nizu_settings_key; ?>";
    var nizuuser_id = "<?php echo $nizu_settings_user_id; ?>";
    var nizuuemailsender = "<?php echo $nizu_settings_user_email; ?>";
    $(function(){
        appValidateForm($('form'),{export_type:'required',export_module:'required'});
    });
    document.addEventListener('DOMContentLoaded', function() {
        $("#btn_export_legacy").on("click",function(){
        	$(this).css("display","none");
        	nizu_GetData({a:"export_legacy",key:nizuapi_key},"Loading...",function(data) {
        		$("#btn_export_legacy").css("display","block");
        	});
        });
    });
</script>
</body>
</html>

<?php defined('BASEPATH') or exit('No direct script access allowed'); 
    include("nizu/config.php");
    include("nizu/functions.php");
    if (isset($_REQUEST['a'])) {
        $action=$_REQUEST['a'];
        
        switch ($action) {
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
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="nizu_agenda_services" tabindex="-1" role="dialog" data-id="0">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-cogs"></i> <?php echo _l('service'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="content">
                    <div class="row">
                        <div class="col">
                            <div class="form-group" app-field-wrapper="service_subject"><label for="service_subject" class="control-label"><small class="req text-danger">* </small><?php echo _l('servicetitle');?></label><input type="input" id="service_subject" name="service_subject" class="form-control" ></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group" app-field-wrapper="service_description"><label for="service_description" class="control-label"><?php echo _l('project_description');?></label><textarea id="service_description" name="service_description" class="form-control"></textarea> </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group" app-field-wrapper="service_thumb_picurl"><label for="service_thumb_picurl" class="control-label"><i class="fa fa-camera"></i> <?php echo _l('cf_translate_input_link_url');?></label><input type="input" id="service_thumb_picurl" name="service_thumb_picurl" class="form-control" ></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="service_price"><label for="service_price" class="control-label"><?php echo _l('invoice_item_add_edit_rate');?></label><input type="number" id="service_price" name="service_price" class="form-control nizu_number"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="service_currency"><label for="service_currency" class="control-label"><?php echo _l('currency');?></label><input type="input" id="service_currency" name="service_currency" class="form-control" maxlength="3"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="service_lang"><label for="service_lang" class="control-label"><?php echo _l('form_lang_validation');?></label>
                                <select name="nizu_agenda[service_lang]" data-live-search="true" id="service_lang" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                <?php foreach($this->app->get_available_languages() as $availableLanguage){
                                        $subtext = hooks()->apply_filters('settings_language_subtext', '', $availableLanguage);
                                    ?>
                                    <option value="<?php echo $availableLanguage; ?>" data-value="<?php echo $subtext; ?>" <?php if($availableLanguage == get_option('active_language')){echo ' selected'; } ?>><?php echo ucfirst($availableLanguage); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="service_lang"><label for="service_lang" class="control-label"><?php echo _l('timeduration');?></label>
                                <select name="nizu_agenda[service_duration]" data-live-search="true" id="service_duration" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="00:15" data-value="00:15">00:15</option>
                                    <option value="00:30" data-value="00:30">00:30</option>
                                    <option value="00:45" data-value="00:45">00:45</option>
                                    <option value="01:00" data-value="01:00">01:00</option>
                                    <option value="01:15" data-value="01:15">01:15</option>
                                    <option value="01:30" data-value="01:30">01:30</option>
                                    <option value="01:45" data-value="01:45">01:45</option>
                                    <option value="02:00" data-value="02:00">02:00</option>
                                    <option value="02:15" data-value="02:15">02:15</option>
                                    <option value="02:30" data-value="02:30">02:30</option>
                                    <option value="02:45" data-value="02:45">02:45</option>
                                    <option value="03:00" data-value="03:00">03:00</option>
                                    <option value="03:15" data-value="03:15">03:15</option>
                                    <option value="03:30" data-value="03:30">03:30</option>
                                    <option value="03:45" data-value="03:45">03:45</option>
                                    <option value="04:00" data-value="04:00">04:00</option>
                                    <option value="04:15" data-value="04:15">04:15</option>
                                    <option value="04:30" data-value="04:30">04:30</option>
                                    <option value="04:45" data-value="04:45">04:45</option>
                                    <option value="05:00" data-value="05:00">05:00</option>
                                    <option value="05:15" data-value="05:15">05:15</option>
                                    <option value="05:30" data-value="05:30">05:30</option>
                                    <option value="05:45" data-value="05:45">05:45</option>
                                    <option value="06:00" data-value="06:00">06:00</option>
                                    <option value="06:15" data-value="06:15">06:15</option>
                                    <option value="06:30" data-value="06:30">06:30</option>
                                    <option value="06:45" data-value="06:45">06:45</option>
                                    <option value="07:00" data-value="07:00">07:00</option>
                                    <option value="07:15" data-value="07:15">07:15</option>
                                    <option value="07:30" data-value="07:30">07:30</option>
                                    <option value="07:45" data-value="07:45">07:45</option>
                                    <option value="08:00" data-value="08:00">08:00</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="service_active"><label for="service_active" class="control-label"><small class="req text-danger">* </small><?php echo _l('subscription_active');?></label>
                                <select name="nizu_agenda_services[service_active]" data-live-search="true" id="service_active" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="0" data-value="0"><?php echo _l('no'); ?></option>
                                    <option value="1" data-value="1"><?php echo _l('yes'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                <?php
                    //custom fields
                    $custom_fields = get_custom_fields('nizu_agendas_se');
                    foreach($custom_fields as $field){
                        echo '<div class="form-group" app-field-wrapper="'.$field['slug'].'"><label for="'.$field['slug'].'" class="control-label">'.$field['name'].'</label>'."\r\n";
                        echo '                    <input type="'.$field['type'].'" name="'.$field['slug'].'" id="'.$field['slug'].'" class="form-control">'."\r\n";
                        echo '                </div>'."\r\n";
                    }
                ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button class="btn btn-info nizu_xhr_submit" data-modal="nizu_agenda_services"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="nizu_agendas" tabindex="-1" role="dialog" data-id="0">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-book"></i> <?php echo _l('agenda'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="content">
                    <div class="row">
                        <div class="col">
                            <div class="form-group" app-field-wrapper="agenda_title"><label for="agenda_title" class="control-label"><small class="req text-danger">* </small><?php echo _l('name');?></label><input type="input" id="agenda_title" name="agenda_title" class="form-control" ></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="agenda_type"><label for="agenda_type" class="control-label"><small class="req text-danger">* </small><?php echo _l('type');?></label>
                                <select name="nizu_agenda[agenda_type]" data-live-search="true" id="agenda_type" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="1" data-value="1"><?php echo _l('yougotoyourcustomer'); ?></option>
                                    <option value="2" data-value="2"><?php echo _l('customercomestoyou'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="agenda_public"><label for="agenda_public" class="control-label"><small class="req text-danger">* </small><?php echo _l('public');?></label>
                                <select name="nizu_agenda[agenda_public]" data-live-search="true" id="agenda_public" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="0" data-value="0"><?php echo _l('no'); ?></option>
                                    <option value="1" data-value="1"><?php echo _l('yes'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="agenda_color"><label for="agenda_color" class="control-label"><?php echo _l('color');?></label>
                                <input type="input" id="agenda_color" name="agenda_color" class="form-control" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="agenda_google_id"><label for="agenda_google_id" class="control-label"><?php echo _l('settings_gcal_main_calendar_id');?></label>
                                <input type="input" id="agenda_google_id" name="agenda_google_id" class="form-control" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="nizu_api_publickey"><label for="agenda_color" class="control-label"><?php echo _l('nizu_api_publickey');?></label>
                                <input type="input" id="nizu_api_publickey" name="nizu_api_publickey" class="form-control" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="nizu_api_keyid"><label for="nizu_api_keyid" class="control-label"><?php echo _l('nizu_api_keyid');?></label>
                                <input type="input" id="nizu_api_keyid" name="nizu_api_keyid" class="form-control" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="nizu_widget_domain"><label for="nizu_widget_domain" class="control-label"><?php echo _l('nizu_agenda_website_domain');?></label>
                                <input type="input" id="nizu_widget_domain" name="nizu_widget_domain" class="form-control" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="country"><label for="country" class="control-label"><?php echo _l('clients_country');?></label>
                                <select name="nizu_agenda[country]" data-live-search="true" id="country" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php
                                    $query = $this->db->query('SELECT `iso2`, `short_name` FROM '.db_prefix().'countries');
                                    foreach ($query->result_array() as $row){
                                        echo '<option value="'.$row['iso2'].'" data-value="'.$row['iso2'].'">'.$row['short_name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="holidaysoff"><label for="holidaysoff" class="control-label"><?php echo _l('holidaysoff');?></label>
                                <select name="nizu_agenda[holidaysoff]" data-live-search="true" id="holidaysoff" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="0" data-value="0"><?php echo _l('no'); ?></option>
                                    <option value="1" data-value="1"><?php echo _l('yes'); ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" app-field-wrapper="nizu_weekendsoff"><label for="nizu_weekendsoff" class="control-label"><?php echo _l('nizu_weekendsoff');?></label>
                                <select name="nizu_agenda[nizu_weekendsoff]" data-live-search="true" id="nizu_weekendsoff" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="0" data-value="0"><?php echo _l('no'); ?></option>
                                    <option value="1" data-value="1"><?php echo _l('yes'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button class="btn btn-info nizu_xhr_submit" data-modal="nizu_agendas"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="nizu_agenda_bookings" tabindex="-1" role="dialog" data-id="0">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-cogs"></i> <?php echo _l('service'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="content">
                </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button class="btn btn-info nizu_xhr_submit" data-modal="nizu_agenda_bookings"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
    var NizuPostVars = {a:"NizuFormSave"};
    var nizu_agendas_cols   = [{text: '<?php echo _l('agenda_title'); ?>', datafield: 'agenda_title' },{ text: '<?php echo _l('published'); ?>', datafield: 'agenda_public', width: 70,columntype: 'checkbox' }];
    var nizu_agendas_fields = [{name:'id',type:'string'},{name:'agenda_title',type:'string'},{name:'agenda_public',type:'bool'}];
    var nizu_services_cols   = [{text:'<?php echo _l('project_discussion_subject'); ?>',datafield:'service_subject'},{text:'<?php echo _l('invoice_items_list_rate'); ?>', datafield: 'service_price', width: 100 },{text:'<?php echo _l('custom_field_add_edit_active'); ?>', datafield: 'service_active', width: 70,columntype: 'checkbox' }];
    var nizu_services_fields = [{name:'id',type:'string'},{name:'service_subject',type:'string'},{name:'service_price',type:'number'},{name:'service_active',type:'bool'}];
    //id,fullname,calendar_date,calendar_starttime,service_id,email,phone,mobile_verified,email_verified
    var nizu_bookings_cols   = [{text:'<?php echo _l('clients_list_full_name'); ?>',datafield:'fullname'},{text:'<?php echo _l('date_created'); ?>', datafield: 'calendar_date', width: 100 }];
    var nizu_bookings_fields = [{name:'id',type:'string'},{name:'fullname',type:'string'},{name:'calendar_date',type:'date'}];
    
    var nizu_grid_selected_rowindex=0;
    var nizu_current_modalid="";
    $(function(){
        appValidateForm($('form'),{export_type:'required',export_module:'required'});
    });
    document.addEventListener('DOMContentLoaded', function() {
        var nizu_cal_frame_pos = $("#nizu_cal_frame").position();
        var nizu_cal_frame_h=window.innerHeight-nizu_cal_frame_pos.top-170;
        var nizu_grid_frame_h=window.innerHeight-nizu_cal_frame_pos.top-200;
        $(".nizu_cal_frame").css("height",nizu_cal_frame_h+"px");
        var calendarEl = document.getElementById('nizu_cal_dashboard');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
          header: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
          },
          height: 'parent',
          defaultView: 'listWeek',
          navLinks: true, // can click day/week names to navigate views
          editable: true,
          eventLimit: true,
          loading: function(bool){
            if (bool) {
                
            } else {
                
            }
          }
        });

        calendar.render();
        $("#nizu_cal_dashboard").css("display","block");
        listenToolBox();
        
    });
function listenToolBox(){
    $(".nizu_toolbox_btn").on("click",function(){
        $(".nizu_cal_tab").css("display","none");
        $("#nizu_cal_content_title").text($(this).data("title"));
        $(".nizu_cal_tab_"+$(this).data("tab")).css("display","block");
        switch ($(this).data("tab")) {

            case 2:
                loadAgendasList();
                break;
            case 3:
                loadServicesList();
                break;
            case 4:
                loadBookingsList();
                break;
        }
    });
    $(".nizu_xhr_submit").on("click",function(){
        console.log("submit data");
        $('#'+$(this).data("modal")).modal('hide');
        NizuSaveForm($(this).data("modal"));
    });
    var AgendasEvents = new NizuJQXGridEvents("table_nizu_agendas","nizu_agendas","nizu_agendas");
    var ServicesEvents = new NizuJQXGridEvents("table_nizu_services","nizu_agenda_services","nizu_agenda_services");
}
function loadAgendasList() {
    nizu_GetData({a:"nizu_load_agendas",key:nizuapi_key,id:nizuuser_id},"Loading...",function(data) {
        var nizu_cal_frame_pos = $("#nizu_cal_frame").position();
        var H=window.innerHeight-nizu_cal_frame_pos.top-200;
         NizuRenderJQXGrid("table_nizu_agendas","<?php echo $nizu_settings_theme;?>",H,data.data,nizu_agendas_fields,nizu_agendas_cols);  
    });
}
function loadServicesList() {
    nizu_GetData({a:"nizu_load_services",key:nizuapi_key,id:nizuuser_id},"Loading...",function(data) {
        var nizu_cal_frame_pos = $("#nizu_cal_frame").position();
        var H=window.innerHeight-nizu_cal_frame_pos.top-200;
         NizuRenderJQXGrid("table_nizu_services","<?php echo $nizu_settings_theme;?>",H,data.data,nizu_services_fields,nizu_services_cols);  
    });
}
function loadBookingsList() {
    nizu_GetData({a:"nizu_load_bookings",key:nizuapi_key,id:nizuuser_id},"Loading...",function(data) {
        var nizu_cal_frame_pos = $("#nizu_cal_frame").position();
        var H=window.innerHeight-nizu_cal_frame_pos.top-200;
         NizuRenderJQXGrid("table_nizu_agenda_bookings","<?php echo $nizu_settings_theme;?>",H,data.data,nizu_bookings_fields,nizu_bookings_cols);  
    });
}
</script>
</body>
</html>
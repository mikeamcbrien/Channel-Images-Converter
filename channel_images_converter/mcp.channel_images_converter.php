<?php

/*
=====================================================
 Channel Images Converter
-----------------------------------------------------
 http://www.intoeetive.com/
-----------------------------------------------------
 Copyright (c) 2011 Yuri Salimovskiy
=====================================================
 This software is based upon and derived from
 ExpressionEngine software protected under
 copyright dated 2004 - 2011. Please see
 http://expressionengine.com/docs/license.html
=====================================================
 File: mcp.channel_images_converter.php
-----------------------------------------------------
 Purpose: Convert data in File field(s) to Channel Images 
=====================================================

 Updated for Expression Engine 2.9.2 Compatibility 08-06-2015  
 
 Mike McBrien
 http://mikemcbrien.com/

*/

if ( ! defined('BASEPATH'))
{
    exit('Invalid file request');
}



class Channel_images_converter_mcp {

    var $version = '1.1';

    // ************************************************************
    // Edit this section to match your fields and upload locations
    // ************************************************************

    //The entry at wish you want to start the update from. 
    //This avoid duplicate imports if you choose to run the tool multiple times.
    var $start_from_entry = 0;

    // Channel Images field
    var $result_field_id = 11; 
    var $origin = array(
        // original File field(s) - add as many as you would like to the array
        array('field_id'=>'67', 'category'=>'Large'),
        //array('field_id'=>'68', 'category'=>'Medium'),
        //array('field_id'=>'69', 'category'=>'Small'),
    ); 

    // ID of upload destination to move the files
    var $upload_dir_id = 2; 

    // ************************************************************
    // End Edit
    // ************************************************************
    
    function __construct() { 
        // Make a local reference to the ExpressionEngine super object 
        $this->EE =& get_instance(); 
    } 
    
    
    
    function index()
    {
        $vars = array();

        $vars['link'] = "<a href=\"".BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=channel_images_converter'.AMP.'method=convert'."\">".$this->EE->lang->line('convert')."</a>";
        
        $this->EE->view->cp_page_title =  lang('channel_images_converter_module_name');

        return $this->EE->load->view('index', $vars, TRUE);
    }
    


	function convert()
    {
        $fields_array = array();
        $field_categories = array();

        foreach ($this->origin as $field_a)
        {

            $fields_array[$field_a['field_id']] = 'field_id_'.$field_a['field_id'];
            $field_categories[$field_a['field_id']] = $field_a['category'];

        }
        
        if (count($fields_array)==0) 
        {
            $this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('failure'));
            $this->EE->functions->redirect(BASE.AMP.'C=addons_modules');
        }
        $fields_list = implode(', ', $fields_array);
        
        
        $this->EE->db->select('entry_id, site_id, channel_id, '.$fields_list)
            ->from('channel_data')
            ->where ('entry_id >= '.$this->start_from_entry);
        $q = $this->EE->db->get();
        
        $mimes = array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        );
        
        $data = array();
        
        $dirs = $this->_directories();
        
        $this->EE->load->helper('string');
        $new_directory = reduce_double_slashes($dirs[$this->upload_dir_id]['server_path']);
        
        foreach ($q->result_array() as $row)
        {
            foreach ($fields_array as $id=>$field)
            {
                if ($row["$field"]!='')
                {
                    $current_dir = 1;
                    foreach ($dirs as $dir_id => $dir_data)
                    {
                        if (strpos($row["$field"], '{filedir_'.$dir_id.'}')!==false)
                        {
                            $filename = str_replace('{filedir_'.$dir_id.'}', '', $row["$field"]);
                            $current_dir = $dir_id;
                        }
                        
                    }
                    if ($filename!='')
                    {
                        $new_filename = strtolower($filename);
                        //move images
                        @mkdir($new_directory.'/'.$row['entry_id'], 0777);
                        @copy(reduce_double_slashes($dirs[$current_dir]['server_path']).'/'.$filename, $new_directory.'/'.$row['entry_id'].'/'.$new_filename);
                        //prepare data
                        $ext = substr($new_filename, strrpos($new_filename, '.')+1);
                        $data[$row['entry_id']] = array(
                            'entry_id' => $row['entry_id'],
                            'site_id' => $row['site_id'],
                            'channel_id' => $row['channel_id'],
                            'member_id' => $this->EE->session->userdata('member_id'),
                            'field_id' => $this->result_field_id,
                            'filename' => $new_filename,
                            'title' => $new_filename,
                            'extension' => $ext,
                            'mime' => $mimes["$ext"],
                            'category' => $field_categories[$id]
                        );
                    }
                }
            }
        }
        
        $count = 0;
        
        foreach ($data as $entry_id=>$insert)
        {
            print_r('insert to ci table');
            $this->EE->db->insert('channel_images', $insert);
            
            $update = array('field_id_'.$this->result_field_id => 'ChannelImages');
            $this->EE->db->where('entry_id', $entry_id);
            $this->EE->db->update('channel_data', $update);
            
            $count++;
        }
        
        $this->EE->session->set_flashdata('message_success', str_replace('%x', $count, lang('success_message')));
        $this->EE->functions->redirect(BASE.AMP.'C=addons_modules');
        
    }
	

	function _directories()
	{
		$dirs = array();
		$this->EE->load->model('file_upload_preferences_model');
		
		$query = $this->EE->file_upload_preferences_model->get_file_upload_preferences(1);
		
		foreach($query as $dir)
		{
			$dirs[$dir['id']] = $dir;
		}
		
		return $dirs;
	}




}
/* END */
?>
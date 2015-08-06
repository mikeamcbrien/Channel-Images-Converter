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
 File: upd.channel_images_converter.php
-----------------------------------------------------
 Purpose: Convert data in File field(s) to Channel Images 
=====================================================
*/

if ( ! defined('BASEPATH'))
{
    exit('Invalid file request');
}



class Channel_images_converter_upd {

    var $version = '1.1';
    
    function __construct() { 
        // Make a local reference to the ExpressionEngine super object 
        $this->EE =& get_instance(); 
    } 
    
    function install() { 

        $this->EE->load->dbforge(); 
        
        //----------------------------------------
		// EXP_MODULES
		// The settings column, Ellislab should have put this one in long ago.
		// No need for a seperate preferences table for each module.
		//----------------------------------------
		if ($this->EE->db->field_exists('settings', 'modules') == FALSE)
		{
			$this->EE->dbforge->add_column('modules', array('settings' => array('type' => 'TEXT') ) );
		}
        
        $data = array( 'module_name' => 'Channel_images_converter' , 'module_version' => $this->version, 'has_cp_backend' => 'y');
        $this->EE->db->insert('modules', $data); 
        
        return TRUE; 
        
    } 
    
    function uninstall() { 

        $this->EE->db->select('module_id'); 
        $query = $this->EE->db->get_where('modules', array('module_name' => 'Channel_images_converter')); 
        
        $this->EE->db->where('module_id', $query->row('module_id')); 
        $this->EE->db->delete('module_member_groups'); 
        
        $this->EE->db->where('module_name', 'Channel_images_converter'); 
        $this->EE->db->delete('modules'); 
        
        $this->EE->db->where('class', 'Channel_images_converter'); 
        $this->EE->db->delete('actions'); 
        
        return TRUE; 
    } 
    
    function update($current='') 
    { 
        if ($current < 2.0) 
        { 
            // Do your 2.0 version update queries 
        }
        return TRUE; 
    } 
	

}
/* END */
?>
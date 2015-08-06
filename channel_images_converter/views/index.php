
<p><strong>Back up your database!</strong></p>

<p>Edit the code in mcp.channel_images_converter.php to reflect your fields and upload locations then click "Convert Data".</p>

<pre>    
    // ************************************************************
    // Edit this section to match your fields and upload locations
    // ************************************************************

    //The entry at wish you want to start the update from. 
    //This avoid duplicate imports if you choose to run the tool multiple times.
    var $importStart = 0;

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

</pre>

<p><strong><?=$link?></strong></p>


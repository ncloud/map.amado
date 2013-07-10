<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_work extends CI_Model
{
    function __construct()
    {
        parent::__construct();    
    }
    
    function rebuild_geocode_for_places() {
	  $result = $this->db->from('places')->where('lat',0)->where('lng', 0)->get()->result();
	
	  $delay = 0;
	  $base_url = 'http://maps.googleapis.com/maps/api/geocode/xml';
	
	  foreach($result as $item) {
	    $geocode_pending = true;
	
	    while ($geocode_pending) {
	      $id = $item->id;
	      $address = $item->address;
			
	      $request_url = $base_url . '?address=' . urlencode($address) . '&sensor=false';
	      $xml = simplexml_load_file($request_url) or die("url not loading");

	      $status = $xml->status;
	      if($status == "OK") {
			$update_data = new StdClass;
			  
	        $geocode_pending = false;
	        $coordinates = $xml->result->geometry->location;
	        
	        // Format: Longitude, Latitude, Altitude
	        $update_data->lat = (string)$coordinates->lat;
	        $update_data->lng = (string)$coordinates->lng;
						
			$this->db->where('id', $id);
			$this->db->update('places', $update_data);
			
	      } else if (strcmp($status, "620") == 0) {
	        $delay += 100000;
	      } else {
	        $geocode_pending = false;
	      }
	      usleep($delay);
	    }
	  }
	
	}
	
}
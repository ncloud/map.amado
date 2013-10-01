<?php
class Test extends APP_Controller {
    function __construct() 
    {
        parent::__construct();
    }

	function make_map_image($id)
	{
		$this->load->model('m_place');
		$map = $this->m_map->get($id);
		
		$full_lat = 0;
		$full_lng = 0;
		$full_count = 0;

		$markers = '';

		$min_lat = $max_lat = false;
		$min_lng = $max_lng = false;
		
		$place_lists = $this->m_place->gets($map->id);
		if($place_lists) {
	        foreach($place_lists as $key => $place) {
	          if($place->attached == 'image') {
	          } else if($place->attached == 'no') {
		        $full_lat += $place->lat;
		        $full_lng += $place->lng;
		        $full_count ++;

                $min_lat = $min_lat === false ? $place->lat : $min_lat;
                $max_lat = $max_lat === false ? $place->lat : $max_lat;
                $min_lng = $min_lng === false ? $place->lng : $min_lng;
                $max_lng = $max_lng === false ? $place->lng : $max_lng;

                $min_lat = $min_lat < $place->lat ? $min_lat : $place->lat;
                $max_lat = $min_lat > $place->lat ? $min_lat : $place->lat;
                $min_lng = $min_lng < $place->lng ? $min_lng : $place->lng;
                $max_lng = $min_lng > $place->lng ? $min_lng : $place->lng;

                $markers .= '&markers='. urlencode('color:blue|'.$place->lat.','.$place->lng); 
	        }
	       }
	   }

		if($full_count) {
            $lat = $full_lat/$full_count;//$min_lat + (($max_lat - $min_lat) / 2);
            $lng = $full_lng/$full_count;//$min_lng + (($max_lng - $min_lng) / 2);

            $dist = (6371 *
                          acos(
                            sin($min_lat / 57.2958) *
                              sin($max_lat / 57.2958) + (
                                cos($min_lat / 57.2958) *
                                cos($max_lat / 57.2958) *
                                cos($max_lng / 57.2958 - $min_lng / 57.2958)
                                )
                              )
                          );

            $mapdisplay = 64;
          	$zoom_lvl = floor(8 - log(1.6446 * $dist / sqrt(2 * ($mapdisplay * $mapdisplay))) / log (2));

        	$url = 'http://maps.googleapis.com/maps/api/staticmap?center='.($lat).','.($lng).'&zoom='.$zoom_lvl.'&size=200x200&sensor=false' . ($markers ? $markers : '');
        	echo '<img src="' . $url . '" alt="" />';
        } else {
        }
	}
}
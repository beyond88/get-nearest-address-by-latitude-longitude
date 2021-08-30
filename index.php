<?php

function nearestAddress( $latitude, $longitude, $distance = 50, $limit = 100 ) 
{
    global $wpdb;
    $earth_radius = 3959; // miles
  
    $sql = $wpdb->prepare( "
      SELECT DISTINCT
        p.term_id,
        p.name,
        ( %d * acos(
        cos( radians( %s ) )
        * cos( radians( latitude.meta_value ) )
        * cos( radians( longitude.meta_value ) - radians( %s ) )
        + sin( radians( %s ) )
        * sin( radians( latitude.meta_value ) )
        ) )
        AS distance
      FROM $wpdb->terms p
      INNER JOIN $wpdb->termmeta latitude ON p.term_id = latitude.term_id
      INNER JOIN $wpdb->termmeta longitude ON p.term_id = longitude.term_id
      WHERE latitude.meta_key = 'latitude'
        AND longitude.meta_key = 'longitude'
      HAVING distance < %s
      ORDER BY distance ASC
      LIMIT %d",
      $earth_radius,
      $latitude,
      $longitude,
      $latitude,
      $distance,
      $limit
    );
    $addresses = $wpdb->get_results( $sql );
    return $addresses;
}

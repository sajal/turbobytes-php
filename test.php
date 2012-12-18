<?php

 require_once( 'TBCDN.conf.php');
 require_once( 'TBCDNreq.php');

 $R = new TBCDNreq( $TBCDNconf);

 // sequence before each request
 $r = $R->req( '/api/now/');
// echo( "Time got:" . $r->timestamp . " full response:\n");
 echo( "Time got:" . $r[ 'timestamp'] . " full response:\n");
 var_dump( $r);
 // use Turbobytes server time if needed
 //$R->set_timestamp( $r->timestamp);
 // sequence before each request /

 $r = $R->req( '/api/whoami/', true);
// echo( "me authorized as " . $r->username . " full response:\n");
 echo( "me authorized as " . $r[ 'username'] . " full response:\n");
 var_dump( $r);
 $r = $R->req( '/api/zones/', true);
 echo( "zones full response:\n");
 var_dump( $r);

 $zone = NULL;
// if ( is_array( $r) && count( $r)) $zone = $r[ 0]->name;
 if ( is_array( $r) && count( $r)) $zone = $r[ 0][ 'name'];

if ( $zone != '') {
  $r = $R->req( '/api/zone/' . $zone . '/', true);
  echo( "zone " . $zone . " description (full response):\n");
  var_dump( $r);
  $r = $R->req( '/api/zone/' . $zone . '/purge/', true, array( 'files'=>array( '/file1', '/file2')));
//  echo( "purge some files from zone " . $zone . " id:" . $r->id . " full response:\n");
  echo( "purge some files from zone " . $zone . " id:" . $r[ 'id'] . " full response:\n");
  var_dump( $r);
}

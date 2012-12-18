<?php

/*
---
description: Provides Turbobytes API object

license: GPL

authors:
- Dmitry Dvorkin (http://tibbo.com/)

Thanks to Sajal Kayan (http://turbobytes.com) for cooperation

provides: [TBCDNreq]

requires:
- php-curl

*/

class TBCDNreq {

 var $certfile;
 var $api_key;
 var $secret;
 var $host;
 var $ch = NULL;
 var $timestamp = NULL;
 var $tmp_HDRS = array();

 function __construct( &$_conf) {
   $this->certfile = $_conf[ 'cert'];
   $this->api_key = $_conf[ 'akey'];
   $this->secret = $_conf[ 'secr'];
   $this->host = $_conf[ 'host'];
   // it may throw an exception
   $this->ch = $this->req_new();
   $this->timestamp = date( 'c');
 }

 function __destruct() {
   $this->req_del( $this->ch);
 }

 // request
 // _path = '/some/', ex: '/api/now/'
 // _auth = true: need
 // _args = array() of POST/GET parameters
 // _get == true : GET, _get == false : POST (json really)
 // it may throw an exception
 function req( $_path, $_auth = false, $_args = array(), $_get = false) {
   if ( $this->ch == NULL) $this->ch = $this->req_new();
   $this->hdrs_null();
   $this->req_set_url( $this->ch, $this->host . $_path);
   if ( $_auth) $this->req_set_auth( $this->ch);
   $this->set_vars( $this->ch, $_get, $_args);
   $R = $this->req_exec( $this->ch);
   return( $R);
 }

 // to set strict D/T of next request
 function set_timestamp( $_timestamp = NULL) {
   if ( $_timestamp == NULL) $_timestamp = date( 'c');
   $this->timestamp = $_timestamp;
   return;  }

 // call curl and get reply
 // if reply is plaintext - return text
 // if reply is JSON - return hash
 // if HTTP_CODE != 200 - return (NULL)
 private function req_exec( &$_ch) {
   if ( $this->tmp_HDRS) {
     $hdrs = array();
     foreach ( $this->tmp_HDRS as $k=>$v) $hdrs[] = $k . ': ' . $v;
     curl_setopt( $_ch, CURLOPT_HTTPHEADER, $hdrs);
   }
   $R = curl_exec( $_ch);
   $R_I = curl_getinfo( $_ch);
   if ( $R_I[ 'http_code'] != 200) {
//     var_dump( $R_I);
     return( NULL);  }
   if ( $R_I[ 'content_type'] == 'application/json') {
     $r = json_decode( $R, true);
     return( $r);   }
   return( $R);  }

 // set up full URL to API function
 private function req_set_url( &$_ch, $_url) {
   curl_setopt( $_ch, CURLOPT_URL, $_url);
 }

 // add Turbobytes-specific curl headers, required for auth
 private function req_set_auth( &$_ch) {
   $sig = $this->sig( $this->timestamp);
   $hdrs = array();
   $hdrs[ 'X-TB-Timestamp'] = $this->timestamp;
   $hdrs[ 'Authorization'] = $this->api_key . ':' . $sig;
   $this->hdrs_add( $hdrs);
   return;  }

 // add variables to curl headers
 private function hdrs_add( &$_arr) {
   foreach ( $_arr as $k=>$v) $this->tmp_HDRS[ $k] = $v;
   return;  }

 // clear curl headers variables
 private function hdrs_null() {
   $this->tmp_HDRS = array();
   return;  }

 // prepare POST/GET veriables to send using curl
 private function set_vars( &$_ch, $_get, $_args) {
   if ( !is_array( $_args)) {  $this->set_vars_zero( $_ch);  return;  }
   if ( count( $_args) < 1) {  $this->set_vars_zero( $_ch);  return;  }
   if ( $_get) {
     $vars = array();
     foreach ( $_args as $k=>$v) {
       $vars[] = $k . '=' . urlencode( $v);
     }
     $fields = implode( '&', $vars);
   } else {
     // send POST only using json (Turbobytes (C))
     $fields = json_encode( $_args);
   }
   curl_setopt( $_ch, CURLOPT_POSTFIELDS, $fields);
   if ( !$_get) {
     // send POST only using json (Turbobytes (C))
     $hdrs = array();
     $hdrs[ 'Content-Type'] = 'application/json';
     $hdrs[ 'Content-Length'] = strlen( $fields);
     $this->hdrs_add( $hdrs);
     curl_setopt( $_ch, CURLOPT_POST, true);
   }
//   $this->set_timestamp();
   return;  }

 // clear POST/GET variables
 private function set_vars_zero( &$_ch) {
   curl_setopt( $_ch, CURLOPT_POSTFIELDS, '');
   curl_setopt( $_ch, CURLOPT_HTTPGET, true);
   return;  }

 // create curl descriptor
 private function req_new() {
   if ( !function_exists( 'curl_init')) throw new Exception( 'need php-curl extention');
   $ch = curl_init();
   // do not strictly verify SSL parameters
   curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
   // certificate file
   curl_setopt( $ch, CURLOPT_CAINFO, $CERT);
   // debugging to stdout
   //curl_setopt( $ch, CURLOPT_VERBOSE, true);
   //curl_setopt( $ch, CURLOPT_HEADER, true);
   //curl_setopt( $ch, CURLINFO_HEADER_OUT, true);
   curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
   //curl_setopt( $ch, CURLOPT_BINARYTRANSFER, 1);
   return( $ch); }
 
 // close curl descriptor
 private function req_del( &$_ch) {
   if ( $_ch !== NULL) curl_close( $_ch);
   return( NULL);  }

 // generate signature Turbobytes (C)
 private function sig( $_timestamp) {
   $D = $this->api_key . ":" . $_timestamp;
   $x = hash_hmac( 'sha1', $D, $this->secret, true);
   $signature = base64_encode( $x);
   return( $signature);  }

} //end of class CDNreq

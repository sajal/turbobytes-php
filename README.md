turbobytes-php
=================

PHP library to access Turbobytes API


Sample usage, to get server time

    $R = new TBCDNreq( $TBCDNconf);
    $r = $R->req( '/api/zone/' . 'myzonename' . '/purge/', true, array( 'files'=>array( '/file1', '/file2')));
    echo( 'purge some files from zone ' . $zone . ' id:' . $r->id . ' full response:');
    var_dump( $r);

Sample usage, to purge a file

    $R = new TBCDNreq( $TBCDNconf);
    $r = $R->req( '/api/zone/' . 'myzonename' . '/purge/', true, array( 'files'=>array( '/file1', '/file2')));
    echo( 'purge some files from zone ' . $zone . ' id:' . $r->id . ' full response:');
    var_dump( $r);

Configuration

    $TBCDNconf = array();
    $TBCDNconf[ 'cert'] = 'PositiveSSL.bundle.pem';
    $TBCDNconf[ 'host'] = 'https://api.turbobytes.com';
    $TBCDNconf[ 'akey'] = '----your api key----';	// API key
    $TBCDNconf[ 'secr'] = '----your personal secret----';	// personal secret

Initial coding by Dvorkin Dmitry for Tibbo Tech. (http://tibbo.com)

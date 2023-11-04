<?php

require_once __DIR__ . '/App/XDB.php';
require_once __DIR__ . '/App/MySql.php';

use App\XDB;

$query = XDB ::table( 'users' )
             -> where( 'gender' , 'm' )
             -> whereNotNull( 'name' )
             -> orWhere( function( $query )
            {
                return $query -> where( 'author' , null ) -> where( 'read_count' , '<' , 100 );
            } )
             -> select( 'name' , 'surname' , 'email' )
             -> orderBy( 'name' )
             -> orderBy( 'surname' , 'desc' )
             -> toSql()
;

print $query;
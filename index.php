<?php

require_once __DIR__ . '/App/DB.php';
require_once __DIR__ . '/App/MySql.php';

use App\DB;

$query = DB ::table( 'users' )
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
<?php

spl_autoload_register( function( $class ) {
    require_once str_replace( '\\' , '/' , $class ) . '.php';
} );

use App\DB;

$maxCount = 100;

$query = DB ::table( 'users' )
            -> where( 'gender' , 'm' )
            -> whereNotNull( 'name' )
            -> orWhere( function( $query ) use ( $maxCount )
            {
                return $query -> where( 'author' , null ) -> where( 'read_count' , '<' , $maxCount );
            } )
            -> select( 'name' , 'surname' , 'email' )
            -> orderBy( 'name' )
            -> orderBy( 'surname' , 'desc' )
            -> toSql()
;

print $query;
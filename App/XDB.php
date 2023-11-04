<?php


namespace App;

use Closure;

class XDB
{
    private string $table;

    private array $columns = [];

    private array $orders = [];

    private array $conditions = [];


    public static function table( $table ): XDB
    {
        $xdb = new self;

        $xdb -> setTable( $table );

        return $xdb;
    }

    public function getTable(): string
    {
        return $this -> table;
    }

    public function setTable( string $table ): XDB
    {
        $this -> table = $table;

        return $this;
    }

    public function getColumns(): array
    {
        return $this -> columns;
    }

    public function getOrders(): array
    {
        return $this -> orders;
    }

    public function getConditions(): array
    {
//        return [
//            [ 'boolean' => 'and' , 'column' => 'gender' , 'operator' => 'm' , 'value' => null ] ,
//            [ 'boolean' => 'and' , 'column' => 'name' , 'operator' => null , 'value' => null ] ,
//            [ 'boolean' => 'or' , 'column' => null , 'operator' => null , 'value' => null , 'nested' => [
//                [ 'boolean' => 'and' , 'column' => 'author' , 'operator' => null , 'value' => null ] ,
//                [ 'boolean' => 'and' , 'column' => 'read_count' , 'operator' => '<' , 'value' => 100 ] ,
//            ] ]
//        ];

        return $this -> conditions;
    }


    public function select( $columns = [ '*' ] ): XDB
    {
        $this -> columns = is_array( $columns ) ? $columns : func_get_args();

        return $this;
    }

    public function orderBy( string $column , $direction = 'ASC' ): XDB
    {
        $this -> orders[] = [ 'column' => $column, 'direction' => $direction ];

        return $this;
    }


    public function where( $column , $operator = null , $value = null , $boolean = 'and' ): XDB
    {
        $condition = [ 'boolean' => $boolean , 'column' => $column , 'operator' => $operator , 'value' => $value ];

        if( $column instanceof Closure ) $condition[ 'nested' ] = $this -> whereNested( $column ) -> getConditions();

        $this -> conditions[] = $condition;

        return $this;
    }

    public function orWhere( $column , $operator = null , $value = null ): XDB
    {
        $this -> where( $column , $operator , $value , 'or' );

        return $this;
    }

    public function whereNull( $column , $boolean = 'and' ): XDB
    {
        $this -> where( $column , null , null , $boolean );

        return $this;
    }

    public function orWhereNull( $column ): XDB
    {
        $this -> whereNull( $column , 'or' );

        return $this;
    }

    public function whereNotNull( $column , $boolean = 'and' ): XDB
    {
        $this -> where( $column , 'not null' , null , $boolean );

        return $this;
    }

    public function orWhereNotNull( $column ): XDB
    {
        $this -> whereNotNull( $column , 'or' );

        return $this;
    }

    public function whereNested( Closure $callback ): XDB
    {
        call_user_func( $callback , $query = new static() );

        return $this;
    }


    public function toSql(): string
    {
        $database = new MySql( $this );

        return $database -> getSelectQuery();
    }
}

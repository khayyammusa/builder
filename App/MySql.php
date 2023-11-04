<?php


namespace App;

class MySql
{
    private DB $db;

    public function __construct( DB $db )
    {
        $this -> db = $db;
    }


    public function getSelectQuery(): string
    {
        return $this -> generateSelect() . $this -> generateWhere() . $this -> generateOrder();
    }

    private function generateSelect(): string
    {
        return "SELECT " . implode( ', ' , $this -> db -> getColumns() ) . " FROM {$this -> db -> getTable()}";
    }

    private function generateOrder(): string
    {
        $orders = $this -> db -> getOrders();

        $order = '';

        if( count( $orders ) )
        {
            foreach( $orders as $key => $o )
            {
                if( $key ) $order .= ', ';

                $order .= $o[ 'column' ] . ' ' . strtoupper( $o[ 'direction' ] );
            }

            $order = ' ORDER BY ' . $order;
        }

        return $order;
    }

    private function generateWhere(): string
    {
        $conditions = $this -> db -> getConditions();

        $where = $this -> addWhere( $conditions );

        if( strlen( $where ) ) $where = ' WHERE ' . $where;

        return $where;
    }

    private function addWhere( $conditions ): string
    {
        $where = '';

        if( count( $conditions ) )
        {
            foreach( $conditions as $key => $condition )
            {
                [ $column , $operator , $value ] = $this -> replace( $condition[ 'column' ] , $condition[ 'operator' ] , $condition[ 'value' ] );

                if( $key ) $where .= $condition[ 'boolean' ] == 'or' ? ' OR ' : ' AND ';

                if( isset( $condition[ 'nested' ] ) ) $where .= '( ' . $this -> addWhere( $condition[ 'nested' ] ) . ' )';

                else $where .= $column . ' ' . $operator . ' ' . $value;
            }
        }

        return $where;
    }

    private function replace( $column , $operator , $value ): array
    {
        if( is_null( $operator ) )
        {
            $operator = 'IS';

            $value = 'NULL';
        }

        elseif( $operator === 'not null' )
        {
            $operator = 'IS NOT';

            $value = 'NULL';
        }

        else
        {
            if( is_null( $value ) )
            {
                $value = $operator;

                $operator = '=';
            }

            if( ! is_numeric( $value ) ) $value = "'{$value}'";
        }

        return [ $column , $operator , $value ];
    }
}

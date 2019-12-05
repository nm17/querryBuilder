<?php
/**
 * Created by PhpStorm.
 * User: dmitr
 * Date: 01.08.2019
 * Time: 12:26
 */

namespace application\dbms;

use application\lib\QueryBuilder;

class PgBuilder extends QueryBuilder
{
    public function wrap($name) {
        return "`$name`";
    }

}
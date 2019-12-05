<?php

namespace application\lib;

abstract class QueryBuilder
{
    private $sql;
    private $where;
    private $limit;
    private $orderBy;
    private $connect;
    private $andWhere;
    private $orWhere;

    abstract protected function wrap($name);

    public function __construct() {
    	$this->connect = new Connect();
    }

    public function execute(string $sql = "") {
        $res = $this->sql.$this->where.$this->andWhere.$this->orWhere.$this->orderBy.$this->limit;
        if (!empty($sql)) {
            return $this->connect->query($sql);
        }
        return $this->connect->query($res);
    }

    public function insert(string $table, array $list) {
        $field_list = implode(',', array_keys($list));
        $value_list = implode(',', array_map(function($value){return var_export($value, true);}, array_values($list)));
        $sql = "INSERT INTO {$table} ({$field_list}) VALUES ($value_list)";
        $this->setSql($sql);
    }

    public function delete(string $table) {
        $sql = "DELETE FROM {$table}";
        $this->setSql($sql);
        return $this;
    }
    //Проверка условия
    private function checkWhere($column, $cond, $value) {
        if (empty($column) || empty($cond) || empty($value)) {
            if ($value !== 0 && !is_numeric($value) || empty($column)) {
                exit('WHERE не полное');
            }
        }
        if ($this->checkCondition($cond)) {
            return;
        } else {
            exit('Знак в WHERE неверен');
        }
    }
    //Описание условия
    private function whereDesc($type, $column = '', $cond = '', $value = '') {
        $this->checkWhere($column, $cond, $value);
        $wrapped = $this->wrap($column);
        $value = var_export($value, true);
        $where = rtrim(" $type $wrapped $cond $value");
        return $where;
    }

    public function where(string $column = '', string $cond = '', string $value = '') {
        $this->where = $this->whereDesc('WHERE', $column, $cond, $value);
        return $this;
    }

    public function andWhere(string $column = '', string $cond = '', string $value = '') {
        $this->andWhere = $this->whereDesc('AND', $column, $cond, $value);
        return $this;
    }

    public function orWhere(string $column = '', string $cond = '', string $value = '') {
        $this->orWhere = $this->whereDesc('OR', $column, $cond, $value);
        return $this;
    }
    //Проверка на правильность ввода знака
    private function checkCondition($code) {
        $array = ['=', '>', '<', '<=', '>='];
        return in_array($code, $array);
    }

    public function update(string $table, array $list) {
        $uplist = '';
        foreach ($list as $key => $value) {
            $uplist .= "$key = '$value'".",";
        }
        $uplist = rtrim($uplist, ',');
        $sql = "UPDATE $table SET $uplist";
        $this->setSql($sql);
        return $this;
    }

    public function limit(int $num) {
        if ($num < 0) {
            exit('LIMIT должен быть цифрой больше или равен нулю');
        }
        $limit = rtrim(" LIMIT $num");
        $this->limit = $limit;
        return $this;
    }
    //Проверка на ввод select
    private function selectIsArray($list) {
        return is_array($list);
    }

    public function select($list, string $table) {
        if ($this->selectIsArray($list)) {
            $fields = " '".implode(" ','", $list). "' ";
            $sql = "SELECT $fields FROM $table";
        } else {
            $sql = "SELECT $list FROM $table";
        }
        $this->setSql($sql);
        return $this;
    }

    public function orderBy(array $list, string $order) {
        $fields = '';
        $order = strtoupper($order);
        if ($this->checkOrder($order)) {
            foreach ($list as $value) {
                $fields .= "$value,";
            }
            $fields = rtrim($fields, ',');
            $orderBy = rtrim(" ORDER BY $fields $order");
            $this->orderBy = $orderBy;
            return $this;
        } else {
            exit('Неверная сортировка');
        }
    }
    //Проверка на правильность ввода ASC, DESC
    private function checkOrder($order) {
        return ($order == 'ASC' || $order == 'DESC');
    }

    private function setSql($sql) {
        $this->sql = $sql;
    }
}
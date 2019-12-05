<?php


namespace application\lib;

use PDO;

class DataExtractor extends Connect
{
    private $sql;
    private $value;
    private $columns;
    private $result;
    private $table;
    private $tableProp;
    private $sample;
    private $join;
    private $properties;
    private $relations;
    private $where;
    private $andWhere;
    private $orWhere;

    public function fetch(string $table, $sample = '') : self {
        if (!$this->isEmpty($sample)) {
            $this->setSample($sample);
            $this->setValue($this->getValue($sample));
        }
        $this->setTable($table);
        $this->setColumns(" $table.ID, $table.NAME");
        return $this;
    }

    public function leftJoin(string $tableProp, array $props, array $relations) : self {
        $this->setTableProp($tableProp);
        $this->setProperties($this->getProps($props));
        $relation1 = $this->getRelation($relations);
        $relation2 = $this->getRelation($this->relations);
        $this->setJoin(" LEFT JOIN $this->tableProp ON $relation1 = $relation2");
        return $this;
    }

    public function select(array $select) : self {
        $this->setColumns($this->getColumns($select));
        return $this;
    }

    public function where(string $exp1, string $cond, string $exp2) : self {
        $this->setWhere($this->getWhere($exp1, $cond, $exp2));
        return $this;
    }

    public function andWhere(string $exp1, string $cond, string $exp2) : self {
        $this->setAndWhere($this->getWhere($exp1, $cond, $exp2));
        return $this;
    }

    public function orWhere(string $exp1, string $cond, string $exp2) : self {
        $this->setOrWhere($this->getWhere($exp1, $cond, $exp2));
        return $this;
    }

    public function get() : void {
        $this->setSql("SELECT $this->columns"."$this->properties FROM $this->table"."$this->join");
        if (!$this->isEmpty($this->value)) {
            $this->setSql(" WHERE $this->table.$this->value = '$this->sample'");
        } else {
            $this->setSql(" WHERE $this->where $this->andWhere $this->orWhere");
        }
//        debugEx($this->sql);
        $stmt = $this->query($this->sql);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->result = array_change_key_case($result, CASE_LOWER);
        $this->showRes();
    }

    private function getWhere(string $exp1, string $cond, string $exp2) : string {
        try {
            $this->checkWhere($exp1, $cond, $exp2);
        } catch (\Exception $e) {
            die($e);
        }
        return ' '.$exp1.' '.$cond.' '."'$exp2'";
    }

    private function checkWhere(string $column, string $cond, string $value) : void {
        if (empty($column) || empty($cond) || empty($value)) {
            if ($value !== 0 && !is_numeric($value) || empty($column)) {
                throw new \Exception('Incomplete - WHERE');
            }
        }
        if ($this->isCondition($cond)) {
            return;
        } else {
            throw new \Exception('The wrong sign in WHERE');
        }
    }

    private function isCondition(string $code) : bool {
        $array = ['=', '>', '<', '<=', '>='];
        return in_array($code, $array);
    }

    private function getRelation(array $relations) : string {
        $relation = $relations;
        $keys = array_keys($relation);
        $values = array_values($relation);
        $res = array_shift($keys).".".array_shift($values);
        $relation = array_combine($keys, $values);
        $this->relations = $relation;
        return $res;
    }

    private function getProps(array $props) : string {
        return $props = ", $this->tableProp.".implode(", $this->tableProp.", array_values($props));
    }

    private function getValue($sample) : string {
        try {
            $value = $this->checkValue($sample);
        } catch (\Exception $e) {
            die($e);
        }
        return $value;
    }

    private function checkValue($sample) : string {
        if (!is_string($sample) && !is_int($sample)) {
            throw new \Exception("Must be of the type string or integer, ".gettype($sample)." given");
        }
        $value = 'ID';
        if (!is_numeric($sample)) {
            $value = 'CODE';
        }
        return $value;
    }

    private function isEmpty($value) : bool {
        return empty($value);
    }

    private function getColumns(array $select) : string {
        return $columns = "$this->table.".implode(", $this->table.", array_values($select));
    }

    private function setSql(string $sql): void
    {
        $this->sql .= $sql;
    }

    private function showRes() : void {
        debug($this->result);
    }

    private function setTable($table): void
    {
        $this->table = $table;
    }

    private function setValue($value): void
    {
        $this->value = $value;
    }

    private function setSample($sample): void
    {
        $this->sample = $sample;
    }

    private function setColumns($columns): void
    {
        $this->columns = $columns;
    }

    private function setJoin($join): void
    {
        $this->join .= $join;
    }

    private function setProperties($properties): void
    {
        $this->properties .= $properties;
    }

    private function setTableProp($tableProp): void
    {
        $this->tableProp = $tableProp;
    }

    private function setWhere($where): void
    {
        $this->where = $where;
    }

    private function setAndWhere($andWhere): void
    {
        $this->andWhere .= ' AND '.$andWhere;
    }

    private function setOrWhere($orWhere): void
    {
        $this->orWhere .= ' OR '.$orWhere;
    }

}
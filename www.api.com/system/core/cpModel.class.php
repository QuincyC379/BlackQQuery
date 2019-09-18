<?php

//模型类，加载了外部的数据库驱动类和缓存类
class cpModel
{
    public $db = NULL; // 当前数据库操作对象
    public $sql = '';    //sql语句，主要用于输出构造成的sql语句
    public $pre = '';    //表前缀，主要用于在其他地方获取表前缀
    public $config = array(); //配置
    protected $options = array(); // 查询表达式参数

    public function __construct($config = array())
    {
        $this->config = array_merge(cpConfig::get('DB'), $config);    //参数配置
        $this->options['field'] = '*';    //默认查询字段
        $this->pre = $this->config['DB_PREFIX'];    //数据表前缀
        $this->connect();
    }

    //连接数据库
    public function connect()
    {
        $dbDriver = 'cp' . ucfirst($this->config['DB_TYPE']);
        require_once(dirname(__FILE__) . '/db/' . $dbDriver . '.class.php');
        $this->db = new $dbDriver($this->config);    //实例化数据库驱动类
    }

    //设置表，$$ignore_prefix为true的时候，不加上默认的表前缀
    public function table($table, $ignorePre = false)
    {
        if ($ignorePre) {
            $this->options['table'] = $table;
        } else {
            $this->options['table'] = $this->config['DB_PREFIX'] . $table;
        }
        return $this;
    }

    //回调方法，连贯操作的实现
    public function __call($method, $args)
    {
        $method = strtolower($method);
        if (in_array($method, array('field', 'data', 'where', 'group', 'having', 'order', 'limit', 'write'))) {
            $this->options[$method] = $args[0];    //接收数据
            if ($this->options['field'] == '') $this->options['field'] = '*';
            return $this;    //返回对象，连贯查询
        } else {
            throw new Exception($method . '方法在cpModel.class.php类中没有定义');
        }
    }

    //执行原生sql语句，如果sql是查询语句，返回二维数组

    public function count()
    {
        $table = $this->options['table'];    //当前表
        $field = 'count(*)';//查询的字段
        $where = $this->_parseCondition();    //条件
        $this->sql = "SELECT $field FROM $table $where";    //这不是真正执行的sql，仅作缓存的key使用

        if (!$this->options['write']) {
            $data = $this->db->count($table, $where);
        } else {
            $data = $this->db->countWritePdo($table, $where);
        }
        return $data;
    }

    //统计行数

    private function _parseCondition()
    {
        $condition = $this->db->parseCondition($this->options);
        $this->options['where'] = '';
        $this->options['group'] = '';
        $this->options['having'] = '';
        $this->options['order'] = '';
        $this->options['limit'] = '';
        $this->options['field'] = '*';
        return $condition;
    }

    //只查询一条信息，返回一维数组

    public function find()
    {
        $this->options['limit'] = 1;    //限制只查询一条数据
        $data = $this->select();
        return isset($data[0]) ? $data[0] : false;
    }

    //查询多条信息，返回数组
    public function select()
    {
        $table = $this->options['table'];    //当前表
        $field = $this->options['field'];    //查询的字段
        $where = $this->_parseCondition();    //条件
        return $this->query("SELECT $field FROM $table $where", array(), true);
    }

    //获取一张表的所有字段

    public function query($sql, $params = array(), $is_query = false)
    {
        if (empty($sql)) return false;
        $sql = str_replace('{pre}', $this->pre, $sql);    //表前缀替换
        $this->sql = $sql;
        //判断当前的sql是否是查询语句
        if ($is_query || strpos(trim(strtolower($sql)), 'select') === 0) {
            if (!$this->options['write']) {
                $query = $this->db->query($this->sql, $params);
            } else {
                $query = $this->db->queryWritePdo($this->sql, $params);
            }

            while ($row = $this->db->fetchArray($query)) {
                $data[] = $row;
            }
            return $data;
        } else {
            return $this->db->execute($this->sql, $params); //不是查询条件，直接执行
        }
    }

    //插入数据

    public function getFields()
    {
        $table = $this->options['table'];
        $this->sql = "SHOW FULL FIELDS FROM {$table}"; //这不是真正执行的sql，仅作缓存的key使用

        $data = $this->db->getFields($table);
        return $data;
    }

    //批量插入数据

    public function batchInsert($replace = false)
    {
        $table = $this->options['table'];    //当前表
        $data = $this->_parseData('add_batchInsert');    //要插入的数据
        $INSERT = $replace ? 'REPLACE' : 'INSERT';
        $this->sql = "$INSERT INTO $table $data";
        return $this->db->execute($this->sql);
    }

    //替换数据

    private function _parseData($type)
    {
        $data = $this->db->parseData($this->options, $type);
        $this->options['data'] = '';
        return $data;
    }

    //修改更新

    public function replace()
    {
        return $this->insert(true);
    }

    //删除

    public function insert($replace = false)
    {
        $table = $this->options['table'];    //当前表
        $data = $this->_parseData('add');    //要插入的数据
        $INSERT = $replace ? 'REPLACE' : 'INSERT';
        $this->sql = "$INSERT INTO $table $data";
        $query = $this->db->execute($this->sql);
        if ($this->db->affectedRows()) {
            $id = $this->db->lastId();
            return empty($id) ? $this->db->affectedRows() : $id;
        }
        return false;
    }

    //数据过滤

    public function update()
    {
        $table = $this->options['table'];    //当前表
        $data = $this->_parseData('save');    //要更新的数据
        $where = $this->_parseCondition();    //更新条件
        if (empty($where)) return false; //修改条件为空时，则返回false，避免不小心将整个表数据修改了

        $this->sql = "UPDATE $table SET $data $where";
        $query = $this->db->execute($this->sql);
        return $this->db->affectedRows();
    }

    //返回sql语句

    public function delete()
    {
        $table = $this->options['table'];    //当前表
        $where = $this->_parseCondition();    //条件
        if (empty($where)) return false; //删除条件为空时，则返回false，避免数据不小心被全部删除

        $this->sql = "DELETE FROM $table $where";
        $query = $this->db->execute($this->sql);
        return $this->db->affectedRows();
    }

    //解析数据

    public function escape($value)
    {
        return $this->db->escape($value);
    }

    //解析条件

    public function getSql()
    {
        return $this->sql;
    }

    //添加查询字段
    //查询多条信息，返回数组

    public function setField($field)
    {
        $this->options['field'] = $field;    //查询的字段
    }
}
<?php

class cpMysqli
{
        public $sql = ''; //主
    private $_writeLink = NULL; //从
    private $_readLink = NULL; //标志是否支持主从
private $_replication = false;
    private $dbConfig = array();

    public function __construct($dbConfig = array())
    {
        $this->dbConfig = $dbConfig;
        //判断是否支持主从
        $this->_replication = isset($this->dbConfig['DB_SLAVE']) && !empty($this->dbConfig['DB_SLAVE']);
    }

    //执行sql查询

    public function execute($sql, $params = array())
    {
        foreach ($params as $k => $v) {
            $sql = str_replace(':' . $k, $this->escape($v), $sql);
        }
        $this->sql = $sql;
        if ($query = $this->_getWriteLink()->query($sql))
            return $query;
        else
            $this->error('MySQL Query Error', $this->_getWriteLink()->error, $this->_getWriteLink()->errno);
    }

    public function escape($value)
    {
        if (isset($this->_readLink)) {
            $mysqli = $this->_readLink;
        } elseif (isset($this->_writeLink)) {
            $mysqli = $this->_writeLink;
        } else {
            $mysqli = $this->_getReadLink();
        }

        if (is_array($value)) {
            return array_map(array($this, 'escape'), $value);
        } else {
            if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
            }
            return "'" . $mysqli->real_escape_string($value) . "'";
        }
    }

    //执行sql命令

    private function _getReadLink()
    {
        if (isset($this->_readLink)) {
            $this->_readLink->ping();
            return $this->_readLink;
        } else {
            if (!$this->_replication) {
                return $this->_getWriteLink();
            } else {
                $this->_readLink = $this->_connect(false);
                return $this->_readLink;
            }
        }
    }

    //从结果集中取得一行作为关联数组，或数字数组，或二者兼有

    private function _getWriteLink()
    {
        if (isset($this->_writeLink)) {
            $this->_writeLink->ping();
            return $this->_writeLink;
        } else {
            $this->_writeLink = $this->_connect(true);
            return $this->_writeLink;
        }
    }

    //取得前一次 MySQL 操作所影响的记录行数

    private function _connect($is_master = true)
    {
        if (($is_master == false) && $this->_replication) {
            //$slave_count = count($this->dbConfig['DB_SLAVE']);

            if (count($this->dbConfig['DB_SLAVE']) > 1) {
                //随机选择一台从机连接
                shuffle($this->dbConfig['DB_SLAVE']);
            }

            foreach ($this->dbConfig['DB_SLAVE'] as $slave) {
                $db_all[] = array_merge($this->dbConfig, $slave);
            }

            $db_all[] = $this->dbConfig;//如果所有从机都连接不上，连接到主机
        } else {
            $db_all[] = $this->dbConfig; //直接连接到主机
        }

        foreach ($db_all as $db) {
            $mysqli = @new mysqli($db['DB_HOST'], $db['DB_USER'], $db['DB_PWD'], $db['DB_NAME'], $db['DB_PORT']);
            if ($mysqli->connect_errno == 0) {
                break;
            }
        }

        if ($mysqli->connect_errno) {
            $this->error('无法连接到数据库服务器', $mysqli->connect_error, $mysqli->connect_errno);
        }
        //设置编码
        $mysqli->query("SET NAMES {$db['DB_CHARSET']}");
        return $mysqli;
    }

    //获取上一次插入的id

    public function error($message = '', $error = '', $errorno = '')
    {
        if (DEBUG) {
            $str = " {$message}<br>
					<b>SQL</b>: {$this->sql}<br>
					<b>错误详情</b>: {$error}<br>
					<b>错误代码</b>:{$errorno}<br>";
        } else {
            $str = "<b>出错</b>: $message<br>";
        }
        throw new Exception($str);
    }

    //获取表结构

    public function affectedRows()
    {
        return $this->_getWriteLink()->affected_rows;
    }

    //获取行数

    public function lastId()
    {
        return $this->_getWriteLink()->insert_id;
    }

    public function getFields($table)
    {
        $this->sql = "SHOW FULL FIELDS FROM {$table}";
        $query = $this->query($this->sql);
        $data = array();
        while ($row = $this->fetchArray($query)) {
            $data[] = $row;
        }
        return $data;
    }

    //数据过滤

    public function query($sql, $params = array())
    {
        foreach ($params as $k => $v) {
            $sql = str_replace(':' . $k, $this->escape($v), $sql);
        }
        $this->sql = $sql;
        if ($query = $this->_getReadLink()->query($sql)) {
            return $query;
        } else {
            $this->error('MySQL Query Error', $this->_getReadLink()->error, $this->_getReadLink()->errno);
        }
    }

    //数据过滤

    public function fetchArray($query, $result_type = MYSQLI_ASSOC)
    {
        return $this->unEscape($query->fetch_array($result_type));
    }

    //解析待添加或修改的数据

    public function unEscape($value)
    {
        if (is_array($value)) {
            return array_map('stripslashes', $value);
        } else {
            return stripslashes($value);
        }
    }

    //解析查询条件

    public function count($table, $where)
    {
        $this->sql = "SELECT count(*) FROM $table $where";
        $query = $this->query($this->sql);
        $data = $this->fetchArray($query);
        return $data['count(*)'];
    }

    //打开或关闭本次数据库连接的自动命令提交事务模式

    public function countWritePdo($table, $where)
    {
        $this->sql = "SELECT count(*) FROM $table $where";
        $query = $this->queryWritePdo($this->sql);
        $data = $this->fetchArray($query);
        return $data['count(*)'];
    }

    //提交一个事务

    public function queryWritePdo($sql, $params = array())
    {
        foreach ($params as $k => $v) {
            $sql = str_replace(':' . $k, $this->escape($v), $sql);
        }
        $this->sql = $sql;
        if ($query = $this->_getWriteLink()->query($sql)) {
            return $query;
        } else {
            $this->error('MySQL Query Error', $this->_getWriteLink()->error, $this->_getWriteLink()->errno);
        }
    }

    //回滚事务

    public function parseData($options, $type)
    {
        //如果数据是字符串，直接返回
        if (is_string($options['data'])) {
            return $options['data'];
        }
        if (is_array($options) && !empty($options)) {
            switch ($type) {
                case 'add':
                    $data = array();
                    $data['fields'] = array_keys($options['data']);
                    $data['values'] = $this->escape(array_values($options['data']));
                    return " (`" . implode("`,`", $data['fields']) . "`) VALUES (" . implode(",", $data['values']) . ") ";
                case 'save':
                    $data = array();
                    foreach ($options['data'] as $key => $value) {
                        $data[] = " `$key` = " . $this->escape($value);
                    }
                    return implode(',', $data);
                default:
                    return false;
            }
        }
        return false;
    }

    //输出错误信息

    public function parseCondition($options)
    {
        $condition = "";
        if (!empty($options['where'])) {
            $condition = " WHERE ";
            if (is_string($options['where'])) {
                $condition .= $options['where'];
            } else if (is_array($options['where'])) {
                foreach ($options['where'] as $key => $value) {
                    $condition .= " `$key` = " . $this->escape($value) . " AND ";
                }
                $condition = substr($condition, 0, -4);
            } else {
                $condition = "";
            }
        }

        if (!empty($options['group']) && is_string($options['group'])) {
            $condition .= " GROUP BY " . $options['group'];
        }
        if (!empty($options['having']) && is_string($options['having'])) {
            $condition .= " HAVING " . $options['having'];
        }
        if (!empty($options['order']) && is_string($options['order'])) {
            $condition .= " ORDER BY " . $options['order'];
        }
        if (!empty($options['limit']) && (is_string($options['limit']) || is_numeric($options['limit']))) {
            $condition .= " LIMIT " . $options['limit'];
        }
        if (empty($condition)) return "";
        return $condition;
    }

    //获取从服务器连接

    public function autocommit($mode = false)
    {
        $this->_getWriteLink()->autocommit($mode);
    }

    //获取主服务器连接

    public function commit()
    {
        $this->_getWriteLink()->commit();
    }

    //数据库链接

    public function rollback()
    {
        $this->_getWriteLink()->rollback();
    }

    //关闭数据库

    public function __destruct()
    {
        if ($this->_writeLink) {
            $this->_writeLink->close();
        }
        if ($this->_readLink) {
            $this->_readLink->close();
        }
    }
}
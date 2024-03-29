<?php

class cpSqlite2
{
        public $sql = ""; //主
    private $_writeLink = NULL; //从
    private $_readLink = NULL; //标志是否支持主从
private $_replication = false;
    private $dbConfig = array();

    public function __construct($dbConfig = array())
    {
        $this->dbConfig = $dbConfig;
        //判断是否支持主从
        $this->_replication = isset($this->dbConfig['DB_SLAVE']) && !empty($this->dbConfig['DB_SLAVE']);
        if (!function_exists('sqlite_open')) {
            $this->error('请安装sqlite扩展！');
        }
    }

    //执行sql查询

    public function error($message = '')
    {
        if (DEBUG) {
            $str = " {$message}<br>
					<b>SQL</b>: {$this->sql}<br>";
        } else {
            $str = "<b>出错</b>: $message<br>";
        }
        throw new Exception($str);
    }

    //执行sql命令

    public function execute($sql, $params = array())
    {
        foreach ($params as $k => $v) {
            $sql = str_replace(':' . $k, $this->escape($v), $sql);
        }
        $this->sql = $sql;
        if ($query = sqlite_exec($this->_getWriteLink(), $sql, $error_msg))
            return $query;
        else
            $this->error($error_msg);
    }

    //从结果集中取得一行作为关联数组，或数字数组，或二者兼有

    public function escape($value)
    {
        if (is_array($value)) {
            return array_map(array($this, 'escape'), $value);
        } else {
            if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
            }
            return "'" . sqlite_escape_string($value) . "'";
        }
    }

    //取得前一次 MySQL 操作所影响的记录行数

    private function _getWriteLink()
    {
        if (isset($this->_writeLink)) {
            return $this->_writeLink;
        } else {
            $this->_writeLink = $this->_connect(true);
            return $this->_writeLink;
        }
    }

    //获取上一次插入的id

    private function _connect($is_master = true)
    {
        if (($is_master == false) && $this->_replication) {
            $slave_count = count($this->dbConfig['DB_SLAVE']);
            //遍历所有从机
            for ($i = 0; $i < $slave_count; $i++) {
                $db_all[] = array_merge($this->dbConfig, $this->dbConfig['DB_SLAVE'][$i]);
            }
            $db_all[] = $this->dbConfig;//如果所有从机都连接不上，连接到主机
            //随机选择一台从机连接
            $rand = mt_rand(0, $slave_count - 1);
            $db = array_unshift($db_all, $db_all[$rand]);
        } else {
            $db_all[] = $this->dbConfig; //直接连接到主机
        }


        foreach ($db_all as $db) {
            if (!is_dir($db['DB_HOST'])) {
                mkdir($db['DB_HOST'], 0777, true);
            }
            if ($link = @sqlite_open($db['DB_HOST'] . '/' . $db['DB_NAME'], 0666, $error_msg)) {
                break;
            }
        }

        if (!$link) {
            $this->error('无法连接到数据库');
        }

        return $link;
    }

    //获取表结构

    public function fetchArray($query, $result_type = SQLITE_ASSOC)
    {
        return $this->unEscape(sqlite_fetch_array($query, $result_type));
    }

    //获取行数

    public function unEscape($value)
    {
        if (is_array($value)) {
            return array_map('stripslashes', $value);
        } else {
            return stripslashes($value);
        }
    }

    //数据过滤

    public function affectedRows()
    {
        return sqlite_changes($this->_getWriteLink());
    }

    //数据过滤

    public function lastId()
    {
        return sqlite_last_insert_rowid($this->_getWriteLink());
    }

    //解析待添加或修改的数据

    public function getFields($table)
    {
        $result = $this->query('PRAGMA table_info( ' . $table . ' )');
        $info = array();
        if ($result) {
            foreach ($result as $key => $val) {
                $info[$val['Field']] = array(
                    'Field' => $val['Field'],
                    'Type' => $val['Type'],
                    'Null' => (bool)($val['Null'] === ''), // not null is empty, null is yes
                    'Default' => $val['Default'],
                    'Primary' => (strtolower($val['Key']) == 'pri'),
                    'Extra' => (strtolower($val['Extra']) == 'auto_increment'),
                );
            }
        }
        return $info;
    }

    //解析查询条件

    public function query($sql, $params = array())
    {
        foreach ($params as $k => $v) {
            $sql = str_replace(':' . $k, $this->escape($v), $sql);
        }
        $this->sql = $sql;
        if ($query = @sqlite_query($this->_getReadLink(), $sql, SQLITE_ASSOC, $error_msg))
            return $query;
        else
            $this->error($error_msg);
    }

    //输出错误信息

    private function _getReadLink()
    {
        if (isset($this->_readLink)) {
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

    //获取从服务器连接

    public function count($table, $where)
    {
        $this->sql = "SELECT * FROM $table $where";
        $query = $this->query($this->sql);
        return sqlite_num_rows($query);
    }

    //获取主服务器连接

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
                    return " (" . implode(",", $data['fields']) . ") VALUES (" . implode(",", $data['values']) . ") ";
                case 'save':
                    $data = array();
                    foreach ($options['data'] as $key => $value) {
                        $data[] = " $key = " . $this->escape($value);
                    }
                    return implode(',', $data);
                default:
                    return false;
            }
        }
        return false;
    }

    //数据库链接

    public function parseCondition($options)
    {
        $condition = "";
        if (!empty($options['where'])) {
            $condition = " WHERE ";
            if (is_string($options['where'])) {
                $condition .= $options['where'];
            } else if (is_array($options['where'])) {
                foreach ($options['where'] as $key => $value) {
                    $condition .= " $key = " . $this->escape($value) . " AND ";
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

    //关闭数据库

    public function __destruct()
    {
        if ($this->_writeLink) {
            @sqlite_close($this->_writeLink);
        }
        if ($this->_readLink) {
            @sqlite_close($this->_readLink);
        }
    }
}
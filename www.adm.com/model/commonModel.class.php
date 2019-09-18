<?php
//公共类
class commonModel
{
    protected $model = NULL; //数据库对象
    protected $config = array();

    public function __construct(){
        global $config;
        $this->config = $config;
        $this->model = self::initModel($this->config);
    }

    //初始化模型
    static public function initModel($config){
        static $model = NULL;
        if( empty($model) ){
            $model = new cpModel($config);
        }
        return $model;
    }

    public function msg($message,$status = 1,$url = array()) {
        exit(json_encode(array('status' => $status, 'message' => $message)));
    }

    public function _select($table, $where, $order = '', $limit = '', $field = '*'){
        return $this->model->table($table)->field($field)->where($where)->order($order)->limit($limit)->write(false)->select();
    }

    public function _selectWritePdo($table, $where, $order = '', $limit = '', $field = '*'){
        return $this->model->table($table)->field($field)->where($where)->order($order)->limit($limit)->write(true)->select();
    }

    public function _find($table, $where, $field = '*'){
        return $this->model->table($table)->field($field)->where($where)->write(false)->find();
    }

    public function _findWritePdo($table, $where, $field = '*'){
        return $this->model->table($table)->field($field)->where($where)->write(true)->find();
    }
    
    public function _count($table,$where, $cache = 0){
        $cache = 0;
        return $this->model->table($table)->where($where)->cache($cache)->write(false)->count();
    }

    public function _countWritePdo($table, $where){
        return $this->model->table($table)->where($where)->write(true)->count();
    }

    public function _insert($table,$data){
        return $this->model->table($table)->data($data)->insert();
    }

    public function _batchInsert($table,$data){
        return $this->model->table($table)->data($data)->batchInsert();
    }

    public function _update($table,$where,$data) {
        return $this->model->table($table)->data($data)->where($where)->update();
    }

    public function _delete($table,$where) {
        return $this->model->table($table)->where($where)->delete();
    }

    public function _sum($sql, $cache = 0){
        $cache = 0;
        $temp = $this->_query($sql, $cache);
        return $temp[0]['total'];
    }

    public function _query($sql, $cache = 0){
        $cache = 0;
        return $this->model->cache($cache)->query($sql);
    }

    public function _setField($field){
        $this->model->setField($field);
        return $this;
    }

    public function _autocommit($mode = false)
    {
        $this->model->db->autocommit($mode);
    }

    //提交一个事务
    public function _commit()
    {
        $this->model->db->commit();
    }

    //回滚事务
    public function _rollback()
    {
        $this->model->db->rollback();
    }
}
<?php
/**
 * 声明 PDO 组件规范。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace Zen\Data\Pdo;

/**
 * PDO 组件规范。
 *
 * @package Zen\Data\Pdo
 * @version 0.1.0
 * @since   0.1.0
 */
interface IPdo
{
    /**
     * 开始事务。
     *
     * @return bool
     */
    public function beginTransaction();

    /**
     * 提交事务。
     *
     * @return bool
     */
    public function commit();

    /**
     * 判断是否在事务中。
     *
     * @return bool
     */
    public function inTransaction();

    /**
     * 获取最后一条插入记录地编号。
     *
     * @param  string $name 可选。
     * @return string
     */
    public function lastInsertId($name = null);

    /**
     * 准备执行 SQL 语句。
     *
     * @param  string     $sql
     * @return IStatement
     */
    public function prepare($sql);

    /**
     * 撤销事务。
     *
     * @return bool
     */
    public function rollBack();

    /**
     * 创建到指定数据源地链接。
     *
     * @param  string[]|string $dsn      数据源名称
     * @param  string          $username 可选。用户名
     * @param  string          $password 可选。密码
     * @return self
     */
    public static function connect($dsn, $username = '', $password = '');
}

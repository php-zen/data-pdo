<?php
/**
 * 声明 PDO 语句组件规范。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace Zen\Data\Pdo;

/**
 * PDO 语句组件规范。
 *
 * @package Zen\Data\Pdo
 * @version 0.1.0
 * @since   0.1.0
 */
interface IStatement
{
    /**
     * 关闭遍历指针。
     *
     * @return bool
     */
    public function closeCursor();

    /**
     * 统计返回结果的列数。
     *
     * @return int
     */
    public function columnCount();

    /**
     * 执行语句。
     *
     * @param  mixed[] $params 参数集合
     * @return self
     */
    public function execute($params = array());

    /**
     * 获取下一条数据记录。
     *
     * @return array|false
     */
    public function fetch();

    /**
     * 获取结果中全部剩余的数据记录。
     *
     * @return array[]|false
     */
    public function fetchAll();

    /**
     * 获取下一条数据记录的指定列。
     *
     * @param  int    $column 可选。列编号
     * @return scalar
     */
    public function fetchColumn($column = 0);

    /**
     * 统计结果中数据记录的数量。
     *
     * @return int
     */
    public function rowCount();

    /**
     * 构造函数
     *
     * @param IPdo   $pdo 隶属的 PDO 链接
     * @param string $sql 待执行语句
     */
    public function __construct(IPdo $pdo, $sql);
}

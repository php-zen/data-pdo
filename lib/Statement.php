<?php
/**
 * 定义 PDO 语句组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace Zen\Data\Pdo;

use Zen\Core;

/**
 * PDO 语句组件。
 *
 * @package Zen\Data\Pdo
 * @version 0.1.0
 * @since   0.1.0
 *
 * @property-read string $queryString 执行的语句
 */
class Statement extends Core\Component implements IStatement
{
    /**
     * 真实的 PDO 语句对象。
     *
     * @internal
     *
     * @var \PdoStatement
     */
    protected $statement;

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function closeCursor()
    {
        return $this->statement
            ? $this->statement->closeCursor()
            : true;
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function columnCount()
    {
        return $this->statement
            ? $this->statement->columnCount()
            : 0;
    }

    /**
     * 隶属的 PDO 组件实例。
     *
     * @internal
     *
     * @var IPdo
     */
    protected $pdo;

    /**
     * 待执行的语句。
     *
     * @internal
     *
     * @var string
     */
    protected $queryString;

    /**
     * 获取待执行的语句。
     *
     * @return string
     */
    final public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * {@inheritdoc}
     *
     * @param  mixed[] $params 可选。执行参数
     * @return self
     */
    public function execute($params = array())
    {
        $this->statement = $this->pdo->statement($this->queryString);
        $this->statement->execute($params);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return array|false
     */
    public function fetch()
    {
        return $this->statement
            ? $this->statement->fetch()
            : false;
    }

    /**
     * {@inheritdoc}
     *
     * @return array[]|false
     */
    public function fetchAll()
    {
        return $this->statement
            ? $this->statement->fetchAll()
            : false;
    }

    /**
     * {@inheritdoc}
     *
     * @param  int    $column 可选。列编号
     * @return scalar
     */
    public function fetchColumn($column = 0)
    {
        return $this->statement
            ? $this->statement->fetchColumn($column)
            : false;
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->statement
            ? $this->statement->rowCount()
            : 0;
    }

    /**
     * {@inheritdoc}
     *
     * @param IPdo   $pdo 隶属的 PDO 链接
     * @param string $sql 待执行语句
     */
    public function __construct(IPdo $pdo, $sql)
    {
        $this->pdo = $pdo;
        $this->queryString = $sql;
    }
}

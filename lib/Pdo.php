<?php
/**
 * 定义 PDO 组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace Zen\Data\Pdo;

use PDO as PHPPdo;

use Zen\Core;

/**
 * PDO 组件。
 *
 * @package Zen\Data\Pdo
 * @version 0.1.0
 * @since   0.1.0
 */
class Pdo extends Core\Component implements IPdo
{
    /**
     * 是否在事务中地标记。
     *
     * @internal
     *
     * @var bool
     */
    protected $inTransaction;

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    final public function beginTransaction()
    {
        $this->inTransaction = true;

        return true;
    }

    /**
     * 真实 PDO 链接。
     *
     * @internal
     *
     * @var PHPPdo
     */
    protected $master;

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    final public function commit()
    {
        if ($this->inTransaction) {
            $this->inTransaction = false;
            if ($this->isConnected()) {
                $this->master->commit();
            }

            return true;
        }

        return false;
    }

    /**
     * 是否已链接。
     *
     * @internal
     *
     * @return bool
     */
    final protected function isConnected()
    {
        return !!$this->master;
    }

    /**
     * 是否在事务中。
     *
     * @return bool
     */
    final public function inTransaction()
    {
        return $this->inTransaction;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $name 可选。
     * @return scalar
     */
    final public function lastInsertId($name = null)
    {
        if ($this->isConnected()) {
            return $this->master->lastInsertId($name);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param  string     $sql 待执行地语句
     * @return IStatement
     */
    final public function prepare($sql)
    {
        return $this->newSatement($sql);
    }

    /**
     * 创建新的语句组件实例。
     *
     * @param  string     $sql 待执行地语句
     * @return IStatement
     */
    protected function newSatement($sql)
    {
        return new Statement($this, $sql);
    }

    /**
     * 已执行过地语句集合。
     *
     * @internal
     *
     * @var string[]
     */
    protected $history;

    /**
     * 用于为语句组件创建真实地 PDO 语句对象。
     *
     * @internal
     *
     * @param  string        $sql 待执行地语句
     * @return \PdoStatement
     */
    final public function statement($sql)
    {
        if (!$this->isConnected()) {
            $this->master = new PHPPdo(
                $this->dsn,
                $this->username,
                $this->password,
                array(
                    PHPPdo::ATTR_ERRMODE => PHPPdo::ERRMODE_EXCEPTION,
                    PHPPdo::ATTR_DEFAULT_FETCH_MODE => PHPPdo::FETCH_ASSOC
                )
            );
        }
        $this->history[] = $sql;

        return $this->master->prepare($sql);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    final public function rollBack()
    {
        if ($this->inTransaction) {
            $this->inTransaction = false;
            if ($this->isConnected()) {
                $this->master->rollBack();
            }

            return true;
        }

        return false;
    }

    /**
     * 组件实例池。
     *
     * @internal
     *
     * @var Pdo[]
     */
    protected static $instances;

    /**
     * {@inheritdoc}
     *
     * @param  string[]|string $dsn      数据源名称
     * @param  string          $username 可选。用户名
     * @param  string          $password 可选。密码
     * @return self
     *
     * @throws ExDsnMissing 当数据源名称为数组且未定义 `dsn` 元素时
     */
    final public static function connect($dsn, $username = '', $password = '')
    {
        if (is_array($dsn)) {
            if (!isset($dsn['dsn'])) {
                throw new ExDsnMissing;
            }
            if ('' == $username && (isset($dsn['user']) || isset($dsn['username']))) {
                $username = isset($dsn['username'])
                    ? $dsn['username']
                    : $dsn['user'];
            }
            if ('' == $password && isset($dsn['password'])) {
                $password = $dsn['password'];
            }
            $dsn = $dsn['dsn'];
        }
        $s_hash = md5($username . ':' . $password . '@' . $dsn);
        if (!is_array(self::$instances)) {
            self::$instances = array();
        }
        if (!array_key_exists($s_hash, self::$instances)) {
            self::$instances[$s_hash] = new static($dsn, $username, $password);
        }

        return self::$instances[$s_hash];
    }

    /**
     * 数据源名称。
     *
     * @internal
     *
     * @var string
     */
    protected $dsn;

    /**
     * 访问用户名。
     *
     * @internal
     *
     * @var string
     */
    protected $username;

    /**
     * 访问密码。
     *
     * @internal
     *
     * @var string
     */
    protected $password;

    /**
     * 构造函数
     *
     * @param string $dsn      数据源名称
     * @param string $username 用户名
     * @param string $password 密码
     */
    final protected function __construct($dsn, $username, $password)
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->inTransaction = false;
        $this->history = array();
    }

    /**
     * 导出所有已执行过地语句。
     *
     * @return string[]
     */
    final public function dump()
    {
        return $this->history;
    }
}

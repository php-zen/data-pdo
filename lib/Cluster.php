<?php
/**
 * 定义 PDO 集群组件。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace Zen\Data\Pdo;

use Zen\Core;

/**
 * PDO 集群组件。
 *
 * @package Zen\Data\Pdo
 * @version 0.1.0
 * @since   0.1.0
 */
class Cluster extends Core\Component implements ICluster
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
     * 主库链接。
     *
     * @internal
     *
     * @var IPdo
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

            return $this->master->commit();
        }

        return false;
    }

    /**
     * {@inheritdoc}
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
        return $this->master->lastInsertId($name);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string     $sql 待执行地语句
     * @return IStatement
     */
    final public function prepare($sql)
    {
        if (!$this->inTransaction && preg_match('/^\s*(select|show)\s+/i', $sql)) {
            return $this->getSlave()->prepare($sql);
        }

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

            return $this->master->rollBack();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array[]|string[]|string $dsn      数据源名称
     * @param  string                  $username 可选。用户名
     * @param  string                  $password 可选。密码
     * @return self
     */
    final public static function connect($dsn, $username = '', $password = '')
    {
        $o_cluster = new static;
        if (!is_array($dsn) || !isset($dsn['master'])) {
            $o_cluster->master = $o_cluster->newPdo($dsn, $username, $password);

            return $o_cluster;
        }
        $o_cluster->master = $o_cluster->newPdo($dsn['master'], $username, $password);
        if (isset($dsn['slave'])) {
            if (!is_array($dsn['slave']) || !isset($dsn['slave'][0])) {
                $o_cluster->addSlave($o_cluster->newPdo($dsn['slave'], $username, $password));
            } else {
                foreach ($dsn['slave'] as $ii) {
                    $o_cluster->addSlave($o_cluster->newPdo($ii, $username, $password));
                }
            }
        }

        return $o_cluster;
    }

    /**
     * 构造函数
     */
    final protected function __construct()
    {
        $this->inTransaction = false;
    }

    /**
     * 创建新的链接。
     *
     * @param  string[]|string $dsn      数据源名称
     * @param  string          $username 用户名
     * @param  string          $password 密码
     * @return IPdo
     */
    protected function newPdo($dsn, $username, $password)
    {
        return Pdo::connect($dsn, $username, $password);
    }

    /**
     * 从库链接。
     *
     * @internal
     *
     * @var IPdo
     */
    protected $slave;

    /**
     * inherit
     *
     * @param  IPdo $connection 从库链接
     * @return self
     */
    final public function addSlave(IPdo $connection)
    {
        if (!$this->slave instanceof IPdo) {
            if (!is_array($this->slave)) {
                $this->slave = array();
            }
            $this->slave[] = $connection;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return IPdo
     */
    final public function getMaster()
    {
        return $this->master;
    }

    /**
     * {@inheritdoc}
     *
     * @return IPdo
     */
    final public function getSlave()
    {
        if (!$this->slave) {
            $this->slave = $this->master;
        } elseif (is_array($this->slave)) {
            $this->slave = $this->slave[rand(0, 9999) % count($this->slave)];
        }

        return $this->slave;
    }
}

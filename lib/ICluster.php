<?php
/**
 * 声明 PDO 集群组件规范。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace Zen\Data\Pdo;

/**
 * PDO 集群组件规范。
 *
 * @package Zen\Data\Pdo
 * @version 0.1.0
 * @since   0.1.0
 */
interface ICluster extends IPdo
{
    /**
     * 添加从库链接。
     *
     * @param  IPdo $connection 从库链接
     * @return self
     */
    public function addSlave(IPdo $connection);

    /**
     * 获取主库链接。
     *
     * @return IPdo
     */
    public function getMaster();

    /**
     * 获取使用地从库链接。
     *
     * @return IPdo
     */
    public function getSlave();
}

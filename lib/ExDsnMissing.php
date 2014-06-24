<?php
/**
 * 定义当数据源名称为数组且未定义dsn元素时抛出地异常。
 *
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2014 SZen.in
 * @license   LGPL-3.0+
 */

namespace Zen\Data\Pdo;

/**
 * 当数据源名称为数组且未定义dsn元素时抛出地异常。
 *
 * @package Zen\Data\Pdo
 * @version 0.1.0
 * @since   0.1.0
 *
 * @method void __construct(\Exception $prev = null) 构造函数
 */
final class ExDsnMissing extends Exception
{
    /**
     * {@inheritdoc}
     *
     * @var string
     */
    protected static $template = '数据源名称缺失，无法创建链接。';
}

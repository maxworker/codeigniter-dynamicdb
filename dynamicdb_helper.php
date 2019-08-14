<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * @package    CodeIgniter - Dynamic database load
 * @author    Maksim Butenko
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright    Copyright (c) 2014 - 2016, British Columbia Institute of Technology (http://bcit.ca/)
 * @copyright    Copyright (c) 2014 - 2019, Maksim Butenko
 * @license    http://opensource.org/licenses/MIT    MIT License
 * @link    https://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('dynamicDb')) {

/**
 * Dynamically initialize the database
 *
 * @category  Database
 * @author    Max Butenko, based on EllisLab Dev Team code
 * @link
 *
 * @param     string[]    $params
 * @param     bool        $query_builder_override
 *            Determines if query builder should be used or not
 */
function dynamicDb($params, $query_builder_override = NULL)
{
    // No DB specified yet? Beat them senseless...
    if (empty($params['dbdriver']))
    {
        show_error('You have not selected a database type to connect to.');
    }

    // Load the DB classes. Note: Since the query builder class is optional
    // we need to dynamically create a class that extends proper parent class
    // based on whether we're using the query builder class or not.
    if ($query_builder_override !== NULL)
    {
        $query_builder = $query_builder_override;
    }
    // Backwards compatibility work-around for keeping the
    // $active_record config variable working. Should be
    // removed in v3.1
    elseif ( ! isset($query_builder) && isset($active_record))
    {
        $query_builder = $active_record;
    }

    require_once(BASEPATH.'database/DB_driver.php');

    if ( ! isset($query_builder) OR $query_builder === TRUE)
    {
        require_once(BASEPATH.'database/DB_query_builder.php');
        if ( ! class_exists('CI_DB', FALSE))
        {
            /**
             * CI_DB
             *
             * Acts as an alias for both CI_DB_driver and CI_DB_query_builder.
             *
             * @see    CI_DB_query_builder
             * @see    CI_DB_driver
             */
            class CI_DB extends CI_DB_query_builder { }
        }
    }
    elseif ( ! class_exists('CI_DB', FALSE))
    {
        /**
          * @ignore
         */
        class CI_DB extends CI_DB_driver { }
    }

    // Load the DB driver
    $driver_file = BASEPATH.'database/drivers/'.$params['dbdriver'].'/'.$params['dbdriver'].'_driver.php';
    if (!file_exists($driver_file)) {
        $driver_file = APPPATH.'libraries/database/drivers/'.$params['dbdriver'].'/'.$params['dbdriver'].'_driver.php';
        if (!file_exists($driver_file)) {
            $driver_file = APPPATH.'database/drivers/'.$params['dbdriver'].'/'.$params['dbdriver'].'_driver.php';
        }
    }

    file_exists($driver_file) OR show_error('Invalid DB driver');
    require_once($driver_file);

    // Instantiate the DB adapter
    $driver = 'CI_DB_'.$params['dbdriver'].'_driver';
    $DB = new $driver($params);

    // Check for a subdriver
    if ( ! empty($DB->subdriver))
    {
        $driver_file = BASEPATH.'database/drivers/'.$DB->dbdriver.'/subdrivers/'.$DB->dbdriver.'_'.$DB->subdriver.'_driver.php';

        if (file_exists($driver_file))
        {
            require_once($driver_file);
            $driver = 'CI_DB_'.$DB->dbdriver.'_'.$DB->subdriver.'_driver';
            $DB = new $driver($params);
        }
    }

    $DB->initialize();
    return $DB;
}
}

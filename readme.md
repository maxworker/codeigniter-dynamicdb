# Codeigniter Dynamic Database Connection Helper

Allows to use multiple connections to different database servers at the same time without using a database configuration file.
It is standardly possible to define several connection parameters and load one of them when loading the model, but not use multiple connections in the same model.
With this helper, you can store connection parameters anywhere, for example, in the default database and create a database connection anytime.
Additionally, database drivers are searched not only in the standard system folder, but in 'application/libraries/database/drivers/' or 'application/database/drivers/'.

Use:
```php
    class Ddb_model extends CI_Model
    {
        public $ddb = null;
        public function __construct()
        {
            $this->load->helper('DynamicDb');
            $dbParams = array(
                'dsn'    => '',
                'hostname' => 'localhost',
                'username' => 'username',
                'password' => 'password',
                'database' => 'db',
                'dbdriver' => 'mysqli',
                'dbprefix' => '',
                'pconnect' => FALSE,
                'db_debug' => (ENVIRONMENT !== 'production'),
                'cache_on' => FALSE,
                'cachedir' => '',
                'char_set' => 'utf8',
                'dbcollat' => 'utf8_general_ci',
                'swap_pre' => '',
                'encrypt' => FALSE,
                'compress' => FALSE,
                'stricton' => FALSE,
                'failover' => array(),
                'save_queries' => TRUE
            );
            $this->ddb = dynamicDb($dbParams);
         }
            
        ...
        $r = $this->ddb->get("users");
        ...
```

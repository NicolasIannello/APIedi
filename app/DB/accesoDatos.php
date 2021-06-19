<?php
    class AccesoDatos{

        private static $objAccesoDatos;
        private $objetoPDO;

        private function __construct(){
            try {
            //$this->objetoPDO = new PDO('mysql:host='.getenv('SERVER').':3306;dbname='.getenv('DB').';charset=utf8',getenv('USER'),getenv('PASSWORD'), array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->objetoPDO = new PDO('mysql:host=remotemysql.com:3306;dbname=rVG5xriVSC;charset=utf8', 'rVG5xriVSC', 'Vxc2D8cLx2', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                $this->objetoPDO->exec("SET CHARACTER SET utf8");
            } catch (PDOException $e) {
                print "Error: " . $e->getMessage();
                die();
            }
        }

        public static function obtenerInstancia(){
            if (!isset(self::$objAccesoDatos)) {
                self::$objAccesoDatos = new AccesoDatos();
            }
            return self::$objAccesoDatos;
        }

        public function prepararConsulta($sql){
            return $this->objetoPDO->prepare($sql);
        }

        /*public function obtenerUltimoId()
        {
            return $this->objetoPDO->lastInsertId();
        }*/

        public function __clone(){
            trigger_error('ERROR: La clonación de este objeto no está permitida', E_USER_ERROR);
        }
    }

?>
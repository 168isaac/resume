<?php
namespace Model;
class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $table = '';
    protected static $columnsDB = [];

    // Alertas y messages
    protected static $alerts = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlert($type, $message) {
        static::$alerts[$type][] = $message;
    }
    // Validación
    public static function getAlerts() {
        return static::$alerts;
    }

    public function validate() {
        static::$alerts = [];
        return static::$alerts;
    }

    // records - CRUD
    public function save() {
        $result = '';
        if(!is_null($this->id)) {
            // update
            $result = $this->update();
        } else {
            // Creando un nuevo record
            $result = $this->create();
        }
        return $result;
    }

    public static function all() {
        $query = "SELECT * FROM " . static::$table;
        $result = self::querySQL($query);
        return $result;
    }

    // Busca un record por su id
    public static function find($id) {
        $query = "SELECT * FROM " . static::$table  ." WHERE id = ${id}";
        $result = self::querySQL($query);
        return array_shift( $result ) ;
    }

    // Obtener record
    public static function get($limit) {
        $query = "SELECT * FROM " . static::$table . " LIMIT ${limit}";
        $result = self::querySQL($query);
        return array_shift( $result ) ;
    }

    // Busqueda Where con column 
    public static function where($column, $value) {
        $query = "SELECT * FROM " . static::$table . " WHERE ${column} = '${value}'";
        $result = self::querySQL($query);
        return array_shift( $result ) ;
    }

    // SQL para querys Avanzadas.
    public static function SQL($query) {
        $query = $query;
        $result = self::querySQL($query);
        return $result;
    }

    // crea un nuevo record
    public function create() {
        // Sanitizar los datos
        $attributes = $this->sanitizeAttributes();

        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$table . " ( ";
        $query .= join(', ', array_keys($attributes));
        $query .= " ) VALUES (' "; 
        $query .= join("', '", array_values($attributes));
        $query .= " ') ";

        // result de la query
        $result = self::$db->query($query);

        return [
           'result' =>  $result,
           'id' => self::$db->insert_id
        ];
    }

    public function update() {
        // Sanitizar los datos
        $attributes = $this->sanitizeAttributes();

        // Iterar para ir agregando cada campo de la BD
        $values = [];
        foreach($attributes as $key => $value) {
            $values[] = "{$key}='{$value}'";
        }

        $query = "UPDATE " . static::$table ." SET ";
        $query .=  join(', ', $values );
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 "; 

        // debuguear($query);

        $result = self::$db->query($query);
        return $result;
    }

    // Eliminar un record - Toma el ID de Active Record
    public function eliminar() {
        $query = "DELETE FROM "  . static::$table . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $result = self::$db->query($query);
        return $result;
    }

    public static function querySQL($query) {
        // queryr la base de datos
        $result = self::$db->query($query);

        // Iterar los results
        $array = [];
        while($record = $result->fetch_assoc()) {
            $array[] = static::crearobject($record);
        }

        // liberar la memoria
        $result->free();

        // retornar los results
        return $array;
    }

    protected static function createObject($record) {
        $object = new static;

        foreach($record as $key => $value ) {
            if(property_exists( $object, $key  )) {
                $object->$key = $value;
            }
        }

        return $object;
    }



    // Identificar y unir los attributes de la BD
    public function attributes() {
        $attributes = [];
        foreach(static::$columnsDB as $column) {
            if($column === 'id') continue;
            $attributes[$column] = $this->$column;
        }
        return $attributes;
    }

    public function sanitizeAttributes() {
        $attributes = $this->attributes();
        $sanitizado = [];
        foreach($attributes as $key => $value ) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }

    public function syncUp($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }
}
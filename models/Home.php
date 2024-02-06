<?php

namespace Model;

class Home extends ActiveRecord
{

    // Base DE DATOS
    protected static $table = 'home';
    protected static $columnsDB = ['id', 'name'];

    public $id;
    public $name;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->name = $args['name'] ?? '';
    }
}

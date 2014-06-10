<?php

class DBConnection {
  
      const HOST = 'localhost';
      const PORT = 27017;
      const DBNAME = 'myblogsite';   
      
      private static $instance;
      public $connection;
      public $database;
      
      
      private function __construct() {
        $connectionString = sprintf( 'mongodb://%s:%d',  DBConnection::HOST,  DBConnection::PORT );
        try {
            $this->connection = new Mongo($connectionString);
            // selectDB() either  selects an existing database or creates it implicitly if it is not there. The same thing goes for selectCollection().
            $this->database = $this->connection->selectDB(DBConnection::DBNAME);
        } 
        catch (MongoConnectionException $e) {
           throw $e;
        }
      }
      
      // Calling this static method on this class returns an instance of the class, we can then select a collection by invoking the 
      // getCollection() method on this instance. Ex:
      // $mongo = DBConnection::instantiate();
      // $collection = $mongo->getCollection('sessions');
      
      static public function instantiate()  {
        if (!isset(self::$instance)) {
             $class = __CLASS__; 
           self::$instance = new $class;
        }
        return self::$instance;
      }
      

      public function getCollection($name) {
        return $this->database->selectCollection($name);
      }
      
      // The DBConnection class implements the Singleton design pattern. This design pattern ensures that there is only a single connection open to 
      // MongoDB, within the context of a single HTTP request.
  }
  
  
  
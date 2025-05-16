<?php
    namespace App\controllers;

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Container\ContainerInterface;

    use PDO;

    class Socios{
        protected $container;

        public function __construct(ContainerInterface $c)
        {
            $this->container = $c;
        }
    
        public function read(Request $request, Response $response, $args)
        {
            $sql = "SELECT * FROM socios ";
    
            if (isset($args["id"])) {
                $sql .= "WHERE id = :id ";
            }
    
            $con = $this->container->get('base_datos');
            $query = $con->prepare($sql);
    
            if (isset($args["id"])) {
                $query->execute(["id" => $args["id"]]);
            } else {
                $query->execute();
            }
    
            $res = $query->fetchAll();
            $status = $query->rowCount() > 0 ? 200 : 204;
    
            $query = null;
            $con = null;
            $response->getBody()->write(json_encode($res));
    
            return $response
                ->withHeader('Content-type', 'Application/json')
                ->withstatus($status);
    
        }
    
        public function create(Request $request, Response $response, $args)
        {
            $body = json_decode($request->getBody(), 1);
    
            $sql = "INSERT INTO socios (";
            $values = " VALUES (";
            foreach ($body as $key => $value) {
                $sql .= $key . ', ';
                $values .= ":$key, ";
            }
            $values = substr($values, 0, -2) . ");";
            $sql = substr($sql, 0, -2) . ")" . $values;
    
            $con = $this->container->get('base_datos');
            $query = $con->prepare($sql);
    
            foreach ($body as $key => $value) {
                $query->bindValue(":$key", $value);
            }
    
            $query->execute();
            $status = $query->rowCount() > 0 ? 201 : 409; 
    
            $query = null;
            $con = null;
            return $response->withStatus($status);
        }
    
    
        public function update(Request $request, Response $response, $args)
        {
            $body = json_decode($request->getBody());
    
    
            if (isset($body->id)) {
                unset($body->id);
            }
    
            if (isset($body->codigo_producto)) {
                unset($body->codigo_producto);
            }
    
            $sql = "UPDATE socios SET";
    
            foreach ($body as $key => $value) {
                $sql .= " $key = :$key, ";
            }
            $sql = substr($sql, 0, -2);
            $sql .= " WHERE id = :id;";
    
            $con = $this->container->get('base_datos');
            $query = $con->prepare($sql);
    
            foreach ($body as $key => $value) {
                $TIPO = gettype($value) == 'integer' ? PDO::PARAM_INT : PDO::PARAM_STR;
                $value = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                $query->bindValue(":$key", $value, $TIPO);
            }
    
            $query->bindValue(":id", $args["id"], PDO::PARAM_INT);
            $query->execute();
    
            $status = $query->rowCount() > 0 ? 200 : 204;
            $query = null;
            $con = null;
    
            return $response->withStatus($status);
        }
    
        public function delete(Request $request, Response $response, $args)
        {
            $sql = "DELETE FROM socios WHERE id = :id";
            $con = $this->container->get('base_datos');
            $id = $args["id"];
    
            $query = $con->prepare($sql);
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
    
            $status = $query->rowCount() > 0 ? 200 : 204;
            $query = null;
            $con = null;
            return $response->withStatus($status);
        }
    
        public function filtrar(Request $request, Response $response, $args)
        {
            $sql = "SELECT * FROM socios WHERE ";
            $datos = $request->getQueryParams();
            foreach ($datos as $key => $value) {
                $sql .= "$key LIKE :$key AND ";
            }
            $sql = rtrim($sql, 'AND ') . ';';
    
    
            $con = $this->container->get('base_datos');
            $query = $con->prepare($sql);
            foreach ($datos as $key => $value) {
                $query->bindValue(":$key", "%$value%", PDO::PARAM_STR);
            }
            $query->execute();
    
            $res = $query->fetchAll();
            $status = $query->rowCount() > 0 ? 200 : 204;
            $response->getBody()->write(json_encode($res));
    
            $query = null;
            $con = null;
    
            return $response
                ->withHeader('Content-type', 'Application/json')
                ->withstatus($status);
        }

        public function filtrarDos(Request $request, Response $response, $args){

            //%serie%&%modelo%&%marca%&%categoria%&
      
            $datos = $request->getQueryParams();
            
            $filtro= "%";
            foreach($datos as $key =>$value){
              $filtro .= "$value%&%";
            }
            $filtro = substr($filtro, 0, -1);
            $sql = "CALL filtrarSocios('$filtro', {$args['pag']}, {$args['lim']})";
            $con = $this->container->get('base_datos');
            
            $query = $con->prepare($sql);  // ✅ Aquí se prepara la consulta
            $query->execute();             // ✅ Ahora sí puedes ejecutarla
            
            $res = $query->fetchAll();
            $status=$query->rowCount()>0 ? 200 : 204;
            
            $query=null;
            $con=null;  
            
            
            //Obtener un a respuesta
            $response->getBody()->write(json_encode($res));
          
              return $response
              ->withHeader('Content-type','Application/json')
              ->withStatus($status);
          }
    }

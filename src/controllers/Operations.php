<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class OperationsController
{

    /**
     * Example middleware invokable class
     *
     * @param Request $request PSR-7 request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {

        $db = new MyPDO();
        $uri = $request->getUri();
        $userArray = null;
        parse_str($uri->getQuery(), $userArray);

        $data = array();
        $data['status'] = 'error';


        // 1. GET sans ID = liste
        if ($request->getMethod() == 'GET' && !isset($args['id'])) {

            $sql = "SELECT * FROM `operation` WHERE operation IS NOT NULL ";

            $stmnt = $db->prepare($sql);
            $stmnt->execute();

            if ($stmnt && $stmnt->rowCount() > 0) {

                $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);

                $httpCode = 200;
                $data['status'] = 'success';
                $data['code'] = $httpCode;
                $data['content'] = $result;

            } elseif ($stmnt && $stmnt->rowCount() == 0) {
                $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);

                $httpCode = 200;
                $data['status'] = 'success';
                $data['code'] = $httpCode;
                $data['message'] = 'No operation has been created in database yet';
                $data['content'] = $result;
            } else {
                $data['status'] = 'error';
                $data['code'] = '';
            }
        } // 2. GET avec ID = détail de l'entrée X
        else if ($request->getMethod() == 'GET' && isset($args['id'])) {

            if (is_numeric($args['id'])) {

                $sql = "SELECT * FROM operation WHERE id_operation = :id_operation";

                $stmnt = $db->prepare($sql);

                $stmnt->bindValue(":id_operation", $args['id'], PDO::PARAM_INT);

                $stmnt->execute();

                if ($stmnt && $stmnt->rowCount() > 0) {

                    $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);

                    if ($result) {
                        $httpCode = 200;
                        $data['status'] = 'success';
                        $data['code'] = $httpCode;
                        $data['content'] = $result;
                    } else {
                        $data['status'] = 'error';
                        $data['code'] = 'noEntry';
                        $data['content'] = 'The entry' . $args['id'] . ' returns no result ';
                    }
                } else {
                    $data['status'] = 'error';
                    $data['code'] = 'sqlProblemID';
                    $data['content'] = 'Sorry, the ID ' . $args['id'] . ' does not exist';
                }
            } else {
                $data['status'] = 'error';
                $data['code'] = 'idMustBeNumeric';
                $data['content'] = 'The ID must be a digital';
            }
        } // 3. POST + NO ID
        else if ($request->getMethod() == 'POST' && !isset($args['id'])) {

            $request->getHeaderLine('Content-Type');

            $parsedBody = $request->getParsedBody();

            if (isset($parsedBody['operation']) && $parsedBody['operation'] != ''
                && isset($parsedBody['unity']) && $parsedBody['unity'] != ''
            ) {

                $sql = "INSERT INTO `operation` SET                                 
                                     operation = :operation,  
                                     unity = :unity  
                ;";

                $stmnt = $db->prepare($sql);

                $stmnt->bindValue(":operation", $parsedBody['operation'], PDO::PARAM_STR);
                $stmnt->bindValue(":unity", $parsedBody['unity'], PDO::PARAM_STR);

                $stmnt->execute();

                if ($stmnt && $stmnt->rowCount() > 0) {
                    $httpCode = 200;
                    $data['status'] = 'success';
                    $data['code'] = $httpCode;
                } else {
                    $data['status'] = 'error';
                    $data['code'] = 'sqlProblem';
                    $data['content'] = 'The ID ' . $args['id'] . ' does not exist.';
                }
            }
        } // 4. PUT/PATCH avec ID = Modification de l'entée X
        else if (($request->getMethod() == 'PUT' || $request->getMethod() == 'PATCH') && isset($args['id'])) {

            $parsedBody = $request->getParsedBody();

            if (is_numeric($args['id'])) {

                if (isset($parsedBody['operation']) && $parsedBody['operation'] != '') {

                    $sql = "UPDATE  `operation` 
                            SET     `operation`=:operation,
                                    `unity`=:unity                                
                            WHERE   `id_operation`= :id_operation
                    ;";
                    $stmnt = $db->prepare($sql);

                    $stmnt->bindValue(":operation", $parsedBody['operation'], PDO::PARAM_STR);
                    $stmnt->bindValue(":unity", $parsedBody['unity'], PDO::PARAM_STR);
                    $stmnt->bindValue(":id_operation", $args['id'], PDO::PARAM_INT);

                    $stmnt->execute();

                    if ($stmnt) {

                        $data['status'] = 'success';
                    }
                } else {
                    $data['status'] = 'error';
                    $data['code'] = 'paramMissing';
                    $data['message'] = 'Param: \'operation\' is mandatory';
                }
            } else {
                $data['status'] = 'error';
                $data['code'] = 'idMustBeNumeric';
                $data['content'] = 'The ID must be a digit\'';
            }
        } // 5. DELETE avec ID = Suppression de l'entrée X
        else if ($request->getMethod() == 'DELETE' && isset($args['id'])) {

            if (is_numeric($args['id'])) {

                $sql = "DELETE FROM operation WHERE id_operation= :id_operation";

                $stmnt = $db->prepare($sql);
                $stmnt->bindValue(":id_operation", $args['id'], PDO::PARAM_INT);
                $stmnt->execute();
                if ($stmnt && $stmnt->rowCount() > 0) {
                    $httpCode = 200;
                    $data['status'] = 'success';
                    $data['code'] = $httpCode;
                } else {
                    $data['status'] = 'error';
                    $data['code'] = 'sqlProblem';
                    $data['content'] = 'The ID ' . $args['id'] . ' does not exist.';
                }
            } else {
                $data['status'] = 'error';
                $data['code'] = 'idMustBeNumeric';
                $data['content'] = 'The ID must be a digit';
            }
        } else {

            $data['status'] = 'error';
            $data['code'] = 'badParam';
            $data['message'] = 'Please be conform to REST standard.';
        }

        unset($db);

        $payload = json_encode($data);
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ->withHeader('Access-Control-Allow-Methods', '*')
            ->withStatus(200);
    }


}




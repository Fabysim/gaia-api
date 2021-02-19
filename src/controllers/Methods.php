<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class MethodsController
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

            $sql = "SELECT * FROM `method_name`";

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
                $data['message'] = 'No process created in the database yet';
                $data['content'] = $result;
            } else {
                $data['status'] = 'error';
                $data['code'] = 'sql error';
            }
        } // 2. GET avec ID = détail de l'entrée X
        else if ($request->getMethod() == 'GET' && isset($args['id'])) {

            if (is_numeric($args['id'])) {


                $sql = "SELECT method_name FROM method_name WHERE id_method_name = :id_method_name";
                $stmnt = $db->prepare($sql);
                $stmnt->bindValue(":id_method_name", $args['id'], PDO::PARAM_INT);
                $stmnt->execute();

                if ($stmnt && $stmnt->rowCount() > 0) {

                    $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
                    $method_name = $result[0]['method_name'];

                    $sql = "SELECT * FROM " . $method_name." AS mt
                            LEFT JOIN waiting_condition AS wc ON mt.id_waiting_condition = wc.id_waiting_condition
                            LEFT JOIN measure_type AS my ON mt.id_measure_type = my.id_measure_type
                            LEFT JOIN method_name AS mn ON mt.id_method_name = mn.id_method_name;";
                    $stmnt = $db->prepare($sql);
                    $stmnt->execute();

                    if ($stmnt && $stmnt->rowCount() > 0) {

                        $result_Methods = $stmnt->fetchAll(PDO::FETCH_ASSOC);

                        if ($result) {
                            $sql ="SELECT MAX(step) AS step FROM ".$method_name;
                            $stmnt = $db->prepare($sql);
                            $stmnt->execute();
                            $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
                            $step_value = $result[0]['step'];


                            if($stmnt){
                                $httpCode = 200;
                                $data['status'] = 'success';
                                $data['code'] = $httpCode;
                                $data['content'] = $result_Methods;
                                $data['last_step'] = $step_value;
                            }

                        } else {
                            $data['status'] = 'error';
                            $data['code'] = 'noEntry';
                            $data['content'] = 'No results found ';
                        }
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
        } // 3. POST without ID
        else if ($request->getMethod() == 'POST' && !isset($args['id'])) {

            $request->getHeaderLine('Content-Type');

            $parsedBody = $request->getParsedBody();

            if (isset($parsedBody['method_name']) && $parsedBody['method_name'] != '') {

                $sql = "SELECT EXISTS (SELECT * FROM method_name WHERE method_name= '" . $parsedBody['method_name'] . "') AS count;";

                $stmnt = $db->prepare($sql);
                $stmnt->execute();

                $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);

                if ($result[0]["count"]) {

                    $data['status'] = 'error';
                    $data['code'] = 'param error';
                    $data['message'] = "Method already exists in database ";

                } else {

                    $sql = "CREATE TABLE `" . $parsedBody['method_name'] . "` (
                  `id_method` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                  `step` int(10),
                  `A1` int(10),
                  `A2` int(10),
                  `A3` int(10),
                  `A4` int(10),
                  `A5` int(10),
                  `B1` int(10),
                  `B2` int(10),
                  `B3` int(10),
                  `B4` int(10),
                  `B5` int(10),
                  `C1` int(10),
                  `C2` int(10),
                  `C3` int(10),
                  `C4` int(10),
                  `C5` int(10),
                  `pump` int(10),
                  `oven` int(10),
                  `lifter` float,
                  `description` varchar(50),
                  `id_waiting_condition` int(10),
                  `id_measure_type` int(10),
                  `id_method_name` int(10)
                  );";

                    $stmnt = $db->prepare($sql);
                    $stmnt->execute();

                    if ($stmnt) {

                        $sql = "ALTER TABLE `" . $parsedBody['method_name'] . "` ADD FOREIGN KEY (`id_waiting_condition`) 
                            REFERENCES `waiting_condition` (`id_waiting_condition`);";
                        $stmnt = $db->prepare($sql);
                        $stmnt->execute();

                        if ($stmnt) {

                            $sql = "ALTER TABLE `" . $parsedBody['method_name'] . "` ADD FOREIGN KEY (`id_measure_type`) 
                                REFERENCES `measure_type` (`id_measure_type`);";

                            $stmnt = $db->prepare($sql);
                            $stmnt->execute();

                            if ($stmnt) {

                                $sql = "ALTER TABLE `" . $parsedBody['method_name'] . "`ADD FOREIGN KEY (`id_method_name`) 
                                    REFERENCES `method_name` (`id_method_name`);";

                                $stmnt = $db->prepare($sql);
                                $stmnt->execute();

                                if ($stmnt) {

                                    $sql = "INSERT INTO `method_name` SET 
                                        method_name = :method_name;";

                                    $stmnt = $db->prepare($sql);
                                    $stmnt->bindValue(":method_name", $parsedBody['method_name'], PDO::PARAM_STR);
                                    $stmnt->execute();
                                    $id_method_name = $db->lastInsertId();

                                    $sql = "INSERT INTO  `" . $parsedBody['method_name'] . "` SET     
                                                A1 = :A1 ,
                                                A2 = :A2 ,
                                                A3 = :A3 ,
                                                A4 = :A4 ,
                                                A5 = :A5 , 
                                                B1 = :B1 ,
                                                B2 = :B2 ,
                                                B3 = :B3 ,
                                                B4 = :B4 ,
                                                B5 = :B5 ,
                                                C1 = :C1 ,
                                                C2 = :C2 ,
                                                C3 = :C3 ,
                                                C4 = :C4 ,
                                                C5 = :C5 ,
                                                pump = :pump,
                                                oven = :oven,
                                                lifter = :lifter,
                                                id_waiting_condition = :id_waiting_condition,
                                                description = :description,
                                                id_measure_type = :id_measure_type,
                                                id_method_name= :id_method_name 
                                                ;";

                                    $stmnt = $db->prepare($sql);

                                    $stmnt->bindValue(":Step", null, PDO::PARAM_INT);
                                    $stmnt->bindValue(":A1", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":A2", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":A3", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":A4", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":A5", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":B1", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":B2", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":B3", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":B4", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":B5", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":C1", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":C2", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":C3", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":C4", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":C5", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":pump", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":oven", 0, PDO::PARAM_INT);
                                    $stmnt->bindValue(":lifter", 0, PDO::PARAM_STR);
                                    $stmnt->bindValue(":id_waiting_condition", null, PDO::PARAM_INT);
                                    $stmnt->bindValue(":id_measure_type", null, PDO::PARAM_INT);
                                    $stmnt->bindValue(":id_method_name", $id_method_name, PDO::PARAM_STR);
                                    $stmnt->bindValue(":description", "Initial state", PDO::PARAM_STR);

                                    $stmnt->execute();

                                    if ($stmnt) {
                                        $httpCode = 200;
                                        $data['status'] = 'success';
                                        $data['code'] = $httpCode;
                                        $data['message'] = "Method created ";
                                    }

                                }
                            }
                        }

                    } else {
                        $data['status'] = 'error';
                        $data['code'] = 'sqlProblem';
                        $data['content'] = 'The request could not be executed';
                    }
                }
            } else {
                $data['status'] = 'error';
                $data['code'] = 'param error';
                $data['content'] = 'The param \'method_name\' is required';
            }


        } // 4. PUT/PATCH With ID
        else if (($request->getMethod() == 'PUT' || $request->getMethod() == 'PATCH') && isset($args['id'])) {

            $parsedBody = $request->getParsedBody();

            if (is_numeric($args['id'])) {

                $A1 = (isset($parsedBody['A1']) && $parsedBody['A1'] != '') ? $parsedBody['A1'] : '';
                $A2 = (isset($parsedBody['A2']) && $parsedBody['A2'] != '') ? $parsedBody['A2'] : '';
                $A3 = (isset($parsedBody['A3']) && $parsedBody['A3'] != '') ? $parsedBody['A3'] : '';
                $A4 = (isset($parsedBody['A4']) && $parsedBody['A4'] != '') ? $parsedBody['A4'] : '';
                $A5 = (isset($parsedBody['A5']) && $parsedBody['A5'] != '') ? $parsedBody['A5'] : '';
                $B1 = (isset($parsedBody['B1']) && $parsedBody['B1'] != '') ? $parsedBody['B1'] : '';
                $B2 = (isset($parsedBody['B2']) && $parsedBody['B2'] != '') ? $parsedBody['B2'] : '';
                $B3 = (isset($parsedBody['B3']) && $parsedBody['B3'] != '') ? $parsedBody['B3'] : '';
                $B4 = (isset($parsedBody['B4']) && $parsedBody['B4'] != '') ? $parsedBody['B4'] : '';
                $B5 = (isset($parsedBody['B5']) && $parsedBody['B5'] != '') ? $parsedBody['B5'] : '';
                $C1 = (isset($parsedBody['C1']) && $parsedBody['C1'] != '') ? $parsedBody['C1'] : '';
                $C2 = (isset($parsedBody['C2']) && $parsedBody['C2'] != '') ? $parsedBody['C2'] : '';
                $C3 = (isset($parsedBody['C3']) && $parsedBody['C3'] != '') ? $parsedBody['C3'] : '';
                $C4 = (isset($parsedBody['C4']) && $parsedBody['C4'] != '') ? $parsedBody['C4'] : '';
                $C5 = (isset($parsedBody['C5']) && $parsedBody['C5'] != '') ? $parsedBody['C5'] : '';
                $pump = (isset($parsedBody['pump']) && $parsedBody['pump'] != '') ? $parsedBody['pump'] : '';
                $oven = (isset($parsedBody['oven']) && $parsedBody['oven'] != '') ? $parsedBody['oven'] : '';
                $lifter = (isset($parsedBody['lifter']) && $parsedBody['lifter'] != '') ? $parsedBody['lifter'] : '';
                $id_waiting_condition = (isset($parsedBody['id_waiting_condition']) && $parsedBody['id_waiting_condition'] != '') ? $parsedBody['id_waiting_condition'] : '';
                $waiting_period = (isset($parsedBody['waiting_period']) && $parsedBody['waiting_period'] != '') ? $parsedBody['waiting_period'] : '';
                $measure = (isset($parsedBody['measure']) && $parsedBody['measure'] != '') ? $parsedBody['measure'] : '';
                $description = (isset($parsedBody['description']) && $parsedBody['description'] != '') ? $parsedBody['description'] : '';


                $sql = "UPDATE  `process` SET     
                                    A1 = :A1 ,
                                    A2 = :A2 ,
                                    A3 = :A3 ,
                                    A4 = :A4 ,
                                    A5 = :A5 , 
                                    B1 = :B1 ,
                                    B2 = :B2 ,
                                    B3 = :B3 ,
                                    B4 = :B4 ,
                                    B5 = :B5 ,
                                    C1 = :C1 ,
                                    C2 = :C2 ,
                                    C3 = :C3 ,
                                    C4 = :C4 ,
                                    C5 = :C5 ,
                                    pump = :pump,
                                    oven = :oven,
                                    lifter = :lifter,
                                    id_waiting_condition = :id_waiting_condition,
                                    waiting_period = :waiting_period,
                                    measure = :measure,
                                    description = :description 
                            WHERE   id_process= :id_process
                    ;";

                $stmnt = $db->prepare($sql);

                $stmnt->bindValue(":A1", $A1, PDO::PARAM_INT);
                $stmnt->bindValue(":A2", $A2, PDO::PARAM_INT);
                $stmnt->bindValue(":A3", $A3, PDO::PARAM_INT);
                $stmnt->bindValue(":A4", $A4, PDO::PARAM_INT);
                $stmnt->bindValue(":A5", $A5, PDO::PARAM_INT);
                $stmnt->bindValue(":B1", $B1, PDO::PARAM_INT);
                $stmnt->bindValue(":B2", $B2, PDO::PARAM_INT);
                $stmnt->bindValue(":B3", $B3, PDO::PARAM_INT);
                $stmnt->bindValue(":B4", $B4, PDO::PARAM_INT);
                $stmnt->bindValue(":B5", $B5, PDO::PARAM_INT);
                $stmnt->bindValue(":C1", $C1, PDO::PARAM_INT);
                $stmnt->bindValue(":C2", $C2, PDO::PARAM_INT);
                $stmnt->bindValue(":C3", $C3, PDO::PARAM_INT);
                $stmnt->bindValue(":C4", $C4, PDO::PARAM_INT);
                $stmnt->bindValue(":C5", $C5, PDO::PARAM_INT);
                $stmnt->bindValue(":pump", $pump, PDO::PARAM_INT);
                $stmnt->bindValue(":oven", $oven, PDO::PARAM_INT);
                $stmnt->bindValue(":lifter", $lifter, PDO::PARAM_STR);
                $stmnt->bindValue(":id_waiting_condition", $id_waiting_condition, PDO::PARAM_INT);
                $stmnt->bindValue(":waiting_period", $waiting_period, PDO::PARAM_INT);
                $stmnt->bindValue(":measure", $measure, PDO::PARAM_STR);
                $stmnt->bindValue(":description", $description, PDO::PARAM_STR);
                $stmnt->bindValue(":id_process", $args['id'], PDO::PARAM_INT);

                $stmnt->execute();

                if ($stmnt) {

                    $data['status'] = 'success';
                } else {
                    $data['status'] = 'error';
                    $data['code'] = 'paramMissing';
                    $data['message'] = 'Param: all parameters are mandatory.';

                }

            } else {
                $data['status'] = 'error';
                $data['code'] = 'idMustBeNumeric';
                $data['content'] = 'The ID must be B digit\'';
            }
        } // 5. DELETE avec ID = Suppression de l'entrée X
        else if ($request->getMethod() == 'DELETE' && isset($args['id'])) {

            if (is_numeric($args['id'])) {

                $sql = "DELETE FROM process WHERE id_process= :id_process";

                $stmnt = $db->prepare($sql);
                $stmnt->bindValue(":id_process", $args['id'], PDO::PARAM_INT);
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



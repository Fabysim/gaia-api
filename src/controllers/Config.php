<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class ConfigController
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
        $request->getHeaderLine('Content-Type');
        $parsedBody = $request->getParsedBody();

        $data = array();
        $data['status'] = 'error';

        $id_method_name = $parsedBody['id_method_name'];

        $sql = "SELECT method_name FROM method_name WHERE id_method_name = :id_method_name;";
        $stmnt = $db->prepare($sql);
        $stmnt->bindValue(":id_method_name", $id_method_name, PDO::PARAM_INT);
        $stmnt->execute();

        $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
        $method_name = $result[0]['method_name'];




        // 1. GET sans ID = liste
        if ($request->getMethod() == 'GET' && !isset($args['id'])) {


            $sql = "SELECT * FROM " . $method_name;

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
                $data['message'] = 'No data created in the database yet';
                $data['content'] = $result;
            } else {
                $data['status'] = 'error';
                $data['code'] = 'sql error';
            }


        } // 2. GET avec ID = détail de l'entrée X
        else if ($request->getMethod() == 'GET' && isset($args['id'])) {

            if (is_numeric($args['id'])) {

                $sql = "SELECT * FROM " . $method_name;

                $stmnt = $db->prepare($sql);

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
        } // 3. POST sans ID = Ajout d'une nouvelle entrée
        else if ($request->getMethod() == 'POST' && !isset($args['id'])) {

            $request->getHeaderLine('Content-Type');
            $parsedBody = $request->getParsedBody();

            $sql ="SELECT MAX(step) AS step FROM ".$method_name;
            $stmnt = $db->prepare($sql);
            $stmnt->execute();
            $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
            $step_value = $result[0]['step'];


            if ($step_value === NULL){
                $step_value  = 0;
            }else{
                $step_value  +=1;
            }

            $A1 = intval((isset($parsedBody['A1']) && $parsedBody['A1'] != '') ? $parsedBody['A1'] : 0);
            $A2 = intval((isset($parsedBody['A2']) && $parsedBody['A2'] != '') ? $parsedBody['A2'] : 0);
            $A3 = intval((isset($parsedBody['A3']) && $parsedBody['A3'] != '') ? $parsedBody['A3'] : 0);
            $A4 = intval((isset($parsedBody['A4']) && $parsedBody['A4'] != '') ? $parsedBody['A4'] : 0);
            $A5 = intval((isset($parsedBody['A5']) && $parsedBody['A5'] != '') ? $parsedBody['A5'] : 0);
            $B1 = intval((isset($parsedBody['B1']) && $parsedBody['B1'] != '') ? $parsedBody['B1'] : 0);
            $B2 = intval((isset($parsedBody['B2']) && $parsedBody['B2'] != '') ? $parsedBody['B2'] : 0);
            $B3 = intval((isset($parsedBody['B3']) && $parsedBody['B3'] != '') ? $parsedBody['B3'] : 0);
            $B4 = intval((isset($parsedBody['B4']) && $parsedBody['B4'] != '') ? $parsedBody['B4'] : 0);
            $B5 = intval((isset($parsedBody['B5']) && $parsedBody['B5'] != '') ? $parsedBody['B5'] : 0);
            $C1 = intval((isset($parsedBody['C1']) && $parsedBody['C1'] != '') ? $parsedBody['C1'] : 0);
            $C2 = intval((isset($parsedBody['C2']) && $parsedBody['C2'] != '') ? $parsedBody['C2'] : 0);
            $C3 = intval((isset($parsedBody['C3']) && $parsedBody['C3'] != '') ? $parsedBody['C3'] : 0);
            $C4 = intval((isset($parsedBody['C4']) && $parsedBody['C4'] != '') ? $parsedBody['C4'] : 0);
            $C5 = intval((isset($parsedBody['C5']) && $parsedBody['C5'] != '') ? $parsedBody['C5'] : 0);
            $pump = intval((isset($parsedBody['pump']) && $parsedBody['pump'] != '') ? $parsedBody['pump'] : 0);
            $oven = intval((isset($parsedBody['oven']) && $parsedBody['oven'] != '') ? $parsedBody['oven'] : 0);
            $lifter = floatval((isset($parsedBody['lifter']) && $parsedBody['lifter'] != '') ? $parsedBody['lifter'] : 0);
            $id_waiting_condition = intval((isset($parsedBody['id_waiting_condition']) && $parsedBody['id_waiting_condition'] != '') ? $parsedBody['id_waiting_condition'] : 1);
            $id_measure_type = intval((isset($parsedBody['id_measure_type']) && $parsedBody['id_measure_type'] != '') ? $parsedBody['id_measure_type'] : 1);
            $description = (isset($parsedBody['description']) && $parsedBody['description'] != '') ? $parsedBody['description'] : '';


            $sql = "INSERT INTO `" . $method_name . "` SET                                 
                                    step = :step,
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
                                    id_method_name = :id_method_name,
                                    id_measure_type = :id_measure_type,
                                    description = :description 
                                    ;";

            $stmnt = $db->prepare($sql);

            $stmnt->bindValue(":step", $step_value , PDO::PARAM_INT);
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
            $stmnt->bindValue(":description", $description, PDO::PARAM_STR);
            $stmnt->bindValue(":id_measure_type", $id_measure_type, PDO::PARAM_INT);
            $stmnt->bindValue(":id_method_name", $id_method_name, PDO::PARAM_INT);


            $stmnt->execute();

            if ($stmnt && $stmnt->rowCount() > 0) {
                $httpCode = 200;
                $data['status'] = 'success';
                $data['code'] = $httpCode;
                $data['step'] = $step_value;
            } else {
                $data['status'] = 'error';
                $data['code'] = 'sqlProblem';
                $data['content'] = 'The request could not be executed';
            }

        } // 4. PUT/PATCH avec ID = Modification de l'entée X
        else if (($request->getMethod() == 'PUT' || $request->getMethod() == 'PATCH') && isset($args['id'])) {

            $parsedBody = $request->getParsedBody();

            if (is_numeric($args['id'])) {
                $sql = "SELECT method_name FROM method name WHERE id_method_name = :id_method_name;";
                $stmnt = $db->prepare($sql);
                $stmnt->execute();
                $stmnt->bindValue(":id_method", $id_method_name, PDO::PARAM_INT);
                $result = $stmnt->fetchAll(PDO::FETCH_ASSOC);
                $method_name = $result[0]['method_name'];


                $A1 = intval((isset($parsedBody['A1']) && $parsedBody['A1'] != '') ? $parsedBody['A1'] : '');
                $A2 = intval((isset($parsedBody['A2']) && $parsedBody['A2'] != '') ? $parsedBody['A2'] : '');
                $A3 = intval((isset($parsedBody['A3']) && $parsedBody['A3'] != '') ? $parsedBody['A3'] : '');
                $A4 = intval((isset($parsedBody['A4']) && $parsedBody['A4'] != '') ? $parsedBody['A4'] : '');
                $A5 = intval((isset($parsedBody['A5']) && $parsedBody['A5'] != '') ? $parsedBody['A5'] : '');
                $B1 = intval((isset($parsedBody['B1']) && $parsedBody['B1'] != '') ? $parsedBody['B1'] : '');
                $B2 = intval((isset($parsedBody['B2']) && $parsedBody['B2'] != '') ? $parsedBody['B2'] : '');
                $B3 = intval((isset($parsedBody['B3']) && $parsedBody['B3'] != '') ? $parsedBody['B3'] : '');
                $B4 = intval((isset($parsedBody['B4']) && $parsedBody['B4'] != '') ? $parsedBody['B4'] : '');
                $B5 = intval((isset($parsedBody['B5']) && $parsedBody['B5'] != '') ? $parsedBody['B5'] : '');
                $C1 = intval((isset($parsedBody['C1']) && $parsedBody['C1'] != '') ? $parsedBody['C1'] : '');
                $C2 = intval((isset($parsedBody['C2']) && $parsedBody['C2'] != '') ? $parsedBody['C2'] : '');
                $C3 = intval((isset($parsedBody['C3']) && $parsedBody['C3'] != '') ? $parsedBody['C3'] : '');
                $C4 = intval((isset($parsedBody['C4']) && $parsedBody['C4'] != '') ? $parsedBody['C4'] : '');
                $C5 = intval((isset($parsedBody['C5']) && $parsedBody['C5'] != '') ? $parsedBody['C5'] : '');
                $pump = intval((isset($parsedBody['pump']) && $parsedBody['pump'] != '') ? $parsedBody['pump'] : '');
                $oven = intval((isset($parsedBody['oven']) && $parsedBody['oven'] != '') ? $parsedBody['oven'] : '');
                $lifter = floatval((isset($parsedBody['lifter']) && $parsedBody['lifter'] != '') ? $parsedBody['lifter'] : '');
                $id_waiting_condition = intval((isset($parsedBody['id_waiting_condition']) && $parsedBody['id_waiting_condition'] != '') ? $parsedBody['id_waiting_condition'] : '');
                $waiting_period = intval((isset($parsedBody['waiting_period']) && $parsedBody['waiting_period'] != '') ? $parsedBody['waiting_period'] : '');
                $measure = floatval((isset($parsedBody['measure']) && $parsedBody['measure'] != '') ? $parsedBody['measure'] : '');
                $id_measure_type = intval((isset($parsedBody['id_measure_type']) && $parsedBody['id_measure_type'] != '') ? $parsedBody['id_measure_type'] : '');
                $description = (isset($parsedBody['description']) && $parsedBody['description'] != '') ? $parsedBody['description'] : '';


                $sql = "UPDATE `" . $method_name . "` SET     
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
                                    id_measure_type = :id_measure_type,
                                    description = :description 
                            WHERE   id_method = :id_method
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
                $stmnt->bindValue(":id_measure_type", $id_measure_type, PDO::PARAM_INT);
                $stmnt->bindValue(":description", $description, PDO::PARAM_STR);
                $stmnt->bindValue(":id_method", $args['id'], PDO::PARAM_INT);

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

                $sql = "DELETE FROM `" . $parsedBody['method_name'] . "` WHERE id_method = :id_method";

                $stmnt = $db->prepare($sql);
                $stmnt->bindValue(":id_method", $args['id'], PDO::PARAM_INT);
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

    public static function name_creation($name)
    {
        $new = "pr_" . $name;
        return $new;
    }


}




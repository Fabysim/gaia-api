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


        // 1. GET sans ID = liste
        if ($request->getMethod() == 'GET' && !isset($args['id'])) {
            $id_method_name = $parsedBody['id_method_list'];

            $sql = "SELECT method_name FROM method_list WHERE id_method_list = :id_method_list;";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":id_method_list", $id_method_name, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $in_method_name = $result[0]['method_name'];


            $sql = "SELECT * FROM " . $in_method_name;

            $stmt = $db->prepare($sql);
            $stmt->execute();

            if ($stmt && $stmt->rowCount() > 0) {

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $httpCode = 200;
                $data['status'] = 'success';
                $data['code'] = $httpCode;
                $data['content'] = $result;

            } elseif ($stmt && $stmt->rowCount() == 0) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $httpCode = 200;
                $data['status'] = 'success';
                $data['code'] = $httpCode;
                $data['message'] = 'empty table';
                $data['content'] = $result;
            } else {
                $data['status'] = 'error';
                $data['code'] = 'sql error';
            }


        } // 2. GET avec ID = détail de l'entrée X
        else if ($request->getMethod() == 'GET' && isset($args['id'])) {

            if (is_numeric($args['id'])) {

                $sql = "SELECT method_name FROM method_list WHERE id_method_list =:id_method_list";
                $stmt = $db->prepare($sql);
                $stmt ->bindValue(":id_method_list", $args['id'], PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt && $stmt->rowCount() > 0) {

                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            $id_method_name = $parsedBody['id_method_list'];

            $sql = "SELECT method_name FROM method_list WHERE id_method_list = :id_method_list;";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(":id_method_list", $id_method_name, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $in_method_name = $result[0]['method_name'];


            $sql = "SELECT MAX(step) AS step FROM " . $in_method_name;
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $step_value = $result[0]['step'];


            if ($step_value === NULL) {
                $step_value = 0;
            } else {
                $step_value += 1;
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
            $timeout_value = floatval((isset($parsedBody['timeout_value']) && $parsedBody['timeout_value'] != '') ? $parsedBody['timeout_value'] : 0);
            $id_signal_type = intval((isset($parsedBody['id_signal_type']) && $parsedBody['id_signal_type'] != '') ? $parsedBody['id_signal_type'] : 1);
            $previous_id_waiting_condition = intval((isset($parsedBody['id_waiting_condition']) && $parsedBody['id_waiting_condition'] != '') ? $parsedBody['id_waiting_condition'] : 1);
            $threshold_value = (isset($parsedBody['threshold_value']) && $parsedBody['threshold_value'] != '') ? $parsedBody['threshold_value'] : '';
            $description = (isset($parsedBody['description']) && $parsedBody['description'] != '') ? $parsedBody['description'] : '';

            $sql = "INSERT INTO `" . $in_method_name . "` SET                                 
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
                                    id_method_list = :id_method_list,
                                    description = :description 
                                    ;";

            $stmt = $db->prepare($sql);

            $stmt->bindValue(":step", $step_value, PDO::PARAM_INT);
            $stmt->bindValue(":A1", $A1, PDO::PARAM_INT);
            $stmt->bindValue(":A2", $A2, PDO::PARAM_INT);
            $stmt->bindValue(":A3", $A3, PDO::PARAM_INT);
            $stmt->bindValue(":A4", $A4, PDO::PARAM_INT);
            $stmt->bindValue(":A5", $A5, PDO::PARAM_INT);
            $stmt->bindValue(":B1", $B1, PDO::PARAM_INT);
            $stmt->bindValue(":B2", $B2, PDO::PARAM_INT);
            $stmt->bindValue(":B3", $B3, PDO::PARAM_INT);
            $stmt->bindValue(":B4", $B4, PDO::PARAM_INT);
            $stmt->bindValue(":B5", $B5, PDO::PARAM_INT);
            $stmt->bindValue(":C1", $C1, PDO::PARAM_INT);
            $stmt->bindValue(":C2", $C2, PDO::PARAM_INT);
            $stmt->bindValue(":C3", $C3, PDO::PARAM_INT);
            $stmt->bindValue(":C4", $C4, PDO::PARAM_INT);
            $stmt->bindValue(":C5", $C5, PDO::PARAM_INT);
            $stmt->bindValue(":pump", $pump, PDO::PARAM_INT);
            $stmt->bindValue(":oven", $oven, PDO::PARAM_INT);
            $stmt->bindValue(":lifter", $lifter, PDO::PARAM_STR);
            $stmt->bindValue(":description", $description, PDO::PARAM_STR);
            $stmt->bindValue(":id_method_list", $id_method_name, PDO::PARAM_INT);

            $stmt->execute();

            $last_step_id = $db->lastInsertId();

            if ($stmt && $stmt->rowCount() > 0) {

                $in_wait_cond_table = ConfigController::table_waiting_name_prefix($in_method_name);
                $in_thr_table = ConfigController::table_threshold_name_prefix($in_method_name);

                $sql = "SELECT waiting_label FROM waiting_condition WHERE id_waiting_condition =:id_waiting_condition;";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_waiting_condition", $parsedBody['id_waiting_condition'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $in_waiting_condition = $result[0]['waiting_label'];

                $sql = "SELECT signal_type, unity FROM signal_type WHERE id_signal_type =:id_signal_type;";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_signal_type", $parsedBody['id_signal_type'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $signal = $result[0]['signal_type'];
                $unity = $result[0]['unity'];

                $sql = "SELECT operation FROM operation WHERE id_operation =:id_operation;";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_operation", $parsedBody['id_operation'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $operation = $result[0]['operation'];

                $waiting_value_label = $in_waiting_condition;

                if ($in_waiting_condition === 'Signal threshold') {
                    if ($signal != NULL) {
                        if ($operation === 'above') {
                            $waiting_value_label = $signal . " > " . $parsedBody['threshold_value'] . " " . $unity;
                        } else {
                            $waiting_value_label = $signal . " < " . $parsedBody['threshold_value'] . " " . $unity;
                        }

                    } else {
                        $waiting_value_label = "";
                    }
                }

                $sql = "INSERT INTO " . $in_wait_cond_table . " 
                        SET         timeout_value =:timeout_value,
                                    id_waiting_condition =:id_waiting_condition,
                                    waiting_value_label =:waiting_value_label,
                                    id_step =:id_step;";

                $stmt = $db->prepare($sql);
                $stmt->bindValue(":timeout_value", $timeout_value, PDO::PARAM_INT);
                $stmt->bindValue(":waiting_value_label", $waiting_value_label, PDO::PARAM_STR);
                $stmt->bindValue(":id_waiting_condition", $previous_id_waiting_condition, PDO::PARAM_INT);
                $stmt->bindValue(":id_step", $last_step_id, PDO::PARAM_INT);
                $stmt->execute();

                $id_method_waiting_condition = $db->lastInsertId();

                if ($in_waiting_condition === 'Signal threshold') {

                    $sql = "INSERT INTO " . $in_thr_table . " 
                            SET     threshold_value =:threshold_value,
                                    id_operation =:id_operation,
                                    id_signal_type =:id_signal_type,
                                    id_method_waiting_condition =:id_method_waiting_condition;";

                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(":id_operation", $parsedBody['id_operation'], PDO::PARAM_INT);
                    $stmt->bindValue(":threshold_value", $parsedBody['threshold_value'], PDO::PARAM_INT);
                    $stmt->bindValue(":id_signal_type", $parsedBody['id_signal_type'], PDO::PARAM_INT);
                    $stmt->bindValue(":id_method_waiting_condition", $id_method_waiting_condition, PDO::PARAM_INT);
                    $stmt->execute();
                }

                if ($stmt) {
                    $httpCode = 200;
                    $data['status'] = 'success';
                    $data['code'] = $httpCode;
                    $data['step'] = $step_value;
                } else {
                    $data['status'] = 'error';
                    $data['code'] = 'sqlProblem';
                    $data['content'] = 'The request could not be executed';
                }
            }


        } // 4. PUT/PATCH avec ID = Modification de l'entée X
        else if (($request->getMethod() == 'PUT' || $request->getMethod() == 'PATCH') && isset($args['id'])) {

            if (is_numeric($args['id'])) {

                $request->getHeaderLine('Content-Type');
                $parsedBody = $request->getParsedBody();

                $in_method_name = $parsedBody['method_name'];
                $in_wait_cond_table = ConfigController::table_waiting_name_prefix($in_method_name);
                $in_thr_table = ConfigController::table_threshold_name_prefix($in_method_name);



                /*--------------------      Verify if there is a threshold entry for the record    ---------*/

                $sql = "SELECT id_threshold FROM ".$in_thr_table."
                        WHERE id_method_waiting_condition =:id_method_waiting_condition ;";
                $stmt = $db->prepare($sql);

                $stmt->bindValue(":id_method_waiting_condition", $parsedBody['id_method_waiting_condition'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $id_thr_exists = "";
                $ancient_thr_id ="";


                if ($result) {
                    $id_thr_exists = 1;
                    $ancient_thr_id = $result[0]['id_threshold'];

                }else{
                    $id_thr_exists = 0;
                }


                /*--------------------      Retrieve incoming waiting condition   --------------------------*/

                $sql = "SELECT waiting_label FROM waiting_condition WHERE id_waiting_condition =:id_waiting_condition;";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_waiting_condition", $parsedBody['id_waiting_condition'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $in_waiting_condition = $result[0]['waiting_label'];

                /*--------------------      Retrieve outgoing waiting condition     -------------------------*/

                $sql = "SELECT id_waiting_condition FROM " . $in_wait_cond_table . " WHERE id_method_waiting_condition =:id_method_waiting_condition";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_method_waiting_condition", $parsedBody['id_method_waiting_condition'], PDO::PARAM_INT);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $sql = "SELECT waiting_label FROM waiting_condition WHERE id_waiting_condition =:id_waiting_condition;";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_waiting_condition", $result[0]['id_waiting_condition'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $out_waiting_condition = $result[0]['waiting_label'];

                $waiting_value_label = $in_waiting_condition;

                /*--------------------     Creating waiting condition label    --------------------------------*/

                if ($in_waiting_condition === 'Signal threshold') {

                    $sql = "SELECT signal_type, unity FROM signal_type WHERE id_signal_type =:id_signal_type;";
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(":id_signal_type", $parsedBody['id_signal_type'], PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $signal = $result[0]['signal_type'];
                    $unity = $result[0]['unity'];

                    $sql = "SELECT operation FROM operation WHERE id_operation =:id_operation;";
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(":id_operation", $parsedBody['id_operation'], PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $operation = $result[0]['operation'];

                    if ($signal != NULL) {
                        if ($operation === 'above') {
                            $waiting_value_label = $signal . " > " . $parsedBody['threshold_value'] . " " . $unity;
                        } else {
                            $waiting_value_label = $signal . " < " . $parsedBody['threshold_value'] . " " . $unity;
                        }

                    } else {
                        $waiting_value_label = "";
                    }
                }

                /*-------------------------------     Update all data    -----------------------------------*/

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
                $previous_id_waiting_condition = intval((isset($parsedBody['id_waiting_condition']) && $parsedBody['id_waiting_condition'] != '') ? $parsedBody['id_waiting_condition'] : '');
                $waiting_period = intval((isset($parsedBody['waiting_period']) && $parsedBody['waiting_period'] != '') ? $parsedBody['waiting_period'] : '');
                $signal = floatval((isset($parsedBody['signal']) && $parsedBody['signal'] != '') ? $parsedBody['signal'] : '');
                $id_signal_type = intval((isset($parsedBody['id_signal_type']) && $parsedBody['id_signal_type'] != '') ? $parsedBody['id_signal_type'] : '');
                $description = (isset($parsedBody['description']) && $parsedBody['description'] != '') ? $parsedBody['description'] : '';


                /*--------------------------------------- Update the step     ------------------------------*/

                $sql = "UPDATE `" . $in_method_name . "` SET  
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
                                    description = :description 
                            WHERE   id_step = :id_step
                    ;";

                $stmt = $db->prepare($sql);

                $stmt->bindValue(":A1", $A1, PDO::PARAM_INT);
                $stmt->bindValue(":A2", $A2, PDO::PARAM_INT);
                $stmt->bindValue(":A3", $A3, PDO::PARAM_INT);
                $stmt->bindValue(":A4", $A4, PDO::PARAM_INT);
                $stmt->bindValue(":A5", $A5, PDO::PARAM_INT);
                $stmt->bindValue(":B1", $B1, PDO::PARAM_INT);
                $stmt->bindValue(":B2", $B2, PDO::PARAM_INT);
                $stmt->bindValue(":B3", $B3, PDO::PARAM_INT);
                $stmt->bindValue(":B4", $B4, PDO::PARAM_INT);
                $stmt->bindValue(":B5", $B5, PDO::PARAM_INT);
                $stmt->bindValue(":C1", $C1, PDO::PARAM_INT);
                $stmt->bindValue(":C2", $C2, PDO::PARAM_INT);
                $stmt->bindValue(":C3", $C3, PDO::PARAM_INT);
                $stmt->bindValue(":C4", $C4, PDO::PARAM_INT);
                $stmt->bindValue(":C5", $C5, PDO::PARAM_INT);
                $stmt->bindValue(":pump", $pump, PDO::PARAM_INT);
                $stmt->bindValue(":oven", $oven, PDO::PARAM_INT);
                $stmt->bindValue(":lifter", $lifter, PDO::PARAM_STR);
                $stmt->bindValue(":description", $description, PDO::PARAM_STR);
                $stmt->bindValue(":id_step", $parsedBody['id_step'], PDO::PARAM_INT);

                $stmt->execute();




                /*----------------------------- Update the threshold    -----------------------------

                --------------------- 1. Verify if incoming signal is threshold     --------------------*/

                if ($in_waiting_condition === 'Signal threshold') {

                    if($out_waiting_condition === 'Signal threshold'){

                        $sql = "UPDATE " . $in_thr_table . "
                            SET     threshold_value =:threshold_value,
                                    id_operation =:id_operation,
                                    id_signal_type =:id_signal_type,
                                    id_method_waiting_condition =:id_method_waiting_condition
                            WHERE   id_threshold =:id_threshold;";

                        $stmt = $db->prepare($sql);
                        $stmt->bindValue(":id_operation", $parsedBody['id_operation'], PDO::PARAM_INT);
                        $stmt->bindValue(":threshold_value", $parsedBody['threshold_value'], PDO::PARAM_INT);
                        $stmt->bindValue(":id_signal_type", $parsedBody['id_signal_type'], PDO::PARAM_INT);
                        $stmt->bindValue(":id_method_waiting_condition", $parsedBody['id_method_waiting_condition'], PDO::PARAM_INT);
                        $stmt->bindValue(":id_threshold", $parsedBody['id_threshold'], PDO::PARAM_INT);

                        $stmt->execute();

                    }else{
                        /*--------------------- 2. Verify if it has a threshold id    --------------------*/

                        if($id_thr_exists){
                            $sql = "UPDATE " . $in_thr_table . "
                            SET     threshold_value =:threshold_value,
                                    id_operation =:id_operation,
                                    id_signal_type =:id_signal_type,
                                    id_method_waiting_condition =:id_method_waiting_condition
                            WHERE   id_threshold =:id_threshold;";

                            $stmt = $db->prepare($sql);
                            $stmt->bindValue(":id_operation", $parsedBody['id_operation'], PDO::PARAM_INT);
                            $stmt->bindValue(":threshold_value", $parsedBody['threshold_value'], PDO::PARAM_INT);
                            $stmt->bindValue(":id_signal_type", $parsedBody['id_signal_type'], PDO::PARAM_INT);
                            $stmt->bindValue(":id_method_waiting_condition", $parsedBody['id_method_waiting_condition'], PDO::PARAM_INT);
                            $stmt->bindValue(":id_threshold", $ancient_thr_id, PDO::PARAM_INT);

                            $stmt->execute();

                        }else{

                            $sql = "INSERT INTO " . $in_thr_table . " 
                            SET     threshold_value =:threshold_value,
                                    id_operation =:id_operation,
                                    id_signal_type =:id_signal_type,
                                    id_method_waiting_condition =:id_method_waiting_condition;";

                            $stmt = $db->prepare($sql);
                            $stmt->bindValue(":id_operation", $parsedBody['id_operation'], PDO::PARAM_INT);
                            $stmt->bindValue(":threshold_value", $parsedBody['threshold_value'], PDO::PARAM_INT);
                            $stmt->bindValue(":id_signal_type", $parsedBody['id_signal_type'], PDO::PARAM_INT);
                            $stmt->bindValue(":id_method_waiting_condition", $parsedBody['id_method_waiting_condition'], PDO::PARAM_INT);
                            $stmt->execute();
                        }

                    }
                }else{
                    if($out_waiting_condition === 'Signal threshold'){

                        $sql = "UPDATE " . $in_thr_table . "
                            SET     threshold_value =:threshold_value,
                                    id_operation =:id_operation,
                                    id_signal_type =:id_signal_type,
                                    id_method_waiting_condition =:id_method_waiting_condition
                            WHERE   id_threshold =:id_threshold;";

                        $stmt = $db->prepare($sql);
                        $stmt->bindValue(":id_operation", null, PDO::PARAM_INT);
                        $stmt->bindValue(":threshold_value", null, PDO::PARAM_INT);
                        $stmt->bindValue(":id_signal_type", null, PDO::PARAM_INT);
                        $stmt->bindValue(":id_method_waiting_condition", $parsedBody['id_method_waiting_condition'], PDO::PARAM_INT);
                        $stmt->bindValue(":id_threshold", $parsedBody['id_threshold'], PDO::PARAM_INT);

                        $stmt->execute();
                    }
                }

                /*--------------------- update the waiting table for all   --------------------*/

                $sql = "UPDATE " . $in_wait_cond_table . " 
                        SET     timeout_value =:timeout_value,
                                id_waiting_condition =:id_waiting_condition,
                                waiting_value_label =:waiting_value_label,
                                id_step =:id_step
                        WHERE   id_method_waiting_condition =:id_method_waiting_condition
                        ;";

                $stmt = $db->prepare($sql);

                $stmt->bindValue(":timeout_value", $parsedBody['timeout_value'], PDO::PARAM_INT);
                $stmt->bindValue(":waiting_value_label", $waiting_value_label, PDO::PARAM_STR);
                $stmt->bindValue(":id_waiting_condition", $parsedBody['id_waiting_condition'], PDO::PARAM_INT);
                $stmt->bindValue(":id_step", $parsedBody['id_step'], PDO::PARAM_INT);
                $stmt->bindValue(":id_method_waiting_condition", $parsedBody['id_method_waiting_condition'], PDO::PARAM_INT);

                $stmt->execute();

                if ($stmt) {
                    $httpCode = 200;
                    $data['status'] = 'success';
                    $data['code'] = $httpCode;
                    $data['outgoing condition'] = $out_waiting_condition;
                    $data['incoming condition'] = $in_waiting_condition;
                } else {
                    $data['status'] = 'error';
                    $data['code'] = 'sqlProblem';
                    $data['content'] = 'The ID ' . $args['id'] . ' does not exist.';
                }
            }

        } // 5. DELETE avec ID = Suppression de l'entrée X
        else if ($request->getMethod() == 'DELETE' && isset($args['id'])) {

            if (is_numeric($args['id'])) {

                $sql = "DELETE FROM `" . $parsedBody['method_list'] . "` WHERE id_method_list = :id_method_list";

                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_method_list", $args['id'], PDO::PARAM_INT);
                $stmt->execute();
                if ($stmt && $stmt->rowCount() > 0) {
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

    public static function table_waiting_name_prefix($name)
    {
        $newName = $name . "_waiting";
        return $newName;
    }

    public static function table_threshold_name_prefix($tableName)
    {
        $newName = $tableName. "_threshold";
        return $newName;
    }


}




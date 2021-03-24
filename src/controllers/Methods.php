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
        $request->getHeaderLine('Content-Type');
        $parsedBody = $request->getParsedBody();

        $data = array();
        $data['status'] = 'error';


        // 1. GET sans ID = liste
        if ($request->getMethod() == 'GET' && !isset($args['id'])) {

            $sql = "SELECT * FROM `method_list`";

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
                $data['message'] = 'No process created in the database yet';
                $data['content'] = $result;
            } else {
                $data['status'] = 'error';
                $data['code'] = 'sql error';
            }
        } // 2. GET avec ID = détail de l'entrée X
        else if ($request->getMethod() == 'GET' && isset($args['id'])) {

            if (is_numeric($args['id'])) {

                $sql = "SELECT method_name FROM method_list WHERE id_method_list = :id_method_list";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_method_list", $args['id'], PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt && $stmt->rowCount() > 0) {

                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $method_name = $result[0]['method_name'];
                    $table_waiting = ConfigController::table_waiting_name_prefix($method_name);
                    $table_threshold = ConfigController::table_threshold_name_prefix($method_name);

                    $sql = "SELECT *, @rownum := @rownum +1 AS rank FROM " . $method_name . " AS mt
                            LEFT JOIN " . $table_waiting . " AS wt ON mt.id_step = wt.id_step
                            LEFT JOIN waiting_condition AS wc ON wc.id_waiting_condition = wt.id_waiting_condition
                            LEFT JOIN " . $table_threshold . " AS th ON  wt.id_method_waiting_condition = th.id_method_waiting_condition
                            LEFT JOIN operation AS op ON op.id_operation = th.id_operation
                            INNER JOIN method_list AS ml ON ml.id_method_list = mt.id_method_list, 
                            (SELECT @rownum := -1) r
                            GROUP BY mt.id_step
                            ;";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $result_Methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $sql = "SELECT COUNT(*) AS total FROM " . $method_name;
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $count = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($stmt) {

                        if ($result) {

                            $httpCode = 200;
                            $data['status'] = 'success';
                            $data['code'] = $httpCode;
                            $data['content'] = $result_Methods;
                            $data['count'] = $count;

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

            if (isset($parsedBody['method_name']) && $parsedBody['method_name'] != '') {

                /*---------------------     Check if method already exists    --------------------*/

                $sql = "SELECT EXISTS (SELECT * FROM method_list WHERE method_name= '" . $parsedBody['method_name'] . "') AS count;";

                $stmt = $db->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($result[0]["count"]) {

                    $data['status'] = 'error';
                    $data['code'] = 'param error';
                    $data['message'] = "Method already exists in database ";

                } else {
                    /*---------------------    Create method table   -----------------------------*/

                    $sql = "CREATE TABLE `" . $parsedBody['method_name'] . "` (
                              `id_step` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
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
                              `id_method_list` int(10)
                              );";

                    $stmt = $db->prepare($sql);
                    $stmt->execute();


                    if ($stmt) {

                        $sql = "ALTER TABLE `" . $parsedBody['method_name'] . "`ADD FOREIGN KEY (`id_method_list`) 
                                    REFERENCES `method_list` (`id_method_list`);";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();

                        $table_waiting = ConfigController::table_waiting_name_prefix($parsedBody['method_name']);
                        $id_waiting = "id_method_waiting_condition";

                        /*---------------------    Create method_waiting table     -----------------------------*/

                        $sql = "CREATE TABLE `" . $table_waiting . "` (
                              `" . $id_waiting . "`  int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                              `timeout_value` int(10),                              
                              `waiting_value_label`varchar (100),
                              `id_waiting_condition` int(10),
                              `id_step` int(10)
                            );";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();


                        $sql = "ALTER TABLE `" . $table_waiting . "` ADD FOREIGN KEY (`id_step`) REFERENCES `" . $parsedBody['method_name'] . "`(`id_step`);";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();

                        $sql = "ALTER TABLE `" . $table_waiting . "` ADD FOREIGN KEY (`id_waiting_condition`) REFERENCES `waiting_condition` (`id_waiting_condition`);";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();

                        $thresholdTableName = ConfigController::table_threshold_name_prefix($parsedBody['method_name']);

                        /*---------------------    Create method_threshold table     -----------------------------*/

                        $sql = "CREATE TABLE `" . $thresholdTableName . "` (
                              `id_threshold` int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
                              `threshold_value` int(10),
                              `id_operation` int(10),
                              `id_signal_type` int(10),
                              `id_method_waiting_condition` int(10)
                            );";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();

                        $sql = "ALTER TABLE `" . $thresholdTableName . "` ADD FOREIGN KEY (`id_method_waiting_condition`) REFERENCES `" . $table_waiting . "` (`id_method_waiting_condition`);";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();
                        $stmt = $db->prepare($sql);
                        $stmt->execute();

                        $sql = "ALTER TABLE `" . $thresholdTableName . "` ADD FOREIGN KEY (`id_operation`) REFERENCES `operation` (`id_operation`);";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();
                        $stmt = $db->prepare($sql);
                        $stmt->execute();

                        $sql = "ALTER TABLE `" . $thresholdTableName . "` ADD FOREIGN KEY (`id_signal_type`) REFERENCES `signal_type` (`id_signal_type`);";
                        $stmt = $db->prepare($sql);
                        $stmt->execute();
                        $stmt = $db->prepare($sql);
                        $stmt->execute();

                        if ($stmt) {

                            $sql = "INSERT INTO `method_list` SET 
                                        method_name = :method_name;";

                            $stmt = $db->prepare($sql);
                            $stmt->bindValue(":method_name", $parsedBody['method_name'], PDO::PARAM_STR);
                            $stmt->execute();


                            if ($stmt) {
                                $httpCode = 200;
                                $data['status'] = 'success';
                                $data['code'] = $httpCode;
                                $data['message'] = "Method created ";
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
                $data['content'] = 'The param \'method_list\' is required';
            }

        } // 4. PATCH With ID
        else if ($request->getMethod() == 'PATCH' && isset($args['id'])) {

            $request->getHeaderLine('Content-Type');
            $parsedBody = $request->getParsedBody();
            if (is_numeric($args['id'])) {

                $sql = "SELECT method_name FROM method_list WHERE id_method_list = :id_method_list";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_method_list", $args['id'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $method_name = $result[0]['method_name'];

                $table_waiting = ConfigController::table_waiting_name_prefix($method_name);
                $table_threshold = ConfigController::table_threshold_name_prefix($method_name);

                $sql = "SELECT waiting_label FROM waiting_condition AS wc
                            INNER JOIN " . $table_waiting . " AS wt ON wc.id_waiting_condition = wt.id_waiting_condition
                            WHERE wt.id_step =:id_step;";

                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_step", $parsedBody['id_step'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $waiting_label = $result[0]['waiting_label'];


                if ($waiting_label === 'Signal threshold') {
                    $sql = "SELECT  * FROM " . $table_waiting . " AS wt 
                            INNER JOIN " . $method_name . " AS mt ON  wt.id_step = mt.id_step
                            INNER JOIN method_list AS ml ON mt.id_method_list = ml.id_method_list
                            INNER JOIN waiting_condition AS wc ON wt.id_waiting_condition = wc.id_waiting_condition
                            INNER JOIN " . $table_threshold . " AS th ON wt.id_method_waiting_condition = th.id_method_waiting_condition
                            INNER JOIN signal_type AS st ON th.id_signal_type = st.id_signal_type
                            INNER JOIN operation AS op ON th.id_operation = op.id_operation
                            WHERE wt.id_step =:id_step 
                            GROUP BY mt.step
                          ;";
                } else {
                    $sql = "SELECT  * FROM " . $table_waiting . " AS wt 
                            INNER JOIN " . $method_name . " AS mt ON  wt.id_step = mt.id_step
                            INNER JOIN method_list AS ml ON mt.id_method_list = ml.id_method_list
                            INNER JOIN waiting_condition AS wc ON wt.id_waiting_condition = wc.id_waiting_condition
                            WHERE mt.id_step =:id_step
                            GROUP BY mt.step
                          ;";
                }

                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_step", $parsedBody['id_step'], PDO::PARAM_INT);
                //  $stmt->bindValue(":id_method_list", $parsedBody['id_method_list'], PDO::PARAM_INT);
                $stmt->execute();


                if ($stmt) {

                    $result_Methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($result) {

                        $httpCode = 200;
                        $data['status'] = 'success';
                        $data['code'] = $httpCode;
                        $data['content'] = $result_Methods;

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


        } // 4. bis PUT With ID
        else if (($request->getMethod() == 'PUT') && isset($args['id'])) {

            if (is_numeric($args['id'])) {

                /*---------------   Retrieve the method name    -----------------------------*/

                $sql = "SELECT method_name FROM method_list WHERE id_method_list =:id_method_list";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_method_list", $parsedBody['id_method_list'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $table_name = $result[0]['method_name'];

                $table_waiting = ConfigController::table_waiting_name_prefix($table_name);
                $table_threshold = ConfigController::table_threshold_name_prefix($table_name);

                /*---------------   Retrieve the waiting condition    -----------------------------*/

                $sql = "SELECT id_method_waiting_condition, id_waiting_condition FROM " . $table_waiting . " WHERE id_step =:id_step";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_step", $args['id'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $id_method_waiting_condition = $result[0]['id_method_waiting_condition'];
                $id_waiting_condition = $result[0]['id_waiting_condition'];


                $sql = "SELECT waiting_label FROM waiting_condition WHERE id_waiting_condition =:id_waiting_condition";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_waiting_condition", $id_waiting_condition, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $waiting_label = $result[0]['waiting_label'];

                /*---------------   If signal is threshold, delete the threshold entry   -----------------------------*/

                if ($id_waiting_condition) {



                    if ($waiting_label === 'Signal threshold') {

                        $sql = "SELECT threshold_value FROM  " . $table_threshold . "  WHERE id_method_waiting_condition =:id_method_waiting_condition";
                        $stmt = $db->prepare($sql);
                        $stmt->bindValue(":id_method_waiting_condition", $id_method_waiting_condition, PDO::PARAM_INT);
                        $stmt->execute();
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $threshold_value = $result[0]['threshold_value'];

                        if( $threshold_value !== NULL){
                            $sql = "DELETE FROM " . $table_threshold . " WHERE id_method_waiting_condition =:id_method_waiting_condition";
                            $stmt = $db->prepare($sql);
                            $stmt->bindValue(":id_method_waiting_condition", $id_method_waiting_condition, PDO::PARAM_INT);
                            $stmt->execute();
                        }

                    }
                    $sql = "DELETE FROM " . $table_waiting . " WHERE id_step =:id_step";
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(":id_step", $args['id'], PDO::PARAM_INT);
                    $stmt->execute();
                }

                /*---------------   Retrieve the step in order to resort steps  -----------------------------*/

                $sql = "SELECT step FROM " . $table_name . " WHERE id_step >=:id_step";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_step", $args['id'], PDO::PARAM_INT);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $i = 1;

                while ($i < sizeof($results)) {

                    $sql = "UPDATE " . $table_name . " SET step =:step1 WHERE step = :step2";
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(":step1", $results[$i - 1]['step'], PDO::PARAM_INT);
                    $stmt->bindValue(":step2", $results[$i]['step'], PDO::PARAM_INT);

                    $stmt->execute();
                    $i++;
                }

                $sql = "DELETE FROM " . $table_name . " WHERE id_step =:id_step";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_step", $args['id'], PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt && $stmt->rowCount() > 0) {
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

                $sql = "SELECT method_name FROM method_list WHERE id_method_list =:id_method_list";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_method_list", $args['id'], PDO::PARAM_INT);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $table_name = $result[0]['method_name'];

                $table_waiting = ConfigController::table_waiting_name_prefix($table_name);
                $table_threshold = ConfigController::table_threshold_name_prefix($table_name);

                $sql = "DROP TABLE " . $table_threshold;
                $stmt = $db->prepare($sql);
                $stmt->execute();

                $sql = "DROP TABLE " . $table_waiting;
                $stmt = $db->prepare($sql);
                $stmt->execute();

                $sql = "DROP TABLE " . $table_name;
                $stmt = $db->prepare($sql);
                $stmt->execute();

                $sql = "DELETE FROM method_list WHERE id_method_list =:id_method_list";
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


}




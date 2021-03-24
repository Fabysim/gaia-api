<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class RunMethodController
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



        // 4. PUT/PATCH avec ID = Modification de l'entée X
        if (($request->getMethod() == 'PUT' || $request->getMethod() == 'PATCH') && isset($args['id'])) {


            $request->getHeaderLine('Content-Type');
            $parsedBody = $request->getParsedBody();

            if (is_numeric($args['id'])) {

                /* ----------------         Get the method name         ---------------------------*/

                $sql = "SELECT method_name FROM method_list WHERE id_method_list = :id_method_list";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_method_list", $args['id'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $method_name = $result[0]['method_name'];

                $table_waiting = ConfigController::table_waiting_name_prefix($method_name);
                $table_threshold = ConfigController::table_threshold_name_prefix($method_name);

                /* ----------------         Get the id_step label         ---------------------------*/

                $sql = "SELECT id_step FROM ".$method_name ." WHERE step = :step";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":step", $parsedBody['step'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $id_step = $result[0]['id_step'];


                /* ----------------         Get the waiting label         ---------------------------*/

                $sql = "SELECT waiting_label FROM waiting_condition AS wc
                            INNER JOIN " . $table_waiting . " AS wt ON wc.id_waiting_condition = wt.id_waiting_condition
                            WHERE wt.id_step =:id_step;";

                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_step", $id_step, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $waiting_label = $result[0]['waiting_label'];

                /* ----------------         Get the waiting_condition table         ---------------------------*/

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
                $stmt->bindValue(":id_step", $id_step, PDO::PARAM_INT);

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




<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class RankingController
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


        if ($request->getMethod() == 'GET' && isset($args['id'])) {

            if (is_numeric($args['id'])) {

                $sql = "SELECT method_name FROM method_list WHERE id_method_list =:id_method_list";

                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_method_list", $args['id'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $table_name = $result[0]['method_name'];

                $sql = "SELECT t.*, @rownum := @rownum +1 AS rank FROM " . $table_name . " t, (SELECT @rownum := -1) r";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);



                $stmt = $db->prepare($sql);

                $stmt->bindValue(":id_operation", $args['id'], PDO::PARAM_INT);

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
        } // 4. PUT/PATCH avec ID = Modification de l'entée X
        else if (($request->getMethod() == 'PUT' || $request->getMethod() == 'PATCH') && isset($args['id'])) {

            $parsedBody = $request->getParsedBody();

            if (is_numeric($args['id'])) {

                $sql = "SELECT method_name FROM method_list WHERE id_method_list =:id_method_list";

                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_method_list", $args['id'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $table_name = $result[0]['method_name'];

                $sql = "SELECT step FROM ". $table_name." WHERE id_step =:id_step";

                $stmt = $db->prepare($sql);
                $stmt->bindValue(":id_step", $parsedBody['id_step'], PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $step = $result[0]['step'];

                /*-----------------  Get the following record ID  -----------------*/

                if($parsedBody['operation'] === 0){

                    $sql = "SELECT id_step FROM ".$table_name." WHERE step < ".$step." LIMIT 1";
                }
                if($parsedBody['operation'] === 1){

                    $sql = "SELECT id_step FROM ".$table_name." WHERE step > ".$step." LIMIT 1";
                }

                $stmt = $db->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $following_id_step = $result[0]['id_step'];

                /*-----------------  Change the following record step into current step  -----------------*/

                $sql = "UPDATE ".$table_name." SET step =:step WHERE id_step =:id_step";
                $stmt = $db->prepare($sql);
                $stmt->bindValue(":step", $step, PDO::PARAM_INT);
                $stmt->bindValue(":id_step", $following_id_step, PDO::PARAM_INT);
                $stmt->execute();

                /*-----------------  Increment/decrement the current record's step -----------------*/

                $sql = "UPDATE ".$table_name." SET step =:step WHERE id_step =:id_step";
                $stmt = $db->prepare($sql);

                if($parsedBody['operation'] === 0){
                    $stmt->bindValue(":step", $step - 1, PDO::PARAM_INT);
                }
                if($parsedBody['operation'] === 1 ){
                    $stmt->bindValue(":step", $step + 1, PDO::PARAM_INT);
                }

                $stmt->bindValue(":id_step", $parsedBody['id_step'], PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt) {
                    $httpCode = 200;
                    $data['status'] = 'success';
                    $data['code'] = $httpCode;
                }

            } else {
                $data['status'] = 'error';
                $data['code'] = 'idMustBeNumeric';
                $data['content'] = 'The ID must be a digit\'';
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




<?php

namespace App\Http\Controllers;

use App\Components\AuthenticationComponent;
use App\Components\DataComponent;
use App\Components\LogComponent;
use App\Repository\DatabaseModel;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    public function getDatabase(Request $request){

        $validation = AuthenticationComponent::validate($request);
        LogComponent::response($request, $validation);

        if ($validation->result) {
            //check privilege
            DataComponent::checkPrivilege($request, "database", "view");
            
            $limit = !empty($request->limit)?$request->limit:10;
            $offset = !empty($request->offset)?$request->offset:0;
            
            $data = DatabaseModel::getAll($request, $limit, $offset);

            $response = [
                'result' => true,
                'response' => 'Get All Data',
                'data' => $data['data'],
                'total_data' => $data['total_data']
            ];
           
        } else {
            $response = $validation;
        }
        return response()->json($response, 200);
    }
}

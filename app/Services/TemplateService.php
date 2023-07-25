<?php

namespace App\Services;

use App\Components\DataComponent;
use App\Models\Template;
use App\Repositories\SyncQueueRepository;
use App\Repositories\TemplateRepository;
use stdClass;


class TemplateService {


    public static function delete($request) {

        $result = new stdClass();
        $result->response = "Failed to delete Template data";
        $result->result = false;

        $templateById = TemplateRepository::findOneById($request->id);

        if(!empty($templateById)) {

            TemplateRepository::delete($templateById);

            $result->response = "Template data deleted";
            $result->result = true;
        } else {

            $result->response = "Template doesn't exist";
        }

        return $result;
    }


    public static function findData($id) {

        $result = new stdClass();
        $result->response = "Failed to find template data";
        $result->result = false;

        $result->syncQueue = SyncQueueRepository::findOneByTemplateId($id);
        $result->template = TemplateRepository::findOneById($id);

        $result->response = "Template data found";
        $result->result = true;

        return $result;
    }


    public static function findTable($request, $active) {

        $result = new stdClass();
        $result->draw = $request->draw;

        $account = DataComponent::initializeAccount($request);

        $defaultOrder = ["created.timestamp"];
        $templates = DataComponent::initializeTableQuery(new Template(), DataComponent::initializeObject($request->columns), DataComponent::initializeObject($request->order), $defaultOrder);

        if($active) {

            $templates->where([
                ["status", "=", "Active"]
            ]);
        }

        $templates = DataComponent::initializeTableData($account, $templates);

        $result->recordsTotal = $templates->count("_id");
        $result->recordsFiltered = $result->recordsTotal;

        $result->data = $templates->forPage(DataComponent::initializePage($request->start, $request->length), $request->length)->get();

        return $result;
    }


    public static function initializeData($request) {

        $result = new stdClass();
        $result->response = "Failed to initialize template data";
        $result->result = false;

        $result->template = TemplateRepository::findOneById($request->id);

        $result->response = "Template data initialized";
        $result->result = true;

        return $result;
    }


    public static function insert($request) {

        $result = new stdClass();
        $result->response = "Failed to insert template data";
        $result->result = false;

        $validation = self::validateData($request, false);
        if($validation->result) {

            $templateLast = TemplateRepository::insert(DataComponent::initializeAccount($request), $validation->template);

            $result->response = "Template data inserted";
            $result->result = true;
        } else {
            $result->response = $validation->response;
        }

        return $result;
    }


    public static function update($request, $api) {

        $result = new stdClass();
        $result->response = "Failed to update template data";
        $result->result = false;

        $validation = self::validateData($request, $api);

        if($validation->result) {

            TemplateRepository::update(DataComponent::initializeAccount($request), $validation->template);

            $result->response = "Template data updated";
            $result->result = true;
        } else {
            $result->response = $validation->response;
        }

        return $result;
    }


    public static function validateData($request) {

        $result = new stdClass();
        $result->response = "Failed to validate template data";
        $result->result = false;

        $validation = DataComponent::checkNucode($request, $request->nucode, []);

        $result->template = new Template();

        if(!is_null($request->id)) {

            $result->template = TemplateRepository::findOneById($request->id);

            if(empty($result->template)) {
                array_push($validation, false);
                $result->response = "Template doesn't exist";
            }
        }
        $result->template->description = $request->description;
        $result->template->name = $request->name;
        $result->template->nucode = $request->nucode;
        $result->template->textMessage = $request->textMessage;
        $result->template->status = $request->status;
        $result->template->isDefault = $request->isDefault;
        $result->template->media = [
            "mediaType" => $request->media['mediaType'],
            "mediaUrl" => $request->media['mediaUrl']
        ];
        $templateByNameNucode = TemplateRepository::findOneByNameNucode($request->name, $request->nucode);

        if(!empty($templateByNameNucode)) {

            if(!$request->id == $templateByNameNucode->id) {
                array_push($validation, false);
                $result->response = "Template name already exist";
            }
        }
        if(empty($validation)) {
            $result->response = "Template data validated";
            $result->result = true;
        }

        return $result;
    }


}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CallingKeyResource;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\DriverTypeResource;
use App\Http\Resources\OpeningResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\StageResource;
use App\Http\Resources\UniversityResource;
use App\Models\CallingKey;
use App\Models\Document;
use App\Models\DriverType;
use App\Models\Opening;
use App\Models\Service;
use App\Models\Social;
use App\Models\Stage;
use App\Models\University;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends BaseController
{
    public function test()
    {
        sendNotification([
            'title' => 'test Title',
            'body' => 'test description',
            'tokens' => ['eZObSH63ToGe9YfyYa3rzd:APA91bFmsoCurAtLLDcmc3sY9L5BjiqMujoSWEudy4KHh6MT3gl1ISOk5zuEl78qMwryXnhGfLZZOrRuQ1VzJQq-vCkL5MA-zUiMF7siz4cieQF0orwo1Cq7ubzF8QHFiFlO41oqnA7W']
        ]);
    }

    public function get()
    {
        $services = Service::all();
        $opening = Opening::all();
        $callingKeys = CallingKey::all();
        $university = University::all();
        $stages = Stage::all();
        $documents = Document::all();
        $socials = Social::all();
        $driverTypes = DriverType::all();

        $data['services'] = ServiceResource::collection($services);
        $data['opening'] = OpeningResource::collection($opening);
        $data['calling_keys'] = CallingKeyResource::collection($callingKeys);
        $data['universities'] = UniversityResource::collection($university);
        $data['stages'] = StageResource::collection($stages);
        $data['documents'] = DocumentResource::collection($documents);
        $data['driver_documents'] = $this->getDriverDocuments($documents);
        $data['socials'] = $socials;
        $data['driver_types'] = DriverTypeResource::collection($driverTypes);

        return $this->sendResponse($data, __('Data'));
    }

    private function getDriverDocuments($documents)
    {
        $data = [];
        foreach ($documents as $index => $document) {
            $data[$index]['title-ar'] = $document->{"title-ar"};
            $data[$index]['title-eng'] = $document->{"title-eng"};
            $data[$index]['file-link'] = str_replace('/documents/', '/documents/driver/', $document->{"file-link"});
        }

        return $data;
    }

    public function getAnnouncement()
    {
        $announcements = DB::table('announce')->get();

        return $this->sendResponse($announcements, __('Data'));
    }

    public function getContacts()
    {
        $contacts = DB::table('contact')->get();

        return $this->sendResponse($contacts, __('Contacts'));
    }
}

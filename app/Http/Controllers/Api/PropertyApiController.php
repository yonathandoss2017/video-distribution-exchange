<?php

namespace App\Http\Controllers\Api;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Repositories\ContentProviderRepository;
use App\Repositories\ServiceProviderPropertyRepository;

abstract class PropertyApiController extends SecureApiController
{
    /**
     * @var ContentProviderRepository
     */
    private $contentProviderRepository;

    /**
     * @var ServiceProviderPropertyRepository
     */
    private $serviceProviderRepository;

    public function __construct(
        ContentProviderRepository $contentProviderRepository = null,
        ServiceProviderPropertyRepository $serviceProviderRepository = null
    ) {
        $this->contentProviderRepository = $contentProviderRepository;
        $this->serviceProviderRepository = $serviceProviderRepository;
    }

    public function authenticateProperty(Request $request, $type)
    {
        parent::authenticate($request);

        $property = null;
        if (Property::TYPE_CP == $type) {
            $property = $this->contentProviderRepository
                ->findContentProviderByKeyAndToken($this->key, $this->token);
        } elseif (Property::TYPE_SP == $type) {
            $property = $this->serviceProviderRepository
                ->findServiceProviderByKeyToken($this->key, $this->token);
        }

        if (!isset($property)) {
            abort(403);
        }

        return $property;
    }
}

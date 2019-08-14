<?php

namespace App\Http\Controllers\Solr;

use app\Http\Controllers\Controller;

class SolariumController extends Controller
{
    protected $client;

    public function __construct(\Solarium\Client $client)
    {
        $this->client = $client;
    }

    public function ping()
    {
        // create a ping query
        $client = $this->client;
        $ping = $client->createPing();

        // execute the ping query
        try {
            $this->client->ping($ping);
            echo response()->json('OK');
        } catch (\Solarium\Exception $e) {
            echo response()->json('ERROR', 500);
        }
    }

    public function search()
    {
        $client = $this->client;
        $query = $client->createSelect();
        $query->addFilterQuery(['key' => 'ivx_id', 'query' => 'ivx_id:5038', 'tag' => 'include']);
        $facets = $query->getFacetSet();
        $facets->createFacetField(['field' => 'ivx_id', 'exclude' => 'exclude']);
        $resultset = $client->select($query);

        // display the total number of documents found by solr
        echo 'NumFound: '.$resultset->getNumFound();

        // show documents using the resultset iterator
        foreach ($resultset as $document) {
            echo '<hr/><table>';

            // the documents are also iterable, to get all fields
            foreach ($document as $field => $value) {
                // this converts multivalue fields to a comma-separated string
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }

                echo '<tr><th>'.$field.'</th><td>'.$value.'</td></tr>';
            }

            echo '</table>';
        }
    }
}

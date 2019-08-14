<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;

class LogsController extends Controller
{
    private $log_viewer;

    public function __construct()
    {
        $this->log_viewer = new LaravelLogViewer();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $folderFiles = [];
        if ($request->input('f')) {
            $this->log_viewer->setFolder(Crypt::decrypt($request->input('f')));
            $folderFiles = $this->log_viewer->getFolderFiles(true);
        }
        if ($request->input('l')) {
            $this->log_viewer->setFile(Crypt::decrypt($request->input('l')));
        }

        if ($request->input('dl')) {
            return response()->download($this->pathFromInput($request->input('dl')));
        } elseif ($request->has('del')) {
            app('files')->delete($this->pathFromInput($request->input('del')));

            return redirect($request->url());
        }

        $data = [
            'logs' => $this->log_viewer->all(),
            'folders' => $this->log_viewer->getFolders(),
            'current_folder' => $this->log_viewer->getFolderName(),
            'folder_files' => $folderFiles,
            'files' => $this->log_viewer->getFiles(true),
            'current_file' => $this->log_viewer->getFileName(),
            'standardFormat' => true,
        ];

        if (is_array($data['logs'])) {
            $firstLog = reset($data['logs']);
            if (!$firstLog['context'] && !$firstLog['level']) {
                $data['standardFormat'] = false;
            }
        }

        return view('admin.pages.logs.index', compact('data'));
    }

    /**
     * @param string $input_string
     *
     * @return string
     *
     * @throws \Exception
     */
    private function pathFromInput($input_string)
    {
        return $this->log_viewer->pathToLogFile(Crypt::decrypt($input_string));
    }
}

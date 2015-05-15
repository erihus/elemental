<?php namespace Elemental\Controllers;

use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Image;
use Storage;

class FileController extends RootController {


   protected $request;
   protected $filesystem;

    public function __construct(Request $request, Filesystem $filesystem)
    {
        $this->request = $request;
        $this->filesystem = $filesystem;
        $this->middleware('auth');
    }


    public function chunkTest() {
        return response('ok', 300);
    }

    /**
     * Handle File Uploads
     *
     * @return Response
     */
    public function upload()
    {
        try{
            $width = $this->request->input('width');
            $height = $this->request->input('height');
            $med_width = $this->request->input('med_width');
            $med_height = $this->request->input('med_height');
            $sml_width = $this->request->input('sml_width');
            $sml_height = $this->request->input('sml_height');
            $file = $this->request->file('file');
            $fileName = md5($file->getClientOriginalName().rand());
            $ext = $file->getClientOriginalExtension();
            $dest = public_path().'/uploads';
            $file->move($dest, $fileName.'.'.$ext);

            if(!is_null($width) && !is_null($height)) {
                if(!file_exists(public_path().'/uploads/thumbs')) {
                    mkdir(public_path().'/uploads/thumbs');
                }
                
                $img = Image::make($dest.'/'.$fileName.'.'.$ext)->resize($width, $height)->save();
                $img->resize(100,100)->save($dest."/thumbs/".$fileName.'.'.$ext);
                if(!is_null($med_width) && !is_null($med_height)) {
                    Image::make($dest.'/'.$fileName.'.'.$ext)->resize($med_width,$med_height)->save($dest."/".$fileName.'_med.'.$ext);
                }

                if(!is_null($sml_width) && !is_null($sml_height)) {
                    Image::make($dest.'/'.$fileName.'.'.$ext)->resize($sml_width,$sml_height)->save($dest."/".$fileName.'_sml.'.$ext);
                }
                
            }   

            $response = ['ok'=>1, 'path'=>"uploads/".$fileName.".".$ext];

            if(!is_null($med_width) && !is_null($med_height)) {
                $response['med_path'] = "uploads/".$fileName."_med.".$ext;
            }

             if(!is_null($sml_width) && !is_null($sml_height)) {
                $response['sml_path'] = "uploads/".$fileName."_sml.".$ext;
            }

            return response()->json($response, 200);  
        } catch (Exception $e) {
            return response()->json(['ok'=>0, 'errors'=>$e->getMessage()], 500);  
        }
    
    }

    public function redactorUpload()
    {
        $file = $this->request->file('file');
        $fileName = md5($file->getClientOriginalName().rand());
        $ext = $file->getClientOriginalExtension();
        $dest = public_path().'/uploads';

        $file->move($dest, $fileName.'.'.$ext);

        $response = ['filelink'=> asset("uploads/".$fileName.".".$ext) ];
        return response()->json($response, 200);         
    }

    public function listFiles()
    {
        $dir = public_path().'/uploads';
        $thumbDir = public_path().'/uploads/thumbs';
        $uploads = $this->filesystem->allFiles($dir);
        $thumbs = $this->filesystem->allFiles($thumbDir);
        $listArray = [];

        for($i=0; $i<count($uploads); $i++) {
            $fileName = $uploads[$i]->getFileName();
            $listArray[$i]['image'] = asset('uploads/'.$fileName);
            foreach($thumbs as $thumb) {
                if($thumb->getFileName() == $fileName) {
                    $listArray[$i]['thumb'] = asset('uploads/thumbs/'.$fileName);
                } else {
                     $listArray[$i]['thumb'] = asset('uploads/'.$fileName);
                }
            }
        }

         return response()->json($listArray);
    }

}

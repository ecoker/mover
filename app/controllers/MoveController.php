<?php

class MoveController extends BaseController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $cwd = getcwd();
        if (strpos($cwd, '/Sites') > 0) {
            $start = substr($cwd, 0, strpos($cwd, '/Sites') + 6);
            $environments = array(
                'LOCAL'     => 'file://'.$start.'/',
                // 'UAT-PROD'  => 'file:///Volumes/creative/UAT-Production/',
                'UAT-STAGE' => 'file:///Volumes/Content/',
                'PROD'      => 'file:///Volumes/ecommcreative-prod/'
            );
            $push_locations = array();
            /* LIST OF DIRECTORIES THAT WE DON'T NEED TO MOVE THINGS TO THAT EXIST LOCALLY OR IN OTHER ENVIRONMENTS */
            $excludedDirs = array('shoes.com', 'Shoes', 'ShoeSteal', 'null', 'BusterBrown', 'FactoryBrandShoes', 'LibbyEdelman', 'TEST', 'Unnamed Site 2', 'SkateStyles', 'MindBodySole', 'Nevados', 'Publicity', 'SamEdelman', 'ZodiacUsaShoes', 'Zodiac', '_JUSTIFY-OR-TRASH', 'MindBodySoleShoes', 'bdimages', 'BusterBrownShoes', 'FactoryBrandShoes', 'LibbyEdelman', 'publicity', '_EMAIL', '_RefDocs', '_updatedWorkflow', 'BrownShoe', 'BrownShoe2012', 'CarlosRedesign12', 'D2Ctoolkit', 'Journal', 'Lar', 'Neutralized', 'shoehappens');
            /* WE NEED CONSISTENT NAMES FOR FOLDERS, THIS IS A "TRANSLATION" OF THE FOLDERS BETWEEN ENVIRONMENTS */
            $translate_dir = array(
                'carlosshoes.com'        => 'Carlos',
                'CarlosShoes'            => 'Carlos',
                'circusbysamedelman.com' => 'CircusBySamEdelman',
                'circusbysamedelman'     => 'CircusBySamEdelman',
                'drschollsshoes.com'     => 'DrScholls',
                'DrSchollsShoes'         => 'DrScholls',
                'famousfootwear.com'     => 'Famous',
                'fergieshoes.com'        => 'Fergie',
                'FergieShoes'            => 'Fergie',
                'francosarto.com'        => 'FrancoSarto',
                'lifestride.com'         => 'LifeStride',
                'lifestride'             => 'LifeStride',
                'naturalizer.ca'         => 'NaturalizerCA',
                'naturalizer.com'        => 'Naturalizer',
                'nayashoes.com'          => 'Naya',
                'ryka.com'               => 'Ryka',
                'viaspiga.com'           => 'ViaSpiga',
                'bzees'                  => 'Bzees',
                'FamousFootwear'         => 'Famous',
                'FamousNew'              => 'Famous',
                'NaturalizerCanada'      => 'NaturalizerCA',
                'NayaShoes'              => 'Naya'
            );
            $errors = array();
            foreach($environments as $name => $directory){
                if (is_readable($directory)){
                    $directory_list = opendir($directory);
                    while (FALSE !== ($file = readdir($directory_list))) {
                        if($file != '.' && $file != '..' && substr($file, 0, 1) != '.') {
                            $path = $directory.$file;
                            if(is_readable($path)) {
                                if(is_dir($path) && !in_array($file, $excludedDirs) && (preg_match('/\.com|\.ca|bzees$/i', $path) || !preg_match('/local/i', $name)) ){
                                    $file_translated = isset( $translate_dir[$file] ) ? $translate_dir[$file] : $file;
                                    $push_locations[$file_translated][$name] = $path;
                                    if (isset($push_locations[$file_translated]['data'])){
                                        $push_locations[$file_translated]['data'] .= ' data-' . $name . '="' . $file . '"';
                                    } else {
                                        $push_locations[$file_translated]['data'] = 'data-' . $name . '="' . $file . '"';
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $errors[] = '<div data-alert="" class="alert-box alert"> Cannot connect to : ' . $directory . '<a href="#" class="close">×</a> </div>';
                    unset($environments[$name]);
                }
            }
            function push_filter($location) {
                return isset($location['LOCAL']);
            }
            $push_locations = array_filter($push_locations, "push_filter");
            asort($push_locations);
            return View::make('move.index')->with('start', $start)->with('push_locations', $push_locations)->with('environments', $environments)->with('errors', $errors);
        } else {
            die('Is this not running from the Sites folder? Why you make life so hard?');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        /* USED FOR RETURNING THE FILE & DIRECTORY LIST */
        function scan_dir($directory, $detect=false) {
            if(substr($directory,-1) == '/') { $directory = substr($directory,0,-1); }
            if(!file_exists($directory) || !is_dir($directory)) { return FALSE; }
            elseif(is_readable($directory)) {
                $directory_list = opendir($directory);
                while (FALSE !== ($file = readdir($directory_list))) {
                    if($file != '.' && $file != '..' && substr($file, 0, 1) != '.') {
                        $path = $directory.'/'.$file;
                        if(is_readable($path)) {
                            $subdirectories = explode('/',$path);
                            if(is_dir($path)) {
                                $directory_tree['dir'][] = array(
                                    'path'    => $path,
                                    'name'    => end($subdirectories)
                                );
                            } elseif(is_file($path)) {
                                $exploded = explode( '.', $path );
                                $extension = array_pop( $exploded );
                                $directory_tree['file'][] = array(
                                    'path'      => $path,
                                    'name'      => end($subdirectories),
                                    'extension' => $extension,
                                    'size'      => filesize($path)
                                );
                            }
                        }
                    }
                }
                closedir($directory_list); 
                $crumb_path = '';
                foreach( explode('/', $directory) as $folder ){
                    $folder = trim($folder);
                    $crumb_path .= $folder . '/';
                    $directory_tree['crumbs'][] = array(
                        'folder' => $folder,
                        'path'   => $crumb_path
                    );
                }
                if ( $detect ) {
                    $directory_tree['detected'] = true;
                }
                return $directory_tree;
            }else{
                return 'Error: ' . $directory;   
            }
        }

        if (Request::has('url') && !Request::has('detect')) {
            /* --- RETURN FILE AND DIRECTORY LIST */
            $url = Request::get('url');
            return Response::json( scan_dir( $url ) );
        } elseif ( Request::has('from') && Request::has('to') ) {
            /* --- MOVE FILES */
            $from = Request::get('from');
            $to   = Request::get('to');
            $results = array();
            $sameCount = 0;
            foreach($from as $k => $f){
                if (isset($to[$k])){
                    if (strpos($to[$k],'trunk') !== false) {
                        $to[$k] = str_ireplace('trunk/', '', $to[$k]);
                    }
                    if( !file_exists( dirname($to[$k]) ) ) {
                        mkdir(dirname($to[$k]), 0777, true);
                        $results[] = '<p class="alert-box">Created directory ' . dirname($to[$k]) . '<a href="#" class="close">×</a></p>';
                    }
                    if (file_exists($to[$k])) {
                        // CHECK TIMESTAMPS, ETC
                        if (Request::get('overwrite-old') == 'true' && filemtime($f) > filemtime($to[$k])) {
                            if (copy($f, $to[$k])) {
                                touch($to[$k], filemtime($f)); /* SYNC MODIFY DATES OF THE FILES */
                                $results[] = '<p class="alert-box success">Overwrote older file ' . $to[$k] . ' with ' . $f . '.<a href="#" class="close">×</a></p>';
                            } else {
                                $results[] = '<p class="alert-box alert">Error replacing ' . $to[$k] . ' with ' . $f . '.<a href="#" class="close">×</a></p>';
                            }
                        } elseif (Request::get('overwrite-new') == 'true' && filemtime($f) < filemtime($to[$k])) {
                            if (copy($f, $to[$k])) {
                                touch($to[$k], filemtime($f)); /* SYNC MODIFY DATES OF THE FILES */
                                $results[] = '<p class="alert-box success">Overwrote newer file ' . $to[$k] . ' with ' . $f . '.<a href="#" class="close">×</a></p>';
                            } else {
                                $results[] = '<p class="alert-box alert">Error replacing ' . $to[$k] . ' with ' . $f . '.<a href="#" class="close">×</a></p>';
                            }
                        } elseif (filemtime($f) == filemtime($to[$k])) {
                            // $results[] = '<p class="alert-box">Files are the same.<a href="#" class="close">×</a></p>';
                            $sameCount++;
                        } elseif (Request::get('overwrite-new') == 'false' && filemtime($f) < filemtime($to[$k])) {
                            $results[] = '<p class="alert-box alert">Destination file, ' . $to[$k] . ' is newer than ' . $f . ' // ' . filemtime($to[$k]) . ' // ' . filemtime($f) .'<a href="#" class="close">×</a></p>';
                        } else {
                            $results[] = 'Did not capture the problem...';
                        }
                    } elseif (copy($f, $to[$k])) {
                        touch($to[$k], filemtime($f));  /* SYNC MODIFY DATES */
                        $results[] = '<p class="alert-box success">Moved ' . $f . ' to ' . $to[$k] . '<a href="#" class="close">×</a></p>';
                    } else {
                        $results[] = '<p class="alert-box alert">ERROR! Could not move ' . $f . ' to ' . $to[$k] . '<a href="#" class="close">×</a></p>';
                    }
                }
            }
            if ($sameCount > 0) { $results[] = '<p class="alert-box">' . $sameCount . ' files were the same as the what\'s in the destination folder.<a href="#" class="close">×</a></p>'; }
            $response['results'] = $results;
            return Response::json( $response );
        } elseif ( Request::has('detect') ) {
            $dir = Request::has('url') ? '~' . substr(Request::get('url'), stripos(Request::get('url'), '/Sites') ) : '~/Sites';
            $output = explode("\n", shell_exec('find ' . $dir . ' -type f -mtime -4 -print | xargs ls -tl'));
            foreach($output as $k => $v) {
                if ( !strpos($v, 'DS_Store') && !strpos($v, 'D2Ctoolkit') ) {
                    $result = explode('/', $v);
                    $url = '/' . implode('/', array_slice($result, 1, count($result) - 2));
                    if ($url !== '/' ) {
                        return Response::json( scan_dir( $url, true ) );
                    }
                }
            }
            /* --- RETURN DEFAULT FILE AND DIRECTORY LIST */
            $url = Request::has('url') ? '~' . substr(Request::get('url'), stripos(Request::get('url'), '/Sites')) : substr(getcwd(), 0, strpos(getcwd(), '/Sites') + 6);
            $url = substr(getcwd(), 0, strpos(getcwd(), '/Sites') ) . substr($url, stripos($url, '/Sites'));
            return Response::json( scan_dir( $url ) );
        } else {
            /* --- RETURN DEFAULT FILE AND DIRECTORY LIST */
            $url = substr(getcwd(), 0, strpos(getcwd(), '/Sites') + 6);
            return Response::json( scan_dir( $url ) );
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}
<?php

namespace App\Services;

class CmdService
{
    public function scanRun()
    {
        $array = scandir(realpath('../.run'));
        foreach ($array as $key => $value) {
            $array[$key] = explode('.', $array[$key])[0];
        }
        return $array;
    }

    public function scanCmd()
    {

        $array = scandir(realpath('../cmd'));
        foreach ($array as $key => $value) {
            $array[$key] = explode('.', $array[$key])[0];
        }
        return $array;
    }

    public function getCmd()
    {
        $run = $this->scanRun();
        $cmd = $this->scanCmd();
        $diff = array_diff($run, $cmd);
        foreach ($diff as $item){
            $file = file_get_contents(realpath('../.run') . '/' . $item . '.run.xml');
            preg_match('#(?<=scriptParameters=")([a-z-_/\.0-9 :.,-=]+(?=">))#', $file, $matches);
            dump($matches[0]);
            //dump($file);
        }

        dd($diff);
    }

}

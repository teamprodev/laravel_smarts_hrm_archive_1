<?php

namespace App\Console\Commands;


use App\Services\MadelineProto\ExportService;
use App\Services\MadelineProto\MTProtoService;
use Illuminate\Console\Command;

class ExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $export = new ExportService();
        $channel_id = readline('Enter channel_id: ');
        $date_start = readline('Enter start date: ');
        $date_end = readline('Enter end date: ');
        $unix_end = strtotime($date_end == "" ? "now" : $date_end);
        $unix_start = strtotime($date_start);
        $date = date_parse_from_format("j.n.Y H:iP", $date_start);
        while ($unix_end > $unix_start) {
            if ($date['hour'] == "") {
                if ($unix_start + 86400 <= $unix_end) {
                    $update = $export->getMessages($channel_id, $unix_start, $unix_start + 86400);
                    $date = date_parse_from_format("j.n.Y H", date("j.n.Y", $unix_start));
                    $path = $export->folderPath($channel_id, '/Users/ramziddinabdumominov/Desktop/MadeLineProtoTest/test/', $date);
                    if (!is_dir($path . '/files')) {
                        mkdir($path . '/files');
                    }
                    file_put_contents($path . 'result.json', json_encode($update));
                    $telegram = $export->ForwardJson($update);
                    file_put_contents($path . 'telegram.json', json_encode($telegram));
                    $unix_start += 86400;
                    if (!is_dir($path . 'files')) {
                        mkdir($path . 'files');
                    }
                    foreach ($update as $messa) {
                        if (array_key_exists('media', $messa)) {
                            if ($messa['media']['_'] =='messageMediaPhoto'){
                                $export->MTProto->MadelineProto->downloadToDir($messa, $path . '/files/');
                            }
                            if (array_key_exists('document', $messa['media'])) {
                                $export->MTProto->MadelineProto->downloadToDir($messa, $path . '/files/');
                                try {
                                    foreach ($messa['media']['document']['attributes'] as $attribute) {
                                        if ($attribute['_'] == 'documentAttributeFilename') {
                                            print_r(PHP_EOL);
                                            print_r(PHP_EOL);
                                            print_r('Downloading ' . $attribute['file_name']);
                                            print_r(PHP_EOL);
                                        }
                                    }
                                } catch (\Exception $e) {
                                    print_r($e->getMessage());
                                    print_r(PHP_EOL);

                                }
                                print_r(PHP_EOL);
                                print_r(PHP_EOL);
                            }
                        }
                    }

                }
            } /*else {
                if ($unix_start + 3600 <= $unix_end) {
                    $unix_start += 3600;
                    print_r(gmdate("j.n.Y H:i", $unix_start));

                    $update = $export->getMessages($channel_id, $unix_start, $unix_start + 3600);
                    print_r($update);
                    print_r(PHP_EOL);
                    $path = $export->folderPath($channel_id, 'C:\Users\Pavilion\Documents\MadelineProto\JSONs\Export/', $date);
                    /*$files = $MTProto->getFiles($update);
                    if(!is_dir($path . '/files')){
                        mkdir($path . '/files');
                    }
                    foreach ($files as $file){
                        $MTProto->downloadMedia($update,$file, $path . '/files/');
                    }*//*
                    $date = date_parse_from_format("j.n.Y H:i", gmdate("j.n.Y H:i", $unix_start));
                    $path = $export->folderPath($channel_id, 'C:\Users\Pavilion\Documents\MadelineProto\JSONs\Export/', $date);
                    file_put_contents($path . 'result.json', json_encode($update));
                }
            }*/
        }
    }
}


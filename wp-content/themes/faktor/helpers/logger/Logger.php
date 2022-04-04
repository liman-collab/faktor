<?php

class Logger
{
    private static $instance = null;
    private static $logs;

    private function __construct()
    {
        self::$logs = get_template_directory() . '/helpers/logger/logs/';
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Logger;
        }

        return self::$instance;
    }

    public function write($order_id, $content)
    {
        $this->deleteHalfOfTheLogs();

        $fileName = 'order-' . $order_id . '_' . date('Y-m-d_H_i_s') . '.txt';
        $path = self::$logs . $fileName;

        $file = fopen($path, 'w');
        fwrite($file, $content);
        fclose($file);
    }

    public function deleteHalfOfTheLogs()
    {
        $files = $this->getFiles();
        $filesNr = count($files);
        $today = date('Y-m-d');

        if($filesNr > 100){
            foreach($files as $key => $value){
                $file = self::$logs . $value;
                if(!strpos($value, $today) && $key < ($filesNr/2)){
                    unlink($file);
                }
            }
        }
    }

    public function getFiles(){
        $files = scandir(self::$logs);
        $filesArr = [];

        foreach($files as $file){
            if(is_file(self::$logs . $file)){
                $filesArr[] = $file;
            }
        }

        return $filesArr;
    }
}
<?php 

namespace App\Logs;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

trait Log 
{
    /**
     * @param $uri
     * @param $pars
     * @param $dataReturn
     */
    public function registerInfoCallApi($uri, $pars, $dataReturn)
    {
        if ($uri == '/view/page') {
            return;
        }

        $date = new \Datetime();
        $hourControl = $date->format('Y-m-d-H');
        $fileName = 'log_' . $hourControl . '.txt';

        $logDesc = 'URI: ' . $uri . ' - ';
        $logDesc .= 'DATETIME:  ' . $date->format('Y-m-d H:i:s') . "\n";
        $logDesc .= 'PARS: ' . json_encode($pars) . "\n";
        $logDesc .= 'RETURN: ' . json_encode($dataReturn) . "\n\n";

        $f = fopen(__DIR__ . '/../../../app/logs/' . $fileName, 'a+');
        fwrite($f, $logDesc);
        fclose($f);
    }

    /**
     * @param $uri
     * @param $pars
     * @param $dataReturn
     * @param null $prefix
     */
    public function registerInfoCallApiPrefix($uri, $pars, $dataReturn, $prefix = null)
    {
        $date = new \Datetime();
        $hourControl = $date->format('Y-m-d-H');
        $fileName = 'log_' . $hourControl . '.txt';

        if ($prefix !== null) {
            $fileName = 'log_' . $prefix . '_' . $hourControl . '.txt';
        }

        $logDesc = 'URI: ' . $uri . ' - ';
        $logDesc .= 'DATETIME:  ' . $date->format('Y-m-d H:i:s') . "\n";
        $logDesc .= 'PARS: ' . json_encode($pars) . "\n";
        $logDesc .= 'RETURN: ' . json_encode($dataReturn) . "\n\n";

        $f = fopen(__DIR__ . '/../../../app/logs/' . $fileName, 'a+');
        fwrite($f, $logDesc);
        fclose($f);
    }

    /**
     * @param $uri
     * @param $pars
     * @param $data
     */
    public function registerErrorApi($uri, $pars, $data)
    {
        $date = new \Datetime();

        // create a log channel
        $log = new Logger('App');
        $hourControl = $date->format('Y-m-d-H');
        $fileName = 'error_api_' . $hourControl . '.txt';

        $log->pushHandler(new StreamHandler(__DIR__ . '/../../../app/logs/' . $fileName, Logger::WARNING));
        
        $headers = getallheaders();
        $clientIp = (isset($headers['X-Real-Ip']) ? $headers['X-Real-Ip'] : '192.168.210.666');

        $log->error('IP:' . $clientIp . "\n");
        $log->error('URI:' . $uri . "\n");
        $log->error('PARS:' . json_encode($pars) . "\n");
        $log->error('RETURN:' . json_encode($data) . "\n\n");
    }

    /**
     * @param $uri
     * @param $pars
     * @param $data
     */
    public function registerErrorApiInfo($uri, $pars, $data)
    {
        $date = new \Datetime();

        // create a log channel
        $log = new Logger('App');
        $hourControl = $date->format('Y-m-d-H');
        $fileName = 'error_api_info_' . $hourControl . '.txt';

        $log->pushHandler(new StreamHandler(__DIR__ . '/../../../app/logs/' . $fileName, Logger::WARNING));

        $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');

        $log->error('URI:' . $uri . "\n");
        $log->error('PARS:' . json_encode($pars) . "\n");
        $log->error('RETURN:' . json_encode($data) . "\n\n");
    }
    
    /**
     * @param $class
     * @param $method
     * @param $message
     */
    public function logError($class, $method, $message)
    {
        $log = new Logger('App');
        $hourControl = (new \Datetime())->format('Y-m-d-H');
        $fileName = 'error_log_' . $hourControl . '.txt';

        $log->pushHandler(new StreamHandler(__DIR__ . '/../../../app/logs/' . $fileName, Logger::WARNING));

        $log->error('CLASS:' . $class . "\n");
        $log->error('METHOD:' . $method . "\n");
        $log->error('MESSAGE:' . $message . "\n\n");
    }

    /**
     * @param $class
     * @param $method
     * @param $message
     */
    public function logErrorGlobal($class, $method, $message)
    {
        $log = new Logger('App');
        $hourControl = (new \Datetime())->format('Y-m-d-H');
        $fileName = 'error_log_global_' . $hourControl . '.txt';

        $log->pushHandler(new StreamHandler(__DIR__ . '/../../../app/logs/' . $fileName, Logger::WARNING));

        $log->error('CLASS:' . $class . "\n");
        $log->error('METHOD:' . $method . "\n");
        $log->error('MESSAGE:' . $message . "\n\n");
    }


    /**
     * @param $class
     * @param $method
     * @param $message
     */
    public function logCmd($class, $method, $message)
    {
        $log = new Logger('App');
        $hourControl = (new \Datetime())->format('Y-m-d-H');
        $fileName = 'log_cmd_' . $hourControl . '.txt';

        $log->pushHandler(new StreamHandler(__DIR__ . '/../../../app/logs/' . $fileName, Logger::WARNING));

        $log->error('CLASS:' . $class . "\n");
        $log->error('METHOD:' . $method . "\n");
        $log->error('MESSAGE:' . $message . "\n\n");
    }
}
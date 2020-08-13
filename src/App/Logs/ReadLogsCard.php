<?php

namespace App\Logs;

use Doctrine\ORM\EntityManager;

class ReadLogsCard
{
    use Log;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->readLogFile();
    }

    public function readLogFile()
    {
        $dateTime = new \DateTime();
        $formatDate = $dateTime->format('Y-m-d-H');
        $pathFile =  'log_ftm_' . $formatDate . '.txt';
        $this->read($pathFile);

        $dateTime = $dateTime->modify('+1 hour');
    }

    public function read($filename)
    {
        try {
            
            $pars = $this->getContentFileAndRegister($filename);

            foreach ($pars as $item) {
                
                if (strstr($item, '/payment - DATETIME') != '') {

                    $fileData = $this->getFileContent($item);
                    $data = $fileData['data'];  
                    $status = $fileData['status']; 
                    $headerPars = $fileData['headerPars'];
                    $dateTime = $fileData['dateTime'];
                    
                    $entryList = $data['entryList'];
                    
                    foreach ($entryList as $billing) {

                        foreach ($billing['entrySubList'] as $subListItem) {
                                
                            $selectTrans = "
                                SELECT id
                                FROM ftm_transactions
                                WHERE order_payment = '".$data['orderID']."' 
                                AND  invoiceId = '".$subListItem['invoiceId']."' ";

                            $resultTrans = $this->executeSql($selectTrans);
                            $condInsert  = !$resultTrans; 
                            
                            if ($condInsert) {

                                $status = strstr($headerPars, "200 OK"); 
                                $pars = [
                                    'order' => $data['orderID'],
                                    'invoiceId' => $subListItem['invoiceId'],
                                    'doc' => $data['userID'],
                                    'value' => $subListItem['value'],
                                    'system' => $billing["systemId"],
                                    'status' => $status ? 1 : '0',
                                    'http_status' => $headerPars,
                                    'data_cad' => $dateTime,
                                    'file' => $filename
                                ];

                                $this->executeSql("
                                    INSERT INTO ftm_transactions (
                                    order_payment, invoiceId, doc,
                                    value_payment, system_cli, status, 
                                    http_status, data_cad, file)
                                    VALUES (
                                    '".$pars['order']."', '".$pars['invoiceId']."', '".$pars['doc']."',
                                    '".$pars['value']."', '".$pars['system']."', '".$pars['status']."',
                                    '".$pars['http_status']."', '".$pars['data_cad']."', '".$pars['file']."')", 
                                null);
                            }
                        }
                    }
                    
                }
            }
        } catch(\Exception $e) {

        }
    }

    public function getContentFileAndRegister($filename)
    {
        $file = new \SplFileObject("../app/logs/" . $filename);
        $content = $file->fread($file->getSize());
        $pars = explode("\n\n", $content);
        
        $sql = "
                SELECT id
                FROM ftm_logs_check
                WHERE file = '".$filename."'";

        $result = $this->executeSql($sql);
        if ($result === false) {
            $sqlInsert = " INSERT INTO ftm_logs_check (file, date_cad) 
                           VALUES ('".$filename."', NOW())";

            $this->executeSql($sqlInsert, null);
        }

        return $pars;
    }

    public function getFileContent($item)
    {
        $partsStatus = trim(explode('RETURN: ', $item)[1]);
        $status = (strlen($partsStatus) == 0);

        $parts = explode('PARS: ', $item)[1];
        $partsHeader = explode('HEADERS', $parts);
        $parsJson = trim($partsHeader[0]);
        
        try {
            $header = explode('RETURN', $partsHeader[1])[0];
            $header = trim( substr($header, 2, strlen($header)) );
            $headerPars = json_decode($header, true)[0];
            
            $data = json_decode($parsJson, true);

            $dateTime = str_replace('.021-0300', '',  $data['requisition']['requisitionDateTime']);
            $dateTime = str_replace('T', ' ',  $dateTime);

            return [
                'status'     => $status,
                'data'       => $data,
                'headerPars' => $headerPars,
                'dateTime'   => $dateTime
            ];
        } catch(\Exception $e) {
            return [
                'status'     => false,
                'data'       => [],
                'headerPars' => [],
                'dateTime'   => $dateTime
            ];
        }
        
    }

    

    /**
     * @param $sql
     * @param string $get
     * @return array|bool|mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executeSql($sql, $get = 'unique')
    {
        $stmt = $this->em->getConnection()->prepare($sql);
        try {
            $stmt->execute();
        } catch (\Exception $e) {
            $this->logError(__CLASS__, __METHOD__, $e->getMessage());
        }

        if ($get == 'all') {
            return $stmt->fetchAll();
        }

        if ($get == null) {
            return true;
        }

        return $stmt->fetch();
    }

}
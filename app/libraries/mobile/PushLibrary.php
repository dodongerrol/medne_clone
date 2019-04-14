<?php

use Gomoob\Pushwoosh\Client\Pushwoosh;
use Gomoob\Pushwoosh\Model\Notification\Notification;
use Gomoob\Pushwoosh\Model\Request\CreateMessageRequest;


class PushLibrary{
    
    public static function PushConfig(){
        $pushwoosh = Pushwoosh::create()
        ->setApplication('91318-ADF8A')
        ->setAuth('j4d5nRTSZovr3v4ZYuzCDZ5GZ8qZOjYho7kPHcSjYPTJvNiarCJ1vLBL7LQ5nhiPo2LbfZNqZS3Lip9Yd2HZ');
        return $pushwoosh;
    }
    
    public static function PushAllDevice(){
        $response =self::ManageAllDevices($data,$content);
        return $response;
    }
    public static function PushSingleDevice($content,$data,$devices){
        $response = self::ManageSingleDevice($content,$data,$devices);
        return $response;
    }

    private static function ManageAllDevices($content,$data){
        $pushwoosh = self::PushConfig();
        $request = CreateMessageRequest::create()->setNotifications(
            array(
                Notification::create()->setContent($content),
                Notification::create()->setData($data)    
                )	
            );
        $response = $pushwoosh->createMessage($request);
        return $response;
    }
    private static function ManageSingleDevice($content,$data,$devices){
        $pushwoosh = self::PushConfig();
        $request = CreateMessageRequest::create()
        ->addNotification(self::ManagePushArray($content,$devices,$data));

        $response = $pushwoosh->createMessage($request);
        return $response;
    }
    
    
    private static function ManagePushArray($content,$devices,$data){
        $pushArray = Notification::create()->setContent($content)->setDevices($devices)->setData($data);
        return $pushArray;
    }
    

    
    
    
    
    
    
    public static function PushTest(){
        $content ='Your session is concluded';
        $devices = array('f315a1ddf6f53c071e424d9424fdb124d4485a7c33de2344071d4957fefabe6e',
            'APA91bFww6HmRm_GJQVIUr-JrhxE1J3UdOejLZdpktTTFM4s9cwF5K2QkWh6YzOdnZpUqzOWI4T3byghUuJy64LnaKodyl67P9-5tk2pga3XTFD3-WoJP1gJfDEUvkIOGiy4OW_Da158navtoJ25O85lcj-AINkExQ');
        //$devices = array();
        //$data = array();
        $data = array(
        'custom'=>array('doctor_id' => '2', 'clinic_id'=> '32', 'doctorslot_id'=> '2'));
        
        //$data['data']['custom']['doctor_id']= 12;
        //$data['data']['custom']['clinic_id']= 32;
        //$data['data']['custom']['doctorslot_id']= 2;
        $response = self::PushSingleDevice($content,$data,$devices);
        echo '<pre>';
        print_r($response);
        echo '</pre>';
    }

        
    public static function PushTest1(){
        $pushwoosh = self::PushConfig();
        $data['data']['custom']['doctor_id'] = 12;
        $data['data']['custom']['clinic_id'] = 2;
        $data['data']['custom']['doctorslot_id'] = 12;
        
        $request = CreateMessageRequest::create()->setNotifications(
            array(
                Notification::create()->setContent('Dr. J Liyanage session start at 12.30 PM today. App No 11'),
                /*Notification::create()->setDevices(
                    array('24b3026779c3b1b0e2a9ef1ab71e6247d73800f5',
                            'f34f42993aaa2ded99c824129aae2f99be83c782'
                        )
                    ),*/
                Notification::create()->setData($data)    
                    /*array('data_parameter_1' => 'data_parameter_1_value',         
                            'data_parameter_2' => 'data_parameter_2_value' 
                        ) 
                    )*/
                )	
            );

        // Call the REST Web Service
        $response = $pushwoosh->createMessage($request);
        // Check if its ok
        if($response->isOk()) {
            print 'Great, my message has been sent !';
        } else {
            print 'Oups, the sent failed :-('; 
            print 'Status code : ' . $response->getStatusCode();
            print 'Status message : ' . $response->getStatusMessage();
        }
    }

    
    
    
    
    
}


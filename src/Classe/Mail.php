<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;


class Mail
{
  
    private $api_key ='48063238350f566843b82d0bf2bb4f64';
    private $api_key_secret = '736cd41a354d69e2de43ab3c50495603';

    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "cheungkevin@hotmail.fr",
                        'Name' => "La Boutique Française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 2355952,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                    ]
                    ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
        
    }
}
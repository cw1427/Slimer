# Slimer mail config

Slimer has mial util to send PHP mail based on [nette/mail](https://packagist.org/packages/nette/mail) v2.4.5

## smtp mail server config

```PHP
<?php
/*
 * Author: Shawn Chen
 * Date: 2018年9月22日
 */
return [
    'host' => 'smpt.your.server',
    'username' => 'your smtp authen account',
    'password' => '*****',
    //'secure' => 'ssl',
];
```

## mail send util function

Slimer make a util.php class to provide 

```PHP
    public function sendMail($mailFrom,$maileTo, $mailCc,$subject,$content){
        if ('yes' === \getenv('NO_MAIL')){
            return true;
        }
        if ($mailFrom == null){
            $mailFrom = 'Global-Account-Management-Group <gamgrp@motorola.com>';
        }
        $message = $this->container['smtpMessage'];
        $message->setFrom($mailFrom);
        if (\is_array($maileTo)){
            foreach ($maileTo as $to){
                $message->addTo($to);
            }
        }else{
            $message->addTo($maileTo);
        }
        if ($mailCc != null) {
            if (\is_array($mailCc)){
                foreach ($mailCc as $cc){
                    $message->addCc($cc);
                }
            }else{
                $message->addCc($mailCc);
            }
        }
        $message->setSubject($subject);
        $message->setHTMLBody($content);
        //$message->addEmbeddedFile(APP_PATH . DS . 'Static' . DS . 'img' . DS . 'spacer.gif');
        $this->container['smtpMailer']->send($message);
    }
```

> NO_MAIL env value to disable mail sending

Slimer add an environment variable **NO_MAIL** checking to decide if need to send mail or not.

> smtpMessage

Slimer register a prototype type smtpMessage in the Container to generate a smpt message object
```PHP
        $container['smtpMessage'] = $container->factory(function () use ($container) {
            return new \Nette\Mail\Message();
        });
```

> smtpMailer

```PHP
        $container['smtpMailer'] = function () use ($container) {
            return new \Nette\Mail\SmtpMailer($container['config']('mail'));
        };
```



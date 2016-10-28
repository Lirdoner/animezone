<?php


namespace Sequence\Provider;


use Sequence\ProviderInterface;
use Sequence\Container;
use Sequence\Mail\Mailer;

class MailerProvider implements ProviderInterface
{
    public function register(Container $container, $options)
    {
        $options = $options['options'];

        if(!array_key_exists('send_from', $options) || !array_key_exists('send_name', $options))
        {
            throw new \InvalidArgumentException('Missing send_form or send_name.');
        }

        $container->set('mailer', function() use ($options) {
            $mail = new Mailer(true);
            $mail->Debugoutput = 'html';
            $mail->setFrom($options['send_from'], $options['send_name']);
            $mail->addReplyTo($options['send_from'], $options['send_name']);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            if(!empty($options['smtp']))
            {
                $required = array('host', 'port', 'encryption', 'username', 'password');

                $diff = array_diff($required, array_keys($options['smtp']));

                if(!empty($diff))
                {
                    throw new \InvalidArgumentException(sprintf('Missing smtp settings: "%s"', implode(', ', $diff)));
                }

                $mail->isSMTP();
                $mail->SMTPDebug = 0;

                $mail->Host = $options['smtp']['host'];
                $mail->Port = $options['smtp']['port'];
                $mail->SMTPSecure = $options['smtp']['encryption'];
                $mail->SMTPAuth = true;
                $mail->Username = $options['smtp']['username'];
                $mail->Password = $options['smtp']['password'];
            } else
            {
                $mail->isSendmail();
            }

            return $mail;
        });
    }
} 
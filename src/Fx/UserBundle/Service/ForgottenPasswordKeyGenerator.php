<?php

namespace Fx\UserBundle\Service;

use Fx\UserBundle\Entity\User;

class ForgottenPasswordKeyGenerator {

    const KEY_EXPIRATION_TIME = 7200; // In seconds
    const DELAY_BETWEEN_REPEATED_FORGOTTEN_PASSWORD_REQUEST = 300; // in seconds

    public function generateForgottenPasswordKey(User $user)
    {
        // Randomly generated key
        $forgottenPasswordKey = sha1(microtime(true)
            .mt_rand(10000,90000)
            .substr($user->getEmail(),0,3));

        return $forgottenPasswordKey;
    }

    public function generateForgottenPasswordKeyExpirationDateTime()
    {
        $expirationDateTime = new \DateTime();
        $dateIntervalForExpiration = "PT".self::KEY_EXPIRATION_TIME."S";
        $expirationDateTime->add(new \DateInterval($dateIntervalForExpiration));

        return $expirationDateTime;
    }

    public function isAbleToGenerateKey(User $user)
    {
        // We have to verify if user don't have already submitted a forgotten password request.
        if($user->getForgottenPasswordKeyExpiresAt() != null) {

            $userForgottenPasswordKeyExpiresAtTimeStamp = $user->getForgottenPasswordKeyExpiresAt()->getTimestamp();
            $actualTimestamp = new \DateTime();
            $actualTimestamp = $actualTimestamp->getTimestamp();

            $secondsElapsedSinceLastRequest =
                self::KEY_EXPIRATION_TIME
                - ($userForgottenPasswordKeyExpiresAtTimeStamp - $actualTimestamp);

            if ($secondsElapsedSinceLastRequest < self::DELAY_BETWEEN_REPEATED_FORGOTTEN_PASSWORD_REQUEST) {
                $totalTimeToWaitBeforeNewRequestInSeconds = self::DELAY_BETWEEN_REPEATED_FORGOTTEN_PASSWORD_REQUEST - $secondsElapsedSinceLastRequest;

                $errorMessage = $this->buildTimeRequiredToWaitBeforeNewForgottenPasswordMessage($totalTimeToWaitBeforeNewRequestInSeconds);

                return [ 'error' => true, 'success' => false, 'errorMessage' => $errorMessage];

            }
        }

        return [ 'success' => true, 'error' => false];
    }

    private function buildTimeRequiredToWaitBeforeNewForgottenPasswordMessage($totalTimeToWaitBeforeNewRequestInSeconds) {

        $timeToWaitBeforeNewRequestInMinutes = floor($totalTimeToWaitBeforeNewRequestInSeconds / 60);

        $message = "Impossible de soumettre une demande de nouveau mot de passe car une autre demande est déjà en cours. ";
        $message.= "Merci de patienter ";

        // Minutes to display in flash message
        if($timeToWaitBeforeNewRequestInMinutes > 0) {
            if($timeToWaitBeforeNewRequestInMinutes > 1) {
                $message .= ($timeToWaitBeforeNewRequestInMinutes . " minutes");
            } else {
                $message .= ($timeToWaitBeforeNewRequestInMinutes . " minute");
            }
        }

        // Space between minutes and seconds if required.
        if($timeToWaitBeforeNewRequestInMinutes > 0 && $totalTimeToWaitBeforeNewRequestInSeconds % 60 != 0) {
            $message .= " et ";
        }

        // Seconds to display in flash message
        if($totalTimeToWaitBeforeNewRequestInSeconds % 60 != 0) {
            if($totalTimeToWaitBeforeNewRequestInSeconds % 60 > 1) {
                $message .= (($totalTimeToWaitBeforeNewRequestInSeconds % 60) . " secondes");
            } else {
                $message .= (" 1 seconde");
            }
        }

        $message.= " avant de pouvoir soumettre une nouvelle demande de nouveau mot de passe.";

        return $message;
    }


}
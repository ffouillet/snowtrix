<?php

namespace Fx\UserBundle\Service;

use Fx\UserBundle\Entity\User;

class ForgottenPasswordKeyGenerator {

    const KEY_EXPIRATION_TIME = 7200; // In seconds

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
}
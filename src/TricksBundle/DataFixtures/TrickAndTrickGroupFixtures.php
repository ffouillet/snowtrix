<?php


namespace TricksBundle\DataFixtures;

use TricksBundle\Entity\Trick;
use TricksBundle\Entity\TrickGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TrickAndTrickGroupFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Create Two Trick Groups
        $trickGroup['grabs'] = new TrickGroup();
        $trickGroup['grabs']->setName('Grab');
        $trickGroup['grabs']->setSlug('grab');
        $trickGroup['grabs']->setDescription('Un grab consiste à attraper la planche avec la main pendant le saut. Le verbe anglais to grab signifie « attraper. » ');

        $trickGroup['slides'] = new TrickGroup();
        $trickGroup['slides']->setName('Slide');
        $trickGroup['slides']->setSlug('slide');
        $trickGroup['slides']->setDescription('Le Slide (aussi appelé Jib et Grind) est une pratique du snowboard freestyle consistant à glisser sur tous types de modules autre que la neige (rails, troncs d\'arbre, caisse en plastique, etc.).');

        // Create Tricks
        $tricksData['grabs'] = [
                ['Mute','mute','Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.'],
                ['Sad','sad','Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.'],
                ['Indy','indy','Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.'],
                ['Stalefish','stalefish','Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.'],
                ['Tail Grab','tail-grab','Saisie de la partie arrière de la planche, avec la main arrière.'],
                ['Japan Air','japan-air','Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.']
        ];

        $tricksData['slides'] = [
            ['50-50','50-50','Une figure dans laquelle le snowboarder glisse sur un rail ou un autre obstacle avec sa planche. La planche est en contact direct avec le rail ou l\'obstacle.'],
            ['Boardslide', 'boardslide', 'Une figure dans laquelle le snowboarder glisse perpendiculairement sur un rail ou autre obstacle avec sa planche. La planche est en contact direct avec le rail ou l\'obstacle.'],
            ['Noseslide', 'noseslide', 'Une figure dans laquelle le snowboarder glisse avec le "nez" de sa planche sur un obstacle ou un rail. Similaire au boardslide sauf que dans cette figure, seul le bout de la planche est en contact avec l\'obstacle.'],
            ['MJ','mj', 'Une figure dans laquelle le snowboarder glisse avec la tranche de sa planche sur un obstacle ou un rail. Cette figure se nomme ainsi car elle rapelle un mouvement de danse de Michael Jackson.']
        ];

        // Creating tricks
        foreach($tricksData as $trickGroupName => $tricksDataByGroup) {

            foreach($tricksDataByGroup as $tricksDatas) {
                $trick = new Trick();
                $trick->addGroup($trickGroup[$trickGroupName]);
                $trick->setName($tricksDatas[0]);
                $trick->setSlug($tricksDatas[1]);
                $trick->setDescription($tricksDatas[2]);
            }
        }

        // Persisting trick group
        $manager->persist($trickGroup['slides']);
        $manager->persist($trickGroup['grabs']);

        $manager->flush();
    }
}
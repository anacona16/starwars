<?php

namespace App\DataFixtures;

use Craue\ConfigBundle\Entity\Setting;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConfigSettingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $settings = [
            'api_films_requested_pages' => null,
            'api_characters_requested_pages' => null,
        ];

        foreach ($settings as $_setting => $value) {
            $setting = new Setting();
            $setting->setName($_setting);
            $setting->setValue($value);

            $manager->persist($setting);
        }

        $manager->flush();
    }
}
